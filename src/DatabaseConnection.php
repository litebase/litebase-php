<?php

namespace Litebase;

use Closure;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class DatabaseConnection
{
    /**
     * The Guzzle client of the connection.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The id of the database connection.
     *
     * @var string
     */
    protected $id;

    /**
     * The opened state of the database connection.
     *
     * @var boolean
     */
    protected $opened = false;

    /**
     * Create a new instance of a database connection.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->url(),
            'timeout'  => 30,
            // 'version' => '2',
            'headers' => [],
        ]);

        $this->open();
    }

    // /**
    //  * Close the database connection.
    //  */
    // public function close(): bool
    // {
    //     if (!$this->id) {
    //         return false;
    //     }

    //     $client = $this->client;
    //     $id = $this->id;

    //     $process = process(function () use ($client, $id) {
    //         try {
    //             $client->send('DELETE', "connections/{$id}");
    //         } catch (Exception $e) {
    //             logger()->error($e->getMessage());
    //             // throw $th;
    //         }
    //     });

    //     $process->withTrails()->run();

    //     $this->opened = false;
    //     $this->id = null;

    //     return true;
    // }

    public function open()
    {
        try {
            $response = $this->client->post('connections', []);
            $result = json_decode($response->getBody(), true);
            $this->id = $result['data']['id'];
            $this->opened = true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function send(array $data)
    {
        $response = $this->client->post('query', [
            'form_params' => [
                'connection_id' => $this->id,
                'data' => $data,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function url()
    {
        return 'http://localhost:8081';
    }
}
