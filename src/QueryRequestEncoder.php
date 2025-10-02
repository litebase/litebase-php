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
            // Parameter type length

            // Parameter value length (INTEGER, FLOAT, TEXT, BLOB, NULL)
            switch ($parameter['type']) {
                case ColumnTypeString::INTEGER->value:
                    $parameterType = ColumnType::INTEGER->value;
                    $parameterValue = pack('P', $parameter['value']);
                    $parameterValueLength = 8;
                    break;
                case ColumnTypeString::FLOAT->value:
                    $parameterType = ColumnType::FLOAT->value;
                    $parameterValue = pack('d', $parameter['value']);
                    $parameterValueLength = 8;
                    break;
                case ColumnTypeString::TEXT->value:
                    $parameterType = ColumnType::TEXT->value;
                    $parameterValue = $parameter['value'];
                    $parameterValueLength = strlen($parameterValue);
                    break;
                case ColumnTypeString::BLOB->value:
                    $parameterType = ColumnType::BLOB->value;
                    $parameterValue = $parameter['value'];
                    $parameterValueLength = mb_strlen($parameterValue, '8bit');
                    break;
                case ColumnTypeString::NULL->value:
                    $parameterType = ColumnType::NULL->value;
                    $parameterValue = '';
                    $parameterValueLength = 0;
                    break;
                default:
                    $parameterType = ColumnType::TEXT->value;
                    $parameterValue = $parameter['value'];
                    $parameterValueLength = strlen($parameter['value']);
                    break;
            }

            // Parameter type
            $parameterType = pack('C', $parameterType);

            // Parameter value
            $parameterValue = pack('V', $parameterValueLength) . $parameterValue;

            $parametersBinary .= $parameterType . $parameterValue;
        }

        $parametersBinaryLength = pack('V', strlen($parametersBinary));
        $binaryData .= $parametersBinaryLength . $parametersBinary;
        $queryBinary = pack('V', strlen($binaryData)) . $binaryData;

        return $queryBinary;
    }
}
