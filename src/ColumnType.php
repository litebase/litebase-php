<?php

declare(strict_types=1);

namespace Litebase;

enum ColumnType: int
{
    case BLOB = 4;
    case FLOAT = 2;
    case INTEGER = 1;
    case NULL = 5;
    case TEXT = 3;
    case UNKNOWN = 0;
}
