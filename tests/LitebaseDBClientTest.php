<?php

namespace LitebaseDB\Tests;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use LitebaseDB\LitebaseDBClient;

class LitebaseDBClientTest extends TestCase
{
    public function afterSetup(): void
    {
        $this->mock = new MockHandler();

        $this->client = new LitebaseDBClient(
            [
                'access_key_id' => 'key',
                'secret_access_key' => 'secret',
                'database' => 'test',
                'host' => 'us-east-1.litebasedb.test',
            ],
            [
                'handler' => HandlerStack::create($this->mock),
            ]
        );
    }

    public function afterTest(): void
    {
        $this->mock->reset();
    }

    public function test_it_cant_be_created_without_an_access_key_id()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The LitebaseDB database connection cannot be created without a valid access key id.');

        new LitebaseDBClient([]);
    }

    public function test_it_cant_be_created_without_a_secret_access_key()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The LitebaseDB database connection cannot be created without a valid secret access key.');

        new LitebaseDBClient(['access_key_id' => 'key']);
    }

    public function test_it_cant_be_created_without_a_database()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The LitebaseDB database connection cannot be created without a valid database.');

        new LitebaseDBClient([
            'access_key_id' => 'key',
            'secret_access_key' => 'secret',
        ]);
    }

    public function test_it_configures_the_client()
    {
        $baseUri = $this->client->getGuzzleClient()->getConfig('base_uri');
        $this->assertEquals((string) $baseUri, 'test.us-east-1.litebasedb.test/');
    }

    public function test_it_can_begin_a_transaction()
    {
        $this->mock->append(
            new Response(200, [], json_encode(['status' => 'success', 'data' => ['rows' => [['id' => '1']]]]))
        );

        $this->assertTrue($this->client->beginTransaction());
        $this->assertTrue($this->client->inTransaction());
    }

    public function test_it_cant_begin_a_transaction_if_one_is_active()
    {
        $this->mock->append(
            new Response(200, [], json_encode(['data' => ['rows' => [['id' => '1']]]]))
        );

        $this->client->beginTransaction();

        $this->assertFalse($this->client->beginTransaction());
    }

    public function test_it_can_commit_a_transaction()
    {
        $this->mock->append(
            new Response(200, [], json_encode(['status' => 'success', 'data' => ['rows' => [['id' => 'test']]]]))
        );

        $this->mock->append(
            new Response(200, [], json_encode(['status' => 'success', 'data' => ['rows' => [['id' => 'test']]]]))
        );

        $this->assertTrue($this->client->beginTransaction());
        $this->assertTrue($this->client->commit());
        $this->assertFalse($this->client->inTransaction());
    }

    public function test_it_cant_commit_a_transaction()
    {
        $this->assertFalse($this->client->commit());
    }

    public function test_it_returns_the_error_code()
    {
        $this->mock->append(
            new Response(
                400,
                [],
                json_encode([
                    'code' => '0000',
                    'status' => 'error',
                    'message' => 'Test Error'
                ])
            )
        );

        $this->client->beginTransaction();

        $this->assertEquals('0000', $this->client->errorInfo()[0]);
    }

    public function test_it_returns_the_error_info()
    {
        $this->mock->append(
            new Response(500, [], json_encode([
                'status' => 'error',
                'message' => 'Test Error'
            ]))
        );

        $this->client->beginTransaction();

        $this->assertStringContainsString('Test Error', $this->client->errorInfo()[2]);
    }

    public function test_it_can_execute_a_statment()
    {
        $this->mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => [
                    'lastID' => 1,
                    'rows' => [['id' => 1]]
                ],
            ]))
        );

        $this->assertNotNull($this->client->exec([
            'statment' => 'select * from users',
        ]));
    }

    public function test_it_returns_the_guzzle_client()
    {
        $this->assertInstanceOf(Client::class, $this->client->getGuzzleClient());
    }

    public function test_it_indicates_if_a_transaction_is_in_progress()
    {
        $this->mock->append(
            new Response(200, [], json_encode(['status' => 'success', 'data' => ['rows' => [['id' => 'test']]]]))
        );

        $this->assertFalse($this->client->inTransaction());
        $this->assertTrue($this->client->beginTransaction());
        $this->assertTrue($this->client->inTransaction());
    }

    public function test_it_returns_the_last_insert_id()
    {
        $this->mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => [
                    'insertId' => '1',
                ],
            ]))
        );

        $this->client->exec([
            'statement' => "INSERT INTO users (name) values (?)",
            'parameters' => ['John'],
        ]);

        $this->assertEquals('1', $this->client->lastInsertId());
    }

    public function test_it_can_rollback_a_transaction()
    {
        $this->mock->append(new Response(200, [], json_encode(['status' => 'success', 'data' => ['rows' => [['id' => '1']]]])));
        $this->mock->append(new Response(200, [], json_encode(['status' => 'success', 'data' => ['rows' => [['id' => '1']]]])));

        $this->assertTrue($this->client->beginTransaction());
        $this->assertTrue($this->client->rollback());
    }

    public function test_it_cant_rollback_a_transaction()
    {
        $this->assertFalse($this->client->rollback());
    }

    public function test_it_sends_api_calls()
    {
        $this->mock->append(new Response(200, [], json_encode([])));

        $this->assertEquals([], $this->client->send('POST', '', []));
    }
}
