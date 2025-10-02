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

    public function __construct(
        private string $host,
        private int $port = 0,
        protected string $database = '',
        private string $key = '',
        private string $secret = '',
    ) {}

    public function send(Query $query): array
    {
        $path = sprintf('%s/query/stream', $this->database);

        if (!isset($this->connection) || !$this->connection->isOpen()) {
            $headers = $this->requestHeaders(
                contentLength: 0,
                headers: [
                    'Content-Type' => 'application/octet-stream',
                ]
            );

            $token = $this->getToken(
                method: 'POST',
                path: $path,
                headers: $headers,
                data: null,
            );

            $url = $this->port === null
                ? sprintf('https://%s/%s', $this->host, $path)
                : sprintf('http://%s:%d/%s', $this->host, $this->port, $path);

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
