<?php

namespace Litebase;

use Exception;
use Litebase\Exceptions\LitebaseConnectionException;

class HttpStreamingTransport implements TransportInterface
{
    use HasRequestHeaders;
    use SignsRequests;

    protected Connection $connection;

    protected ?ChunkedSignatureSigner $chunkedSigner = null;

    /**
     * Create a new instance of the transport.
     */
    public function __construct(
        protected Configuration $config,
    ) {}

    public function send(Query $query): ?QueryResult
    {
        $path = sprintf(
            'v1/databases/%s/branches/%s/query/stream',
            $this->config->getDatabase(),
            $this->config->getBranch()
        );

        if (! isset($this->connection) || ! $this->connection->isOpen()) {
            $headers = $this->requestHeaders(
                host: $this->config->getHost(),
                port: $this->config->getPort(),
                contentLength: 0,
                headers: [
                    'Content-Type' => 'application/octet-stream',
                ]
            );

            $url = $this->config->getPort() === null
                ? sprintf('https://%s/%s', $this->config->getHost(), $path)
                : sprintf('http://%s:%d/%s', $this->config->getHost(), $this->config->getPort(), $path);

            if (! empty($this->config->getUsername()) || ! (empty($this->config->getPassword()))) {
                $headers['Authorization'] = 'Basic ' . base64_encode($this->config->getUsername() . ':' . $this->config->getPassword());
            }

            if (! empty($this->config->getAccessToken())) {
                $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
            }

            if (! empty($this->config->getAccessKeyId())) {
                // Use the streaming payload marker for chunked signature validation
                $token = $this->getToken(
                    accessKeyID: $this->config->getAccessKeyId(),
                    accessKeySecret: $this->config->getAccessKeySecret(),
                    method: 'POST',
                    path: $path,
                    headers: $headers,
                    data: 'STREAMING-LITEBASE-HMAC-SHA256-PAYLOAD',
                );

                $headers['Authorization'] = sprintf('Litebase-HMAC-SHA256 %s', $token);

                // Extract the seed signature from the token for chunk signing
                $seedSignature = ChunkedSignatureSigner::extractSignatureFromToken($token);

                if ($seedSignature !== null) {
                    // Create the chunked signature signer with the seed signature
                    $this->chunkedSigner = new ChunkedSignatureSigner(
                        $this->config->getAccessKeySecret(),
                        $headers['X-Litebase-Date'],
                        $seedSignature
                    );
                }
            }

            $this->connection = new Connection($url, $headers, $this->chunkedSigner);
        }

        try {
            $result = $this->connection->send($query);
        } catch (Exception $e) {
            throw new LitebaseConnectionException(
                code: $e->getCode(),
                message: $e->getMessage(),
            );
        }

        // if ($result === null) {
        //     $this->connection->close();

        //     throw new LitebaseConnectionClosedException(
        //         code: 0,
        //         message: 'Connection closed',
        //     );
        // }

        if (($result->errorMessage ?? null) === 'error') {
            $this->connection->close();

            throw new LitebaseConnectionException(
                code: $result->errorCode ?? 0,
                message: $result->errorMessage ?? 'Unknown error',
            );
        }

        return $result;
    }
}
