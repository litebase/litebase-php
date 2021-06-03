<?php

namespace Litebase;

use React\Datagram\Factory as DatagramFactory;
use React\Datagram\Socket;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Http\Message\Response;
use React\Promise\Promise;

class QueryProxyServer
{
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
     * Create the React PHP Http server.
     */
    public function createServer(LoopInterface $loop)
    {
        $factory = new DatagramFactory($loop);

        $factory->createServer('localhost:8082')
            ->then(function (Socket $server) {
                $server->on('message', function ($message, $address, $server) {
                    $this->handleRequest($message)->then(
                        fn ($response) => $server->send(json_encode($response), $address)
                    );
                });
            });
    }

    public function forwardQuery(array $request): Promise
    {
        return new Promise(function ($resolve) use ($request) {
            $connection = $this->connections[$request['connection_id']];
            $connection->on('data', fn ($data) => $resolve($data));
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
        $id = uniqid(time());
        $connection = new ProxyConnection($this->loop, $id);
        $this->connections[$id] = $connection;
        $connection->openRequest();

        return ['id' => $id];
    }

    /**
     * Run the server.
     */
    public static function run()
    {
        $instance = new static;
        $instance->loop = Factory::create();
        $instance->createServer($instance->loop);
        $instance->loop->run();
    }
}
