<?php

namespace Litebase;

use Exception;
use React\Datagram\Factory;
use React\Datagram\Socket;
use React\EventLoop\Loop;
use React\Promise\Promise;
use Throwable;

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
     * Create the ReactPHP Datagram server.
     */
    public function createServer(int $port)
    {
        $factory = new Factory();

        $factory->createServer("localhost:{$port}")
            ->then(function (Socket $server) {


                $server->on('message', function ($message, $address, $server) {
                    $this->handleRequest($message)->then(
                        fn ($response) => $server->send(json_encode($response), $address),
                        function (Exception $error) {
                            var_dump($error->getMessage());
                            throw $error;
                        }
                    );
                });

                $server->on('error', function (Throwable $error) {
                    var_dump($error->getMessage());
                    throw $error;
                });

                try {
                    $this->openConnection();
                } catch (Throwable $th) {
                    var_dump($th->getMessage());

                    throw $th;
                }
            }, function (Throwable $error) {
                var_dump($error->getMessage());
                throw $error;
            });
    }

    public function getClient(): LitebaseClient
    {
        return $this->client;
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
        $instance->createServer($port);

        print("\nLitebase proxy server is running on port: {$port}\n");
    }
}
