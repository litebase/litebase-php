<?php

namespace LitebaseDB;

use PDO;
use PDOStatement;

class LitebaseDBPDO extends PDO
{
    /**
     * The LitebaseDB client instance.
     *
     * @var LitebaseDBClient
     */
    protected LitebaseDBClient $client;

    /**
     * Create a new instance of the PDO connection.
     *
     * @param array<string, string>
     */
    public function __construct(array $config)
    {
        $this->client = new LitebaseDBClient($config);
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
     * Execute a prepared statement.
     */
    public function exec(string $statement): int
    {
        return $this->client->exec([
            'statement' => $statement
        ]);
    }

    /**
     * Determie if the connection has an error.
     */
    public function hasError()
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
        return new LitebaseDBStatement($this->client, $statement);
    }

    /**
     * Rollback a database transaction.
     */
    public function rollBack(): bool
    {
        return $this->client->rollback();
    }
}
