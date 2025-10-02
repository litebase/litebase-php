<?php

namespace Litebase\Middleware;

use Litebase\Configuration;
use Litebase\HasRequestHeaders;
use Litebase\LitebaseConfiguration;
use Litebase\SignsRequests;
use Psr\Http\Message\RequestInterface;

/**
 * Guzzle middleware to automatically sign requests with HMAC-SHA256
 */
class AuthMiddleware
{
    use HasRequestHeaders;
    use SignsRequests;

    public function __construct(private Configuration $config) {}

    /**
     * Create middleware callable
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            if (!$this->config->hasAccessKey()) {
                return $handler($request, $options);
            }

            $signedRequest = $this->signRequest($request);

            return $handler($signedRequest, $options);
        };
    }

    /**
     * Sign the request with HMAC-SHA256
     */
    private function signRequest(RequestInterface $request): RequestInterface
    {
        $body = (string) $request->getBody();

        $headers = $this->requestHeaders(
            host: $request->getUri()->getHost(),
            port: $request->getUri()->getPort(),
            contentLength: strlen($body)
        );

        $token = $this->getToken(
            accessKeyID: $this->config->getAccessKeyId(),
            accessKeySecret: $this->config->getAccessKeySecret(),
            method: $request->getMethod(),
            path: $request->getUri()->getPath(),
            headers: $headers,
            data: json_decode($body, true),
        );

        // Add signed headers to request
        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        $request = $request->withHeader('Authorization', sprintf('Litebase-HMAC-SHA256 %s', $token));

        // Recreate the body stream to ensure it's readable for the actual request
        if ($body !== '') {
            // $stream = \GuzzleHttp\Psr7\Utils::streamFor($body);
            // $request = $request->withBody($stream);
        }

        return $request;
    }

    /**
     * Static factory method for easy middleware creation
     */
    public static function create(Configuration $config): callable
    {
        return new self($config);
    }
}
