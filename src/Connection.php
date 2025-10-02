<?php

namespace Litebase;

use Exception;
use Fiber;
use Litebase\QueryResponseDecoder;
use Throwable;

class Connection
{
    protected $headers = [
        'Content-Type: application/octet-stream',
        'Transfer-Encoding: chunked',
        'Connection: close',
    ];

    protected string $host;
    protected string $buffer = '';
    protected array $messages = [];
    protected $open = false;
    protected string $path;
    protected int $port;
    protected QueryRequestEncoder $queryRequestEncoder;
    protected ?Fiber $reader;
    protected array $responses = [];
    protected mixed $socket;
    protected ?Fiber $writer;

    /**
     * Create a new connection instance.
     */
    public function __construct(public $url, public $requestHeaders = [])
    {
        $this->host = parse_url($url, PHP_URL_HOST);
        $this->port = parse_url($url, PHP_URL_PORT) ?: 80;
        $this->path = parse_url($url, PHP_URL_PATH);

        foreach ($requestHeaders as $key => $value) {
            $this->headers[] = "$key: $value";
        }

        $this->queryRequestEncoder = new QueryRequestEncoder();
    }

    /**
     * Close the connection.
     */
    public function close()
    {
        $this->open = false;

        if (is_resource($this->socket) && !feof($this->socket)) {
            fwrite($this->socket, "0\r\n\r\n");
        }

        if (is_resource($this->socket)) {
            fclose($this->socket);
        }

        $this->socket = null;
        $this->messages = [];
        $this->responses = [];
        $this->reader = null;
        $this->writer = null;
    }

    /**
     * Create the reader and writer fibers for handling the connection.
     */
    protected function createThreads()
    {
        $this->writer = new Fiber(function () {
            while (true) {
                $message = array_shift($this->messages);

                if ($message) {
                    // Check for broken connection before writing
                    if ($this->isBrokenConnection()) {
                        Fiber::suspend(new Exception("[Litebase Client Error]: Connection broken - server disconnected"));
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
                    Fiber::suspend(new Exception("[Litebase Client Error]: Connection broken - server disconnected"));
                }

                // Check if we have buffered data first
                if (!empty($this->buffer)) {
                    $line = $this->buffer;
                    $this->buffer = '';
                } else {
                    $line = fgets($this->socket);
                }

                if ($line === false) {
                    // Check if connection was broken
                    if ($this->isBrokenConnection()) {
                        Fiber::suspend(new Exception("[Litebase Client Error]: Connection broken - server disconnected"));
                    } else {
                        Fiber::suspend(new Exception("[Litebase Client Error]: Unable to read request headers from socket"));
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
                    Fiber::suspend(new Exception("[Litebase Client Error]: Connection broken - server disconnected"));
                }

                // Read the chunk size
                $chunkSizeHex = '';

                while (true) {
                    $byte = fread($this->socket, 1);

                    if ($byte === false) {
                        // Check if connection was broken
                        if ($this->isBrokenConnection()) {
                            Fiber::suspend(new Exception("[Litebase Client Error]: Connection broken - server disconnected"));
                        } else {
                            Fiber::suspend(new Exception("[Litebase Client Error]: Unable to read from socket"));
                        }
                    }

                    if ($byte === "\r") {
                        // Expecting "\n" after "\r"
                        $nextByte = fread($this->socket, 1);
                        if ($nextByte === "\n") {
                            break;
                        } else {
                            Fiber::suspend(new Exception("[Litebase Client Error]: Invalid chunk size format"));
                        }
                    }

                    $chunkSizeHex .= $byte;
                }

                // Convert the chunk size from hex to decimal
                $chunkSize = hexdec(trim($chunkSizeHex));

                // If chunk size is 0, this indicates end of chunked transfer
                if ($chunkSize === 0) {
                    // Read the final "\r\n"
                    fread($this->socket, 2);
                    break;
                }

                // Read the chunk data
                $buffer = [];
                $bytesRead = 0;

                while ($bytesRead < $chunkSize) {
                    $remainingBytes = $chunkSize - $bytesRead;
                    $data = fread($this->socket, $remainingBytes);

                    if ($data === false) {
                        // Check if connection was broken
                        if ($this->isBrokenConnection()) {
                            Fiber::suspend(new Exception("[Litebase Client Error]: Connection broken - server disconnected"));
                        } else {
                            Fiber::suspend(new Exception("[Litebase Client Error]: Unable to read from socket"));
                        }
                    }

                    $buffer[] = $data;
                    $bytesRead += strlen($data);
                }

                $messageBytes = implode('', $buffer);

                // Read the trailing "\r\n" after the chunk data
                fread($this->socket, 2);

                // Begin reading the frame
                $messageTypeByte = substr($messageBytes, 0, 1);
                $messageType = unpack('C',  $messageTypeByte)[1];

                $lengthBytes = substr($messageBytes, 1, 4);
                $length = unpack('V', $lengthBytes)[1];
                $frameBytes = substr($messageBytes, 5, $length);

                $response = (new QueryResponseDecoder(QueryStreamMessageType::from($messageType), $frameBytes))->decode();

                if ($messageType === QueryStreamMessageType::FRAME->value) {
                    foreach ($response as $item) {
                        $this->responses[] = $item;
                    }
                }

                Fiber::suspend();
            }

            $this->close();
        });

        $this->writer->start();
        $this->reader->start();
    }

    /**
     * Check if debug mode is enabled via environment variable
     */
    protected function debug(): bool
    {
        return isset($_ENV['LITEBASE_DEBUG']) ? filter_var($_ENV['LITEBASE_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN) === true : false;
    }

    /**
     * Check if the connection is broken by detecting disconnected socket
     */
    protected function isBrokenConnection(): bool
    {
        if (!is_resource($this->socket)) {
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
        if (!$this->open || !is_resource($this->socket)) {
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
    public function open()
    {
        $context = stream_context_create([
            'socket' => [
                'tcp_nodelay' => true,
            ],
            'ssl' => [
                'verify_peer' =>  $this->debug() ? false : true,
                'verify_peer_name' => $this->debug() ? false : true,
            ],
        ]);

        try {
            // TODO: Need to detect a broken connection
            $this->socket = stream_socket_client(
                str_starts_with($this->url, "https://") ? "tls://{$this->host}:{$this->port}" : "{$this->host}:{$this->port}",
                $errno,
                $errstr,
                5,
                STREAM_CLIENT_CONNECT,
                $context
            );
        } catch (Exception $e) {
            throw new Exception("[Litebase Client Error]: Unable to connect to server");
        }

        if (!$this->socket) {
            throw new Exception("[Litebase Client Error]: Unable to connect to server");
        }

        stream_set_timeout($this->socket, 5);

        $error = fwrite($this->socket, "POST {$this->path} HTTP/1.1\r\n");
        $error = fwrite($this->socket, implode("\r\n", $this->headers) . "\r\n");
        $error = fwrite($this->socket, "\r\n");

        if ($error === false) {
            throw new Exception("[Litebase Client Error]: Unable to write to socket");
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
    public function send(Query $query)
    {
        $queryRequest = $this->queryRequestEncoder->encode($query);

        $frame = pack('C', QueryStreamMessageType::FRAME->value) . pack('V', strlen($queryRequest)) . $queryRequest;

        $this->messages[] = $frame;

        if (!$this->isOpen()) {
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
                            throw new Exception("[Litebase Client Error]: Failed to reconnect after connection loss: " . $reconnectException->getMessage());
                        }
                    }
                }

                throw $e;
            }

            if (!$this->isOpen()) {
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

        if ($response['close'] ?? false) {
            $this->close();

            return [
                'error' => 'connection closed',
            ];
        }

        if (isset($response['error'])) {
            return [
                'error' => $response['error']
            ];
        }

        if (isset($response['id']) && $response['id'] === $query->id) {
            return [
                'data' => $response,
            ];
        }

        return [
            'error' => 'no response found'
        ];
    }

    /**
     * Process the connection by resuming the reader and writer fibers.
     */
    protected function tick()
    {
        // Check if connection is broken before processing
        if ($this->isBrokenConnection()) {
            $this->close();
            throw new Exception("[Litebase Client Error]: Connection broken - server disconnected");
        }

        if (!$this->reader?->isSuspended() || !$this->writer?->isSuspended()) {
            $this->close();
            return;
        }

        // Important: resume the writer first
        if ($this->writer?->isSuspended()) {
            $return = $this->writer->resume();

            if ($return instanceof Throwable) {
                throw $return;
            }
        }

        if ($this->reader?->isSuspended()) {
            $return = $this->reader->resume();

            if ($return instanceof Throwable) {
                throw $return;
            }
        }
    }

    /**
     * Write a message to the socket with proper chunked encoding.
     */
    protected function writeMessage(string $message)
    {
        // Check for broken connection before writing
        if ($this->isBrokenConnection()) {
            Fiber::suspend(new Exception("[Litebase Client Error]: Connection broken - server disconnected"));
        }

        $chunkSize = dechex(strlen($message));
        $n = fwrite($this->socket, $chunkSize . "\r\n" . $message . "\r\n");

        if ($n === false) {
            // Check if the failure was due to broken connection
            if ($this->isBrokenConnection()) {
                Fiber::suspend(new Exception("[Litebase Client Error]: Connection broken - server disconnected"));
            } else {
                Fiber::suspend(new Exception("[Litebase Client Error]: Unable to write to socket"));
            }
        }

        $flushed = fflush($this->socket);

        if ($flushed === false) {
            // Check if the failure was due to broken connection
            if ($this->isBrokenConnection()) {
                Fiber::suspend(new Exception("[Litebase Client Error]: Connection broken - server disconnected"));
            } else {
                Fiber::suspend(new Exception("[Litebase Client Error]: Unable to flush data to socket"));
            }
        }
    }
}
