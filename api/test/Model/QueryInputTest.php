<?php
/**
 * Litebase Server API
 *
 * Litebase Server OpenAPI specification
 *
 * The version of the OpenAPI document: 1.0.0
 */


/**
 * NOTE: This class is auto generated, do not edit the class manually.
 */

namespace Litebase\OpenAPI\Test\Model;

use PHPUnit\Framework\TestCase;

/**
 * QueryInputTest Class Doc Comment
 *
 * @category    Class
 * @description QueryInput is a struct that represents the input of a query.  | Offset          | Length | Description                           | |-----------------|--------|---------------------------------------| | 0               | 4      | The length of the id                  | | 4               | n      | The unique identifier for the query   | | 4 + n           | 4      | The length of the statement           | | 8 + n           | m      | The statement to execute              | | 8 + n + m       | 4      | The length of the parameters array    | | 12 + n + m      | p      | The parameters to bind to the statement | | 12 + n + m + p  | 4      | The length of the transaction id       | | 16 + n + m + p  | q      | The transaction id                    |
 * @package     Litebase\OpenAPI
 * @author      OpenAPI Generator team
 * @link        https://openapi-generator.tech
 */
class QueryInputTest extends TestCase
{

    /**
     * Setup before running any test case
     */
    public static function setUpBeforeClass(): void
    {
    }

    /**
     * Setup before running each test case
     */
    public function setUp(): void
    {
    }

    /**
     * Clean up after running each test case
     */
    public function tearDown(): void
    {
    }

    /**
     * Clean up after running all test cases
     */
    public static function tearDownAfterClass(): void
    {
    }

    /**
     * Test "QueryInput"
     */
    public function testQueryInput()
    {
        // TODO: implement
        self::markTestIncomplete('Not implemented');
    }

    /**
     * Test attribute "id"
     */
    public function testPropertyId()
    {
        // TODO: implement
        self::markTestIncomplete('Not implemented');
    }

    /**
     * Test attribute "parameters"
     */
    public function testPropertyParameters()
    {
        // TODO: implement
        self::markTestIncomplete('Not implemented');
    }

    /**
     * Test attribute "statement"
     */
    public function testPropertyStatement()
    {
        // TODO: implement
        self::markTestIncomplete('Not implemented');
    }

    /**
     * Test attribute "transaction_id"
     */
    public function testPropertyTransactionId()
    {
        // TODO: implement
        self::markTestIncomplete('Not implemented');
    }
}
