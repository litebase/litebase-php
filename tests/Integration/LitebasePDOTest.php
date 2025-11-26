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
        throw new \RuntimeException('Failed to connect to Litebase server for integration tests: ' . $e->getMessage());
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

    test('can handle all column data types', function () {
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

        // Create table with all supported column types
        $pdo->exec('CREATE TABLE IF NOT EXISTS type_test (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            int_col INTEGER,
            float_col REAL,
            text_col TEXT,
            blob_col BLOB,
            null_col TEXT
        )');

        // Insert data with different types using execute with params
        $statement = $pdo->prepare('INSERT INTO type_test (int_col, float_col, text_col, blob_col, null_col) VALUES (?, ?, ?, ?, ?)');

        $blobData = hex2bin('48656c6c6f20576f726c64'); // "Hello World" as binary

        $statement->execute([
            42,                    // INTEGER
            3.14159,              // FLOAT
            'Hello World',        // TEXT
            $blobData,            // BLOB
            null,                 // NULL
        ]);

        expect($statement->rowCount())->toBe(1);

        // Now test bindValue for a second insert
        $statement2 = $pdo->prepare('INSERT INTO type_test (int_col, float_col, text_col, blob_col, null_col) VALUES (?, ?, ?, ?, ?)');

        $blobData2 = hex2bin('776f726c6420686921'); // "world hi!" as binary

        $statement2->bindValue(1, 99, PDO::PARAM_INT);
        $statement2->bindValue(2, 2.71828, PDO::PARAM_STR);
        $statement2->bindValue(3, 'Test String', PDO::PARAM_STR);
        $statement2->bindValue(4, $blobData2, PDO::PARAM_LOB);
        $statement2->bindValue(5, null, PDO::PARAM_NULL);

        $statement2->execute();

        expect($statement2->rowCount())->toBe(1);

        // Retrieve and verify the first row
        $statement = $pdo->prepare('SELECT * FROM type_test WHERE int_col = ?');
        $statement->execute([42]);

        /** @var array<string, mixed> $row */
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        /** @var float $floatValue */
        $floatValue = $row['float_col'];
        /** @var string  $blobValue */
        $blobValue = $row['blob_col'];

        expect($row)->not->toBeNull();
        expect($row['int_col'])->toBe(42);
        expect($row['float_col'])->toBeFloat();
        expect(abs($floatValue - 3.14159))->toBeLessThan(0.00001);
        expect($row['text_col'])->toBe('Hello World');
        expect($row['blob_col'])->toBe($blobData);
        expect(bin2hex($blobValue))->toBe('48656c6c6f20576f726c64');
        expect($row['null_col'])->toBeNull();

        // Retrieve and verify the second row
        $statement = $pdo->prepare('SELECT * FROM type_test WHERE int_col = ?');
        $statement->execute([99]);

        /** @var array<string, mixed> $row2 */
        $row2 = $statement->fetch(PDO::FETCH_ASSOC);

        expect($row2)->not->toBeNull();
        expect($row2['int_col'])->toBe(99);
        expect($row2['float_col'])->toBeFloat();
        expect($row2['text_col'])->toBe('Test String');
        expect($row2['blob_col'])->toBe($blobData2);
        expect($row2['null_col'])->toBeNull();
    });
});
