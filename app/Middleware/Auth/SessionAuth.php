<?php

namespace Ziro\Middleware\Auth;

use Ziro\System\Http\Request;
use Ziro\System\Middleware\MiddlewareInterface;
use Ziro\Support\Context;

class SessionAuth implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) return redirect('/login');

        Context::set('user', $_SESSION['user']);

        return $next($request);
    }
}
