<?php

namespace App\Middleware\Auth;

use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;
use App\Support\Context;

class SessionAuth implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        session_start();

        if (!isset($_SESSION['user'])) return redirect('/login');

        // inject into Context for global access
        Context::set('user', $_SESSION['user']);

        return $next($request);
    }
}
