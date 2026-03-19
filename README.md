AnotherPHPProject (APP)

A minimal, no-nonsense PHP framework focused on simplicity, direct execution, and full developer control.

---

Overview

AnotherPHPProject (APP) is designed for developers who prefer writing raw, understandable PHP without heavy abstractions or unnecessary framework layers.

No magic. No containers. Just straightforward routing and execution.

---

Features

- Simple routing (closures and controllers)
- Direct execution flow
- Lightweight core
- Flexible architecture (no enforced structure)

---

Usage

Define Routes

$router->get('/hello', function () {
    return "Hello world";
});

$router->get('/user/{id}', [UserController::class, 'show']);

---

Controller Example

class UserController
{
    public function show($id)
    {
        return "User: " . $id;
    }
}

---

Route Execution

public function executeRoute(array $route)
{
    $action = $route['action'];
    $params = $route['params'] ?? [];

    if (is_callable($action)) return $action(...array_values($params));

    if (is_array($action)) {
        [$controller, $method] = $action;
        return (new $controller)->$method(...array_values($params));
    }
}

---

Philosophy

- Keep it simple
- Avoid unnecessary abstraction
- Give developers full control

---

Not Included

- Dependency injection containers
- Middleware pipelines
- Heavy framework features

---

License

MIT