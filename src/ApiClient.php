<?php

namespace Litebase;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Litebase\Generated\API\AccessKeyApi;
use Litebase\Generated\API\ClusterStatusApi;
use Litebase\Generated\API\DatabaseApi;
use Litebase\Generated\API\DatabaseBackupApi;
use Litebase\Generated\API\DatabaseBranchApi;
use Litebase\Generated\API\DatabaseRestoreApi;
use Litebase\Generated\API\DatabaseSnapshotApi;
use Litebase\Generated\API\KeyActivateApi;
use Litebase\Generated\API\KeyApi;
use Litebase\Generated\API\QueryApi;
use Litebase\Generated\API\QueryLogApi;
use Litebase\Generated\API\QueryStreamApi;
use Litebase\Generated\API\TokenApi;
use Litebase\Generated\API\UserApi;
use Litebase\Middleware\AuthMiddleware;

/**
 * Litebase API Client Factory
 *
 * Provides convenient methods to create configured API clients
 */
class ApiClient
{
    // Create a new instance of the API client.
    public function __construct(
        protected Configuration $config,
        protected ?Client $httpClient = null,
    ) {}

    /**
     * Get the underlying configuration
     */
    public function getConfiguration(): Configuration
    {
        return $this->config;
    }

    /**
     * Get configured HTTP client with authentication middleware
     */
    public function getHttpClient(): Client
    {
        if ($this->httpClient === null) {
            $stack = HandlerStack::create();

            // Add authentication middleware if access key is configured
            if ($this->config->hasAccessKey()) {
                $stack->push(AuthMiddleware::create($this->config), 'litebase_auth');
            }

            $this->httpClient = new Client([
                'handler' => $stack,
                'base_uri' => $this->config->getPort() === null
                    ? sprintf('https://%s', $this->config->getHost())
                    : sprintf('http://%s:%s', $this->config->getHost(), $this->config->getPort()),
                // 'http_errors' => false,
                'timeout' => 30,
                'headers' => [
                    'Connection' => 'keep-alive',
                ],
                'version' => '2.0',
            ]);
        }

        return $this->httpClient;
    }

    // API Client Factories

    public function accessKey(): AccessKeyApi
    {
        return new AccessKeyApi($this->getHttpClient(), $this->config);
    }

    public function clusterStatus(): ClusterStatusApi
    {
        return new ClusterStatusApi($this->getHttpClient(), $this->config);
    }

    public function database(): DatabaseApi
    {
        return new DatabaseApi($this->getHttpClient(), $this->config);
    }

    public function databaseBackup(): DatabaseBackupApi
    {
        return new DatabaseBackupApi($this->getHttpClient(), $this->config);
    }

    public function databaseBranch(): DatabaseBranchApi
    {
        return new DatabaseBranchApi($this->getHttpClient(), $this->config);
    }

    public function databaseRestore(): DatabaseRestoreApi
    {
        return new DatabaseRestoreApi($this->getHttpClient(), $this->config);
    }

    public function databaseSnapshot(): DatabaseSnapshotApi
    {
        return new DatabaseSnapshotApi($this->getHttpClient(), $this->config);
    }

    public function key(): KeyApi
    {
        return new KeyApi($this->getHttpClient(), $this->config);
    }

    public function keyActivate(): KeyActivateApi
    {
        return new KeyActivateApi($this->getHttpClient(), $this->config);
    }

    public function query(): QueryApi
    {
        return new QueryApi($this->getHttpClient(), $this->config);
    }

    public function queryLog(): QueryLogApi
    {
        return new QueryLogApi($this->getHttpClient(), $this->config);
    }

    public function queryStream(): QueryStreamApi
    {
        return new QueryStreamApi($this->getHttpClient(), $this->config);
    }

    public function token(): TokenApi
    {
        return new TokenApi($this->getHttpClient(), $this->config);
    }

    public function user(): UserApi
    {
        return new UserApi($this->getHttpClient(), $this->config);
    }
}
