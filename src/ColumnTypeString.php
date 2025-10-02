<?php

namespace Litebase;

enum ColumnTypeString: string
{
    case BLOB = 'BLOB';
    case FLOAT = 'FLOAT';
    case INTEGER = 'INTEGER';
    case NULL = 'NULL';
    case TEXT = 'TEXT';

    public static function fromInteger(int $value): ColumnType
    {
        return match ($value) {
            1 => ColumnType::INTEGER,
            2 => ColumnType::FLOAT,
            3 => ColumnType::TEXT,
            4 => ColumnType::BLOB,
            5 => ColumnType::NULL,
            default => throw new \InvalidArgumentException("Invalid ColumnType value: $value"),
        };
    }
}
