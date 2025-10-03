<?php

namespace Litebase;

class HttpTransport implements TransportInterface
{
    /**
     * The API client instance.
     */
    protected ApiClient $client;

    /**
     * Create a new instance of the transport.
     */
    public function __construct(
        protected Configuration $config,
    ) {
        $this->client = new ApiClient($config);
    }

    /**
     * Send a request to the data api.
     */
    public function send(Query $query): ?QueryResult
    {
        $result = $this->client->query()->createQuery(
            $this->config->getDatabase(),
            $this->config->getBranch(),
            $query->toArray()
        );

        $dataItems = $result->getData();

        // Get the first query result (since we're executing a single query)
        $firstResult = $dataItems[0] ?? null;

        if (!$firstResult) {
            return null;
        }

        return new QueryResult(
            changes: $firstResult->getChanges(),
            columns: $firstResult->getColumns(),
            id: $firstResult->getId(),
            lastInsertRowID: $firstResult->getLastInsertRowId(),
            latency: $firstResult->getLatency(),
            rowsCount: $firstResult->getRowCount(),
            rows: $firstResult->getRows(),
            transactionID: $firstResult->getTransactionId(),
        );
    }
}
