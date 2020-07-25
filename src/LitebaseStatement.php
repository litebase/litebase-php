<?php

namespace SpaceStudio\Litebase;

use PDO;
use PDOStatement;

class LitebaseStatement extends PDOStatement
{
    protected $boundParams = [];
    protected $query = '';
    protected $rowCount;

    public function __construct(LitebaseClient $client, $query)
    {
        $this->client = $client;
        $this->query = $query;
    }

    public function bindParam(
        $parameter,
        &$variable,
        $data_type = PDO::PARAM_STR,
        $length = null,
        $driver_options = null
    ) {
        $this->boundParams[$parameter] = &$variable;
    }

    public function bindValue($parameter, $value, $data_type = PDO::PARAM_STR)
    {
        if (is_int($parameter)) {
            $this->boundParams[$parameter - 1] = $value;
        } else {
            $this->boundParams[$parameter] = $value;
        }

        return true;
    }

    public function columnCount()
    {
        return count($this->columns);
    }

    public function debugDumpParams()
    {
        var_dump($this->query, $this->boundParams);
    }

    public function errorCode()
    {
        return $this->client->errorCode();
    }

    public function errorInfo()
    {
        return $this->client->errorInfo();
    }

    public function execute($params = [])
    {
        $result = $this->client->exec([
            "statement" => $this->query,
            "parameters" => array_merge($this->boundParams, $params),
        ]);

        if (isset($result['data'][0])) {
            $this->columns = array_keys($result['data'][0]);
            $this->rows = $result['data'];
            $this->rowCount = count($this->rows);
            $this->cursor = 0;
        }
    }

    public function fetch(
        $fetchStyle = PDO::ATTR_DEFAULT_FETCH_MODE,
        $cursorOrientation = PDO::FETCH_ORI_NEXT,
        $cursorOffset = 0
    ) {
        //
    }

    public function fetchAll(
        $fetchStyle = PDO::ATTR_DEFAULT_FETCH_MODE,
        $fetchArgument = 0,
        $ctorArgs = null
    ) {
        if ($fetchStyle === PDO::ATTR_DEFAULT_FETCH_MODE) {
            $fetchStyle = $this->fetchMode;
        }

        switch ($fetchStyle) {
            case PDO::FETCH_BOTH:
        }

        return $this->rows;
    }

    public function fetchColumn($columnNo = 0)
    {
        $column = array();
        for ($i = 0; $i < $this->rowCount; $i++) {
            $column[$i] = $this->rows[$i]['row'][$columnNo];
        }

        return $column;
    }

    public function getBoundParams()
    {
        return $this->boundParams;
    }

    public function rowCount()
    {
        return $this->rowCount;
    }

    public function setFetchMode($mode, $params = NULL)
    {
        //TODO: How does this impact the statement?
        $this->fetchMode = $mode;

        return true;
    }
}
