<?php

declare(strict_types=1);

namespace Litebase\Tests\Integration;

use Litebase\ApiClient;
use Litebase\Configuration;
use Litebase\LitebaseClient;
use Litebase\OpenAPI\Model\Privilege;
use Litebase\OpenAPI\Model\Statement;
use Litebase\OpenAPI\Model\StatementEffect;

$configuration = new Configuration;

$configuration
    ->setHost('127.0.0.1')
    ->setPort('8888')
    ->setUsername('root')
    ->setPassword('password');

$client = new ApiClient($configuration);

beforeAll(function () use ($client) {
    LitebaseContainer::start();

    try {
        $response = $client->clusterStatus()->listClusterStatuses();
    } catch (\Exception $e) {
        throw new \RuntimeException('Failed to connect to Litebase server for integration tests: ' . $e->getMessage());
    }

    if ($response->getStatus() !== 'success') {
        throw new \RuntimeException('Failed to connect to Litebase server for integration tests.');
    }
});

afterAll(function () {
    LitebaseContainer::stop();
});

describe('LitebaseClient', function () use ($client) {
    test('LQTP support', function () use ($client) {
        $databaseResponse = $client->database()
            ->createDatabase(new \Litebase\OpenAPI\Model\DatabaseStoreRequest([
                'name' => 'test',
            ]));

        if (!$databaseResponse instanceof \Litebase\OpenAPI\Model\CreateDatabase200Response) {
            throw new \RuntimeException('Invalid response when creating database for integration tests.');
        }

        $response = $client->accessKey()->createAccessKey(
            new \Litebase\OpenAPI\Model\AccessKeyStoreRequest([
                'description' => 'test-key',
                'statements' => [
                    new Statement([
                        'effect' => StatementEffect::ALLOW,
                        'actions' => [Privilege::STAR],
                        'resource' => '*',
                    ]),
                ],
            ])
        );

        if (!$response instanceof \Litebase\OpenAPI\Model\CreateAccessKey201Response) {
            throw new \RuntimeException('Invalid response when creating access key for integration tests.');
        }

        $accessKeyId = $response->getData()->getAccessKeyId();
        $accessKeySecret = $response->getData()->getAccessKeySecret();

        $configuration = new Configuration;

        $configuration
            ->setHost('127.0.0.1')
            ->setPort('8888')
            ->setAccessKey($accessKeyId, $accessKeySecret)
            ->setDatabase(sprintf(
                '%s/%s',
                $databaseResponse->getData()->getDatabaseName(),
                $databaseResponse->getData()->getBranchName()
            ));

        $litebaseClient = new LitebaseClient($configuration);

        $litebaseClient = $litebaseClient->withTransport('http');

        // Create a table
        $result = $litebaseClient->exec([
            'statement' => 'CREATE TABLE IF NOT EXISTS lqtp_test (id INTEGER PRIMARY KEY AUTOINCREMENT, test_value INTEGER)'
        ]);

        expect($result?->errorMessage)->toBeNull();

        // Insert a value
        $result = $litebaseClient->exec([
            'statement' => 'INSERT INTO lqtp_test (test_value) VALUES (?)',
            'parameters' => [
                [
                    'type' => 'INTEGER',
                    'value' => 42
                ]
            ],
        ]);

        expect($result?->changes)->toBe(1);

        // Query the value
        $queryResult = $litebaseClient->exec([
            'statement' => 'SELECT test_value FROM lqtp_test WHERE id = ?',
            'parameters' => [
                [
                    'type' => 'INTEGER',
                    'value' => 1
                ]
            ],
        ]);

        expect($queryResult?->rows[0][0])->toBe(42);
    });
});
