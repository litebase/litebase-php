<?php

declare(strict_types=1);

namespace Litebase\Tests\Integration;

use Litebase\ApiClient;
use Litebase\Configuration;

beforeAll(function () {
    exec('docker compose -f ./tests/docker-compose.test.yml up -d');

    // Wait for the API to be healthy. Poll the configured host/port until we receive a response
    $url = 'http://localhost:8888/';
    $timeoutSeconds = 3;
    $start = time();

    while (true) {
        $success = file_get_contents($url, false, stream_context_create([
            'http' => [
                'method' => 'GET',
                'ignore_errors' => true,
            ],
        ]));

        if ($success !== false) {
            // API is healthy
            break;
        }

        if (time() - $start > $timeoutSeconds) {
            // Tear down in case of failure and raise an error
            exec('docker compose -f ./tests/docker-compose.test.yml down -v');
            throw new \RuntimeException("Timed out waiting for the API to become healthy at {$url}");
        }

        // Back off before retrying
        sleep(1);
    }
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
