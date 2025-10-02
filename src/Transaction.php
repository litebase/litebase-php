<?php

namespace Litebase;

class Transaction
{
    public function __construct(
        readonly public string $id,
    ) {}
}
