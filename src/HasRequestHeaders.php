<?php

declare(strict_types=1);

namespace Litebase;

trait HasRequestHeaders
{
    /**
     * @param  array<string, string>  $headers
     * @return array<string, string>
     */
    protected function requestHeaders(string $host, ?string $port, int $contentLength, array $headers = []): array
    {
        // Include port if non standard port is used
        if ($port !== null && ! in_array($port, [80, 443])) {
            $host = sprintf('%s:%d', $host, $port);
        } else {
            $host = $host;
        }

        return [
            'Content-Type' => 'application/json',
            'Content-Length' => (string) $contentLength,
            'Host' => $host,
            'X-Litebase-Date' => date('U'),
            ...$headers,
        ];
    }
}
