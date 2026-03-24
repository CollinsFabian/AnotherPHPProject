# Ziro

A minimal, no-nonsense PHP framework focused on simplicity, direct execution, and full developer control.

---

## Overview

Ziro is designed for developers who prefer writing raw, understandable PHP without heavy abstractions or unnecessary framework layers.

No magic. No complex containers. Just straightforward routing and execution.

---

## Features

- Simple routing (closures and controllers)
- Direct execution flow
- Lightweight core
- Flexible architecture (no enforced structure)

---

## Usage

**Starting a server**

```console
php -S localhost:3000 -t public
```

---


**Define Routes**

```php
$router->get('/', function () {
    return "Hello world";
});

$router->get('/home', fn() => to("/")); // redirecting urls (old/legacy) to new routes

$router->get('/user/{id}', [UserController::class, 'show']);

$router->get('/dashboard', [DashboardController::class, 'index'])->middleware([\Core\Middleware\AuthMiddleware::class]);
```

---

**Controller Example**

```php
class UserController
{
    public function show($id)
    {
        return "User: " . $id;
    }
}
```

---

**Route Execution**

```php
public function executeRoute(array $route, Request $request)
{
    $action = $route['action'];
    $params = $route['params'] ?? [];

    if (is_callable($action)) return $action($request, ...array_values($params));

    if (is_array($action)) {
        [$controller, $method] = $action;
        $instance = $this->container->make($controller);

        return $instance->$method(...array_values($params));
    }

    throw new \Exception("Invalid route action");
}
```

---

### Philosophy

- Keep it simple
- Avoid unnecessary abstraction
- Give developers full control

---

### _Not (yet) Inplemented_

- Models (under works)
- Additional features

---

### License

**MIT**
