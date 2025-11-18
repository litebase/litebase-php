<?php

declare(strict_types=1);

namespace Litebase;

use Litebase\OpenAPI\Configuration as BaseConfiguration;

/**
 * Extended configuration class with HMAC-SHA256 authentication support.
 */
class Configuration extends BaseConfiguration
{
    protected ?string $accessKeyId;

    protected ?string $accessKeySecret;

    protected ?string $database = '';

    protected ?string $branch = '';

    protected ?string $port = null;

    protected ?string $transport = null;

    /**
     * Create a new Configuration instance from an array of settings.
     *
     * @param  array<string, string|null>  $config
     */
    public static function create(array $config = []): self
    {
        $configuration = new self;

        $host = $config['host'] ?? '';
        $port = $config['port'] ?? null;
        $database = $config['database'] ?? null;
        $configuration->transport = $config['transport'] ?? 'http';

        $configuration
            ->setHost($host)
            ->setPort($port)
            ->setDatabase($database);

        if (isset($config['access_key_id'], $config['access_key_secret'])) {
            $configuration->setAccessKey($config['access_key_id'], $config['access_key_secret']);
        }

        if (isset($config['token'])) {
            $configuration->setAccessToken($config['token']);
        }

        if (isset($config['username'], $config['password'])) {
            $configuration->setUsername($config['username'])
                ->setPassword($config['password']);
        }

        return $configuration;
    }

    /**
     * Get the access key ID.
     */
    public function getAccessKeyId(): string
    {
        return $this->accessKeyId ?? '';
    }

    /**
     * Get the access key secret.
     */
    public function getAccessKeySecret(): string
    {
        return $this->accessKeySecret ?? '';
    }

    /**
     * Get the database name.
     */
    public function getDatabase(): ?string
    {
        return $this->database;
    }

    /**
     * Get the branch name.
     */
    public function getBranch(): ?string
    {
        return $this->branch;
    }

    /**
     * Check if access key authentication is configured.
     */
    public function hasAccessKey(): bool
    {
        return ! empty($this->accessKeyId) && ! empty($this->accessKeySecret);
    }

    /**
     * Get the port.
     */
    public function getPort(): ?string
    {
        return $this->port;
    }

    /**
     * Get the transport type.
     */
    public function getTransport(): ?string
    {
        return $this->transport;
    }

    /**
     * Set access key credentials for HMAC-SHA256 authentication.
     */
    public function setAccessKey(?string $accessKeyId, ?string $accessKeySecret): self
    {
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;

        return $this;
    }

    /**
     * Set the database name (and optional branch) in the format "database/branch".
     */
    public function setDatabase(?string $database): self
    {
        if ($database) {
            [$this->database, $this->branch] = explode('/', $database);
        }

        return $this;
    }

    /**
     * Set the port.
     */
    public function setPort(?string $port): self
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Set the transport type.
     */
    public function setTransport(?string $transport): self
    {
        $this->transport = $transport;

        return $this;
    }
}
