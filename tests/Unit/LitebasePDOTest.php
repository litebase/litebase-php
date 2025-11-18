<?php

namespace Tests\Unit;

uses(\Tests\TestCase::class);

use Litebase\ColumnType;
use Litebase\Configuration;
use Litebase\LitebaseClient;
use Litebase\LitebasePDO;
use Litebase\LitebaseStatement;
use Litebase\QueryResult;
use Mockery;

test('it can be created', function () {
    $pdo = new LitebasePDO(
        new LitebaseClient(
            Configuration::create([
                'host' => 'localhost',
                'port' => '8888',
                'username' => 'root',
                'password' => 'password',
                'database' => 'test/main',
            ])
        )
    );

    expect($pdo)->toBeInstanceOf(LitebasePDO::class);
});

test('it can begin a transaction', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $pdo = createPDO($client);
    $client->shouldReceive('beginTransaction')->andReturn(true);
    $result = $pdo->beginTransaction();
    expect($result)->toBeTrue();
});

test('it can commit a transaction', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $pdo = createPDO($client);
    $client->shouldReceive('commit')->andReturn(true);
    $result = $pdo->commit();
    expect($result)->toBeTrue();
});

test('it can return an error code', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $pdo = createPDO($client);
    $client->shouldReceive('errorCode')->andReturn(500);
    $result = $pdo->errorCode();
    expect($result)->toEqual(500);
});

test('it can return error info', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $pdo = createPDO($client);
    $client->shouldReceive('errorInfo')->andReturn([0, 0, 'Server error']);
    $result = $pdo->errorInfo();
    expect($result)->toEqual([0, 0, 'Server error']);
});

test('it can execute a statment', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $pdo = createPDO($client);

    $client->shouldReceive('exec')->andReturn(new QueryResult(
        changes: 0,
        columns: [['type' => ColumnType::INTEGER, 'name' => 'id']],
        id: '1',
        lastInsertRowId: 0,
        latency: 0.1,
        rowCount: 2,
        rows: [[1], [2]],
        transactionId: '',
        errorMessage: null,
    ));

    $result = $pdo->exec('select * from users');

    expect($result)->not->toBeFalse();
});

test('it can return its client', function () {
    $client = Mockery::mock(LitebaseClient::class);
    expect(createPDO($client)->getClient())->toBeInstanceOf(LitebaseClient::class);
});

test('it can return if it has error', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $pdo = createPDO($client);
    $client->shouldReceive('errorCode')->andReturn(null, 500);
    expect($pdo->hasError())->toBeFalse();
    expect($pdo->hasError())->toBeTrue();
});

test('it indicates if it has a transaction', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $pdo = createPDO($client);
    $client->shouldReceive('inTransaction')->andReturn(true);
    expect($pdo->inTransaction())->toBeTrue();
});

test('it returns the last inserted id', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $pdo = createPDO($client);
    $client->shouldReceive('lastInsertId')->andReturn('1');
    expect($pdo->lastInsertId())->toEqual('1');
});

test('it can prepre a statement', function () {
    $query = 'select * from users';
    $client = Mockery::mock(LitebaseClient::class);
    $pdo = createPDO($client);
    $client->shouldReceive('prepare')
        ->andReturn(new LitebaseStatement($client, $query));

    $statement = $pdo->prepare($query);
    expect($statement)->not->toBeNull();
    expect($statement)->toBeInstanceOf(LitebaseStatement::class);
});

test('it can roll back a transaction', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $pdo = createPDO($client);
    $client->shouldReceive('rollback')->andReturn(true);
    $result = $pdo->rollBack();
    expect($result)->toBeTrue();
});

test('the client can be set', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $pdo = createPDO($client);
    expect($pdo->getClient())->toBeInstanceOf(LitebaseClient::class);
});

function createPDO(LitebaseClient $client): LitebasePDO
{
    return new LitebasePDO($client);
}
