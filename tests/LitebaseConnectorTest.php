<?php

namespace SpaceStudio\Litebase\Tests;

use Exception;
use Illuminate\Database\Query\Processors\SQLiteProcessor;
use SpaceStudio\Litebase\LitebaseConnection;
use Illuminate\Database\Schema\Grammars\SQLiteGrammar;
use SpaceStudio\Litebase\LitebaseConnector;
use SpaceStudio\Litebase\LitebasePDO;

class LitebaseConnectorTest extends TestCase
{
    public function test_it_can_be_created()
    {
        $connector = new LitebaseConnector;

        $this->assertInstanceOf(LitebaseConnector::class, $connector);
    }

    public function test_it_can_connect()
    {
        $connector = new LitebaseConnector;
        $connection = $connector->connect([
            'database' => 'testdatabase',
            'username' => 'test',
            'password' => 'password',
        ]);

        $this->assertInstanceOf(LitebasePDO::class, $connection);
    }

    public function test_it_cant_connect_with_an_invalid_database_name()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The database identifier contains illegal characters.');
        $connector = new LitebaseConnector;
        $connector->connect([
            'database' => 'testdatabase!.,$%',
            'username' => 'test',
            'password' => 'password',
        ]);
    }
}
