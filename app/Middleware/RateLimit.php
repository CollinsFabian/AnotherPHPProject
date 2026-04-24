<?php

namespace Ziro\Middleware;

use Ziro\System\Cache\Cache;
use Ziro\System\Http\Request;
use Ziro\System\Http\Response;
use Ziro\System\Middleware\MiddlewareInterface;

class RateLimit implements MiddlewareInterface
{
    protected int $max = 15;

    public function handle(Request $request, callable $next)
    {
        $ip = $request->server['REMOTE_ADDR'] ?? 'unknown';
        $key = "rate_{$ip}";

        $cache = new Cache();
        $count = $cache->get($key) ?: 0;

        if ($count >= $this->max) {
            return Response::json(['status' => 'error', 'message' => 'Too many requests'], 429);
        }

        $cache->set($key, $count + 1, 60);

        return $next($request);
    }
}
