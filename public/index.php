<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Core\Kernel;
use Core\Http\Request;

$kernel = new Kernel();
$kernel->registerRoutes();

$request = Request::capture();
$response = $kernel->handle($request);
$response->send();