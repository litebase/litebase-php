<?php

namespace Litebase;

class QueryRequestEncoder
{
    public static function encode(Query $query): string
    {
        $binaryData = '';
        $id = $query->id;
        $idLength = pack('V', strlen($id));
        $binaryData .= $idLength . $id;

        $transactionIdLength = pack('V', strlen($query->transactionId ?? ''));
        $binaryData .= $transactionIdLength;

        if ($query->transactionId) {
            $binaryData .= $query->transactionId;
        }

        $statement = $query->statement;
        $statementLength = pack('V', strlen($statement));
        $binaryData .= $statementLength . $statement;

        $parametersBinary = '';

        foreach ($query->parameters as $parameter) {
            // Parameter value length and encoding based on type
            switch ($parameter->getType()) {
                case ColumnTypeString::INTEGER->value:
                    $parameterType = ColumnType::INTEGER->value;
                    // Use 'P' for 64-bit signed integer, little-endian
                    // Or use 'q' for explicit 64-bit signed integer
                    $parameterValue = pack('q', $parameter->getValue());
                    $parameterValueLength = 8;
                    break;

                case ColumnTypeString::FLOAT->value:
                    $parameterType = ColumnType::FLOAT->value;
                    // Use 'e' for little-endian double, or 'd' if your system is little-endian
                    $parameterValue = pack('e', $parameter->getValue());
                    $parameterValueLength = 8;
                    break;

                case ColumnTypeString::TEXT->value:
                    $parameterType = ColumnType::TEXT->value;
                    $parameterValue = (string) $parameter->getValue();
                    $parameterValueLength = strlen($parameterValue);
                    break;

                case ColumnTypeString::BLOB->value:
                    $parameterType = ColumnType::BLOB->value;
                    $parameterValue = $parameter->getValue();
                    // Use strlen for binary-safe length
                    $parameterValueLength = strlen((string) $parameterValue);
                    break;

                case ColumnTypeString::NULL->value:
                    $parameterType = ColumnType::NULL->value;
                    $parameterValue = '';
                    $parameterValueLength = 0;
                    break;

                default:
                    $parameterType = ColumnType::TEXT->value;
                    $parameterValue = (string) $parameter->getValue();
                    $parameterValueLength = strlen($parameterValue);
                    break;
            }

            // Parameter type (1 byte)
            $parameterType = pack('C', $parameterType);

            // Parameter value with length prefix (4 bytes little-endian + value)
            $parameterValueWithLength = pack('V', $parameterValueLength) . $parameterValue;

            $parametersBinary .= $parameterType . $parameterValueWithLength;
        }

        $parametersBinaryLength = pack('V', strlen($parametersBinary));
        $binaryData .= $parametersBinaryLength . $parametersBinary;
        $queryBinary = pack('V', strlen($binaryData)) . $binaryData;

        return $queryBinary;
    }
}
