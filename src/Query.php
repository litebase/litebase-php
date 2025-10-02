<?php

namespace Litebase;

class Query
{
    public function __construct(
        public string $id,
        public string $statement,
        public ?array $parameters = [],
        public ?string $transactionId = null,
    ) {}

    function toArray(): array
    {
        return [
            "queries" => [
                [
                    'id' => $this->id,
                    'transaction_id' => $this->transactionId,
                    'statement' => $this->statement,
                    'parameters' => $this->parameters ?? [],
                ]
            ],
        ];
    }
}
