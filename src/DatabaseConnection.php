<?php

namespace Litebase;

use Exception;
use React\Datagram\Factory as DatagramFactory;
use React\Datagram\Socket;
use React\EventLoop\Factory;

class DatabaseConnection
{
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
    public function __construct()
    {
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
    public function port()
    {
        // @todo: implement configuration
        return 8081;
    }

    /**
     * Send a query request to the proxy.
     */
    public function send(array $data)
    {
        $response = $this->transmit([
            'type' => 'query',
            'connection_id' => $this->id,
            'data' => $data,
        ]);

        return json_decode($response, true);
    }

    /**
     * Transit a message to the query proxy server.
     */
    public function transmit(array $message)
    {
        $response = null;
        $loop = Factory::create();
        $factory = new DatagramFactory($loop);

        $factory->createClient("localhost:{$this->port()}")
            ->then(function (Socket $client) use ($loop, $message, &$response) {
                $client->send(json_encode($message));

                $client->on('message', function ($message, $serverAddress, $client) use ($loop, &$response) {
                    $response = json_decode($message, true);
                    $loop->stop();
                });

                $loop->addTimer(1, function () use ($loop) {
                    $loop->stop();
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
