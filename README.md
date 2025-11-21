# Litebase PHP SDK (Alpha)

[![tests](https://github.com/litebase/litebase-php/actions/workflows/tests.yml/badge.svg)](https://github.com/litebase/litebase-php/actions/workflows/tests.yml)

A PHP SDK for interacting with [Litebase](https://github.com/litebase/litebase), an open source distributed database built on SQLite, distributed file systems, and object storage.

## Installation

You can install the package via composer:

```bash
composer require litebase/litebase-php
```

## Usage

```php
use Litebase\Configuration;
use Litebase\LitebasePDO;

$pdo = new LitebasePDO([
    'host' => 'localhost',
    'port' => 8888,
    'token' => 'your_api_token',
    'database' => 'your_database_name/main',
]);

$statement = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$statement->execute([1]);
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
    print_r($row);
}

// Use transactions
$pdo = $pdo->beginTransaction();

try {
    $statement = $pdo->prepare('INSERT INTO users (name, email) VALUES (?, ?)');
    $statement->execute(['John Doe', 'john@example.com']);

    $statement = $pdo->prepare('INSERT INTO logs (user_id, action) VALUES (?, ?)');
    $statement->execute([$pdo->lastInsertId(), 'user_created']);
    
    $pdo->commit();
} catch (\Exception $e) {
    $pdo->rollBack();
    throw $e;
}
```

## Contributing

Please see [CONTRIBUTING](https://github.com/litebase/litebase-php?tab=contributing-ov-file) for details.

### Testing

You can run the tests:

``` bash
composer test
```

*Integration tests require a running Litebase Server. When running integration tests, a server will be automatically started using Docker.*
You can run the tests with:

``` bash
composer test-integration
```

## Code of Conduct

Please see [CODE OF CONDUCT](https://github.com/litebase/litebase-php?tab=coc-ov-file) for details.

## Security

All security related issues should be reported directly to [security@litebase.com](mailto:security@litebase.com).

## License

Litebase is [open-sourced](https://opensource.org/) software licensed under the [MIT License](LICENSE.md).
