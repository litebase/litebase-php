<?php

namespace Litebase;

use Exception;
use Litebase\Exceptions\LitebaseConnectionClosedException;
use Litebase\Exceptions\LitebaseConnectionException;

class HttpStreamingTransport implements TransportInterface
{
    use HasRequestHeaders;
    use SignsRequests;

    protected Connection $connection;

    /**
     * Create a new instance of the transport.
     */
    public function __construct(
        protected Configuration $config,
    ) {}

    public function send(Query $query): array
    {
        $path = sprintf(
            '%s/branches/%s/query/stream',
            $this->config->getDatabase(),
            $this->config->getBranch()
        );

        if (!isset($this->connection) || !$this->connection->isOpen()) {
            $headers = $this->requestHeaders(
                host: $this->config->getHost(),
                port: $this->config->getPort(),
                contentLength: 0,
                headers: [
                    'Content-Type' => 'application/octet-stream',
                ]
            );

            $token = $this->getToken(
                accessKeyID: $this->config->getAccessKeyId(),
                accessKeySecret: $this->config->getAccessKeySecret(),
                method: 'POST',
                path: $path,
                headers: $headers,
                data: null,
            );

            $url = $this->config->getPort() === null
                ? sprintf('https://%s/%s', $this->config->getHost(), $path)
                : sprintf('http://%s:%d/%s', $this->config->getHost(), $this->config->getPort(), $path);

            $this->connection = new Connection(
                $url,
                [
                    ...$headers,
                    'Authorization' => sprintf('Litebase-HMAC-SHA256 %s', $token),
                ],
            );
        }

        $result = null;

        try {
            $result = $this->connection->send($query);
        } catch (Exception $e) {
            throw new LitebaseConnectionException(
                code: $e->getCode(),
                message: $e->getMessage(),
            );

            return [];
        }

        if ($result === null) {
            $this->connection->close();

            throw new LitebaseConnectionClosedException(
                code: 0,
                message: 'Connection closed',
            );
        }


        if (($result['status'] ?? null) === 'error') {
            $this->connection->close();

            throw new LitebaseConnectionException(
                code: $result['error_code'] ?? 0,
                message: $result['message'] ?? 'Unknown error',
            );
        }

        if (empty($result)) {
            return [];
        }

        return $result;
    }
}
