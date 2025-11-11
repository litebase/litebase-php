<?php

namespace Litebase;

class RequestSigner
{
    /**
     * Sign a request and return the authorization token.
     *
     * @param  array<string, string>  $headers
     * @param  array<string, string>  $queryParams
     */
    public static function handle(
        string $accessKeyID,
        string $accessKeySecret,
        string $method,
        string $path,
        array $headers,
        string $data,
        array $queryParams = [],
    ): string {
        $headers = array_change_key_case($headers);
        ksort($headers);
        $headers = array_filter(
            $headers,
            fn($value, $key) => in_array($key, ['content-type', 'host', 'x-litebase-date']),
            ARRAY_FILTER_USE_BOTH
        );

        $queryParams = array_change_key_case($queryParams);
        ksort($queryParams);

        // Handle special streaming payload marker
        if ($data === 'STREAMING-LITEBASE-HMAC-SHA256-PAYLOAD') {
            $bodyHash = hash('sha256', $data);
        } else {
            $bodyHash = hash('sha256', (empty($data) ? '' : $data));
        }

        $requestString = implode('', [
            $method,
            '/' . ltrim($path, '/'),
            json_encode($headers, JSON_UNESCAPED_SLASHES),
            json_encode((empty($queryParams)) ? (object) [] : $queryParams, JSON_UNESCAPED_SLASHES),
            $bodyHash,
        ]);

        $signed_request = hash('sha256', trim($requestString));

        $date = hash_hmac('sha256', $headers['x-litebase-date'], $accessKeySecret);
        $service = hash_hmac('sha256', 'litebase_request', $date);
        $signature = hash_hmac('sha256', $signed_request, $service);
        $token = base64_encode("credential=$accessKeyID;signed_headers=content-type,host,x-litebase-date;signature=$signature");

        return $token;
    }
}
