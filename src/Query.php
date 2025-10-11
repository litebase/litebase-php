<?php

namespace Litebase;

use Litebase\Generated\Model\CreateQueryRequest;

class Query
{
    /**
     * @param  \Litebase\Generated\Model\StatementParameter[]  $parameters
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
    public function toRequest(): CreateQueryRequest
    {
        $request = new CreateQueryRequest;

        $request->setQueries([
            (new \Litebase\Generated\Model\QueryInput)
                ->setId($this->id)
                ->setTransactionId($this->transactionId ?? '')
                ->setStatement($this->statement)
                ->setParameters($this->parameters),
        ]);

        return $request;
    }
}
