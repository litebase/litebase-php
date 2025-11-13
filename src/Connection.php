<?php

namespace Litebase;

use Exception;
use Fiber;
use Throwable;

class Connection
{
    /** @var array<int, string> */
    protected $headers = [
        'Content-Type: application/octet-stream',
        'Transfer-Encoding: chunked',
        'Connection: close',
    ];

    protected ?string $host;

    protected string $buffer = '';

    /** @var array<int, string> */
    protected array $messages = [];

    protected bool $open = false;

    protected string $path;

    protected int $port;

    protected QueryRequestEncoder $queryRequestEncoder;

    /** @var \Fiber<never, void, \Throwable|null, \Throwable|null>|null */
    protected ?Fiber $reader;

    /** @var array<int, array<string, mixed>> */
    protected array $responses = [];

    /** @var resource|false */
    protected $socket;

    /** @var \Fiber<never, void, \Throwable|null, \Throwable|null>|null */
    protected ?Fiber $writer;

    protected ?ChunkedSignatureSigner $chunkedSigner;

    /**
     * Create a new connection instance.
     *
     * @param  array<string, int|string>  $requestHeaders
     */
    public function __construct(public string $url, public array $requestHeaders = [], ?ChunkedSignatureSigner $chunkedSigner = null)
    {
        $host = parse_url($this->url, PHP_URL_HOST);

        if ($host === false || $host === null) {
            throw new Exception('[Litebase Client Error]: Invalid URL provided');
        }

        $this->host = $host;

        $this->port = parse_url($this->url, PHP_URL_PORT) ?: 80;

        $path = parse_url($this->url, PHP_URL_PATH);

        if ($path === false || $path === null) {
            throw new Exception('[Litebase Client Error]: Invalid URL provided');
        }

        $this->path = $path;

        foreach ($requestHeaders as $key => $value) {
            $this->headers[] = "$key: $value";
        }

        $this->queryRequestEncoder = new QueryRequestEncoder;
        $this->chunkedSigner = $chunkedSigner;
    }

    /**
     * Close the connection.
     */
    public function close(): void
    {
        $this->open = false;

        if (is_resource($this->socket) && ! feof($this->socket)) {
            fwrite($this->socket, "0\r\n\r\n");
        }

        if (is_resource($this->socket)) {
            fclose($this->socket);
        }

        $this->socket = false;
        $this->messages = [];
        $this->responses = [];
        $this->reader = null;
        $this->writer = null;
    }

    /**
     * Create the reader and writer fibers for handling the connection.
     */
    protected function createThreads(): void
    {
        $this->writer = new Fiber(function () {
            while ($this->open) {
                $message = array_shift($this->messages);

                if ($message) {
                    // Check for broken connection before writing
                    if ($this->isBrokenConnection()) {
                        Fiber::suspend(new Exception('[Litebase Client Error]: Connection broken - server disconnected'));
                    }

                    $this->writeMessage($message);
                }

                Fiber::suspend();
            }

            $this->close();
        });

        $this->reader = new Fiber(function () {
            $responseHeaders = '';

            while ($this->isOpen()) {
                // Check for broken connection before reading
                if ($this->isBrokenConnection()) {
                    Fiber::suspend(new Exception('[Litebase Client Error]: Connection broken - server disconnected'));
                }

                // Check if we have buffered data first
                if (! empty($this->buffer)) {
                    $line = $this->buffer;
                    $this->buffer = '';
                } else {
                    $line = $this->socket ? fgets($this->socket) : false;
                }

                if ($line === false) {
                    // Check if connection was broken
                    if ($this->isBrokenConnection()) {
                        Fiber::suspend(new Exception('[Litebase Client Error]: Connection broken - server disconnected'));
                    } else {
                        Fiber::suspend(new Exception('[Litebase Client Error]: Unable to read request headers from socket'));
                    }
                }

                if ($line === "\r\n") {
                    // Headers are done, start receiving the body
                    break;
                }

                $responseHeaders .= $line;
            }

            $statusCode = explode(' ', explode("\r\n", $responseHeaders)[0])[1];
            $reasonPhrase = explode(' ', explode("\r\n", $responseHeaders)[0], 3)[2];

            if ($statusCode >= 400) {
                throw new Exception("[Litebase Client Error]: $reasonPhrase");
            }

            while ($this->isOpen()) {
                // Check for broken connection before reading chunks
                if ($this->isBrokenConnection()) {
                    Fiber::suspend(new Exception('[Litebase Client Error]: Connection broken - server disconnected'));
                }

                // Read the chunk size
                $chunkSizeHex = '';

                while (true) {
                    $byte = $this->socket ? fread($this->socket, 1) : false;

                    if ($byte === false) {
                        // Check if connection was broken
                        if ($this->isBrokenConnection()) {
                            Fiber::suspend(new Exception('[Litebase Client Error]: Connection broken - server disconnected'));
                        } else {
                            Fiber::suspend(new Exception('[Litebase Client Error]: Unable to read from socket'));
                        }
                    }

                    if ($byte === "\r") {
                        // Expecting "\n" after "\r"
                        $nextByte = $this->socket ? fread($this->socket, 1) : false;

                        if ($nextByte === "\n") {
                            break;
                        } else {
                            Fiber::suspend(new Exception('[Litebase Client Error]: Invalid chunk size format'));
                        }
                    }

                    $chunkSizeHex .= $byte;
                }

                // Convert the chunk size from hex to decimal
                $chunkSize = hexdec(trim($chunkSizeHex));

                // If chunk size is 0, this indicates end of chunked transfer
                if ($chunkSize === 0) {
                    // Read the final "\r\n"
                    if ($this->socket) {
                        fread($this->socket, 2);
                    }

                    break;
                }

                // Read the chunk data
                $buffer = [];
                $bytesRead = 0;

                while ($bytesRead < $chunkSize) {
                    $remainingBytes = $chunkSize - $bytesRead;
                    $readLength = max(1, (int) $remainingBytes);

                    $data = $this->socket && $remainingBytes > 0 ?
                        fread($this->socket, $readLength) :
                        false;

                    if ($data === false) {
                        // Check if connection was broken
                        if ($this->isBrokenConnection()) {
                            Fiber::suspend(new Exception('[Litebase Client Error]: Connection broken - server disconnected'));
                        } else {
                            Fiber::suspend(new Exception('[Litebase Client Error]: Unable to read from socket'));
                        }
                    }

                    if ($data !== false) {
                        $buffer[] = $data;
                        $bytesRead += strlen($data);
                    }
                }

                $messageBytes = implode('', $buffer);

                // Read the trailing "\r\n" after the chunk data
                if ($this->socket) {
                    fread($this->socket, 2);
                }

                // Begin reading the frame
                $messageTypeByte = substr($messageBytes, 0, 1);
                $messageType = unpack('C', $messageTypeByte)[1] ?? 0;

                $lengthBytes = substr($messageBytes, 1, 4);
                $lengthUnpacked = unpack('V', $lengthBytes)[1] ?? 0;
                $length = is_int($lengthUnpacked) ? $lengthUnpacked : 0;
                $frameBytes = substr($messageBytes, 5, $length);

                $messageTypeValue = is_int($messageType) ? $messageType : 0;
                $response = (new QueryResponseDecoder(QueryStreamMessageType::from($messageTypeValue), $frameBytes))->decode();

                if ($messageType === QueryStreamMessageType::FRAME->value) {
                    foreach ($response as $item) {
                        $this->responses[] = $item;
                    }
                }

                Fiber::suspend();
            }

            $this->close();
        });

        $this->writer?->start();
        $this->reader->start();
    }

    /**
     * Check if debug mode is enabled via environment variable
     */
    protected function debug(): bool
    {
        return isset($_ENV['LITEBASE_DEBUG']) ? filter_var($_ENV['LITEBASE_DEBUG'], FILTER_VALIDATE_BOOLEAN) === true : false;
    }

    /**
     * Check if the connection is broken by detecting disconnected socket
     */
    protected function isBrokenConnection(): bool
    {
        if (! is_resource($this->socket)) {
            return true;
        }

        // Check if socket is at EOF
        if (feof($this->socket)) {
            return true;
        }

        // Get socket status
        $status = stream_get_meta_data($this->socket);

        // Check if the socket has been closed or has errors
        if ($status['eof'] || $status['timed_out']) {
            return true;
        }

        return false;
    }

    /**
     * Check if the connection is open.
     */
    public function isOpen(): bool
    {
        if (! $this->open || ! is_resource($this->socket)) {
            return false;
        }

        // Check for broken connection
        if ($this->isBrokenConnection()) {
            $this->close();

            return false;
        }

        return true;
    }

    /**
     * Open the connection.
     */
    public function open(): void
    {
        $context = stream_context_create([
            'socket' => [
                'tcp_nodelay' => true,
            ],
            'ssl' => [
                'verify_peer' => $this->debug() ? false : true,
                'verify_peer_name' => $this->debug() ? false : true,
            ],
        ]);

        try {
            $this->socket = stream_socket_client(
                str_starts_with($this->url, 'https://') ? "tls://{$this->host}:{$this->port}" : "{$this->host}:{$this->port}",
                $errno,
                $errstr,
                5,
                STREAM_CLIENT_CONNECT,
                $context
            );
        } catch (Exception $e) {
            throw new Exception('[Litebase Client Error]: Unable to connect to server');
        }

        if (! $this->socket) {
            throw new Exception('[Litebase Client Error]: Unable to connect to server');
        }

        stream_set_timeout($this->socket, 5);

        $error = fwrite($this->socket, "POST {$this->path} HTTP/1.1\r\n");
        $error = fwrite($this->socket, implode("\r\n", $this->headers) . "\r\n");
        $error = fwrite($this->socket, "\r\n");

        if ($error === false) {
            throw new Exception('[Litebase Client Error]: Unable to write to socket');
        }

        $this->open = true;

        $this->messages = [
            pack('C', QueryStreamMessageType::OPEN_CONNECTION->value . pack('V', 0)),
            ...$this->messages,
        ];

        $this->createThreads();
    }

    /**
     * Send a request to the data api.
     */
    public function send(Query $query): QueryResult
    {
        $queryRequest = $this->queryRequestEncoder->encode($query);

        // If chunked signer is available, create a signed frame per LQTP protocol
        if ($this->chunkedSigner !== null) {
            // Frame data: [QueryLength:4][QueryData]
            $frameData = pack('V', strlen($queryRequest)) . $queryRequest;

            // Sign the frame data using chunked signature scheme (similar to AWS Sig4)
            $chunkSignature = $this->chunkedSigner->signChunk($frameData);

            // Build complete frame with signature per LQTP protocol
            // Frame format: [MessageType:1][FrameLength:4][SignatureLength:4][Signature:N][FrameData]
            $signatureBytes = $chunkSignature;
            $totalLength = 4 + strlen($signatureBytes) + strlen($frameData);

            $frame = pack('C', QueryStreamMessageType::FRAME->value) // Message type (0x04)
                . pack('V', $totalLength)                            // Total length (signature metadata + frame data)
                . pack('V', strlen($signatureBytes))                 // Signature length
                . $signatureBytes                                     // Hex-encoded chunk signature
                . $frameData;                                         // Frame data
        } else {
            // Fallback to unsigned frame format (deprecated)
            $frame = pack('C', QueryStreamMessageType::FRAME->value) . pack('V', strlen($queryRequest)) . $queryRequest;
        }

        $this->messages[] = $frame;

        if (! $this->isOpen()) {
            $this->open();
        }

        $tries = 0;
        $maxTries = 3;

        while (empty($this->responses)) {
            try {
                $this->tick();
            } catch (Throwable $e) {
                // If connection is broken, try to reconnect
                if (str_contains($e->getMessage(), 'Connection broken') || str_contains($e->getMessage(), 'server disconnected')) {
                    $this->close();

                    if ($tries < $maxTries) {
                        // Re-add the message to the queue for retry
                        array_unshift($this->messages, $frame);

                        try {
                            $this->open();
                            $tries++;

                            continue;
                        } catch (Exception $reconnectException) {
                            throw new Exception('[Litebase Client Error]: Failed to reconnect after connection loss: ' . $reconnectException->getMessage());
                        }
                    }
                }

                throw $e;
            }

            if (! $this->isOpen()) {
                if ($tries < $maxTries) {
                    // Re-add the message to the queue for retry
                    array_unshift($this->messages, $frame);
                    $this->open();
                } else {
                    throw new Exception("[Litebase Client Error]: Connection lost and failed to reconnect after $maxTries attempts");
                }
            }

            if ($tries > $maxTries) {
                throw new Exception("[Litebase Client Error]: Request connection timeout after $maxTries attempts");
            }

            $tries++;
        }

        $response = array_shift($this->responses);

        if (! is_array($response)) {
            return new QueryResult(
                errorMessage: 'no response found',
            );
        }

        if ($response['close'] ?? false) {
            $this->close();

            return new QueryResult(
                errorMessage: 'connection closed',
            );
        }

        if (isset($response['error'])) {
            $errorValue = $response['error'];
            $errorMessage = is_scalar($errorValue) ? (string) $errorValue : 'Unknown error';

            return new QueryResult(
                errorMessage: $errorMessage,
            );
        }

        if (isset($response['id']) && $response['id'] === $query->id) {
            /** @var array<int, array{type: ColumnType, name: string}> $columns */
            $columns = [];

            if (isset($response['columns'])) {
                $rawColumns = $response['columns'];
                if (is_array($rawColumns)) {
                    foreach ($rawColumns as $col) {
                        if (is_array($col) && isset($col['type'], $col['name'])) {
                            $type = $col['type'] instanceof ColumnType ? $col['type'] : ColumnType::TEXT;
                            $name = is_string($col['name']) ? $col['name'] : '';
                            $columns[] = [
                                'type' => $type,
                                'name' => $name,
                            ];
                        }
                    }
                }
            }

            $id = (string) $response['id'];

            $changesValue = $response['changes'] ?? 0;
            $changes = is_int($changesValue) || is_float($changesValue) || is_string($changesValue) ? (int) $changesValue : 0;

            $lastInsertRowIdValue = $response['lastInsertRowId'] ?? 0;
            $lastInsertRowId = is_int($lastInsertRowIdValue) || is_float($lastInsertRowIdValue) || is_string($lastInsertRowIdValue) ? (int) $lastInsertRowIdValue : 0;

            $latencyValue = $response['latency'] ?? 0;
            $latency = is_int($latencyValue) || is_float($latencyValue) || is_string($latencyValue) ? (float) $latencyValue : 0.0;

            $rowCountValue = $response['rowCount'] ?? 0;
            $rowCount = is_int($rowCountValue) || is_float($rowCountValue) || is_string($rowCountValue) ? (int) $rowCountValue : 0;

            /** @var array<int, array<int, bool|float|int|string|null>> $rows */
            $rows = isset($response['rows']) && is_array($response['rows']) ? $response['rows'] : [];

            $transactionIDValue = $response['transactionID'] ?? '';
            $transactionID = is_scalar($transactionIDValue) ? (string) $transactionIDValue : '';

            $errorMessageValue = $response['errorMessage'] ?? null;
            $errorMessage = $errorMessageValue !== null && is_scalar($errorMessageValue) ? (string) $errorMessageValue : null;

            return new QueryResult(
                id: $id,
                changes: $changes,
                columns: $columns,
                lastInsertRowId: $lastInsertRowId,
                latency: $latency,
                rowCount: $rowCount,
                rows: $rows,
                transactionId: $transactionID,
                errorMessage: $errorMessage,
            );
        }

        return new QueryResult(
            errorMessage: 'no response found',
        );
    }

    /**
     * Process the connection by resuming the reader and writer fibers.
     */
    protected function tick(): void
    {
        // Check if connection is broken before processing
        if ($this->isBrokenConnection()) {
            $this->close();
            throw new Exception('[Litebase Client Error]: Connection broken - server disconnected');
        }

        if (! $this->reader?->isSuspended() || ! $this->writer?->isSuspended()) {
            $this->close();

            return;
        }

        // Important: resume the writer first
        $return = $this->writer->resume();

        if ($return instanceof Throwable) {
            throw $return;
        }

        $return = $this->reader->resume();

        if ($return instanceof Throwable) {
            throw $return;
        }
    }

    /**
     * Write a message to the socket with proper chunked encoding.
     */
    protected function writeMessage(string $message): void
    {
        // Check for broken connection before writing
        if ($this->isBrokenConnection()) {
            Fiber::suspend(new Exception('[Litebase Client Error]: Connection broken - server disconnected'));
        }

        $chunkSize = dechex(strlen($message));

        $n = $this->socket ?
            fwrite($this->socket, $chunkSize . "\r\n" . $message . "\r\n") :
            false;

        if ($n === false) {
            // Check if the failure was due to broken connection
            if ($this->isBrokenConnection()) {
                Fiber::suspend(new Exception('[Litebase Client Error]: Connection broken - server disconnected'));
            } else {
                Fiber::suspend(new Exception('[Litebase Client Error]: Unable to write to socket'));
            }
        }

        $flushed = $this->socket ? fflush($this->socket) : false;

        if ($flushed === false) {
            // Check if the failure was due to broken connection
            if ($this->isBrokenConnection()) {
                Fiber::suspend(new Exception('[Litebase Client Error]: Connection broken - server disconnected'));
            } else {
                Fiber::suspend(new Exception('[Litebase Client Error]: Unable to flush data to socket'));
            }
        }
    }
}
