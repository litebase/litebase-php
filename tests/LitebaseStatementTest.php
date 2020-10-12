<?php

namespace Litebase\Tests;

use PDO;
use Litebase\LitebaseClient;
use Litebase\LitebaseStatement;
use Mockery;

class LitebaseStatementTest extends TestCase
{
    public function test_it_can_be_created()
    {
        $statement = $this->createStatement();

        $this->assertInstanceOf(LitebaseStatement::class, $statement);
    }

    public function test_it_can_bind_a_param()
    {
        $statement = $this->createStatement();
        $sessionId = '1';
        $statement->bindParam(':id', $sessionId);
        $this->assertEquals($statement->getBoundParams()[':id'], $sessionId);
    }

    public function test_it_can_bind_values_without_keys()
    {
        $statement = $this->createStatement();
        $this->assertTrue($statement->bindValue(1, '?'));
        $this->assertTrue($statement->bindValue(2, '?'));
        $this->assertTrue($statement->bindValue(3, '?'));
        $this->assertCount(3, $statement->getBoundParams());
        $this->assertEquals($statement->getBoundParams()[0], '?');
        $this->assertEquals($statement->getBoundParams()[1], '?');
        $this->assertEquals($statement->getBoundParams()[2], '?');
    }

    public function test_it_can_bind_values_with_keys()
    {
        $statement = $this->createStatement();
        $this->assertTrue($statement->bindValue(':id', '1', PDO::PARAM_INT));
        $this->assertTrue($statement->bindValue(':name', 'John'));
        $this->assertCount(2, $statement->getBoundParams());
        $this->assertEquals($statement->getBoundParams()[':id'], '1');
        $this->assertEquals($statement->getBoundParams()[':name'], 'John');
    }

    public function test_it_can_return_a_column_count()
    {
        $statement = $this->createStatement();
        $this->client->shouldReceive('errorCode')->andReturn(null);
        $this->client->shouldReceive('exec')->andReturn([
            'data' => [
                [
                    'id' => '1',
                    'name' => 'John',
                ],
                [
                    'id' => '2',
                    'name' => 'Jane',
                ],
            ],
        ]);

        $statement->execute();
        $this->assertEquals(2, $statement->columnCount());
    }

    public function test_it_can_return_debug_params()
    {
        $statement = $this->createStatement();
        ob_start();
        $statement->debugDumpParams();
        $debug = ob_get_clean();
        $this->assertStringContainsString('SELECT * FROM users', $debug);
    }

    public function test_it_should_return_an_error_code()
    {
        $statement = $this->createStatement();
        $this->client->shouldReceive('errorCode')->andReturn(500);
        $this->assertEquals(500, $statement->errorCode());
    }

    public function test_it_should_return_error_info()
    {
        $statement = $this->createStatement();
        $this->client->shouldReceive('errorInfo')->andReturn('Server error');
        $this->assertEquals('Server error', $statement->errorInfo());
    }

    public function test_it_can_return_the_row_count()
    {
        $statement = $this->createStatement();
        $this->client->shouldReceive('errorCode')->andReturn(null);
        $this->client->shouldReceive('exec')->andReturn([
            'status' => 'success',
            'data' => [
                [
                    'id' => '1',
                    'name' => 'John',
                ],
                [
                    'id' => '2',
                    'name' => 'Jane',
                ],
            ],
            'row_count' => 2,
        ]);

        $statement->execute();
        $this->assertEquals(2, $statement->rowCount());
    }

    public function test_it_can_set_the_fetch_mode()
    {
        $statement = $this->createStatement();
        $this->assertTrue($statement->setFetchMode(PDO::FETCH_COLUMN));
    }

    protected function createStatement()
    {
        /**  @var LitebaseClient */
        $this->client = Mockery::mock(LitebaseClient::class);
        $query = 'SELECT * FROM users';
        return new LitebaseStatement($this->client, $query);
    }
}
