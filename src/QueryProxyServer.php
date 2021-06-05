<?php

namespace Litebase;

use Exception;
use React\Datagram\Factory as DatagramFactory;
use React\Datagram\Socket;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Promise\Promise;

class QueryProxyServer
{
    /**
     * The client of the server.
     *
     * @var LitebaseClient
     */
    protected $client;

    /**
     * The connections of the server.
     *
     * @var array<string, \Litebase\ProxyConnection>
     */
    protected $connections = [];

    /**
     * The loop of the service.
     *
     * @var \React\EventLoop\LoopInterface
     */
    protected $loop;

    /**
     * Create a new instance of the server.
     */
    public function __construct(LitebaseClient $client)
    {
        $this->client = $client;
    }

    public function closeConnection($id)
    {
        if (isset($this->connections[$id])) {
            unset($this->connections[$id]);
        }
    }

    /**
     * Create the React PHP Http server.
     */
    public function createServer(LoopInterface $loop, int $port)
    {
        $factory = new DatagramFactory($loop);

        $factory->createServer("localhost:{$port}")
            ->then(function (Socket $server) {
                $server->on('message', function ($message, $address, $server) {
                    $this->handleRequest($message)->then(
                        fn ($response) => $server->send(json_encode($response), $address),
                        fn (Exception $error) => var_dump($error->getMessage())
                    );
                });

                $server->on('error', function (Exception $error) {
                    var_dump($error->getMessage());
                });
            });
    }

    public function getClient(): LitebaseClient
    {
        return $this->client;
    }

    public function getLoop(): LoopInterface
    {
        return $this->loop;
    }

    public function forwardQuery(array $request): Promise
    {
        return new Promise(function ($resolve) use ($request) {
            $connection = $this->connections[$request['connection_id']];
            $connection->onResponse($request['data']['id'], fn ($data) => $resolve($data));
            $connection->send($request['data']);
        });
    }

    /**
     * Handle the incoming request.
     */
    public function handleRequest($request): Promise
    {
        $request = json_decode($request, true);

        return new Promise(function ($resolve) use ($request) {
            if ($request['type'] === 'connection') {
                return $resolve($this->openConnection());
            }

            if ($request['type'] === 'query') {
                return $this->forwardQuery($request)->then(
                    fn ($response) => $resolve($response)
                );
            }
        });
    }

    public function openConnection(): array
    {
        $openConnections = array_filter(
            $this->connections,
            function (ProxyConnection $connection) {
                return $connection->isClosing() === false && $connection->isOpen();
            }
        );

        if (count($openConnections)) {
            return ['id' => current($openConnections)->getId()];
        }

        $id = uniqid(time());
        $connection = new ProxyConnection($this, $id);
        $this->connections[$id] = $connection;
        $connection->open();

        return ['id' => $id];
    }

    /**
     * Run the server.
     */
    public static function run(LitebaseClient $client, int $port = 8100)
    {
        $instance = new static($client);
        $instance->loop = Factory::create();
        $instance->createServer($instance->loop, $port);

        print("\nLitebase proxy server is running on port: {$port}\n");

        $instance->loop->run();
    }
}
