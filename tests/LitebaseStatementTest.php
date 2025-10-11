<?php

uses(\Litebase\Tests\TestCase::class);

use Litebase\ColumnType;
use Litebase\LitebaseClient;
use Litebase\LitebaseStatement;
use Litebase\QueryResult;

test('it can be created', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $statement = createStatement($client);

    expect($statement)->toBeInstanceOf(LitebaseStatement::class);
});

test('it can bind a param', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $statement = createStatement($client);
    $sessionId = '1';
    $statement->bindParam(':id', $sessionId);

    ob_start();
    $statement->debugDumpParams();
    $debug = ob_get_clean();

    $this->assertStringContainsString(':id', $debug ?: '');
    $this->assertStringContainsString('1', $debug ?: '');
});

test('it can bind values without keys', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $statement = createStatement($client);

    expect($statement->bindValue(1, '?'))->toBeTrue();
    expect($statement->bindValue(2, '?'))->toBeTrue();
    expect($statement->bindValue(3, '?'))->toBeTrue();

    ob_start();
    $statement->debugDumpParams();
    $debug = ob_get_clean();

    $this->assertStringContainsString('1', $debug ?: '');
    $this->assertStringContainsString('2', $debug ?: '');
    $this->assertStringContainsString('3', $debug ?: '');
});

test('it can bind values with keys', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $statement = createStatement($client);

    expect($statement->bindValue(':id', '1', PDO::PARAM_INT))->toBeTrue();
    expect($statement->bindValue(':name', 'John'))->toBeTrue();

    ob_start();
    $statement->debugDumpParams();
    $debug = ob_get_clean();

    $this->assertStringContainsString(':id', $debug ?: '');
    $this->assertStringContainsString('1', $debug ?: '');
    $this->assertStringContainsString(':name', $debug ?: '');
    $this->assertStringContainsString('John', $debug ?: '');
});

test('it can return a column count', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $statement = createStatement($client);

    $client->shouldReceive('errorInfo')->andReturn([]);
    $client->shouldReceive('exec')->andReturn(new QueryResult(
        changes: 0,
        columns: [['name' => 'id', 'type' => ColumnType::INTEGER], ['name' => 'name', 'type' => ColumnType::TEXT]],
        id: '1',
        lastInsertRowID: 0,
        latency: 0.1,
        rowsCount: 2,
        rows: [
            ['1', 'John'],
            ['2', 'Jane'],
        ],
        transactionID: '',
        errorMessage: null,
    ));

    $statement->execute();
    expect($statement->columnCount())->toEqual(2);
});

test('it can return debug params', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $statement = createStatement($client);

    ob_start();
    $statement->debugDumpParams();
    $debug = ob_get_clean();
    $this->assertStringContainsString('SELECT * FROM users', $debug ?: '');
});

test('it should return an error code', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $statement = createStatement($client);

    $client->shouldReceive('errorCode')->andReturn(500);
    expect($statement->errorCode())->toEqual(500);
});

test('it should return error info', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $statement = createStatement($client);

    $client->shouldReceive('errorInfo')->andReturn([0, 0, 'Server error']);
    expect($statement->errorInfo()[2])->toEqual('Server error');
});

test('it can return the row count', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $statement = createStatement($client);

    $client->shouldReceive('errorInfo')->andReturn([]);
    $client->shouldReceive('exec')->andReturn(new QueryResult(
        changes: 0,
        columns: [['type' => ColumnType::INTEGER, 'name' => 'id'], ['type' => ColumnType::TEXT, 'name' => 'name']],
        id: '1',
        lastInsertRowID: 0,
        latency: 0.1,
        rowsCount: 2,
        rows: [
            ['1', 'John'],
            ['2', 'Jane'],
        ],
        transactionID: '',
    ));

    $statement->execute();
    expect($statement->rowCount())->toEqual(2);
});

test('it can set the fetch mode', function () {
    $client = Mockery::mock(LitebaseClient::class);
    $statement = createStatement($client);
    expect($statement->setFetchMode(PDO::FETCH_COLUMN))->toBeTrue();
});

function createStatement(LitebaseClient $client): LitebaseStatement
{
    $query = 'SELECT * FROM users';

    return new LitebaseStatement($client, $query);
}
