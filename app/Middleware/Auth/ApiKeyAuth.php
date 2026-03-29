<?php

namespace App\Middleware\Auth;

use App\Services\Auth\TokenStore;
use Core\Http\Request;

class ApiKeyAuth
{
    public function handle(Request $request, $next)
    {
        $key = $_SERVER['HTTP_X_API_KEY'] ?? null;

        if (!$key || !TokenStore::validate($key)) return json(['error' => 'Forbidden'], 403);
        return $next($request);
    }
}
