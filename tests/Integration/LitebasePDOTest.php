<?php

declare(strict_types=1);

namespace Litebase\Tests\Integration;

use Litebase\ApiClient;
use Litebase\Configuration;
use Litebase\LitebasePDO;
use Litebase\OpenAPI\Model\DatabaseStoreRequest;
use PDO;

beforeAll(function () {
    // Create Test directory
    if (file_exists('./tests/.litebase')) {
        exec('rm -rf ./tests/.litebase');
    }

    exec('mkdir -p ./tests/.litebase');
    exec('chmod 777 ./tests/.litebase');

    exec('docker compose -f ./tests/docker-compose.test.yml up -d');
    sleep(2);

    $configuration = new Configuration();

    $configuration
        ->setHost('127.0.0.1')
        ->setPort('8888')
        ->setUsername('root')
        ->setPassword('password');

    $client = new ApiClient($configuration);


    try {
        $client->database()->createDatabase(new DatabaseStoreRequest([
            'name' => 'test',
        ]));
    } catch (\Exception $e) {
        $lines = [];
        exec('docker compose -f ./tests/docker-compose.test.yml logs --tail=200 --no-color', $lines, $rc);

        $logs = implode("\n", $lines);

        throw new \RuntimeException('Failed to connect to Litebase server for integration tests: ' . $e->getMessage() . "\nContainer logs:\n{$logs}");
    }
});

afterAll(function () {
    exec('docker compose -f ./tests/docker-compose.test.yml down -v');

    // Delete the .litebase directory to clean up any persisted data
    exec('rm -rf ./tests/.litebase');
});

describe('LitebasePDO', function () {
    $pdo = new LitebasePDO([
        'host' => 'localhost',
        'port' => '8888',
        'username' => 'root',
        'password' => 'password',
        'database' => 'test/main',
    ]);

    test('can perform a transaction', function () use ($pdo) {
        $result = $pdo->beginTransaction();
        expect($result)->toBeTrue();

        $affectedRows = $pdo->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT)');

        expect($affectedRows)->toBeGreaterThanOrEqual(0);

        $statement = $pdo->prepare('INSERT INTO users (name, email) VALUES (?, ?)');

        $insertResult = $statement->execute(['Alice', 'alice@example.com']);

        expect($insertResult)->toBeTrue();

        $result = $pdo->commit();
        expect($result)->toBeTrue();

        $statement = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $statement->execute(['alice@example.com']);

        /** @var array<string, mixed> $user */
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        expect($user['name'])->toBe('Alice');
        expect($user['email'])->toBe('alice@example.com');
    });
});
