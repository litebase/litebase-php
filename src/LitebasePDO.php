<?php

namespace SpaceStudio\Litebase;

use Exception;
use PDO;

class LitebasePDO extends PDO
{
    public function __construct($database, $username, $password)
    {
        // $dsn = "sqlite:host=litebase.test;dbname=$database";

        // parent::__construct($dsn, $username, $password, [
        // 	PDO::ATTR_STATEMENT_CLASS => [LitebaseStatement::class, [$this]],
        // ]);

        if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $database)) {
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
        $result = $this->client->exec([
            ["statement" => $statement, "includeStats" => true]
        ]);

        $stats = $result['results'][0]['stats'];

        return $stats['nodes_created'] + $stats['nodes_deleted'] + $stats['properties_set']
            + $stats['relationships_created'] + $stats['relationship_deleted']
            + $stats['labels_added'] + $stats['labels_removed'];
    }

    public function getAttribute($attribute)
    {
    }

    public function inTransaction()
    {
        return $this->client->inTransaction();
    }

    public function lastInsertId($name = null)
    {
        return $this->client->lastInsertId();
    }

    public function prepare($query, $options = null)
    {
        return new LitebaseStatement($this->client, $query);
    }

    public function query($statement)
    {
        return tap($this->prepare($statement), function ($statement) {
            $statement->execute();
        });
    }

    public function rollBack()
    {
        return $this->client->rollback();
    }

    public function setAttribute($attribute, $value)
    {
    }
}
