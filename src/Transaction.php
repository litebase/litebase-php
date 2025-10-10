<?php

namespace Litebase;

class Transaction
{
    public function __construct(
        public readonly string $id,
    ) {}
}
