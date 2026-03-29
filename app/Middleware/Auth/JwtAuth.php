<?php

namespace App\Middleware\Auth;

use App\Services\Auth\JwtService;
use App\Support\Context;
use Core\Http\Request;

class JwtAuth
{
    public function handle(Request $request, $next)
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!preg_match('/Bearer\s(\S+)/', $header, $matches)) return json(['error' => 'Unauthorized'], 401);

        $payload = JwtService::verify($matches[1]);
        if (!$payload) return json(['error' => 'Invalid token'], 401);
        Context::set('user', $payload);

        return $next($request);
    }
}
