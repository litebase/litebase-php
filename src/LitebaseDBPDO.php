<?php

namespace LitebaseDB;

use PDO;
use PDOStatement;

class LitebaseDBPDO extends PDO
{
    protected LitebaseDBClient $client;

    public function __construct(array $config)
    {
        $this->client = new LitebaseDBClient($config);
    }

    public function beginTransaction(): bool
    {
        return $this->client->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->client->commit();
    }

    public function connect()
    {
        return;
    }

    public function disconnect()
    {
        return;
    }

    public function errorCode(): null|string
    {
        return $this->client->errorCode();
    }

    public function errorInfo(): array
    {
        return $this->client->errorInfo();
    }

    public function exec(string $statement): int
    {
        return $this->client->exec([
            'statement' => $statement
        ]);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function hasError()
    {
        $code = $this->errorCode();

        if (is_null($code)) {
            return false;
        }

        return  $code >= 0;
    }

    public function inTransaction(): bool
    {
        return $this->client->inTransaction();
    }

    public function lastInsertId($name = null): string|false
    {
        return $this->client->lastInsertId();
    }

    public function prepare($statement, $options = null): PDOStatement
    {
        return new LitebaseDBStatement($this->client, $statement);
    }

    public function rollBack(): bool
    {
        return $this->client->rollback();
    }

    public function setClient(LitebaseDBClient $client)
    {
        $this->client = $client;

        return $this;
    }
}
