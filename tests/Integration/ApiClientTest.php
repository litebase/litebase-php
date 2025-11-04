<?php

declare(strict_types=1);

namespace Litebase\Tests\Integration;

use Litebase\ApiClient;
use Litebase\Configuration;

beforeAll(function () {
    exec('docker compose -f ./tests/docker-compose.test.yml up -d');
    sleep(1); // Wait for services to be ready
});

afterAll(function () {
    exec('docker compose -f ./tests/docker-compose.test.yml down -v');
    // Delete the .litebase directory to clean up any persisted data
    exec('rm -rf ./tests/.litebase');
});

describe('ApiClient', function () {
    $configuration = new Configuration;

    $configuration
        ->setHost('localhost')
        ->setPort('8888')
        ->setUsername('root')
        ->setPassword('password');

    $client = new ApiClient($configuration);

    ApiClientTestRunner::run($client);
});
