<?php

namespace Litebase\Tests;

use Mockery;
use PHPUnit\Framework\TestCase as FrameworkTestCase;

class TestCase extends FrameworkTestCase
{
    /**
     * Run code after test setup.
     */
    public function afterSetup(): void
    {
        # code...
    }
    /**
     * Run code after test setup.
     */
    public function afterTest(): void
    {
        # code...
    }

    /**
     * Setup the test.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->afterSetup();
    }

    /**
     * Tear down the test.
     */
    public function tearDown(): void
    {
        Mockery::close();
        $this->afterTest();
    }
}
