<?php

namespace Ziro\Middleware\Auth;

use Ziro\Services\Auth\TokenStore;
use Ziro\System\Http\Request;
use Ziro\System\Http\Response;
use Ziro\System\Middleware\MiddlewareInterface;

class ApiKeyAuth implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        $key = $request->header('X-Api-Key');

        if (!$key || !TokenStore::validate($key)) {
            return Response::json(['status' => 'error', 'message' => 'Forbidden'], 403);
        }
        return $next($request);
    }
}
