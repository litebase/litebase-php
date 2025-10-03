<?php

namespace Litebase;

class QueryResult
{
    public function __construct(
        public int $changes = 0,
        public array $columns = [],
        public string $id = '',
        public int $lastInsertRowID = 0,
        public float $latency = 0.0,
        public int $rowsCount = 0,
        public array $rows = [],
        public string $transactionID = '',
        public ?string $errorMessage = null,
    ) {}
}
