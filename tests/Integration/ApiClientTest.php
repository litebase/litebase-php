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
    LitebaseContainer::start();

    try {
        $response = $client->clusterStatus()->listClusterStatuses();
    } catch (\Exception $e) {
        throw new \RuntimeException('Failed to connect to Litebase server for integration tests: '.$e->getMessage());
    }

    if ($response->getStatus() !== 'success') {
        throw new \RuntimeException('Failed to connect to Litebase server for integration tests.');
    }
});

afterAll(function () {
    LitebaseContainer::stop();
});

describe('ApiClient', function () use ($client) {
    ApiClientTestRunner::run($client);
});
