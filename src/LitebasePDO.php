<?php

namespace Litebase;

use PDO;

class LitebasePDO extends PDO
{
    protected $client;

    public function __construct(array $config)
    {
        $this->client = new LitebaseClient($config);
    }

    public function beginTransaction()
    {
        return $this->client->beginTransaction();
    }

    public function commit()
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

    public function errorCode()
    {
        return $this->client->errorCode();
    }

    public function errorInfo()
    {
        return $this->client->errorInfo();
    }

    public function exec($statement)
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

    public function inTransaction()
    {
        return $this->client->inTransaction();
    }

    public function lastInsertId($name = null)
    {
        return $this->client->lastInsertId();
    }

    public function prepare($statement, $options = null)
    {
        return new LitebaseStatement($this->client, $statement);
    }

    public function rollBack()
    {
        return $this->client->rollback();
    }

    public function setClient(LitebaseClient $client)
    {
        $this->client = $client;

        return $this;
    }
}
