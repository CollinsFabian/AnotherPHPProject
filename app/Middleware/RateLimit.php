<?php

namespace App\Middleware;

use Core\Cache\Cache;
use Core\Http\Request;

class RateLimit
{
    protected int $max = 15;

    public function handle(Request $request, $next)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $key = "rate_{$ip}";

        $cache = new Cache();
        $count = $cache->get($key) ?: 0;

        if ($count >= $this->max) return json(['status' => 'error', 'message' => 'Too many requests'], 429);

        $cache->set($key, $count + 1, 60);

        return $next($request);
    }
}
