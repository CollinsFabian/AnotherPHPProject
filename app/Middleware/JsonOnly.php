<?php

namespace App\Middleware;

use Core\Http\Request;

class JsonOnly
{
    public function handle(Request $request, $next)
    {
        if (!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') return json(['error' => 'JSON required'], 415);
        return $next($request);
    }
}
