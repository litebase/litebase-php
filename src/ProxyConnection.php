<?php

namespace Litebase;

use Closure;
use Clue\React\NDJson\Decoder;
use Exception;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Loop;
use React\Http\Browser;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use React\Stream\ThroughStream;

class ProxyConnection
{
    /**
     * Number of attempts to auto close the connection.
     *
     * @var int
     */
    protected $autoCloseAttepts = 0;

    /**
     * The timer to auto close the connection.
     *
     * @var \React\EventLoop\TimerInterface
     */
    protected $autoCloseTimer;

    /**
     * If the connection is in the process of closing.
     *
     * @var boolean
     */
    protected $closing = false;

    /**
     * The id of the connection.
     *
     * @var string
     */
    protected $id;

    /**
     * The number of requests that are currently being sent.
     *
     * @var integer
     */
    protected $inFlightRequests = 0;

    /**
     * The opened state of the connection.
     *
     * @var boolean
     */
    protected $opened = false;

    /**
     * The stream used to read data from the api.
     *
     * @var \React\Stream\ThroughStream
     */
    protected $reader;

    /**
     * Callbacks to execute for readers.
     *
     * @var array
     */
    protected $readCallbacks = [];

    /**
     * The server of the connection.
     *
     * @var \Litebase\QueryProxyServer
     */
    protected $server;

    /**
     * The stream used to write data to the api.
     *
     * @var \React\Stream\ThroughStream
     */
    protected $writer;

    /**
     * Create a new instance of a proxy connection.
     */
    public function __construct(QueryProxyServer $server, string $id)
    {
        $this->server = $server;
        $this->id = $id;
        $this->init();
    }

    /**
     * Destroy the instance.
     */
    public function __destruct()
    {
        $this->reader->close();
        $this->writer->close();
    }

    /**
     * Close the connection after an elapsed amount of time. Backing off until
     * all in-flight requests are completed.
     */
    public function autoCloseConnection(int $interval = 30, callable $callback = null)
    {
        $this->autoCloseAttepts++;

        if ($this->autoCloseTimer) {
            Loop::cancelTimer($this->autoCloseTimer);
        }

        $this->autoCloseTimer = Loop::addTimer($interval, function () use ($callback, $interval) {
            $this->closing = true;

            if ($this->shouldNotAutoClose()) {
                $this->autoCloseConnection(max($interval / 2, 1), $callback);
                return;
            }

            if ($callback) {
                $callback();
            }

            $this->close();
        });
    }

    public function client(): LitebaseClient
    {
        return $this->server->getClient();
    }

    public function close()
    {
        $this->opened = false;
        $this->end();
        $this->server->closeConnection($this->id);
    }

    /**
     * The the streams of the connection.
     */
    public function end()
    {
        $this->reader->end();
        $this->writer->end();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Return the reader stream of the connection.
     */
    public function getReadStream()
    {
        return $this->reader;
    }

    /**
     * Return the write stream of the connection.
     */
    public function getWriteStream()
    {
        return $this->writer;
    }

    public function hasRequestsInFlight(): bool
    {
        return $this->inFlightRequests <= 0;
    }

    /**
     * Initialize the connection.
     */
    public function init()
    {
        $this->reader = new ThroughStream();
        $this->writer = new ThroughStream();
        $this->readData();
    }

    public function isClosing()
    {
        return $this->closing;
    }

    public function isOpen()
    {
        return $this->opened;
    }

    public function open()
    {
        $this->opened = true;
        $this->openRequest();
        // $this->openConnection();
    }

    public function onResponse(string $id, Closure $callback)
    {
        $this->readCallbacks[$id] = $callback;
    }

    // protected function openConnection()
    // {
    //     $connector = new Connector([
    //         'timeout' => false,
    //         "socket" => [
    //             // "tcp_nodelay" => true
    //         ],
    //     ]);

    //     $connector->connect("{$this->url()}")
    //         ->then(
    //             function (ConnectionInterface $connection) {
    //                 $this->getWriteStream()->on(
    //                     'data',
    //                     function ($data) use ($connection) {
    //                         $connection->write($data);
    //                     }
    //                 );

    //                 $connection->on(
    //                     'data',
    //                     fn ($data) => $this->getReadStream()->write($data)
    //                 );

    //                 $connection->on('error', fn ($error) => print($error));
    //                 $connection->on('close', fn () => $this->close());
    //                 $connection->on('end', fn () => $this->close());
    //             },
    //             fn (Exception $exception) => var_dump('Connection Error: ' . $exception->getMessage())
    //         );
    // }

    protected function openRequest()
    {
        $date = date('U');
        $headers  = [
            'Connection' => 'keep-alive',
            'Host' => $this->client()->getHost(),
            'Transfer-Encoding' => 'chunked',
            'X-LBDB-Date' => $date,
        ];

        $token = $this->client()->getToken(
            method: 'POST',
            path: $this->client()->getDatabasePath('steam'),
            headers: $headers,
            data: [],
        );

        // Loop::addPeriodicTimer(0.01, function () {
        // });
        Loop::addTimer(0.1, function () {
            $this->writer->write(' ');
        });

        // TODO update with signed request.
        (new Browser())
            ->withTimeout(300)
            ->requestStreaming(
                'POST',
                $this->url(),
                [], //$headers + ['Authorization' => $token],
                $this->writer
            )
            ->then(
                function (ResponseInterface $response) {
                    dump('connected', $response->getStatusCode());
                    if ($response->getStatusCode() !== 200) {
                        // @Todo: Handle request error.
                        print('Server error');
                        return;
                    }

                    /** @var \React\Stream\ReadableStreamInterface */
                    $responseBody = $response->getBody();
                    $responseBody->on('data', fn ($data) => $this->getReadStream()->write($data));
                    $this->getWriteStream()->on('end', fn () => $responseBody->close());

                    $responseBody->on('close', fn () => $this->close());
                    $responseBody->on('end', fn () => $this->close());
                    $responseBody->on(
                        'error',
                        fn (Exception $error) => print('Response Body Error: ' . $error->getMessage() . PHP_EOL)
                    );

                    $this->autoCloseConnection(30, fn () => $responseBody->close());
                },
                fn (Exception $exception) => print('Streaming Request Error: ' . $exception->getMessage())
            );
    }

    public function readData()
    {
        $nldJsonStream = new ThroughStream();
        $decoder = new Decoder($nldJsonStream);

        $decoder->on('data', function ($data) {
            $data = (array) $data;

            if (isset($data['id']) && isset($this->readCallbacks[$data['id']])) {
                $this->readCallbacks[$data['id']]($data);
                unset($this->readCallbacks[$data['id']]);
                $this->inFlightRequests--;
            }
        });

        $this->reader->pipe($nldJsonStream);
    }

    public function send(array $data)
    {
        $this->writer->write(json_encode($data) . PHP_EOL);
        $this->inFlightRequests++;
    }

    protected function shouldNotAutoClose()
    {
        return $this->hasRequestsInFlight() || $this->autoCloseAttepts >= 10;
    }

    public function url()
    {
        return $this->server->getClient()->url('stream');
    }
}
