<?php

namespace Litebase;

interface TransportInterface
{
    public function send(Query $query): ?QueryResult;
}
