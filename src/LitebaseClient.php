<?php

namespace Litebase;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Litebase\Exceptions\LitebaseConnectionException;

class LitebaseClient
{
    /**
     * The base uri of the client.
     */
    const BASE_URI = 'http://sqlite_python';
    // const BASE_URI = 'https://u3t7cfugc9.execute-api.us-east-1.amazonaws.com/databases';

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
     * An error code received from a request.
     *
     * @var int
     */
    protected $errorCode = null;

    /**
     * Error info received from a request.
     *
     * @var string
     */
    protected $errorInfo;

    /**
     * The id of the last instered record.
     *
     * @var null|string
     */
    protected $lastInsertId = null;

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
        if (!isset($attributes['database'])) {
            throw new Exception('The Litebase database is missing.');
        }

        if (!isset($attributes['username'])) {
            throw new Exception('The Litebase database connection cannot be created without a username.');
        }

        if (!isset($attributes['password'])) {
            throw new Exception('The Litebase database connection cannot be created without a password.');
        }

        $this->database = $attributes['database'];

        $this->client = new Client(array_merge([
            'base_uri' => "{$this->baseURI()}/{$this->database}/",
            'headers' => [],
            'http_errors' => false,
            'timeout'  => 30,
            'version' => '2',
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
     * Return the base uri for the client.
     */
    public function baseURI()
    {
        return static::BASE_URI;
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
        return $this->errorCode;
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
        if ($this->connection || $this->waitForConnection()) {
            $result = $this->connection->send($input);
        } else {
            $result = $this->send('POST', 'query', $input);
        }

        if (isset($result['data']['lastID'])) {
            $this->lastInsertId = $result['data']['lastID'];
        }

        return $result;
    }

    /**
     * Return the guzzle http client.
     */
    public function getGuzzleClient(): Client
    {
        return $this->client;
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
     * Set a request to the data api.
     */
    public function send(string $method, string $path, $data = [])
    {
        try {
            $response = $this->client->request($method, $path, ['json' => $data]);
            $result = json_decode((string) $response->getBody(), true);

            if (isset($result['status']) && $result['status'] === 'error') {
                $this->errorCode = $result['code'] ?? null;
                $this->errorInfo = $result['message'];
            }

            return $result;
        } catch (Exception $e) {
            if ($e instanceof ConnectException) {
                throw new LitebaseConnectionException($e->getMessage());
            }

            $this->errorCode = $e->getCode();
            $this->errorInfo = $e->getMessage();

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
            $this->errorCode = $e->getCode();
            $this->errorInfo = $e->getMessage();

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

    public function waitForConnection()
    {
        if ($this->shouldWaitForConnection()) {
            $this->openConnection();
            return true;
        }
    }
}
