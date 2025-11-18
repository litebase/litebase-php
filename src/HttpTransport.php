<?php

declare(strict_types=1);

namespace Litebase;

use GuzzleHttp\Client;
use Litebase\OpenAPI\Model\CreateQuery200Response;

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
        ?Client $httpClient = null,
    ) {
        $this->client = new ApiClient($config, $httpClient);
    }

    /**
     * Send a request to the data api.
     */
    public function send(Query $query): ?QueryResult
    {
        $result = $this->client->query()->createQuery(
            $this->config->getDatabase() ?? '',
            $this->config->getBranch() ?? '',
            $query->toRequest(),
        );

        if ($result instanceof CreateQuery200Response === false) {
            return new QueryResult(
                errorMessage: $result->getMessage() ?? 'An unknown error occurred',
            );
        }

        $dataItems = $result->getData();

        // Get the first query result (since we're executing a single query)
        $firstResult = $dataItems[0] ?? null;

        if (! $firstResult) {
            return null;
        }

        /** @var array<int, array<int, bool|float|int|string|null>> $rows */
        $rows = array_values(array_map(
            fn ($row) => is_array($row) ? array_values($row) : (array) $row,
            $firstResult->getRows() ?? []
        ));

        return new QueryResult(
            changes: $firstResult->getChanges() ?? 0,
            columns: array_values(array_map(fn ($col) => [
                'type' => ColumnType::from($col->getType() ?? 1),
                'name' => $col->getName() ?? '',
            ], $firstResult->getColumns() ?? [])),
            id: $firstResult->getId() ?? '',
            lastInsertRowId: $firstResult->getLastInsertRowId() ?? 0,
            latency: $firstResult->getLatency() ?? 0,
            rowCount: $firstResult->getRowCount() ?? 0,
            rows: $rows,
            transactionId: $firstResult->getTransactionId() ?? '',
        );
    }

    public function setClient(ApiClient $client): self
    {
        $this->client = $client;

        return $this;
    }
}
