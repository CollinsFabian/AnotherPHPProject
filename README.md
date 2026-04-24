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

**Starting the API + SPA dev server**

```console
zi serve
```

This serves the frontend as a real SPA:

- `/api/*` is handled by PHP
- static files are served from `public/`
- all other routes fall back to `public/index.html`

---


**Define API Routes**

```php
$router->get('/api/v1/user', [UserController::class, 'profile']);
$router->post('/api/v1/login', [AuthController::class, 'login']);
```

---

**Create an API controller**

```console
zi make:controller UserController
```

New controllers are generated in `app/Controllers/Api`.

---

**Controller Example**

```php
class UserController
{
    public function profile()
    {
        return json([
            "status" => "success",
            "data" => ["id" => 1]
        ]);
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

## Production Routing

Production must follow the same split:

- `/api/*` -> PHP
- existing static files -> serve directly
- everything else -> `public/index.html`

**Nginx**

```nginx
location /api/ {
    try_files $uri /index.php?$query_string;
}

location / {
    try_files $uri $uri/ /index.html;
}
```

A ready-to-edit example is included at [`deploy/nginx.conf.example`](./deploy/nginx.conf.example).

**Apache (.htaccess)**

```apache
RewriteEngine On

RewriteCond %{REQUEST_URI} ^/api/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [QSA,L]

RewriteCond %{REQUEST_URI} !^/api/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.html [L]
```

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
