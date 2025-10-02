<?php

namespace Litebase;

class QueryResponseDecoder
{
    public function __construct(
        protected QueryStreamMessageType $messageType,
        protected string $message,
    ) {}

    public function decode(): array
    {
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
                $responseType = QueryStreamMessageType::from(unpack('C', $messageTypeByte)[1]);
                $messageOffset += 1;

                switch ($responseType) {
                    case QueryStreamMessageType::ERROR:
                        $frameEntryLengthBytes = substr($this->message, $messageOffset, 4);
                        $frameEntryLength = unpack('V', $frameEntryLengthBytes)[1];
                        $messageOffset += 4;

                        $response = substr($this->message, $messageOffset, $frameEntryLength);
                        $offset = 0;
                        $version = unpack('C', substr($response, $offset, 1))[1];
                        $idLength = unpack('V', substr($response, $offset += 1, 4))[1];
                        $id = substr($response, $offset += 4, $idLength);
                        $transactionIdLength = unpack('V', substr($response, $offset += $idLength, 4))[1];

                        if ($transactionIdLength > 0) {
                            $transactionId = substr($response, $offset += 4, $transactionIdLength);
                        } else {
                            $transactionId = null;
                            $offset += 4;
                        }

                        $errorLength = unpack('V', substr($response, $offset, 4))[1];
                        $errorMessage = substr($response, $offset + 4, $errorLength);
                        $messageOffset += $frameEntryLength;

                        $responses[] = [
                            'error' => $errorMessage,
                        ];
                        break;
                    case QueryStreamMessageType::FRAME_ENTRY:
                        $frameEntryLengthBytes = substr($this->message, $messageOffset, 4);
                        $frameEntryLength = unpack('V', $frameEntryLengthBytes)[1];
                        $messageOffset += 4;

                        $response = substr($this->message, $messageOffset, $frameEntryLength);

                        $offset = 0;
                        $version = unpack('C', substr($response, $offset, 1))[1];
                        $idLength = unpack('V', substr($response,  $offset += 1, 4))[1];
                        $id = substr($response, $offset += 4, $idLength);
                        $transactionIdLength = unpack('V', substr($response, $offset += $idLength, 4))[1];
                        $transactionId =  substr($response, $offset += 4, $transactionIdLength);
                        $changes = unpack('V', substr($response, $offset += $transactionIdLength, 4))[1];
                        $latency = unpack('e', substr($response, $offset += 4, 8))[1];
                        $columnsCount = unpack('V', substr($response, $offset += 8, 4))[1];
                        $rowsCount = unpack('V', substr($response, $offset += 4, 4))[1];
                        $lastInsertRowID = unpack('V', substr($response, $offset += 4, 4))[1];
                        $columnsLength = unpack('V', substr($response, $offset += 4, 4))[1];

                        $columnBytes = substr($response, $offset += 4, $columnsLength);
                        $columns = $this->decodeColumns($columnBytes);
                        $rowsBytes = substr($response,   $offset += $columnsLength);
                        $rows = $this->decodeRows($rowsBytes);

                        $responses[] = [
                            'changes' => $changes,
                            'column_count' => $columnsCount,
                            'columns' => $columns,
                            'rows_count' => $rowsCount,
                            'rows' => $rows,
                            'id' => $id,
                            'last_insert_row_id' => $lastInsertRowID,
                            'latency' => $latency,
                            'transaction_id' => $transactionId,
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

    protected function decodeColumns(string $columnBytes): array
    {
        $offset = 0;
        $columns = [];

        while ($offset < strlen($columnBytes)) {
            $columnLengthBytes = substr($columnBytes, $offset, 4);
            $columnLength = unpack('V', $columnLengthBytes)[1];
            $offset += 4;
            $columnName = substr($columnBytes, $offset, $columnLength);

            $columns[] = $columnName;

            $offset += $columnLength;
        }

        return $columns;
    }

    protected function decodeRows(string $rowsBytes): array
    {
        $rows = [];
        $rowsOffset = 0;

        while ($rowsOffset < strlen($rowsBytes)) {
            $rowLengthBytes = substr($rowsBytes, $rowsOffset, 4);
            $rowLength = unpack('V', $rowLengthBytes)[1];

            $rowsOffset += 4;
            $rowOffset = $rowsOffset;
            $rowsOffset += $rowLength;

            $currentRow = [];

            while ($rowOffset < $rowsOffset) {
                $columnTypeByte = substr($rowsBytes, $rowOffset, 1);
                $rowOffset += 1;
                $columnType = unpack('C', $columnTypeByte)[1];
                $columnValueLength = unpack('V', substr($rowsBytes, $rowOffset, 4))[1];
                $rowOffset += 4;
                $columnValue = substr($rowsBytes, $rowOffset, $columnValueLength);

                $rowOffset += $columnValueLength;

                switch ($columnType) {
                    case ColumnType::INTEGER->value:
                        $columnValue = unpack('P', $columnValue)[1];
                        break;
                    case 2:
                        $columnValue = unpack('e', $columnValue)[1];
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
