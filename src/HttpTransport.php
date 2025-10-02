<?php

namespace Litebase;


class HttpTransport implements TransportInterface
{
    use HasRequestHeaders;
    use SignsRequests;

    /**
     * The API client instance.
     */
    protected ApiClient $client;

    /**
     * Create a new instance of the client.
     */
    public function __construct(
        private Configuration $config,
        array $clientConfig = []
    ) {
        // if ($config->getPort() === null) {
        //     $baseUri = sprintf('https://%s', $config->getHost());
        // } else {
        //     $baseUri = sprintf('http://%s:%d', $config->getHost(), $config->getPort());
        // }

        $this->client = new ApiClient($config);

        // $this->client = new Client(array_merge([
        //     'base_uri' => $baseUri,
        //     'http_errors' => false,
        //     'timeout'  => 30,
        //     'headers' => [
        //         'Connection' => 'keep-alive',
        //     ],
        //     'version' => '2.0',
        // ], $clientConfig));
    }

    /**
     * Send a request to the data api.
     */
    public function send(Query $query)
    {
        // $method = 'POST';
        // $path = sprintf('%s/query', $this->database);
        // $data = $query->toArray();
        // $headers = $this->requestHeaders(contentLength: strlen(json_encode($data)));

        // $token = $this->getToken(
        //     method: $method,
        //     path: $path,
        //     headers: $headers,
        //     data: $data,
        // );

        // try {
        //     $response = $this->client->request($method, $path, [
        //         'json' => $data,
        //         'headers' =>  [
        //             ...$headers,
        //             'Authorization' => $token,
        //         ],
        //     ]);

        //     $result = json_decode((string) $response->getBody(), true);

        //     if ($response->getStatusCode() >= 400 || ($result['status'] ?? null) === 'error') {
        //         throw new LitebaseConnectionException(
        //             code: $response->getStatusCode(),
        //             message: $result['message'] ?? 'Unknown error',
        //         );
        //     }

        //     return $result;
        // } catch (Exception $e) {
        //     if ($e instanceof ConnectException) {
        //         throw new LitebaseConnectionException($e->getMessage());
        //     }

        //     throw $e;
        // }

        return $this->client->query()->createQuery(
            $this->config->getDatabase(),
            $this->config->getBranch(),
            $query->toArray()
        );
    }
}
