<?php

namespace Litebase;

use Exception;
use GuzzleHttp\Client;
use Litebase\OpenAPI\Model\StatementParameter;
use Throwable;

class LitebaseClient
{
    /**
     * Error info received from a request.
     *
     * @var array<int, int|string|null>
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
    protected ?Transaction $transaction = null;

    /**
     * Create a new instance of the client.
     */
    public function __construct(
        protected Configuration $configuration,
    ) {}

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

            if (! empty($response->errorMessage)) {
                $this->errorInfo = [0, 0, $response->errorMessage];

                return false;
            }

            if (empty($response->transactionID)) {
                $this->errorInfo = [0, 0, 'Transaction ID not found'];

                return false;
            }

            $this->transaction = new Transaction($response->transactionID);

            return true;
        } catch (Exception $e) {
            $this->errorInfo = [0, 0, $e->getMessage()];

            return false;
        }
    }

    /**
     * Commit a transaction.
     */
    public function commit(): bool
    {
        if (! $this->transaction) {
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
        }
    }

    public function errorCode(): ?string
    {
        return (string) $this->errorInfo()[0];
    }

    /**
     * Return the error info.
     *
     * @return array<int, int|string|null>
     */
    public function errorInfo(): array
    {
        return $this->errorInfo;
    }

    /**
     * Exectute a statement on the database.
     *
     * @param array{
     *      statement: string,
     *      parameters?: array<array{type: string, value: int|float|string|null}>,
     *      transaction_id?: string,
     * } $input
     */
    public function exec(array $input): ?QueryResult
    {
        // Set a unique id for the request.
        $input['id'] = uniqid();

        if ($this->transaction) {
            $input['transaction_id'] = $this->transaction->id;
        }

        /** @var array<int, StatementParameter> $parameters */
        $parameters = [];

        foreach ($input['parameters'] ?? [] as $param) {
            $parameters[] = new StatementParameter($param);
        }

        $result = $this->transport->send(new Query(
            id: $input['id'],
            transactionId: $input['transaction_id'] ?? null,
            statement: $input['statement'],
            parameters: $parameters,
        ));

        // Store the last insert ID if available
        if (isset($result->lastInsertRowID)) {
            $this->lastInsertId = (string) $result->lastInsertRowID;
        }

        return $result;
    }

    /**
     * Check if the client has a transaction in progress.
     */
    public function inTransaction(): bool
    {
        return $this->transaction !== null;
    }

    public function lastInsertId(): ?string
    {
        return $this->lastInsertId;
    }

    public function prepare(string $statement): LitebaseStatement
    {
        return new LitebaseStatement($this, $statement);
    }

    /**
     * Rollback a transaction.
     */
    public function rollback(): bool
    {
        if (! $this->transaction) {
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

    public function withHttpTransport(?Client $httpClient): LitebaseClient
    {
        $transport = new HttpTransport($this->configuration, $httpClient);

        $this->transport = $transport;

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
