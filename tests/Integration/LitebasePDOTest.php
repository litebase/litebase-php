<?php

declare(strict_types=1);

namespace Tests\Integration;

use Litebase\ApiClient;
use Litebase\Configuration;
use Litebase\LitebaseClient;
use Litebase\LitebasePDO;
use Litebase\OpenAPI\Model\DatabaseStoreRequest;
use PDO;

beforeAll(function () {
    LitebaseContainer::start();

    $configuration = new Configuration;

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
        throw new \RuntimeException('Failed to connect to Litebase server for integration tests: '.$e->getMessage());
    }
});

afterAll(function () {
    LitebaseContainer::stop();
});

describe('LitebasePDO', function () {
    test('can perform a transaction', function () {
        $client = new LitebaseClient(
            Configuration::create([
                'host' => 'localhost',
                'port' => '8888',
                'username' => 'root',
                'password' => 'password',
                'database' => 'test/main',
            ])
        );

        $pdo = new LitebasePDO($client);

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

    test('can perform a transaction with http_streaming', function () {
        $config = Configuration::create([
            'host' => 'localhost',
            'port' => '8888',
            'username' => 'root',
            'password' => 'password',
            'database' => 'test/main',
            'transport' => 'http_streaming',
        ]);

        $apiClient = new ApiClient($config);

        // Create an Access Key to use http_streaming transport
        $response = $apiClient->accessKey()->createAccessKey(
            new \Litebase\OpenAPI\Model\AccessKeyStoreRequest([
                'description' => 'http-streaming-key',
                'statements' => [
                    new \Litebase\OpenAPI\Model\Statement([
                        'effect' => \Litebase\OpenAPI\Model\StatementEffect::ALLOW,
                        'actions' => [\Litebase\OpenAPI\Model\Privilege::STAR],
                        'resource' => '*',
                    ]),
                ],
            ])
        );

        if (! $response instanceof \Litebase\OpenAPI\Model\CreateAccessKey201Response) {
            throw new \RuntimeException('Invalid response when creating access key for integration tests.');
        }

        $config = $config->setAccessKey(
            $response->getData()->getAccessKeyId(),
            $response->getData()->getAccessKeySecret(),
        );

        $pdo = new LitebasePDO(new LitebaseClient($config));

        $result = $pdo->beginTransaction();
        expect($pdo->hasError())->toBeFalse();
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
