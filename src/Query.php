<?php

declare(strict_types=1);

namespace Litebase;

use Litebase\OpenAPI\Model\QueryRequest;

class Query
{
    /**
     * @param  \Litebase\OpenAPI\Model\StatementParameter[]  $parameters
     */
    public function __construct(
        public string $id,
        public string $statement,
        public array $parameters = [],
        public ?string $transactionId = null,
    ) {}

    /**
     * Convert the Query object to an associative array.
     */
    public function toRequest(): QueryRequest
    {
        $request = new QueryRequest;

        $request->setQueries([
            (new \Litebase\OpenAPI\Model\QueryInput)
                ->setId($this->id)
                ->setTransactionId($this->transactionId ?? '')
                ->setStatement($this->statement)
                ->setParameters($this->parameters),
        ]);

        return $request;
    }
}
