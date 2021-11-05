<?php
ini_set("error_log", __DIR__ . '/error.log');

use Litebase\QueryProxyServer;

require_once __DIR__ . '/vendor/autoload.php';

// TODO: setup the client
QueryProxyServer::run();
