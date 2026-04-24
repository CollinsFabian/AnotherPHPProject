<?php

namespace Ziro\Middleware;

use Ziro\System\Http\Request;
use Ziro\System\Http\Response;
use Ziro\System\Middleware\MiddlewareInterface;

class JsonOnly implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        $contentType = strtolower((string) $request->header('Content-Type', ''));

        if (!str_starts_with($contentType, 'application/json')) {
            return Response::json(['status' => 'error', 'message' => 'JSON required'], 415);
        }

        return $next($request);
    }
}
