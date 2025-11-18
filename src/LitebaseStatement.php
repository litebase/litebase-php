<?php

declare(strict_types=1);

namespace Litebase;

use IteratorAggregate;
use Litebase\Exceptions\QueryException;
use PDO;
use PDOStatement;

/**
 * Class LitebaseStatement
 *
 * A custom PDOStatement implementation for Litebase.
 *
 * @implements IteratorAggregate<int, array<string, mixed>>
 */
class LitebaseStatement extends PDOStatement implements IteratorAggregate
{
    /**
     * @var array<int|string, array{type: ColumnTypeString, value: int|float|string|null}|mixed>
     */
    protected array $boundParams = [];

    /** @var array<int, array{type: ColumnType, name: string}> */
    protected array $columns;

    protected int $fetchMode;

    protected ?QueryResult $result = null;

    /** @var array<int, array<string, mixed>> */
    protected array $rows = [];

    protected int $rowCount = 0;

    /**
     * Create a new instance of the prepare statement.
     */
    public function __construct(protected LitebaseClient $client, protected string $statement) {}

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function bindValue(int|string $parameter, mixed $value, int $data_type = PDO::PARAM_STR): bool
    {
        $type = 'NULL';

        switch ($data_type) {
            case PDO::PARAM_BOOL:
            case PDO::PARAM_INT:
                $type = 'INTEGER';
                break;
            case PDO::PARAM_STR:
                $type = 'TEXT';
                break;
            case PDO::PARAM_NULL:
                $type = 'NULL';
                break;
            // TODO: Test BLOB type
            case PDO::PARAM_LOB:
                $type = 'BLOB';
                break;
            // TODO: Add a case for float type
            // case PDO::PARAM_FLOAT:
            // $type = "REAL";
            // break;
            default:
                $type = 'TEXT'; // Default to TEXT if no match
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
     * {@inheritDoc}
     */
    public function closeCursor(): bool
    {
        if (! empty($this->result->rows)) {
            $this->result->rows = [];
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function columnCount(): int
    {
        return $this->columns ? count($this->columns) : 0;
    }

    /**
     * {@inheritDoc}
     */
    public function debugDumpParams(): ?bool
    {
        var_dump($this->statement, $this->boundParams);

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function errorCode(): ?string
    {
        return $this->client->errorCode();
    }

    /**
     * {@inheritDoc}
     *
     * @return array<int|string|null>
     */
    public function errorInfo(): array
    {
        return $this->client->errorInfo();
    }

    /**
     * {@inheritDoc}
     *
     * @param  array<int|string, mixed>|null  $params
     */
    public function execute(?array $params = null): bool
    {
        // Transform incoming parameters to the expected API shape
        /** @var array<int|string, array{type: ColumnTypeString, value: int|float|string|null}> $transformedParams */
        $transformedParams = [];

        if ($params !== null) {
            foreach ($params as $key => $value) {
                // Determine the type based on the value
                $type = match (true) {
                    $value === null => 'NULL',
                    is_int($value) => 'INTEGER',
                    is_float($value) => 'REAL',
                    is_bool($value) => 'INTEGER',
                    default => 'TEXT',
                };

                $transformedParams[$key] = [
                    'type' => $type,
                    'value' => $value,
                ];
            }
        }

        // Merge with bound parameters
        /** @var array<array{type: string, value: int|float|string|null}> $allParams */
        $allParams = array_merge($this->boundParams, $transformedParams);

        $response = $this->client->exec([
            'statement' => $this->statement,
            'parameters' => $allParams,
        ]);

        if ($this->errorInfo()) {
            [$errorCode, $statusCode, $message] = $this->errorInfo();

            if ($message === null || $message === '') {
                $message = 'An unknown error occurred during query execution.';
            }

            throw new QueryException((string) $message, $this->statement, $params ?? []);
        }

        if (isset($response->errorMessage)) {
            throw new QueryException($response->errorMessage, $this->statement, $allParams);
        }

        $this->result = $response ?? null;

        if (isset($this->result->columns)) {
            $this->columns = $this->result->columns;
        }

        if (isset($this->result->rows)) {
            $this->rows = array_map(function ($row) {
                $columns = $this->columns ?? [];

                return array_combine(
                    array_map(fn($col) => $col['name'], $columns),
                    $row
                );
            }, $this->result->rows);
        }

        if (isset($this->result->rowCount)) {
            $this->rowCount = $this->result->rowCount;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch(
        $fetchMode = PDO::ATTR_DEFAULT_FETCH_MODE,
        $cursorOrientation = PDO::FETCH_ORI_NEXT,
        $cursorOffset = 0
    ): mixed {
        if ($cursorOrientation !== PDO::FETCH_ORI_NEXT) {
            throw new \RuntimeException('Cursor direction not implemented');
        }

        $result = current($this->rows);

        if (! is_array($result)) {
            return $result;
        }

        $fetchMode = $fetchMode !== PDO::FETCH_DEFAULT ? [$fetchMode, null] : $this->fetchMode;

        next($this->rows);

        return $result;
    }

    /**
     * {@inheritDoc}
     *
     * @return array<int, mixed>
     */
    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array
    {
        if ($mode !== PDO::FETCH_DEFAULT) {
            $this->setFetchMode($mode, $args['fetchArgument'] ?? 0, $args['ctorArgs'] ?? null);
        }
        // dd($this);
        // $result = iterator_to_array($this);

        return $this->rows;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchColumn($columnIndex = 0): mixed
    {
        $row = $this->fetch();

        if (! is_array($row)) {
            return false;
        }

        $value = array_search($columnIndex, array_keys($row));

        return $value !== false ? $row[$value] : null;
    }

    public function rowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * {@inheritDoc}
     */
    public function setFetchMode(int $mode, mixed ...$args): true
    {
        $this->fetchMode = $mode;

        // Optionally handle $args for fetchArgument, ctorArgs, etc.
        return true;
    }
}
