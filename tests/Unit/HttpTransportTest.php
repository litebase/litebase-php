<?php

namespace Litebase\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Litebase\ApiClient;
use Litebase\ColumnType;
use Litebase\Configuration;
use Litebase\HttpTransport;
use Litebase\Query;
use Litebase\QueryResult;
use Ramsey\Uuid\Uuid;

describe('HttpTransport', function () {
    $mock = new MockHandler;

    afterEach(function () use ($mock) {
        $mock->reset();
    });

    it('can be created', function () {
        $configuration = (new Configuration);

        $transport = new HttpTransport($configuration);

        expect($transport)->toBeInstanceOf(HttpTransport::class);
    });

    it('can send', function () use ($mock) {
        $configuration = (new Configuration)
            ->setHost('localhost')
            ->setPort('8080')
            ->setDatabase('test-database/main')
            ->setAccessToken('valid-token');

        $httpClient = new Client(['handler' => $mock]);

        $mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'query-id',
                        'changes' => 1,
                        'columns' => [
                            ['type' => ColumnType::INTEGER, 'name' => 'id'],
                            ['type' => ColumnType::TEXT, 'name' => 'name'],
                        ],
                        'columnsCount' => 2,
                        'lastInsertRowId' => 1,
                        'latency' => 10.0,
                        'rowCount' => 1,
                        'rows' => [
                            ['id' => 1, 'name' => 'Test'],
                        ],
                        'transactionId' => 'transaction-id',
                    ],
                ],
            ]) ?: null)
        );

        $transport = new HttpTransport($configuration);

        $transport->setClient(new ApiClient($configuration, $httpClient));

        $query = new Query(
            id: Uuid::uuid4()->toString(),
            statement: 'SELECT 1',
            parameters: [],
        );

        $queryResult = $transport->send($query);

        expect($queryResult)->toBeInstanceOf(QueryResult::class);
        expect($queryResult?->id)->toBe('query-id');
        expect($queryResult?->changes)->toBe(1);
        expect($queryResult?->columns)->toBe([['type' => ColumnType::INTEGER, 'name' => 'id'], ['type' => ColumnType::TEXT, 'name' => 'name']]);
        expect($queryResult?->lastInsertRowId)->toBe(1);
        expect($queryResult?->latency)->toBe(10.0);
        expect($queryResult?->rowCount)->toBe(1);
        expect($queryResult?->rows)->toBe([['id' => 1, 'name' => 'Test']]);
        expect($queryResult?->transactionId)->toBe('transaction-id');
    });
});
