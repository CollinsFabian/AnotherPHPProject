<?php

declare(strict_types=1);

use Ziro\System\Http\Request;
use Ziro\System\Http\Response;
use Ziro\System\Support\ErrorLogger;

$kernel = require __DIR__ . '/../bootstrap/app.php';

try {
    $response = $kernel->handle(Request::capture());
} catch (Throwable $exception) {
    ErrorLogger::logThrowable($exception);
    $response = Response::json([
        'status' => 'error',
        'message' => 'Application error',
        'detail' => $exception->getMessage(),
    ], 500);
}

$response->send();
