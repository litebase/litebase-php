<?php

declare(strict_types=1);

namespace Litebase;

trait SignsRequests
{
    /**
     * Get an authorization token for a request.
     *
     * @param  array<string, string>  $headers
     * @param  array<string, string>  $queryParams
     */
    public function getToken(
        string $accessKeyID,
        #[\SensitiveParameter]
        string $accessKeySecret,
        string $method,
        string $path,
        array $headers,
        string $data,
        array $queryParams = [],
    ): string {
        return RequestSigner::handle(
            accessKeyID: $accessKeyID,
            accessKeySecret: $accessKeySecret,
            method: $method,
            path: $path,
            headers: $headers,
            data: $data,
            queryParams: $queryParams,
        );
    }
}
