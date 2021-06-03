<?php

namespace Litebase;

use Closure;
use Exception;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;
use React\Http\Browser;
use React\Stream\ThroughStream;

class ProxyConnection
{
    /**
     * The id of the connection.
     *
     * @var string
     */
    protected $id;

    /**
     * The loop reference of the connection.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;

    /**
     * The stream used to read data from the api.
     *
     * @var \React\Stream\ThroughStream
     */
    protected $reader;

    /**
     * The stream used to write data to the api.
     *
     * @var \React\Stream\ThroughStream
     */
    protected $writer;

    /**
     * Create a new instance of a proxy connection.
     */
    public function __construct(LoopInterface $loop, string $id)
    {
        $this->id = $id;
        $this->loop = $loop;
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
     * The the streams of the connection.
     */
    public function end()
    {
        $this->reader->end();
        $this->writer->end();
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

    /**
     * Initialize the connection.
     */
    public function init()
    {
        $this->reader = new ThroughStream();
        $this->writer = new ThroughStream();
    }

    public function on(string $key, Closure $callback)
    {
        $this->reader->on($key, $callback);
    }

    public function openRequest()
    {
        (new Browser($this->loop))->requestStreaming('POST', $this->url(), [], $this->getWriteStream())
            ->then(function (ResponseInterface $response) {
                if ($response->getStatusCode() !== 200) {
                    // $connection->getWriteStream()->close();
                    return;
                }

                /** @var ReadableStreamInterface */
                $responseBody = $response->getBody();

                $responseBody->on('data', function ($data) {
                    $this->getReadStream()->write($data);
                });

                $this->getWriteStream()->on('end', function () use ($responseBody) {
                    $responseBody->close();
                });

                $responseBody->on('error', function (Exception $error) {
                    echo 'Error: ' . $error->getMessage() . PHP_EOL;
                });

                // End streaming after 10 seconds.
                $this->loop->addTimer(10, function () {
                    $this->end();
                });
            }, function (Exception $exception) {
                error_log('test');
                var_dump($exception->getMessage());
            });
    }

    public function send(array $data)
    {
        $this->writer->write(json_encode($data) . "\n");
    }

    public function url()
    {
        return "http://localhost:8081/stream";
        // return "https://muddy-wave-4569.fly.dev/stream";
    }
}
