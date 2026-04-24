<?php

namespace Ziro\Middleware\Auth;

use Ziro\Services\Auth\JwtService;
use Ziro\Support\Context;
use Ziro\System\Http\Request;
use Ziro\System\Http\Response;
use Ziro\System\Middleware\MiddlewareInterface;

class JwtAuth implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        $header = (string) $request->header('Authorization', '');
        if (!preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return Response::json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $payload = JwtService::verify($matches[1]);
        if (!$payload) {
            return Response::json(['status' => 'error', 'message' => 'Invalid token'], 401);
        }
        Context::set('user', $payload);

        return $next($request);
    }
}
