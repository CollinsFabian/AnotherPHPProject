<?php

// use Core\Routing\Router;

/**@var \Core\Routing\Router $router */

use App\Controllers\HomeController;

$router->get('/', [HomeController::class, 'index']);
$router->get('/home', fn() => to("/"));
$router->get('/home/{slug1}', [HomeController::class, 'index']);

$router->get('/login', [HomeController::class, 'login']);

$router->get('/dashboard', [HomeController::class, 'dashboard'])
    ->middleware(['session_auth']);
