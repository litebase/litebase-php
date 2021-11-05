<?php

namespace Litebase;

use Exception;
use Iterator;
use IteratorAggregate;
use Litebase\Exceptions\QueryException;
use PDO;
use PDOStatement;

class LitebaseStatement extends PDOStatement implements IteratorAggregate
{
    protected $boundParams = [];

    /**
     * Undocumented variable
     *
     * @var LitebaseClient
     */
    protected $client;
    protected $columns;
    protected $fetchMode;
    protected $query = '';
    protected $result;
    protected $rows = [];
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

    /**
     * @inheritDoc
     */
    public function closeCursor(): bool
    {
        if (isset($this->result['records'])) {
            $this->result['records'] = null;
        }

        return true;
    }

    public function columnCount()
    {
        return $this->columns ? count($this->columns) : 0;
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
        $response = $this->client->exec([
            "statement" => $this->query,
            "parameters" => $params = array_merge($this->boundParams, $params),
        ]);

        if ($this->errorInfo()) {
            list($errorCode, $statusCode, $message) = $this->errorInfo();

            throw new QueryException($message, $this->query, $params);
        }

        $this->result = $response['data'] ?? [];

        if (isset($this->result['rows'])) {
            $this->rows = $this->result['rows'];
        }

        if (isset($this->result['rows'][0])) {
            $this->columns = array_keys($this->result['rows'][0]);
            $this->cursor = 0;
        }

        if (isset($this->result['rowCount'])) {
            $this->rowCount = $this->result['rowCount'];
        }

        return true;
    }

    public function fetch(
        $fetchMode = PDO::ATTR_DEFAULT_FETCH_MODE,
        $cursorOrientation = PDO::FETCH_ORI_NEXT,
        $cursorOffset = 0
    ) {
        if ($cursorOrientation !== PDO::FETCH_ORI_NEXT) {
            throw new \RuntimeException("Cursor direction not implemented");
        }

        $result = current($this->rows);

        if (!is_array($result)) {
            return $result;
        }

        $fetchMode = $fetchMode !== null ? [$fetchMode, null] : $this->fetchMode;

        // advance the pointer and return
        next($this->rows);

        return $result;
    }

    public function fetchAll(
        int $mode = PDO::FETCH_DEFAULT,
        ...$args
    ) {
        $previousFetchMode =  $this->fetchMode;

        if ($mode !== null) {
            $this->setFetchMode($mode, $args['fetchArgument'] ?? 0, $args['ctorArgs'] ?? null);
        }

        $result = iterator_to_array($this);

        // $this->setFetchMode($previousFetchMode);

        return $result;
    }

    public function fetchColumn($columnIndex = 0)
    {
        $row = $this->fetch();

        if (!is_array($row)) {
            return false;
        }

        return array_search($columnIndex, array_keys($row)) ?? false;
    }

    public function getBoundParams()
    {
        return $this->boundParams;
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): Iterator
    {
        while (($row = $this->fetch()) !== false) {
            yield $row;
        }
    }

    public function rowCount()
    {
        return $this->rowCount;
    }

    public function setFetchMode(int $mode, mixed ...$args)
    {
        $this->fetchMode = $mode;

        return true;
    }
}
