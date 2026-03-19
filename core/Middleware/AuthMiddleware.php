<?php

namespace Core\Middleware;

use App\Services\UserService;
use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(protected UserService $service)
    {
        $this->service = $service;
    }

    public function handle(Request $request, callable $next)
    {
        if (!isset($_SESSION["user"])) {
            return 'Unauthorized';
        }

        return $next($request);
    }
}
