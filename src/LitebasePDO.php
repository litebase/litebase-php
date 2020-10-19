<?php

namespace Litebase;

use Exception;
use PDO;

class LitebasePDO extends PDO
{
    protected $client;

    public function __construct($database, $username, $password)
    {
        if (!ctype_alnum($database)) {
            throw new Exception('The database identifier contains illegal characters.');
        }

        $this->client = new LitebaseClient([
            'database' => $database,
            'username' => $username,
            'password' => $password,
        ]);
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
        return $this->client->openConnection();
    }

    public function disconnect()
    {
        return $this->client->closeConnection();
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

    public function pendingConnection()
    {
        $this->client->shouldConnect();
    }

    public function prepare($statement, $options = null)
    {
        return new LitebaseStatement($this->client, $statement);
    }

    public function query($statement)
    {
        $statement = $this->prepare($statement);

        $statement->execute();

        return $statement;
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
