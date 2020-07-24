<?php

namespace SpaceStudio\Litebase;

use Exception;
use GuzzleHttp\Client;

class LitebaseClient
{
    /**
     * The base uri of the client.
     */
    const BASE_URI = 'http://litebase.test/database';

    /**
     * The Http client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The database identifier of the client.
     *
     * @var string
     */
    protected $database;

    /**
     * The id of the last instered record.
     *
     * @var null|string
     */
    protected $lastInsertId = null;

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

        $baseUri = static::BASE_URI;
        $this->database = $attributes['database'];

        $this->client = new Client([
            'base_uri' => "{$baseUri}/{$this->database}",
            'timeout'  => 30,
        ] + $clientConfig);
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

            $this->transactionId = $response['data']['id'] ?? null;
        } catch (Exception $e) {
            return false;
        }

        return true;
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
        $result = $this->send('POST', 'exec', $input);

        if (isset($result['last_insert_id'])) {
            $this->lastInsertId = $result['last_insert_id'];
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
     * Rollbacka transaction.
     */
    public function rollback()
    {
        if (!$this->transactionId) {
            return false;
        }

        $this->send('DELETE', 'transaction',  [
            'transaction' => $this->transactionId,
        ]);

        $this->transactionId = null;

        return true;
    }

    /**
     * Set a request to the data api.
     */
    public function send(string $method, string $path, $data)
    {
        try {
            $response = $this->client->request($method, $path, ['json' => $data]);

            return json_decode((string) $response->getBody(), true);
        } catch (Exception $e) {
            $this->errorCode = $e->getCode();
            $this->errorInfo = $e->getMessage();

            throw $e;
        }
    }
}
