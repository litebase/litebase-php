<?php

namespace Litebase;

use Closure;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Http\Browser;
use React\Stream\ReadableResourceStream;
use React\Stream\ThroughStream;
use React\Stream\WritableResourceStream;

class DatabaseConnection
{
    /**
     * The Litebase client of the connection
     *
     * @var LitebaseClient
     */
    protected $client;

    /**
     * The id of the database connection.
     *
     * @var string
     */
    protected $id;

    /**
     * The opened state of the database connection.
     *
     * @var boolean
     */
    protected $opened = false;

    /**
     * Create a new instance of a database connection.
     */
    public function __construct(LitebaseClient $client)
    {
        $this->client = $client;
        // $this->open();
        $this->startLoop();
    }

    /**
     * Close the database connection.
     */
    public function close(): bool
    {
        if (!$this->id) {
            return false;
        }

        $client = $this->client;
        $id = $this->id;

        $process = process(function () use ($client, $id) {
            try {
                $client->send('DELETE', "connections/{$id}");
            } catch (Exception $e) {
                logger()->error($e->getMessage());
                // throw $th;
            }
        });

        $process->withTrails()->run();

        $this->opened = false;
        $this->id = null;

        return true;
    }

    public function open()
    {
        try {
            $result  = $this->client->send('POST', 'connections');
            $this->id = $result['data']['id'];
            $this->opened = true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function send(array $data)
    {
        $this->writer->emit('query', ['id' => Str::uuid()->toString()] + $data);
        $result = $this->writer->listen('data');

        return json_decode($result, true);
    }

    public function startLoop()
    {
        $this->writer = process(function ($process) {
            $loop = Factory::create();
            $messages = new ReadableResourceStream($process->socket()->stream(), $loop);
            $browser = new Browser($loop);
            $requestBody = new ThroughStream();
            $url = 'http://127.0.0.1:8081';

            logger('Starting request...');
            $browser->requestStreaming('POST', $url, [], $requestBody)
                ->then(function (ResponseInterface $response) use ($process) {
                    if ($response->getStatusCode() !== 200) {
                        $this->close();
                        return;
                    }

                    /** @var ReadableStreamInterface */
                    $body = $response->getBody();

                    $body->on('data', function ($data) use ($process) {
                        $process->emit('data', $data);
                    });
                });

            $messages->on('data', function ($data) use ($requestBody) {
                $data = json_decode($data, true);
                $requestBody->write(json_encode($data['value']));
            });

            // Timeout after some time...and restart if more messages are sent...(debounce)
            $loop->addTimer(3, function () use ($requestBody) {
                $requestBody->end();
            });

            $loop->run();
        });

        $this->writer->withTrails()->loop();
        $this->writer->dispatch();
    }

    public function url()
    {
        return implode('/', [
            $this->client->baseURI(),
            $this->client->database(),
            'connections',
            $this->id,
        ]);
    }
}
