<?php

// use Core\Routing\Router;

/**@var \Core\Routing\Router $router */

use App\Controllers\HomeController;

$router->get('/', [HomeController::class, 'index']);

$router->get('/home', fn() => to("/"));
$router->get('/home/{id}', [HomeController::class, 'index']);

$router->get('/dashboard', [HomeController::class, 'index'])->middleware([\Core\Middleware\AuthMiddleware::class]);
