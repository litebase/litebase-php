<?php

namespace Litebase;

use Exception;
use React\Datagram\Factory;
use React\Datagram\Socket;
use React\EventLoop\Loop;

class DatabaseConnection
{
    /**
     * The litebase client instance.
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
        $this->open();
    }

    /**
     * Close the database connection.
     */
    public function close(): bool
    {
        if (!$this->id) {
            return false;
        }

        $this->opened = false;
        $this->id = null;

        return true;
    }

    public function open()
    {
        try {
            $response = $this->transmit(['type' => 'connection']);
            $this->id = $response['id'];
            $this->opened = true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * The url to the proxy server.
     */
    public function port(): int
    {
        return $this->client->getQueryProxyPort();
    }

    /**
     * Send a query request to the proxy.
     */
    public function send(array $data)
    {
        return $this->transmit([
            'type' => 'query',
            'connection_id' => $this->id,
            'data' => $data,
        ]);
    }

    /**
     * Transit a message to the query proxy server.
     */
    public function transmit(array $message)
    {
        $response = '';
        $loop = Loop::get();
        $factory = new Factory($loop);

        $factory->createClient("localhost:{$this->port()}")
            ->then(function (Socket $client) use ($message, &$response) {
                $client->send(json_encode($message) . PHP_EOL);

                $client->on('message', function ($message) use ($client, &$response) {
                    $response = json_decode($message, true);
                    $client->close();
                    Loop::stop();
                });

                // @todo: Add proper timeout and throw exception if no response is received.
                Loop::addTimer(3, function () use ($client) {
                    $client->close();
                    Loop::stop();
                });

                $client->on('error', function ($error) {
                    throw new Exception($error);
                });
            }, function ($error) {
                throw $error;
            });

        $loop->run();

        return $response;
    }
}
