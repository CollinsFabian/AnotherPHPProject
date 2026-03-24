<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Core\Kernel;
use Core\Http\Request;
use ZQuery\Support\Environment;

Environment::load(__DIR__ . '/../.env');

$kernel = new Kernel();
$kernel->registerRoutes();

$request = Request::capture();
$response = $kernel->handle($request);
$response->send();
