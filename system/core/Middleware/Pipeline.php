<?php

namespace Ziro\System\Middleware;

use Ziro\System\Container;
use Ziro\System\Http\Request;

class Pipeline
{
    protected array $middlewares = [];
    protected $request;

    public function __construct(protected Container $container)
    {
        $this->container = $container;
    }

    public function send(Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    public function through(array $middlewares): self
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    public function then(callable $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            fn($next, $middleware)
            => fn($request)
            => $this->container->make($middleware)->handle($request, $next),
            $destination
        );
        return $pipeline($this->request);
    }
}
