<?php

namespace Ziro\Middleware;

use Ziro\System\Http\Request;
use Ziro\System\Http\Response;
use Ziro\System\Middleware\MiddlewareInterface;

class CorsMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        $headers = $this->headers();

        if ($request->method === 'OPTIONS') {
            return Response::json(['status' => 'ok'], 200, $headers);
        }

        $response = $next($request);

        if (!$response instanceof Response) {
            $response = is_array($response)
                ? Response::json($response)
                : new Response((string) $response);
        }

        return $response->withHeaders($headers);
    }

    protected function headers(): array
    {
        return [
            'Access-Control-Allow-Origin' => (string) config('APP_CORS_ALLOW_ORIGIN', '*'),
            'Access-Control-Allow-Methods' => (string) config('APP_CORS_ALLOW_METHODS', 'GET, POST, PUT, PATCH, DELETE, OPTIONS'),
            'Access-Control-Allow-Headers' => (string) config('APP_CORS_ALLOW_HEADERS', 'Content-Type, Authorization, X-Api-Key, X-Requested-With'),
            'Access-Control-Allow-Credentials' => (string) config('APP_CORS_ALLOW_CREDENTIALS', 'true'),
        ];
    }
}
