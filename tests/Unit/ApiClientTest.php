<?php

namespace Litebase\Tests\Unit;

use GuzzleHttp\Client;
use Litebase\ApiClient;
use Litebase\Configuration;

describe('ApiClient', function () {
    it('can be created', function () {
        $client = new ApiClient(new Configuration);

        expect($client)->toBeInstanceOf(ApiClient::class);
        expect($client->getConfiguration())->toBeInstanceOf(Configuration::class);
    });

    it('returns the configuration', function () {
        $configuration = new Configuration;
        $client = new ApiClient($configuration);

        expect($client->getConfiguration())->toBeInstanceOf(Configuration::class);
    });

    it('returns the http client', function () {
        $configuration = (new Configuration)
            ->setPort('8080');

        $client = new ApiClient($configuration);

        expect($client->getHttpClient())->toBeInstanceOf(Client::class);
    });
});
