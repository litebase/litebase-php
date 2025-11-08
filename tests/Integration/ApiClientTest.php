<?php

declare(strict_types=1);

namespace Litebase\Tests\Integration;

use Litebase\ApiClient;
use Litebase\Configuration;

$configuration = new Configuration;

$configuration
    ->setHost('127.0.0.1')
    ->setPort('8888')
    ->setUsername('root')
    ->setPassword('password');

$client = new ApiClient($configuration);

beforeAll(function () use ($client) {
    if (file_exists('./tests/.litebase')) {
        exec('rm -rf ./tests/.litebase');
    }

    exec('mkdir -p ./tests/.litebase');
    exec('chmod 777 ./tests/.litebase');
    exec('docker compose -f ./tests/docker-compose.test.yml up -d');

    // Give the container a moment to initialize
    sleep(2);

    try {
        $response = $client->clusterStatus()->listClusterStatuses();
    } catch (\Exception $e) {
        $lines = [];
        exec('docker compose -f ./tests/docker-compose.test.yml logs --tail=200 --no-color', $lines, $rc);

        $logs = implode("\n", $lines);

        throw new \RuntimeException('Failed to connect to Litebase server for integration tests: ' . $e->getMessage() . "\nContainer logs:\n{$logs}");
    }

    if ($response->getStatus() !== 'success') {
        $lines = [];
        exec('docker compose -f ./tests/docker-compose.test.yml logs --tail=200 --no-color', $lines, $rc);

        $logs = implode("\n", $lines);

        throw new \RuntimeException('Failed to connect to Litebase server for integration tests.' . "Container logs:\n{$logs}");
    }
});

afterAll(function () {
    exec('docker compose -f ./tests/docker-compose.test.yml down -v');

    // Delete the .litebase directory to clean up any persisted data
    exec('rm -rf ./tests/.litebase');
});

describe('ApiClient', function () use ($client) {
    ApiClientTestRunner::run($client);
});
