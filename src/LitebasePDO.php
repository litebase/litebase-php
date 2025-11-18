<?php

namespace Litebase;

use PDO;
use PDO\Sqlite;
use PDOStatement;

class LitebasePDO extends Sqlite
{
    /**
     * Create a new instance of the PDO connection.
     */
    public function __construct(protected LitebaseClient $client) {}

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
    public function errorCode(): ?string
    {
        return $this->client->errorCode();
    }

    /**
     * Return the error info.
     *
     * @return array<int, int|string|null>
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

        if (! empty($result->errorMessage)) {
            return false;
        }

        return $result->changes ?? 0;
    }

    public function getAttribute(int $attribute): mixed
    {
        return match ($attribute) {
            PDO::ATTR_SERVER_VERSION => '0.0.0',
            PDO::ATTR_CLIENT_VERSION => '0.0.0',
            default => null,
        };
    }

    /**
     * Get the Litebase client instance.
     */
    public function getClient(): LitebaseClient
    {
        return $this->client;
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

        return $code >= 0;
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
        return $this->client->lastInsertId() ?? false;
    }

    /**
     * Create a new prepared statement.
     *
     * @param  array<string, mixed>|null  $options  Driver options.
     */
    public function prepare(string $statement, $options = null): PDOStatement
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
