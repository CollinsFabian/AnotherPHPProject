<?php

use Core\Http\Request;
use Core\Middleware\MiddlewareInterface;

class FeatureFlagMiddleware implements MiddlewareInterface
{
    public function __construct(protected FeatureService $feature)
    {
    }

    public function handle(Request $request, callable $next)
    {
        if (!$this->feature->isEnabled('new_dashboard')) {
            return redirect('/old-dashboard');
        }
    }
}