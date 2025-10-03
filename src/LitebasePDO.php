<?php

namespace Litebase;

use PDO;
use PDO\Sqlite;
use PDOStatement;

class LitebasePDO extends Sqlite
{
    /**
     * The Litebase client instance.
     */
    protected LitebaseClient $client;

    /**
     * Create a new instance of the PDO connection.
     *
     * @param array<string, string>
     */
    public function __construct(array $config)
    {
        $host = $config['host'] ?? null;
        $port = $config['port'] ?? null;
        $database = $config['database'] ?? null;
        $transport = $config['transport'] ?? 'http';

        $configuration  = new Configuration();

        $configuration
            ->setHost($host)
            ->setPort($port)
            ->setDatabase($database);

        if (isset($config['access_key_id'], $config['access_key_secret'])) {
            $configuration->setAccessKey($config['access_key_id'], $config['access_key_secret']);
        }

        if (isset($config['token'])) {
            $configuration->setAccessToken($config['token']);
        }

        if (isset($config['username'], $config['password'])) {
            $configuration->setUsername($config['username'])
                ->setPassword($config['password']);
        }

        $this->client = new LitebaseClient($configuration)
            ->withTransport($transport);
    }

    /**
     * Being a database transaction.
     */
    public function beginTransaction(): bool
    {
        return $this->client->beginTransaction();
    }

    /**
     * Commit a database transaction.
     */
    public function commit(): bool
    {
        return $this->client->commit();
    }

    /**
     * Return the last error code.
     */
    public function errorCode(): null|string
    {
        return $this->client->errorCode();
    }

    /**
     * Return the error info.
     */
    public function errorInfo(): array
    {
        return $this->client->errorInfo();
    }

    /**
     * Execute an SQL statement and return the number of affected rows.
     */
    public function exec(string $statement): int|false
    {
        $result = $this->client->exec([
            'statement' => $statement,
        ]);

        if (isset($result['error']) || ($result['status'] ?? null) === 'error') {
            return false;
        }

        return $result['changes'] ?? 0;
    }

    public function getAttribute(int $attribute): mixed
    {
        return match ($attribute) {
            PDO::ATTR_SERVER_VERSION => "0.0.0",
            PDO::ATTR_CLIENT_VERSION => "0.0.0",
            default => null,
        };
    }

    /**
     * Determie if the connection has an error.
     */
    public function hasError(): bool
    {
        $code = $this->errorCode();

        if (is_null($code)) {
            return false;
        }

        return  $code >= 0;
    }

    /**
     * Determine if the connection is in a transaction.
     */
    public function inTransaction(): bool
    {
        return $this->client->inTransaction();
    }

    /**
     * Return the last inserted id.
     */
    public function lastInsertId($name = null): string|false
    {
        return $this->client->lastInsertId();
    }

    /**
     * Create a new prepared statement.
     */
    public function prepare($statement, $options = null): PDOStatement
    {
        return new LitebaseStatement($this->client, $statement);
    }

    /**
     * Rollback a database transaction.
     */
    public function rollBack(): bool
    {
        return $this->client->rollback();
    }
}
