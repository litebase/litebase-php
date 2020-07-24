<?php

namespace SpaceStudio\Litebase\Tests;

use Illuminate\Database\Query\Processors\SQLiteProcessor;
use SpaceStudio\Litebase\LitebaseConnection;
use Illuminate\Database\Schema\Grammars\SQLiteGrammar;
use Illuminate\Database\Schema\SQLiteBuilder;

class LitebaseConnectionTest extends TestCase
{
    public function test_it_can_be_created()
    {
        $connection = new LitebaseConnection('testdatabase', [
            'username' => 'test',
            'password' => 'password',
        ]);

        $this->assertInstanceOf(LitebaseConnection::class, $connection);
    }

    public function test_it_returns_the_schema_builder()
    {
        $connection = new LitebaseConnection('testdatabase', [
            'username' => 'test',
            'password' => 'password',
        ]);

        $this->assertInstanceOf(SQLiteBuilder::class, $connection->getSchemaBuilder());
    }

    public function test_it_returns_the_default_schema_grammar()
    {
        $connection = new LitebaseConnection('testdatabase', [
            'username' => 'test',
            'password' => 'password',
        ]);

        $connection->useDefaultSchemaGrammar();

        $this->assertInstanceOf(SQLiteGrammar::class, $connection->getSchemaGrammar());
    }

    public function test_it_returns_the_default_post_processor()
    {
        $connection = new LitebaseConnection('testdatabase', [
            'username' => 'test',
            'password' => 'password',
        ]);

        $connection->useDefaultPostProcessor();

        $this->assertInstanceOf(SQLiteProcessor::class, $connection->getPostProcessor());
    }
}
