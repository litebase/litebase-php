<?php

namespace Litebase;

class HttpTransport implements TransportInterface
{
    /**
     * The API client instance.
     */
    protected ApiClient $client;

    /**
     * Create a new instance of the transport.
     */
    public function __construct(
        protected Configuration $config,
    ) {
        $this->client = new ApiClient($config);
    }

    /**
     * Send a request to the data api.
     */
    public function send(Query $query)
    {
        return $this->client->query()->createQuery(
            $this->config->getDatabase(),
            $this->config->getBranch(),
            $query->toArray()
        );
    }
}
