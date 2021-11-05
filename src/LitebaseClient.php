<?php

namespace Litebase;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Litebase\Exceptions\LitebaseConnectionException;

class LitebaseClient
{
    /**
     * The Http client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The database connection of the client.
     *
     * @var DatabaseConnection
     */
    protected $connection;

    /**
     * The database identifier of the client.
     *
     * @var string
     */
    protected $database;

    /**
     * Error info received from a request.
     *
     * @var string
     */
    protected $errorInfo;

    /**
     * The host of the database.
     *
     * @var string
     */
    protected $host;

    /**
     * The accesss key id of the client.
     *
     * @var string
     */
    protected $key;

    /**
     * The id of the last instered record.
     *
     * @var null|string
     */
    protected $lastInsertId = null;

    /**
     * The port for the query proxy server.
     */
    protected $proxyPort;

    /**
     * The region of the database.
     *
     * @var string
     */
    protected $region;

    /**
     * The accesss key secret of the client.
     *
     * @var string
     */
    protected $secret;

    /**
     * Indicates if the client should create a connection before executing
     * any queries.
     *
     * @var bool
     */
    protected $shouldCreateConnection = false;

    /**
     * An active transaction id.
     *
     * @var null|string
     */
    protected $transactionId = null;

    /**
     * Create a new instance of the client.
     */
    public function __construct(array $attributes, array $clientConfig = [])
    {
        if (!isset($attributes['host'])) {
            throw new Exception('The Litebase host is missing.');
        }

        if (!isset($attributes['database'])) {
            throw new Exception('The Litebase database is missing.');
        }

        if (!isset($attributes['key'])) {
            throw new Exception('The Litebase database connection cannot be created without a valid key id.');
        }

        if (!isset($attributes['secret'])) {
            throw new Exception('The Litebase database connection cannot be created without a valid secret key.');
        }

        if (!isset($attributes['region'])) {
            throw new Exception('The Litebase database connection cannot be created without a valid region.');
        }

        $this->host = $attributes['host'];
        $this->database = $attributes['database'];
        $this->proxyPort = $attributes['proxy_port'] ?? 6000;
        $this->key = $attributes['key'];
        $this->secret = $attributes['secret'];
        $this->region = $attributes['region'];

        $this->client = new Client(array_merge([
            'base_uri' => "{$this->host}/",
            'headers' => [
                'Connection' => 'keep-alive',
            ],
            'http_errors' => false,
            'timeout'  => 30,
        ], $clientConfig));
    }

    /**
     * Destroy the instance of the client.
     */
    public function __destruct()
    {
        if ($this->connection) {
            $this->closeConnection();
        }
    }

    /**
     *
     */
    public function beginTransaction(): bool
    {
        // Only allow one transaction to occur at a time.
        if ($this->transactionId) {
            return false;
        }

        try {
            $response = $this->send('POST', 'transaction',  [
                'database' => $this->database,
            ]);

            $this->transactionId = $response['data']['rows'][0]['id'] ?? null;
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Close the database connection.
     */
    public function closeConnection()
    {
        // $this->connection->close();
    }

    /**
     *
     */
    public function commit()
    {
        if (!$this->transactionId) {
            return false;
        }

        $this->send('PUT', 'transaction',  [
            'database' => $this->database,
            'transaction' => $this->transactionId,
        ]);

        $this->transactionId = null;

        return true;
    }

    /**
     * Returns the database identifier.
     */
    public function database(): string
    {
        return $this->database;
    }

    public function errorCode()
    {
        return $this->errorInfo()[0];
    }

    public function errorInfo()
    {
        return $this->errorInfo;
    }

    /**
     * Exectute a statement on the database.
     */
    public function exec(array $input = [])
    {
        $id = uniqid(time());
        $input = array_merge(['id' => $id], $input);

        if ($this->connection || $this->waitForConnection()) {
            $result = $this->connection->send($input);
        } else {
            $result = $this->send('POST', 'query', $input);
        }

        if (isset($result['data']['insertId'])) {
            $this->lastInsertId = $result['data']['insertId'];
        }

        return $result;
    }

    /**
     * Return a database path for a request.
     */
    public function getDatabasePath(string $path)
    {
        return "{$this->database}/$path";
    }

    /**
     * Return the guzzle http client.
     */
    public function getGuzzleClient(): Client
    {
        return $this->client;
    }

    /**
     * Return the host of the client.
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Return the query proxy server port.
     */
    public function getQueryProxyPort(): int
    {
        return $this->proxyPort;
    }

    /**
     * Get an authorization token for a request.
     */
    public function getToken(
        string $method,
        string $path,
        array $headers,
        array $data,
        array $queryParams = [],
    ) {
        return RequestSigner::handle(
            accessKeyID: $this->key,
            accessKeySecret: $this->secret,
            region: $this->region,
            method: $method,
            path: $path,
            headers: $headers,
            data: $data,
        );
    }


    /**
     * Check if the client has a transaction in progress.
     */
    public function inTransaction()
    {
        return (bool) $this->transactionId;
    }

    public function lastInsertId()
    {
        return $this->lastInsertId;
    }

    /**
     * Open a database connection.
     */
    public function openConnection()
    {
        if ($this->connection) {
            return true;
        }

        try {
            $this->connection = new DatabaseConnection($this);

            return true;
        } catch (\Throwable $th) {
            //TODO: Store code and message
            throw $th;

            return false;
        }
    }

    /**
     * Rollbacka transaction.
     */
    public function rollback()
    {
        if (!$this->transactionId) {
            return false;
        }
        // TODO: transform to query
        $this->send('DELETE', 'transaction',  [
            'transaction' => $this->transactionId,
        ]);

        $this->transactionId = null;

        return true;
    }

    /**
     * Send a request to the data api.
     */
    public function send(string $method, string $path, $data = [])
    {
        $path = $this->getDatabasePath($path);
        $date = date('U');

        $headers = [
            'Content-Type' => 'application/json',
            'Content-Length' => strlen(json_encode($data)),
            'Host' => $this->host,
            'X-LBDB-Date' => $date,
        ];

        $token = $this->getToken(
            method: $method,
            path: $path,
            headers: $headers,
            data: $data
        );

        try {
            $response = $this->client->request($method, $path, [
                'json' => $data,
                'headers' => $headers + [
                    'Authorization' => $token,
                ],
            ]);

            $result = json_decode((string) $response->getBody(), true);

            if (isset($result['status']) && $result['status'] === 'error') {
                $this->errorInfo = [
                    $result['code'] ?? 0,
                    $response->getStatusCode(),
                    $result['message'] ?? 'Unknown error',
                ];
            }

            return $result;
        } catch (Exception $e) {
            if ($e instanceof ConnectException) {
                throw new LitebaseConnectionException($e->getMessage());
            }

            $this->errorInfo = [
                0,
                0,
                $e->getMessage()
            ];

            return [];
        }
    }

    /**
     * Set a request to the data api.
     */
    public function sendAsync(string $method, string $path, $data = [])
    {
        try {
            $this->client->requestAsync($method, $path, ['json' => $data]);
            return true;
        } catch (Exception $e) {
            $this->errorInfo = [
                0,
                0,
                $e->getMessage()
            ];

            throw $e;
        }
    }

    public function shouldConnect(): void
    {
        $this->shouldCreateConnection = true;
    }

    public function shouldWaitForConnection(): bool
    {
        return $this->shouldCreateConnection;
    }

    public function url(string $path = '')
    {
        return "http://{$this->host}/{$this->getDatabasePath($path)}";
    }

    public function waitForConnection()
    {
        if ($this->shouldWaitForConnection()) {
            $this->openConnection();
            return true;
        }
    }
}
