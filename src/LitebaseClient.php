<?php

namespace Litebase;

use Exception;
use Throwable;

class LitebaseClient
{
    /**
     * Error info received from a request.
     *
     * @var array
     */
    protected array $errorInfo = [];

    /**
     * The accesss key id of the client.
     */
    protected string $key;

    /**
     * The id of the last instered record.
     */
    protected ?string $lastInsertId = null;

    /**
     * The accesss key secret of the client.
     */
    protected string $secret;

    /**
     * The transport used to communicate with the Litebase API.
     */
    protected TransportInterface $transport;

    /**
     * The active transaction of the client.
     */
    protected null|Transaction $transaction = null;

    /**
     * Create a new instance of the client.
     */
    public function __construct(
        protected Configuration $configuration,
    ) {}

    // /**
    //  * Ensure the require attributes to create a client connection are provided
    //  * before creating a new instance.
    //  */
    // protected function ensureRequiredAttributesAreProvided(array $attributes)
    // {
    //     if (!isset($attributes['access_key_id'])) {
    //         throw new Exception('The Litebase database connection cannot be created without a valid access key id.');
    //     }

    //     if (!isset($attributes['access_key_secret'])) {
    //         throw new Exception('The Litebase database connection cannot be created without a valid secret access key.');
    //     }

    //     if (!isset($attributes['url'])) {
    //         throw new Exception('The Litebase database connection cannot be created without a valid url.');
    //     }
    // }

    /**
     * Begin a transaction.
     */
    public function beginTransaction(): bool
    {
        // Only allow one transaction to occur at a time.
        if ($this->transaction) {
            return false;
        }

        try {
            $response = $this->transport->send(
                new Query(
                    id: uniqid(),
                    statement: 'BEGIN',
                )
            );

            if ($response['error'] ?? false) {
                $this->errorInfo = [0, 0, $response['error']];

                return false;
            }

            if (!isset($response['data']['transaction_id'])) {
                $this->errorInfo = [0, 0, 'Transaction ID not found'];

                return false;
            }

            $this->transaction = new Transaction($response['data']['transaction_id']);

            return true;
        } catch (Exception $e) {
            $this->errorInfo = [0, 0, $e->getMessage()];

            return false;
        }
    }

    /**
     * Commit a transaction.
     */
    public function commit()
    {
        if (!$this->transaction) {
            return false;
        }

        try {
            $this->transport->send(
                new Query(
                    id: uniqid(),
                    transactionId: $this->transaction->id,
                    statement: 'COMMIT',
                )
            );

            $this->transaction = null;

            return true;
        } catch (Throwable $th) {
            throw $th;
            return false;
        }
    }

    public function errorCode(): null | string
    {
        return $this->errorInfo()[0];
    }

    public function errorInfo(): array
    {
        return $this->errorInfo;
    }

    /**
     * Exectute a statement on the database.
     */
    public function exec(array $input = []): array
    {
        // Set a unique id for the request.
        $input['id'] = uniqid();

        if ($this->transaction) {
            $input['transaction_id'] = $this->transaction->id;
        }

        $result = $this->transport->send(new Query(
            id: $input['id'],
            transactionId: $input['transaction_id'] ?? null,
            statement: $input['statement'],
            parameters: $input['parameters'] ?? [],
        ));

        // $result is a CreateQuery200Response object
        // getData() returns an array of CreateQuery200ResponseDataInner objects
        $dataItems = $result->getData();

        // Get the first query result (since we're executing a single query)
        $firstResult = $dataItems[0] ?? null;

        if (!$firstResult) {
            return [];
        }

        // Store the last insert ID if available
        $lastInsertRowId = $firstResult->getLastInsertRowId();

        if ($lastInsertRowId !== null) {
            $this->lastInsertId = (string) $lastInsertRowId;
        }

        return [
            'changes' => $firstResult->getChanges(),
            'columns' => $firstResult->getColumns(),
            'id' => $firstResult->getId(),
            'last_insert_row_id' => $firstResult->getLastInsertRowId(),
            'latency' => $firstResult->getLatency(),
            'row_count' => $firstResult->getRowCount(),
            'rows' => $firstResult->getRows(),
            'transaction_id' => $firstResult->getTransactionId(),
        ];
    }

    /**
     * Check if the client has a transaction in progress.
     */
    public function inTransaction(): bool
    {
        return $this->transaction !== null;
    }

    public function lastInsertId(): null|string
    {
        return $this->lastInsertId;
    }

    public function prepare($statement): LitebaseStatement
    {
        return new LitebaseStatement($this, $statement);
    }

    /**
     * Rollback a transaction.
     */
    public function rollback()
    {
        if (!$this->transaction) {
            return false;
        }

        $this->transport->send(
            new Query(
                id: uniqid(),
                transactionId: $this->transaction->id,
                statement: 'ROLLBACK',
            )
        );

        $this->transaction = null;

        return true;
    }

    public function withTransport(string $transportType): LitebaseClient
    {
        switch ($transportType) {
            case 'http':
                $this->transport = new HttpTransport($this->configuration);
                break;
            case 'http_streaming':
                $this->transport = new HttpStreamingTransport($this->configuration);
                break;
            default:
                throw new Exception('Invalid transport type: ' . $transportType);
        }

        return $this;
    }

    public function withAccessKey(string $accessKeyID, string $accessKeySecret): LitebaseClient
    {
        $this->configuration->setAccessKey($accessKeyID, $accessKeySecret);

        return $this;
    }

    public function withBasicAuth(string $username, string $password): LitebaseClient
    {
        $this->configuration->setUsername($username);
        $this->configuration->setPassword($password);

        return $this;
    }
}
