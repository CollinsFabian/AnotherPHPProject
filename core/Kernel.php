<?php

declare(strict_types=1);

namespace Core;

use Core\Middleware\Pipeline;
use Core\Http\Request;
use Core\Http\Response;
use Core\Routing\Router;

class Kernel
{
    protected Router $router;
    protected Container $container;
    protected array $middlewareStack = [];

    public function __construct($container = null)
    {
        $this->container = $container ?? new Container();
        $this->router = new Router($this->container);
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function middleware(array $middlewares): void
    {
        $this->middlewareStack = $middlewares;
    }

    public function registerRoutes(): void
    {
        $router = $this->router; // DI
        require base_path("routes/web.php");
    }

    public function handle(Request $request): Response
    {
        $route = $this->router->match($request);
        if (!$route) return new Response("Route not found", 404);

        $middlewares = [...$this->middlewareStack, ...$route['middleware'] ?? []];

        $pipeline = new Pipeline($this->container);
        $result = $pipeline
            ->send($request)
            ->through($middlewares)
            ->then(fn($req) => $this->router->executeRoute($route, $req));

        if ($result instanceof Response) return $result;
        return new Response($result);
    }
}
