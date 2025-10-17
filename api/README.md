# Litebase PHP SDK

Litebase Server OpenAPI specification


## Installation & Usage

### Requirements

PHP 8.1 and later.

### Composer

To install the bindings via [Composer](https://getcomposer.org/), add the following to `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/GIT_USER_ID/GIT_REPO_ID.git"
    }
  ],
  "require": {
    "GIT_USER_ID/GIT_REPO_ID": "*@dev"
  }
}
```

Then run `composer install`

### Manual Installation

Download the files and include `autoload.php`:

```php
<?php
require_once('/path/to/Litebase PHP SDK/vendor/autoload.php');
```

## Getting Started

Please follow the [installation procedure](#installation--usage) and then run the following:

```php
<?php
require_once(__DIR__ . '/vendor/autoload.php');



// Configure HTTP basic authorization: BasicAuth
$config = Litebase\Generated\Configuration::getDefaultConfiguration()
              ->setUsername('YOUR_USERNAME')
              ->setPassword('YOUR_PASSWORD');

// Configure Bearer authorization: TokenAuth
$config = Litebase\Generated\Configuration::getDefaultConfiguration()->setAccessToken('YOUR_ACCESS_TOKEN');



$apiInstance = new Litebase\Generated\Api\AccessKeyApi(
    // If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
    // This is optional, `GuzzleHttp\Client` will be used as default.
    new GuzzleHttp\Client(),
    $config
);
$create_access_key_request = new \Litebase\Generated\Model\CreateAccessKeyRequest(); // \Litebase\Generated\Model\CreateAccessKeyRequest | Access key creation data

try {
    $result = $apiInstance->createAccessKey($create_access_key_request);
    print_r($result);
} catch (Exception $e) {
    echo 'Exception when calling AccessKeyApi->createAccessKey: ', $e->getMessage(), PHP_EOL;
}

```

## API Endpoints

All URIs are relative to *http://localhost:8080*

Class | Method | HTTP request | Description
------------ | ------------- | ------------- | -------------
*AccessKeyApi* | [**createAccessKey**](docs/Api/AccessKeyApi.md#createaccesskey) | **POST** /v1/access-keys | Create a new access key
*AccessKeyApi* | [**deleteAccessKey**](docs/Api/AccessKeyApi.md#deleteaccesskey) | **DELETE** /v1/access-keys/{accessKeyId} | Delete an access key
*AccessKeyApi* | [**getAccessKey**](docs/Api/AccessKeyApi.md#getaccesskey) | **GET** /v1/access-keys/{accessKeyId} | Show details of an specific access key
*AccessKeyApi* | [**listAccessKeys**](docs/Api/AccessKeyApi.md#listaccesskeys) | **GET** /v1/access-keys | List all access keys
*AccessKeyApi* | [**updateAccessKey**](docs/Api/AccessKeyApi.md#updateaccesskey) | **PUT** /v1/access-keys/{accessKeyId} | Update an existing access key
*ClusterStatusApi* | [**listClusterStatuses**](docs/Api/ClusterStatusApi.md#listclusterstatuses) | **GET** /v1/status | List all cluster statuses
*DatabaseApi* | [**createDatabase**](docs/Api/DatabaseApi.md#createdatabase) | **POST** /v1/databases | Create a new database
*DatabaseApi* | [**deleteDatabase**](docs/Api/DatabaseApi.md#deletedatabase) | **DELETE** /v1/databases/{databaseName} | Delete a database
*DatabaseApi* | [**getDatabase**](docs/Api/DatabaseApi.md#getdatabase) | **GET** /v1/databases/{databaseName} | Show details of a specific database
*DatabaseApi* | [**listDatabases**](docs/Api/DatabaseApi.md#listdatabases) | **GET** /v1/databases | List all databases
*DatabaseBackupApi* | [**createDatabaseBackup**](docs/Api/DatabaseBackupApi.md#createdatabasebackup) | **POST** /v1/databases/{databaseName}/branches/{branchName}/backups | Create a new database backup
*DatabaseBackupApi* | [**deleteDatabaseBackup**](docs/Api/DatabaseBackupApi.md#deletedatabasebackup) | **DELETE** /v1/databases/{databaseName}/branches/{branchName}/backups/{timestamp} | Delete a database backup
*DatabaseBackupApi* | [**getDatabaseBackup**](docs/Api/DatabaseBackupApi.md#getdatabasebackup) | **GET** /v1/databases/{databaseName}/branches/{branchName}/backups/{timestamp} | Show details of a specific database backup
*DatabaseBackupApi* | [**listDatabaseBackups**](docs/Api/DatabaseBackupApi.md#listdatabasebackups) | **GET** /v1/databases/{databaseName}/branches/{branchName}/backups | List all database backups
*DatabaseBranchApi* | [**createDatabaseBranch**](docs/Api/DatabaseBranchApi.md#createdatabasebranch) | **POST** /v1/databases/{databaseName}/branches | Create a new database branch
*DatabaseBranchApi* | [**deleteDatabaseBranch**](docs/Api/DatabaseBranchApi.md#deletedatabasebranch) | **DELETE** /v1/databases/{databaseName}/branches/{branchName} | Delete a database branch
*DatabaseBranchApi* | [**getDatabaseBranch**](docs/Api/DatabaseBranchApi.md#getdatabasebranch) | **GET** /v1/databases/{databaseName}/branches/{branchName} | Show details of a specific database branch
*DatabaseBranchApi* | [**listDatabaseBranches**](docs/Api/DatabaseBranchApi.md#listdatabasebranches) | **GET** /v1/databases/{databaseName}/branches | List all database branches
*DatabaseRestoreApi* | [**createDatabaseRestore**](docs/Api/DatabaseRestoreApi.md#createdatabaserestore) | **POST** /v1/databases/{databaseName}/branches/{branchName}/restore | Create a new database restore
*DatabaseSnapshotApi* | [**getDatabaseSnapshot**](docs/Api/DatabaseSnapshotApi.md#getdatabasesnapshot) | **GET** /v1/databases/{databaseName}/branches/{branchName}/snapshots/{timestamp} | Show details of a specific database snapshot
*DatabaseSnapshotApi* | [**listDatabaseSnapshots**](docs/Api/DatabaseSnapshotApi.md#listdatabasesnapshots) | **GET** /v1/databases/{databaseName}/branches/{branchName}/snapshots | List all database snapshots
*KeyApi* | [**createKey**](docs/Api/KeyApi.md#createkey) | **POST** /v1/keys | Create a new key
*KeyActivateApi* | [**createKeyActivate**](docs/Api/KeyActivateApi.md#createkeyactivate) | **POST** /v1/keys/activate | Create a new key activate
*QueryApi* | [**createQuery**](docs/Api/QueryApi.md#createquery) | **POST** /v1/databases/{databaseName}/branches/{branchName}/query | Create a new query
*QueryLogApi* | [**listQueryLogs**](docs/Api/QueryLogApi.md#listquerylogs) | **GET** /v1/databases/{databaseName}/branches/{branchName}/metrics/query | List all query logs
*QueryStreamApi* | [**createQueryStream**](docs/Api/QueryStreamApi.md#createquerystream) | **POST** /v1/databases/{databaseName}/branches/{branchName}/query/stream | Create a new query stream
*TokenApi* | [**createToken**](docs/Api/TokenApi.md#createtoken) | **POST** /v1/tokens | Create a new token
*TokenApi* | [**deleteToken**](docs/Api/TokenApi.md#deletetoken) | **DELETE** /v1/tokens/{tokenId} | Delete a token
*TokenApi* | [**getToken**](docs/Api/TokenApi.md#gettoken) | **GET** /v1/tokens/{tokenId} | Show details of a specific token
*TokenApi* | [**listTokens**](docs/Api/TokenApi.md#listtokens) | **GET** /v1/tokens | List all tokens
*TokenApi* | [**updateToken**](docs/Api/TokenApi.md#updatetoken) | **PUT** /v1/tokens/{tokenId} | Update an existing token
*UserApi* | [**createUser**](docs/Api/UserApi.md#createuser) | **POST** /v1/users | Create a new user
*UserApi* | [**deleteUser**](docs/Api/UserApi.md#deleteuser) | **DELETE** /v1/users/{username} | Delete a user
*UserApi* | [**getUser**](docs/Api/UserApi.md#getuser) | **GET** /v1/users/{username} | Show details of a specific user
*UserApi* | [**listUsers**](docs/Api/UserApi.md#listusers) | **GET** /v1/users | List all users
*UserApi* | [**updateUser**](docs/Api/UserApi.md#updateuser) | **PUT** /v1/users/{username} | Update an existing user

## Models

- [Any](docs/Model/Any.md)
- [BranchSettings](docs/Model/BranchSettings.md)
- [ColumnDefinition](docs/Model/ColumnDefinition.md)
- [CreateAccessKey201Response](docs/Model/CreateAccessKey201Response.md)
- [CreateAccessKey201ResponseData](docs/Model/CreateAccessKey201ResponseData.md)
- [CreateAccessKeyRequest](docs/Model/CreateAccessKeyRequest.md)
- [CreateDatabase200Response](docs/Model/CreateDatabase200Response.md)
- [CreateDatabase200ResponseData](docs/Model/CreateDatabase200ResponseData.md)
- [CreateDatabaseBackup200Response](docs/Model/CreateDatabaseBackup200Response.md)
- [CreateDatabaseBackup200ResponseData](docs/Model/CreateDatabaseBackup200ResponseData.md)
- [CreateDatabaseBranch200Response](docs/Model/CreateDatabaseBranch200Response.md)
- [CreateDatabaseBranch200ResponseData](docs/Model/CreateDatabaseBranch200ResponseData.md)
- [CreateDatabaseBranchRequest](docs/Model/CreateDatabaseBranchRequest.md)
- [CreateDatabaseRequest](docs/Model/CreateDatabaseRequest.md)
- [CreateDatabaseRestore200Response](docs/Model/CreateDatabaseRestore200Response.md)
- [CreateDatabaseRestoreRequest](docs/Model/CreateDatabaseRestoreRequest.md)
- [CreateKey200Response](docs/Model/CreateKey200Response.md)
- [CreateKeyActivate200Response](docs/Model/CreateKeyActivate200Response.md)
- [CreateKeyActivateRequest](docs/Model/CreateKeyActivateRequest.md)
- [CreateKeyRequest](docs/Model/CreateKeyRequest.md)
- [CreateQuery200Response](docs/Model/CreateQuery200Response.md)
- [CreateQuery200ResponseDataInner](docs/Model/CreateQuery200ResponseDataInner.md)
- [CreateQueryRequest](docs/Model/CreateQueryRequest.md)
- [CreateToken201Response](docs/Model/CreateToken201Response.md)
- [CreateUser201Response](docs/Model/CreateUser201Response.md)
- [CreateUserRequest](docs/Model/CreateUserRequest.md)
- [DatabaseBackupRestorePoint](docs/Model/DatabaseBackupRestorePoint.md)
- [DatabaseBackupSettings](docs/Model/DatabaseBackupSettings.md)
- [DatabaseIncrementalBackupSettings](docs/Model/DatabaseIncrementalBackupSettings.md)
- [DatabaseSettings](docs/Model/DatabaseSettings.md)
- [DeleteAccessKey200Response](docs/Model/DeleteAccessKey200Response.md)
- [DeleteDatabase200Response](docs/Model/DeleteDatabase200Response.md)
- [DeleteDatabaseBackup200Response](docs/Model/DeleteDatabaseBackup200Response.md)
- [DeleteDatabaseBranch200Response](docs/Model/DeleteDatabaseBranch200Response.md)
- [DeleteToken200Response](docs/Model/DeleteToken200Response.md)
- [DeleteUser204Response](docs/Model/DeleteUser204Response.md)
- [ErrorResponse](docs/Model/ErrorResponse.md)
- [GetAccessKey200Response](docs/Model/GetAccessKey200Response.md)
- [GetAccessKey200ResponseData](docs/Model/GetAccessKey200ResponseData.md)
- [GetDatabase200Response](docs/Model/GetDatabase200Response.md)
- [GetDatabaseBackup200Response](docs/Model/GetDatabaseBackup200Response.md)
- [GetDatabaseBackup200ResponseData](docs/Model/GetDatabaseBackup200ResponseData.md)
- [GetDatabaseBranch200Response](docs/Model/GetDatabaseBranch200Response.md)
- [GetDatabaseSnapshot200Response](docs/Model/GetDatabaseSnapshot200Response.md)
- [GetToken200Response](docs/Model/GetToken200Response.md)
- [GetToken200ResponseData](docs/Model/GetToken200ResponseData.md)
- [GetUser200Response](docs/Model/GetUser200Response.md)
- [ListAccessKeys200Response](docs/Model/ListAccessKeys200Response.md)
- [ListAccessKeys200ResponseDataInner](docs/Model/ListAccessKeys200ResponseDataInner.md)
- [ListClusterStatuses200Response](docs/Model/ListClusterStatuses200Response.md)
- [ListClusterStatuses200ResponseData](docs/Model/ListClusterStatuses200ResponseData.md)
- [ListDatabaseBackups200Response](docs/Model/ListDatabaseBackups200Response.md)
- [ListDatabaseBranches200Response](docs/Model/ListDatabaseBranches200Response.md)
- [ListDatabaseSnapshots200Response](docs/Model/ListDatabaseSnapshots200Response.md)
- [ListDatabases200Response](docs/Model/ListDatabases200Response.md)
- [ListQueryLogs200Response](docs/Model/ListQueryLogs200Response.md)
- [ListQueryLogs200ResponseMeta](docs/Model/ListQueryLogs200ResponseMeta.md)
- [ListTokens200Response](docs/Model/ListTokens200Response.md)
- [ListTokens200ResponseDataInner](docs/Model/ListTokens200ResponseDataInner.md)
- [ListUsers200Response](docs/Model/ListUsers200Response.md)
- [ListUsers200ResponseDataInner](docs/Model/ListUsers200ResponseDataInner.md)
- [Privilege](docs/Model/Privilege.md)
- [QueryInput](docs/Model/QueryInput.md)
- [Statement](docs/Model/Statement.md)
- [StatementEffect](docs/Model/StatementEffect.md)
- [StatementParameter](docs/Model/StatementParameter.md)
- [SuccessResponse](docs/Model/SuccessResponse.md)
- [UpdateAccessKey200Response](docs/Model/UpdateAccessKey200Response.md)
- [UpdateToken200Response](docs/Model/UpdateToken200Response.md)
- [ValidationErrorResponse](docs/Model/ValidationErrorResponse.md)
- [ValidationErrorResponseErrorsInner](docs/Model/ValidationErrorResponseErrorsInner.md)

## Authorization

Authentication schemes defined for the API:
### AccessKeyAuth

### BasicAuth

- **Type**: HTTP basic authentication

### TokenAuth

- **Type**: Bearer authentication

## Tests

To run the tests, use:

```bash
composer install
vendor/bin/phpunit
```

## Author



## About this package

This PHP package is automatically generated by the [OpenAPI Generator](https://openapi-generator.tech) project:

- API version: `1.0.0`
    - Package version: `1.0.0`
    - Generator version: `7.14.0`
- Build package: `org.openapitools.codegen.languages.PhpClientCodegen`
