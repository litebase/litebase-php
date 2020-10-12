<?php

namespace Litebase\Tests;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Litebase\LitebaseClient;

class LitebaseClientTest extends TestCase
{
    public function afterSetup(): void
    {
        $this->mock = new MockHandler();

        $this->client = new LitebaseClient([
            'database' => 'testdatabase',
            'username' => 'test',
            'password' => 'password',
        ], [
            'handler' => HandlerStack::create($this->mock),
        ]);
    }

    public function afterTest(): void
    {
        $this->mock->reset();
    }

    public function test_it_cant_be_created_without_a_database()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The Litebase database is missing.');

        new LitebaseClient([]);
    }

    public function test_it_cant_be_created_without_a_username()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The Litebase database connection cannot be created without a username.');

        new LitebaseClient(['database' => 'testdatabase']);
    }

    public function test_it_cant_be_created_without_a_password()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The Litebase database connection cannot be created without a password.');

        new LitebaseClient(['database' => 'testdatabase', 'username' => 'test']);
    }

    public function test_it_configures_the_client()
    {
        $baseUri = $this->client->getGuzzleClient()->getConfig('base_uri');

        $this->assertEquals(
            $baseUri,
            LitebaseClient::BASE_URI . "/{$this->client->database()}/"
        );
    }

    public function test_it_can_begin_a_transaction()
    {
        $this->mock->append(
            new Response(200, [], json_encode(['status' => 'success', 'data' => ['id' => '1']]))
        );

        $this->assertTrue($this->client->beginTransaction());
        $this->assertTrue($this->client->inTransaction());
    }

    public function test_it_cant_begin_a_transaction_if_one_is_active()
    {
        $this->mock->append(
            new Response(200, [], json_encode(['data' => ['id' => '1']]))
        );

        $this->client->beginTransaction();

        $this->assertFalse($this->client->beginTransaction());
    }

    public function test_it_can_commit_a_transaction()
    {
        $this->mock->append(
            new Response(200, [], json_encode(['status' => 'success', 'data' => ['id' => 'test']]))
        );

        $this->mock->append(
            new Response(200, [], json_encode(['status' => 'success', 'data' => ['id' => 'test']]))
        );

        $this->assertTrue($this->client->beginTransaction());
        $this->assertTrue($this->client->commit());
        $this->assertFalse($this->client->inTransaction());
    }

    public function test_it_cant_commit_a_transaction()
    {
        $this->assertFalse($this->client->commit());
    }

    public function test_it_returns_the_database()
    {
        $this->assertEquals('testdatabase', $this->client->database());
    }

    public function test_it_returns_the_error_code()
    {
        $this->mock->append(
            new Response(500, [], json_encode(['message' => 'Test Error']))
        );

        $this->client->beginTransaction();

        $this->assertEquals(500, $this->client->errorCode());
    }

    public function test_it_returns_the_error_info()
    {
        $this->mock->append(
            new Response(500, [], json_encode(['message' => 'Test Error']))
        );

        $this->client->beginTransaction();

        $this->assertStringContainsString('Test Error', $this->client->errorInfo());
    }

    public function test_it_can_execute_a_statment()
    {
        $this->mock->append(
            new Response(200, [], json_encode([
                'status' => 'success',
                'data' => ['id' => 1],
                'last_insert_id' => 1,
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
            new Response(200, [], json_encode(['status' => 'success', 'data' => ['id' => 'test']]))
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
                'data' => ['id' => 'test'],
                'last_insert_id' => '1',
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
        $this->mock->append(new Response(200, [], json_encode(['status' => 'success', 'data' => ['id' => '1']])));
        $this->mock->append(new Response(200, [], json_encode(['status' => 'success', 'data' => ['id' => '1']])));

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

    public function test_it_captures_errors_when_sending_api_calls()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test Error');
        $this->mock->append(new Response(500, [], 'Test Error'));

        $this->assertEquals([], $this->client->send('POST', '', []));
    }
}
