<?php

namespace LitebaseDB;

use Iterator;
use IteratorAggregate;
use LitebaseDB\Exceptions\QueryException;
use PDO;
use PDOStatement;

class LitebaseDBStatement extends PDOStatement implements IteratorAggregate
{
    protected $boundParams = [];

    /**
     * The LitebaseDB Client instance.
     *
     * @var LitebaseDBClient
     */
    protected $client;

    protected $columns;
    protected $fetchMode;
    protected $query = '';
    protected $result;
    protected $rows = [];
    protected $rowCount = 0;

    /**
     * Create a new instance of the prepare statement.
     */
    public function __construct(LitebaseDBClient $client, $query)
    {
        $this->client = $client;
        $this->query = $query;
    }

    /**
     * @inheritDoc
     */
    public function bindParam(
        int|string $param,
        mixed &$variable,
        int $data_type = PDO::PARAM_STR,
        $length = null,
        $driver_options = null
    ): bool {
        $this->boundParams[$param] = &$variable;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function bindValue(int|string $parameter, mixed $value, int $data_type = PDO::PARAM_STR): bool
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

    /**
     * @inheritDoc
     */
    public function columnCount(): int
    {
        return $this->columns ? count($this->columns) : 0;
    }

    /**
     * @inheritDoc
     */
    public function debugDumpParams(): null|bool
    {
        var_dump($this->query, $this->boundParams);

        return null;
    }

    /**
     * @inheritDoc
     */
    public function errorCode(): null | string
    {
        return $this->client->errorCode();
    }

    /**
     * @inheritDoc
     */
    public function errorInfo(): array
    {
        return $this->client->errorInfo();
    }

    /**
     * @inheritDoc
     */
    public function execute($params = []): bool
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

    /**
     * @inheritDoc
     */
    public function fetch(
        $fetchMode = PDO::ATTR_DEFAULT_FETCH_MODE,
        $cursorOrientation = PDO::FETCH_ORI_NEXT,
        $cursorOffset = 0
    ): mixed {
        if ($cursorOrientation !== PDO::FETCH_ORI_NEXT) {
            throw new \RuntimeException("Cursor direction not implemented");
        }

        $result = current($this->rows);

        if (!is_array($result)) {
            return $result;
        }

        $fetchMode = $fetchMode !== null ? [$fetchMode, null] : $this->fetchMode;

        next($this->rows);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array
    {
        if ($mode !== null) {
            $this->setFetchMode($mode, $args['fetchArgument'] ?? 0, $args['ctorArgs'] ?? null);
        }

        $result = iterator_to_array($this);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function fetchColumn($columnIndex = 0): mixed
    {
        $row = $this->fetch();

        if (!is_array($row)) {
            return false;
        }

        return array_search($columnIndex, array_keys($row)) ?? false;
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

    /**
     * @inheritDoc
     */
    public function setFetchMode(int $mode, mixed ...$args)
    {
        $this->fetchMode = $mode;

        return true;
    }
}
