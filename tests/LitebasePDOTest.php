<?php

namespace SpaceStudio\Litebase\Tests;

use Exception;
use SpaceStudio\Litebase\LitebaseClient;
use SpaceStudio\Litebase\LitebasePDO;
use SpaceStudio\Litebase\LitebaseStatement;

class LitebasePDOTest extends TestCase
{
    public function test_it_can_be_created()
    {
        $this->assertInstanceOf(LitebasePDO::class, new LitebasePDO('datasbse', 'username', 'password'));
    }

    public function test_it_cant_be_created_with_an_improper_database_identifier()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The database identifier contains illegal characters.');
        new LitebasePDO('database!', 'username', 'password');
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

    public function test_it_can_run_a_query()
    {

        $query = 'SELECT * from users';
        $pdo = $this->createPDO();
        $this->client->shouldReceive('exec'); //->andReturn($statement);
        $statement = $pdo->query($query);

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
        $this->client = $this->mock(LitebaseClient::class);
        $pdo = new LitebasePDO('database', 'username', 'password');

        return $pdo->setClient($this->client);
    }
}
