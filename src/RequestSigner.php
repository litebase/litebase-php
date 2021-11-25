<?php

namespace LitebaseDB;

class RequestSigner
{
    public static function handle(
        string $accessKeyID,
        string $accessKeySecret,
        string $method,
        string $path,
        array $headers,
        array $data,
        array $queryParams = [],
    ): string {
        $headers = array_change_key_case($headers);
        ksort($headers);
        $headers = array_filter(
            $headers,
            fn ($value, $key) => in_array($key, ['content-type', 'host', 'x-lbdb-date']),
            ARRAY_FILTER_USE_BOTH
        );

        $queryParams = array_change_key_case($queryParams);
        ksort($queryParams);

        $data = array_change_key_case($data);
        ksort($data);

        $requestString = implode('', [
            $method,
            $path,
            json_encode($headers),
            json_encode($queryParams),
            json_encode($data),
        ]);

        $signed_request = hash('sha256', $requestString);
        $date = hash_hmac('sha256', date('Ymd'), $accessKeySecret);
        $service = hash_hmac('sha256', 'litebasedb_request', $date);
        $signature = hash_hmac('sha256', $signed_request, $service);
        $token = base64_encode("credential=$accessKeyID;signed_headers=content-type,host,x-lbdb-date;signature=$signature");

        return $token;
    }
}
