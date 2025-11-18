<?php

declare(strict_types=1);

namespace Litebase;

class Transaction
{
    public function __construct(
        public readonly string $id,
    ) {}
}
