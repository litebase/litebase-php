<?php

namespace Litebase;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Http\Browser;
use React\Http\Message\Response;
use React\Http\Server;
use React\Promise\Promise;
use React\Socket\Server as SocketServer;
use React\Stream\ReadableStreamInterface;
use React\Stream\ThroughStream;

class QueryProxyServer
{
    /**
     * The React PHP Http Browser instnace of the server.
     *
     * @var  \React\Http\Browser
     */
    protected $browser;

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
     * The React PHP Http Server instance.
     *
     * @var \React\Http\Server
     */
    protected $server;

    /**
     * Create the React PHP Http server.
     */
    public function createServer(LoopInterface $loop): Server
    {
        $this->server = new Server($loop, function (ServerRequestInterface $request) {
            return $this->handleRequest($request);
        });

        return $this->server;
    }

    public function forwardQuery(ServerRequestInterface $request): Promise
    {
        return new Promise(function ($resolve) use ($request) {
            $data = $request->getParsedBody();
            $connection = $this->connections[$data['connection_id']];

            $connection->on('data', function ($data) use ($resolve) {
                $resolve($data);
            });

            $connection->send($data['data']);
        });
    }

    /**
     * Handle the incoming request.
     */
    public function handleRequest(ServerRequestInterface $request): Promise
    {
        return new Promise(function ($resolve) use ($request) {
            if ($request->getMethod() === 'POST' && $request->getUri()->getPath() === '/connections') {
                $response = $this->openConnection();

                return $resolve(
                    new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        json_encode($response)
                    )
                );
            }

            if ($request->getMethod() === 'POST' && $request->getUri()->getPath() === '/query') {
                return $this->forwardQuery($request)->then(function ($response) use ($resolve) {
                    $resolve(new Response(200, [], $response));
                });
            }

            $resolve(
                new Response(404, ['Content-Type' => 'application/json'], 'Not found')
            );
        });
    }

    public function openConnection(): array
    {
        $this->connections[$id = uniqid(time())] = $connection = new ProxyConnection($this->loop, $id);

        $connection->openRequest();

        return [
            'data' => [
                'id' => $id,
            ],
        ];
    }

    /**
     * Run the server.
     */
    public static function run()
    {
        $instance = new static;
        $instance->loop = Factory::create();
        $server = $instance->createServer($instance->loop);
        $socket = new SocketServer(8081, $instance->loop);
        $server->listen($socket);
        $instance->loop->run();
    }
}
