<?php

namespace Litebase;

class QueryResponseDecoder
{
    /**
     * Create a new instance of the decoder.
     */
    public function __construct(
        protected QueryStreamMessageType $messageType,
        protected string $message,
    ) {}

    /**
     * Decode the binary response message into an associative array.
     *
     * @return array<int, array<string, bool|float|int|string|null>>
     */
    public function decode(): array
    {
        /**
         * @var array<int, array<string, bool|float|int|string|null>> $responses
         */
        $responses = [];
        $messageOffset = 0;

        switch ($this->messageType) {
            case QueryStreamMessageType::OPEN_CONNECTION:
                $responses[] = [
                    'open' => true,
                ];
                break;
            case QueryStreamMessageType::CLOSE_CONNECTION:
                $responses[] = [
                    'close' => true,
                ];

                break;
            case QueryStreamMessageType::ERROR:
                $responses[] = [
                    'close' => true,
                    'error' => '[Litebase Client Error]: Connection closed by server',
                ];

                break;
            case QueryStreamMessageType::FRAME:
                // Loop over the frame bytes to decode the responses
                $messageTypeByte = substr($this->message, $messageOffset, 1);
                $responseType = QueryStreamMessageType::from(unpack('C', $messageTypeByte)[1] ?? 0);
                $messageOffset += 1;

                switch ($responseType) {
                    case QueryStreamMessageType::ERROR:
                        $frameEntryLengthBytes = substr($this->message, $messageOffset, 4);
                        $frameEntryLength = unpack('V', $frameEntryLengthBytes)[1] ?? 0;
                        $messageOffset += 4;

                        $response = substr($this->message, $messageOffset, $frameEntryLength);
                        $offset = 0;
                        $version = unpack('C', substr($response, $offset, 1))[1] ?? 0;
                        $idLength = unpack('V', substr($response, $offset += 1, 4))[1] ?? 0;
                        $id = substr($response, $offset += 4, $idLength);
                        $transactionIdLength = unpack('V', substr($response, $offset += $idLength, 4))[1] ?? 0;

                        if ($transactionIdLength > 0) {
                            $transactionId = substr($response, $offset += 4, $transactionIdLength);
                        } else {
                            $transactionId = null;
                            $offset += 4;
                        }

                        $errorLength = unpack('V', substr($response, $offset, 4))[1] ?? 0;
                        $errorMessage = substr($response, $offset + 4, $errorLength);
                        $messageOffset += $frameEntryLength;

                        $responses[] = [
                            'error' => $errorMessage,
                        ];
                        break;
                    case QueryStreamMessageType::FRAME_ENTRY:
                        $frameEntryLengthBytes = substr($this->message, $messageOffset, 4);
                        $frameEntryLength = unpack('V', $frameEntryLengthBytes)[1] ?? 0;
                        $messageOffset += 4;

                        $response = substr($this->message, $messageOffset, $frameEntryLength);

                        $offset = 0;
                        $version = unpack('C', substr($response, $offset, 1))[1] ?? 0;
                        $idLength = unpack('V', substr($response, $offset += 1, 4))[1] ?? 0;
                        $id = substr($response, $offset += 4, $idLength);
                        $transactionIdLength = unpack('V', substr($response, $offset += $idLength, 4))[1] ?? 0;
                        $transactionId = substr($response, $offset += 4, $transactionIdLength);
                        $changes = unpack('V', substr($response, $offset += $transactionIdLength, 4))[1] ?? 0;
                        $latency = unpack('e', substr($response, $offset += 4, 8))[1] ?? 0;
                        $columnsCount = unpack('V', substr($response, $offset += 8, 4))[1] ?? 0;
                        $rowCount = unpack('V', substr($response, $offset += 4, 4))[1] ?? 0;
                        $lastInsertRowId = unpack('V', substr($response, $offset += 4, 4))[1] ?? 0;
                        $columnsLength = unpack('V', substr($response, $offset += 4, 4))[1] ?? 0;

                        $columnBytes = substr($response, $offset += 4, $columnsLength);
                        $columns = $this->decodeColumns($columnBytes);
                        $rowsBytes = substr($response, $offset += $columnsLength);
                        $rows = $this->decodeRows($rowsBytes);

                        $responses[] = [
                            'changes' => $changes,
                            'columnCount' => $columnsCount,
                            'columns' => $columns,
                            'rowCount' => $rowCount,
                            'rows' => $rows,
                            'id' => $id,
                            'lastInsertRowId' => $lastInsertRowId,
                            'latency' => $latency,
                            'transactionId' => $transactionId,
                            'version' => $version,
                        ];
                        break;
                    default:
                        $responses[] = [
                            'error' => '[Litebase Client Error]: Unknown response type',
                        ];
                }
                break;
            default:
                $responses[] = [
                    'error' => '[Litebase Client Error]: Unknown message type',
                ];
        }

        return $responses;
    }

    /**
     * Decode the column names from the binary response message.
     *
     * @return array<int, array{name: string, type: ColumnType}>
     */
    protected function decodeColumns(string $columnBytes): array
    {
        $offset = 0;
        $columns = [];

        while ($offset < strlen($columnBytes)) {
            $columnLengthBytes = substr($columnBytes, $offset, 4);
            $columnLength = unpack('V', $columnLengthBytes)[1] ?? 0;
            $offset += 4;

            $columnName = substr($columnBytes, $offset, $columnLength);
            $offset += $columnLength;

            // Column type is 4 bytes (uint32), not 1 byte
            $columnTypeBytes = substr($columnBytes, $offset, 4);
            $columnType = unpack('V', $columnTypeBytes)[1] ?? 0;
            $offset += 4;

            $columns[] = [
                'name' => $columnName,
                'type' => ColumnType::from($columnType),
            ];
        }

        return $columns;
    }

    /**
     * Decode the rows from the binary response message.
     *
     * @return array<int, array<int, mixed>>
     */
    protected function decodeRows(string $rowsBytes): array
    {
        $rows = [];
        $rowsOffset = 0;

        while ($rowsOffset < strlen($rowsBytes)) {
            $rowLengthBytes = substr($rowsBytes, (int) $rowsOffset, 4);
            $rowLength = unpack('V', $rowLengthBytes)[1] ?? 0;

            $rowsOffset += 4;
            $rowOffset = $rowsOffset;
            $rowsOffset += $rowLength;

            $currentRow = [];

            while ($rowOffset < $rowsOffset) {
                $columnTypeByte = substr($rowsBytes, $rowOffset, 1);
                $rowOffset += 1;

                $columnType = unpack('C', $columnTypeByte)[1] ?? null;
                $columnValueLength = unpack('V', substr($rowsBytes, $rowOffset, 4))[1] ?? 0;

                $rowOffset += 4;
                $columnValue = substr($rowsBytes, $rowOffset, $columnValueLength);

                $rowOffset += $columnValueLength;

                switch ($columnType) {
                    case ColumnType::INTEGER->value:
                        $columnValue = unpack('P', $columnValue)[1] ?? null;
                        break;
                    case 2:
                        $columnValue = unpack('e', $columnValue)[1] ?? null;
                        break;
                    case 3:
                    case 4:
                        break;
                    case 5:
                        $columnValue = null;
                        break;
                    default:
                        throw new \InvalidArgumentException("Invalid ColumnType value: $columnType");
                }

                $currentRow[] = $columnValue;
            }

            $rows[] = $currentRow;
        }

        return $rows;
    }
}
