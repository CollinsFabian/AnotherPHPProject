<?php

namespace Ziro\System\Middleware;

use Ziro\System\Http\Request;

interface MiddlewareInterface
{
    public function handle(Request $request, callable $next);
}
