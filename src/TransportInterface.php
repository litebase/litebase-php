<?php

declare(strict_types=1);

namespace Litebase;

interface TransportInterface
{
    public function send(Query $query): ?QueryResult;
}
