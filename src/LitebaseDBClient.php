<?php

namespace LitebaseDB;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use LitebaseDB\Exceptions\LitebaseConnectionException;

class LitebaseDBClient
{
    /**
     * The Http client.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The database identifier of the client.
     *
     * @var string
     */
    protected $database;

    /**
     * Error info received from a request.
     *
     * @var string
     */
    protected $errorInfo;

    /**
     * The accesss key id of the client.
     *
     * @var string
     */
    protected $key;

    /**
     * The id of the last instered record.
     *
     * @var null|string
     */
    protected $lastInsertId = null;

    /**
     * The region of the database.
     *
     * @var string
     */
    protected $region;

    /**
     * The accesss key secret of the client.
     *
     * @var string
     */
    protected $secret;

    /**
     * An active transaction id.
     *
     * @var null|string
     */
    protected $transactionId = null;

    /**
     * Create a new instance of the client.
     */
    public function __construct(array $attributes, array $clientConfig = [])
    {
        $this->ensureRequiredAttributesAreProvided($attributes);

        $this->key = $attributes['access_key_id'];
        $this->secret = $attributes['access_key_secret'];
        $this->url = "{$attributes['database']}.{$attributes['host']}";

        $this->client = new Client(array_merge([
            'base_uri' => "http://{$this->url}",
            'http_errors' => false,
            'timeout'  => 30,
            'headers' => $this->defaultHeaders($attributes),

        ], $clientConfig));
    }

    protected function defaultHeaders(array $attributes): array
    {
        $headers = [];

        foreach ($attributes as $key => $value) {
            if (str_starts_with($key, '_x_')) {
                $header = strtoupper(str_replace('_x_', 'x-', $key));
                $headers[$header] = $value;
            }
        }


        return $headers + [
            'Keep-Alive' => 'true',
        ];
    }

    /**
     * Ensure the require attributes to create a client connection are provided
     * before creating a new instance.
     */
    protected function ensureRequiredAttributesAreProvided(array $attributes)
    {
        if (!isset($attributes['access_key_id'])) {
            throw new Exception('The LitebaseDB database connection cannot be created without a valid access key id.');
        }

        if (!isset($attributes['access_key_secret'])) {
            throw new Exception('The LitebaseDB database connection cannot be created without a valid secret access key.');
        }

        if (!isset($attributes['database'])) {
            throw new Exception('The LitebaseDB database connection cannot be created without a valid database.');
        }

        if (!isset($attributes['host'])) {
            throw new Exception('The LitebaseDB database connection cannot be created without a valid host.');
        }
    }

    /**
     *
     */
    public function beginTransaction(): bool
    {
        // Only allow one transaction to occur at a time.
        if ($this->transactionId) {
            return false;
        }

        try {
            $response = $this->send('POST', 'transaction',  [
                'database' => $this->database,
            ]);

            $this->transactionId = $response['data']['rows'][0]['id'] ?? null;
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     *
     */
    public function commit()
    {
        if (!$this->transactionId) {
            return false;
        }

        $this->send('PUT', 'transaction',  [
            'database' => $this->database,
            'transaction' => $this->transactionId,
        ]);

        $this->transactionId = null;

        return true;
    }

    protected function decrypt(string $value)
    {
        $payload = json_decode(base64_decode($value), true);
        $iv = base64_decode($payload['iv']);
        $tag = empty($payload['tag']) ? null : base64_decode($payload['tag']);

        $decrypted = \openssl_decrypt(
            $payload['value'],
            'aes-256-gcm',
            md5($this->secret),
            0,
            $iv,
            $tag ?? ''
        );

        if ($decrypted === false) {
            throw new Exception('Could not decrypt the data.');
        }

        return $decrypted;
    }

    public function errorCode()
    {
        return $this->errorInfo()[0];
    }

    public function errorInfo()
    {
        return $this->errorInfo;
    }

    /**
     * Exectute a statement on the database.
     */
    public function exec(array $input = [])
    {
        $id = uniqid(time());
        $input = array_merge(['id' => $id], $input);
        $result = $this->send('POST', 'query', $input);

        if (isset($result['data']['insertId'])) {
            $this->lastInsertId = $result['data']['insertId'];
        }

        return $result;
    }

    protected function encrypt(mixed $value, string $secret = null)
    {
        $iv = random_bytes(openssl_cipher_iv_length(strtolower('aes-256-gcm')));

        $value = openssl_encrypt(
            json_encode($value),
            'aes-256-gcm',
            md5($secret ?? $this->secret),
            0,
            $iv,
            $tag
        );

        return base64_encode(
            json_encode([
                'iv' => base64_encode($iv),
                'value' => $value,
                'mac' => '',
                'tag' => base64_encode($tag ?? ''),
            ], JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Return the guzzle http client.
     */
    public function getGuzzleClient(): Client
    {
        return $this->client;
    }

    /**
     * Get an authorization token for a request.
     */
    public function getToken(
        string $method,
        string $path,
        array $headers,
        array $data,
        array $queryParams = [],
    ) {
        return RequestSigner::handle(
            accessKeyID: $this->key,
            accessKeySecret: $this->secret,
            method: $method,
            path: $path,
            headers: $headers,
            data: $data,
            queryParams: $queryParams,
        );
    }


    /**
     * Check if the client has a transaction in progress.
     */
    public function inTransaction()
    {
        return (bool) $this->transactionId;
    }

    public function lastInsertId()
    {
        return $this->lastInsertId;
    }

    /**
     * Rollbacka transaction.
     */
    public function rollback()
    {
        if (!$this->transactionId) {
            return false;
        }
        // TODO: transform to query
        $this->send('DELETE', 'transaction',  [
            'transaction' => $this->transactionId,
        ]);

        $this->transactionId = null;

        return true;
    }

    /**
     * Send a request to the data api.
     */
    public function send(string $method, string $path, $data = [])
    {
        $data['statement'] = $this->encrypt($data['statement'], $this->key);
        $data['parameters'] = $this->encrypt($data['parameters']);
        $date = date('U');

        $headers = [
            'Content-Type' => 'application/json',
            'Content-Length' => strlen(json_encode($data)),
            'Host' => $this->url,
            'X-LBDB-Date' => $date,
        ];

        $token = $this->getToken(
            method: $method,
            path: $path,
            headers: $headers,
            data: $data
        );

        try {
            $response = $this->client->request($method, $path, [
                'json' => $data,
                'headers' => $headers + [
                    'Authorization' => $token,
                ],
            ]);

            $result = json_decode((string) $response->getBody(), true);

            if (isset($result['status']) && $result['status'] === 'error') {
                $this->errorInfo = [
                    $result['code'] ?? 0,
                    $response->getStatusCode(),
                    $result['message'] ?? 'Unknown error',
                ];
            } else {
                $result['data'] = json_decode($this->decrypt($result['data']), true);
            }

            return $result;
        } catch (Exception $e) {
            if ($e instanceof ConnectException) {
                throw new LitebaseConnectionException($e->getMessage());
            }

            $this->errorInfo = [
                0,
                0,
                $e->getMessage()
            ];

            return [];
        }
    }

    /**
     * Set a request to the data api.
     */
    public function sendAsync(string $method, string $path, $data = [])
    {
        try {
            $this->client->requestAsync($method, $path, ['json' => $data]);
            return true;
        } catch (Exception $e) {
            $this->errorInfo = [
                0,
                0,
                $e->getMessage()
            ];

            throw $e;
        }
    }
}
