<?php

namespace Litebase\Tests;

use Litebase\LitebaseClient;
use Litebase\LitebasePDO;
use Litebase\LitebaseStatement;
use Mockery;

class LitebasePDOTest extends TestCase
{
    public function test_it_can_be_created()
    {
        $this->assertInstanceOf(
            LitebasePDO::class,
            new LitebasePDO(['access_key_id' => 'key', 'access_key_secret' => 'secret', 'url' => 'http://litebase.test'])
        );
    }

    public function test_it_can_begin_a_transaction()
    {
        $pdo = $this->createPDO();
        $this->client->shouldReceive('beginTransaction')->andReturn(true);
        $result = $pdo->beginTransaction();
        $this->assertTrue($result);
    }

    public function test_it_can_commit_a_transaction()
    {
        $pdo = $this->createPDO();
        $this->client->shouldReceive('commit')->andReturn(true);
        $result = $pdo->commit();
        $this->assertTrue($result);
    }

    public function test_it_can_return_an_error_code()
    {
        $pdo = $this->createPDO();
        $this->client->shouldReceive('errorCode')->andReturn(500);
        $result = $pdo->errorCode();
        $this->assertEquals(500, $result);
    }

    public function test_it_can_return_error_info()
    {
        $pdo = $this->createPDO();
        $this->client->shouldReceive('errorInfo')->andReturn('Server error');
        $result = $pdo->errorInfo();
        $this->assertEquals('Server error', $result);
    }

    public function test_it_can_execute_a_statment()
    {
        $pdo = $this->createPDO();

        $this->client->shouldReceive('exec')->andReturn([
            'data' => [
                ['id' => 1],
                ['id' => 2],
            ],
        ]);

        $result = $pdo->exec([
            'statement' => '* from users'
        ]);

        $this->assertNotNull($result);
        $this->assertCount(2, $result['data']);
    }

    public function test_it_can_return_its_client()
    {
        $this->assertInstanceOf(LitebaseClient::class, $this->createPDO()->getClient());
    }

    public function test_it_can_return_if_it_has_error()
    {
        $pdo = $this->createPDO();
        $this->client->shouldReceive('errorCode')->andReturn(null, 500);
        $this->assertFalse($pdo->hasError());
        $this->assertTrue($pdo->hasError());
    }

    public function test_it_indicates_if_it_has_a_transaction()
    {
        $pdo = $this->createPDO();
        $this->client->shouldReceive('inTransaction')->andReturn(true);
        $this->assertTrue($pdo->inTransaction());
    }

    public function test_it_returns_the_last_inserted_id()
    {
        $pdo = $this->createPDO();
        $this->client->shouldReceive('lastInsertId')->andReturn('1');
        $this->assertEquals('1', $pdo->lastInsertId());
    }

    public function test_it_can_prepre_a_statement()
    {
        $query = 'select * from users';
        $pdo = $this->createPDO();
        $this->client->shouldReceive('prepare')
            ->andReturn(new LitebaseStatement($this->client, $query));

        $statement = $pdo->prepare($query);
        $this->assertNotNull($statement);
        $this->assertInstanceOf(LitebaseStatement::class, $statement);
    }

    public function test_it_can_roll_back_a_transaction()
    {
        $pdo = $this->createPDO();
        $this->client->shouldReceive('rollback')->andReturn(true);
        $result = $pdo->rollBack();
        $this->assertTrue($result);
    }

    public function test_the_client_can_be_set()
    {
        $pdo = $this->createPDO();
        $this->assertEquals($this->client, $pdo->getClient());
    }

    protected function createPDO()
    {
        $this->client = Mockery::mock(LitebaseClient::class);

        $pdo = new LitebasePDO(['access_key_id' => 'key', 'access_key_secret' => 'secret', 'url' => 'http://litebase.test']);

        return $pdo->setClient($this->client);
    }
}
