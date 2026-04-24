<?php

declare(strict_types=1);

namespace Ziro\System\Routing;

use Ziro\System\Container;
use Ziro\System\Http\Request;

class Router
{
    protected array $routes = [];
    protected string $lastUri;
    protected string $lastMethod;
    protected int $lastIndex;
    protected array $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    public function __construct(protected Container $container) {}

    public function __call($method, $args)
    {
        $method = strtoupper($method);
        if (!in_array($method, $this->allowedMethods)) {
            throw new \BadMethodCallException("Router method $method not allowed");
        }
        [$uri, $action] = $args;
        $index = count($this->routes[$method] ?? []);

        $this->routes[$method][$index] = [
            'uri' => $uri,
            'action' => $action,
            'middleware' => $middleware ?? [],
            'pattern' => $this->compileRoute($uri),
        ];

        $this->lastMethod = $method;
        $this->lastIndex = $index;
        return $this;
    }

    protected function compileRoute(string $uri): array
    {
        $paramNames = [];
        $patterns = preg_replace_callback('/\{(\w+)\}/', function ($matches) use (&$paramNames) {
            $paramNames[] = $matches[1];
            return '([^\/]+)';
        }, $uri);

        return [
            'regex' => "#^" . $patterns . "$#",
            'params' => $paramNames,
        ];
    }

    public function middleware(array $middlewares)
    {
        $this->routes[$this->lastMethod][$this->lastIndex]['middleware'] = $middlewares;
        return $this;
    }

    public function match(Request $request)
    {
        $method = strtoupper($request->method);
        $uri = $request->uri;

        foreach ($this->routes[$method] ?? [] as $route) {
            $pattern = $route["pattern"]["regex"];

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                $params = array_combine($route["pattern"]["params"], $matches);
                return [...$route, 'params' => $params];
            }
        }

        return null;
    }

    public function executeRoute(array $route, Request $request)
    {
        $action = $route['action'];
        $params = $route['params'] ?? [];

        if (is_callable($action)) return $action($request, ...array_values($params));

        if (is_array($action)) {
            [$controller, $method] = $action;
            $instance = $this->container->make($controller);

            return $instance->$method($request, ...array_values($params));
        }

        throw new \Exception("Invalid route action");
    }
}
