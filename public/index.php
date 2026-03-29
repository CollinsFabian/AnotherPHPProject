<?php

ini_set("log_errors", 1);
ini_set("display_errors", 0);
ini_set("error_log", __DIR__ . "/../zirologs/php.log");
error_reporting(E_ALL);

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
