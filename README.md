# Ziro

Ziro is a PHP-first API and SPA framework skeleton with a regulated project layout, a bundled asset pipeline, and a cleaner HTTP lifecycle.

## Architecture

Framework boundaries:

- `public/` is the only web root
- `bootstrap/app.php` builds the kernel
- `routes/api.php` owns API route definitions
- `app/` contains controllers, middleware, entities, and services
- `resources/assets/` contains frontend source files
- `system/tools/builder/` contains the asset compiler
- `database/migrations/` is the migration target
- `storage/` holds cache, logs, and runtime files

Runtime flow:

- `Ziro\System\Http\Request` normalizes headers, query params, and JSON bodies
- `Ziro\System\Http\Response` returns response objects instead of terminating execution
- `Ziro\System\Kernel` runs global middleware, route middleware, then controller actions
- `Ziro\Middleware\CorsMiddleware` handles preflight and API CORS headers

## Setup

Backend prerequisites:

- PHP `^8.0`
- `ext-pdo`
- `ext-mbstring`
- Composer

Install backend dependencies:

```console
composer install
```

Frontend prerequisites:

- Node.js 18+
- npm

Install builder dependencies:

```console
cd system/tools/builder
npm install
cd ../../..
```

Create `.env` from `.env.example` and define:

```dotenv
APP_NAME=Ziro
APP_ENV=local
APP_DEBUG=false
APP_JWT_SECRET=replace-me
APP_API_KEYS=abc1234,rxyz789
APP_CORS_ALLOW_ORIGIN=*
APP_CORS_ALLOW_METHODS=GET, POST, PUT, PATCH, DELETE, OPTIONS
APP_CORS_ALLOW_HEADERS=Content-Type, Authorization, X-Api-Key, X-Requested-With
APP_CORS_ALLOW_CREDENTIALS=true
DB_PDO_DSN=mysql:host=127.0.0.1;dbname=ziro;charset=utf8mb4;port=3306
DB_USER=root
DB_PASSWORD=
```

## Development

Start the PHP server:

```console
zi serve
```

That serves:

- `/api/*` through `public/index.php`
- static assets from `public/`
- SPA routes through `public/index.html`

Run the asset pipeline in watch mode:

```console
zi build:assets --dev
```

Build assets once:

```console
zi build:assets
```

Build production assets with hashed filenames:

```console
zi build:assets --prod
```

Low-level npm scripts still exist, but they are implementation details for the builder. The preferred framework interface is the `zi` launcher.

## Regulated Structure

Validate framework layout:

```console
zi structure:validate
```

The validator enforces required framework paths and rejects legacy misuse such as placing route definitions under `app/Routes`.
The validator enforces required framework paths and rejects legacy misuse such as keeping API routes in `app/Routes/api.php`.

Canonical locations:

- API routes: `routes/api.php`
- bootstrap: `bootstrap/app.php`
- migrations: `database/migrations`
- cache: `storage/cache`
- logs: `storage/logs`
- frontend sources: `resources/`
- compiled assets: `public/`

## Obsolete Patterns

These older conventions should be treated as obsolete:

- PHP `^7.4 || ^8.0` in older docs. The actual framework requirement is PHP `^8.0`.
- Defining API routes in `app/Routes/api.php`. The canonical route file is `routes/api.php`.
- Using `php system/core/CLI/zi.php ...` as the primary workflow in documentation. The preferred project-root interface is `zi ...`.
- Using `npm run dev`, `npm run build`, or `npm run build:prod` as the primary framework workflow. The preferred interface is `zi build:assets ...`.
- Returning framework responses by directly exiting from helpers or middleware. Controllers and middleware should return `Response` objects.

## Backend Best Practices

The backend was tightened around these rules:

- controllers receive `Request` instead of reading globals directly
- middleware returns `Response` objects instead of calling `exit`
- JWT verification uses structured parsing and constant-time signature checks
- API keys and CORS behavior come from configuration
- route definitions are separated from application classes

## CORS

Ziro now includes explicit CORS middleware for browser and third-party clients.

This covers:

- origin policy
- allowed methods
- allowed headers
- credential support
- preflight `OPTIONS` requests

All CORS behavior is configured from `.env`.

## Error Logging

Framework-managed PHP errors and uncaught exceptions are written to:

- `storage/logs/php-error.log`

The bootstrap configures:

- `log_errors=1`
- `error_log=storage/logs/php-error.log`
- exception logging for uncaught throwables
- fatal shutdown logging for parse/runtime fatal errors

Use `APP_DEBUG=true` in `.env` if you want PHP error display enabled during development.

## Build System

The frontend pipeline compiles `resources/assets/` into `public/assets/`:

1. JavaScript is bundled with `esbuild`
2. CSS is flattened and processed through `postcss`
3. templates are copied into `public/assets/templates`
4. `manifest.json` records output asset paths

## Production Routing

Production should preserve the same split:

- `/api/*` -> `public/index.php`
- existing files -> serve directly
- other routes -> `public/index.html`

Nginx example:

```nginx
location /api/ {
    try_files $uri /index.php?$query_string;
}

location / {
    try_files $uri $uri/ /index.html;
}
```

See [`nginx.conf.example`](./system/deploy/nginx.conf.example).

## CLI

```console
zi serve
zi build:assets --dev
zi build:assets --prod
zi make:controller UserController
zi make:model User
zi make:migration create_users_table
zi cache:clear
zi structure:validate
```

## Route Example

```php
use Ziro\Controllers\Api\AuthController;
use Ziro\Controllers\Api\UserController;

$router->get('/api/v1/user', [UserController::class, 'profile'])
    ->middleware(['rate_limit']);

$router->post('/api/v1/login', [AuthController::class, 'login'])
    ->middleware(['json_only', 'rate_limit']);
```

## License

MIT
