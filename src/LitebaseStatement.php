<?php

namespace Litebase;

use Iterator;
use IteratorAggregate;
use Litebase\Exceptions\QueryException;
use PDO;
use PDOStatement;

class LitebaseStatement extends PDOStatement implements IteratorAggregate
{
    protected $boundParams = [];

    /**
     * The Litebase Client instance.
     *
     * @var LitebaseClient
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
    public function __construct(LitebaseClient $client, $query)
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
        $type = "NULL";

        switch ($data_type) {
            case PDO::PARAM_BOOL:
            case PDO::PARAM_INT:
                $type = "INTEGER";
                break;
            case PDO::PARAM_STR:
                $type = "TEXT";
                break;
            case PDO::PARAM_NULL:
                $type = "NULL";
                break;
            // TODO: Test BLOB type
            case PDO::PARAM_LOB:
                $type = "BLOB";
                break;
            // TODO: Add a case for float type
            // case PDO::PARAM_FLOAT:
            // $type = "REAL";
            // break;
            default:
                $type = "TEXT"; // Default to TEXT if no match
                break;
        }

        if (is_int($parameter)) {
            $this->boundParams[$parameter - 1] = [
                'type' => $type,
                'value' => $value,
            ];
        } else {
            $this->boundParams[$parameter] = [
                'type' => $type,
                'value' => $value,
            ];
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

        if (isset($response->errorMessage)) {
            throw new QueryException($response->errorMessage, $this->query, $params);
        }

        $this->result = $response ?? [];

        if (isset($this->result->columns)) {
            $this->columns = $this->result->columns;
        }

        if (isset($this->result->rows)) {
            $this->rows = array_map(function ($row) {
                return array_combine($this->columns, $row);
            }, $this->result->rows);
        }

        if (isset($this->result->rowsCount)) {
            $this->rowCount = $this->result->rowsCount;
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

    public function rowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * @inheritDoc
     */
    public function setFetchMode(int $mode, mixed ...$args): true
    {
        $this->fetchMode = $mode;

        return true;
    }
}
