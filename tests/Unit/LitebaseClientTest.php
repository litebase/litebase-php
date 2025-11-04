<?php

namespace Litebase\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Litebase\ColumnTypeString;
use Litebase\Configuration;
use Litebase\LitebaseClient;

describe('LitebaseClient', function () {
    $configuration = (new Configuration)
        ->setAccessKey('key', 'secret')
        ->setDatabase('test/main')
        ->setHost('litebase.localhost');

    $client = new LitebaseClient($configuration);

    $mock = new MockHandler;

    beforeEach(function () {});

    afterEach(function () use ($mock) {
        $mock->reset();
    });

    test('it can be created', function () {
        $client = new LitebaseClient(new Configuration);
        expect($client)->toBeInstanceOf(LitebaseClient::class);
    });

    test('it can begin a transaction', function () use ($client, $mock) {
        $mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'query-id',
                        'changes' => 0,
                        'columns' => [],
                        'lastInsertRowId' => 0,
                        'latency' => 0.0,
                        'rowCount' => 0,
                        'rows' => [],
                        'transactionId' => 'transaction-id',
                    ],
                ],
            ]) ?: null)
        );

        $client = $client->withHttpTransport(new Client(['handler' => HandlerStack::create($mock)]));

        expect($client->beginTransaction())->toBeTrue();
        expect($client->inTransaction())->toBeTrue();
    });

    test('it cant begin a transaction if one is active', function () use ($client, $mock) {
        $mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'query-id',
                        'changes' => 0,
                        'columns' => [],
                        'lastInsertRowId' => 0,
                        'latency' => 0.0,
                        'rowCount' => 0,
                        'rows' => [],
                        'transactionId' => 'transaction-id',
                    ],
                ],
            ]) ?: null)
        );

        $mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'query-id',
                        'changes' => 0,
                        'columns' => [],
                        'lastInsertRowId' => 0,
                        'latency' => 0.0,
                        'rowCount' => 0,
                        'rows' => [],
                        'transactionId' => 'transaction-id',
                    ],
                ],
            ]) ?: null)
        );

        $client = $client->withHttpTransport(new Client(['handler' => HandlerStack::create($mock)]));

        $client->beginTransaction();

        expect($client->beginTransaction())->toBeFalse();
        $client->rollback();
    });

    test('it can commit a transaction', function () use ($client, $mock) {
        $mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'query-id-1',
                        'changes' => 0,
                        'columns' => [],
                        'lastInsertRowId' => 0,
                        'latency' => 0.0,
                        'rowCount' => 0,
                        'rows' => [],
                        'transactionId' => 'transaction-id',
                    ],
                ],
            ]) ?: null)
        );

        $mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'query-id-2',
                        'changes' => 0,
                        'columns' => [],
                        'lastInsertRowId' => 0,
                        'latency' => 0.0,
                        'rowCount' => 0,
                        'rows' => [],
                        'transactionId' => 'transaction-id',
                    ],
                ],
            ]) ?: null)
        );

        $client = $client->withHttpTransport(new Client(['handler' => HandlerStack::create($mock)]));

        expect($client->beginTransaction())->toBeTrue();
        expect($client->commit())->toBeTrue();
        expect($client->inTransaction())->toBeFalse();
    });

    test('it cant commit a transaction', function () use ($client) {
        expect($client->commit())->toBeFalse();
    });

    test('it returns the error code', function () use ($mock, $client) {
        $mock->append(
            new Response(
                400,
                [],
                json_encode([
                    'code' => '0000',
                    'status' => 'error',
                    'message' => 'Test Error',
                ]) ?: null
            )
        );

        $client = $client->withHttpTransport(new Client(['handler' => HandlerStack::create($mock)]));

        $client->beginTransaction();

        expect($client->errorInfo()[0])->toEqual('0000');
        $client->rollback();
    });

    test('it returns the error info', function () use ($mock, $client) {
        $mock->append(
            new Response(500, [], json_encode([
                'status' => 'error',
                'message' => 'Test Error',
            ]) ?: null)
        );

        $client = $client->withHttpTransport(new Client(['handler' => HandlerStack::create($mock)]));

        $client->beginTransaction();

        $this->assertStringContainsString('Test Error', (string) $client->errorInfo()[2]);
    });

    test('it can execute a statement', function () use ($mock, $client) {
        $mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'query-id',
                        'changes' => 0,
                        'columns' => ['id', 'name'],
                        'lastInsertRowId' => 0,
                        'latency' => 0.123,
                        'rowCount' => 1,
                        'rows' => [
                            [1, 'Test'],
                        ],
                        'transactionId' => 'transaction-id',
                    ],
                ],
            ]) ?: null)
        );

        $client = $client->withHttpTransport(new Client(['handler' => HandlerStack::create($mock)]));

        expect($client->exec([
            'statement' => 'select * from users',
        ]))->not->toBeNull();
    });

    test('it indicates if a transaction is in progress', function () use ($client, $mock) {
        $mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'query-id-1',
                        'changes' => 0,
                        'columns' => [],
                        'columnsCount' => 0,
                        'lastInsertRowId' => 0,
                        'latency' => 0.0,
                        'rowCount' => 0,
                        'rows' => [],
                        'transactionId' => 'transaction-id',
                    ],
                ],
            ]) ?: null)
        );

        $mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'query-id-2',
                        'changes' => 0,
                        'columns' => [],
                        'lastInsertRowId' => 0,
                        'latency' => 0.0,
                        'rowCount' => 0,
                        'rows' => [],
                        'transactionId' => 'transaction-id',
                    ],
                ],
            ]) ?: null)
        );

        $client = $client->withHttpTransport(new Client(['handler' => HandlerStack::create($mock)]));

        expect($client->inTransaction())->toBeFalse();
        expect($client->beginTransaction())->toBeTrue();
        expect($client->inTransaction())->toBeTrue();
        $client->rollback();
    });

    test('it returns the last insert id', function () use ($mock, $client) {
        $mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'query-id',
                        'changes' => 0,
                        'columns' => [],
                        'lastInsertRowId' => 1,
                        'latency' => 0.0,
                        'rowCount' => 0,
                        'rows' => [],
                        'transactionId' => 'transaction-id',
                    ],
                ],
            ]) ?: null)
        );

        $client->withHttpTransport(new Client(['handler' => HandlerStack::create($mock)]));

        $client->exec([
            'statement' => 'INSERT INTO users (name) values (?)',
            'parameters' => [['type' => ColumnTypeString::TEXT->value, 'value' => 'John']],
        ]);

        expect($client->lastInsertId())->toEqual('1');
    });

    test('it can rollback a transaction', function () use ($client, $mock) {
        $mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'query-id',
                        'changes' => 0,
                        'columns' => [],
                        'lastInsertRowId' => 0,
                        'latency' => 0.0,
                        'rowCount' => 0,
                        'rows' => [],
                        'transactionId' => 'transaction-id',
                    ],
                ],
            ]) ?: null)
        );

        $mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => [
                    [
                        'id' => 'query-id',
                        'changes' => 0,
                        'columns' => [],
                        'lastInsertRowId' => 0,
                        'latency' => 0.0,
                        'rowCount' => 0,
                        'rows' => [],
                        'transactionId' => 'transaction-id',
                    ],
                ],
            ]) ?: null)
        );

        $client = $client->withHttpTransport(new Client(['handler' => HandlerStack::create($mock)]));

        expect($client->beginTransaction())->toBeTrue();
        expect($client->rollback())->toBeTrue();
    });

    test('it cant rollback a transaction', function () use ($client) {
        // $client = Mockery::mock(LitebaseClient::class);
        // $client->makePartial();
        expect($client->rollback())->toBeFalse();
    });
});
