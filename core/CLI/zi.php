#!/usr/bin/env php
<?php

require __DIR__ . "/../../vendor/autoload.php";

use Core\CLI\CommandRegistry;

$registry = new CommandRegistry();

$registry->register("make:controller", Core\CLI\Commands\Make\Controller::class);
$registry->register("make:model", Core\CLI\Commands\Make\Model::class);
$registry->register("make:migration", Core\CLI\Commands\Make\Migration::class);
$registry->register("migrate", Core\CLI\Commands\DB\Migrate::class);
$registry->register("serve", Core\CLI\Commands\Dev\Serve::class);

$registry->register("build:assets", Core\CLI\Commands\Build\Assets::class);
$registry->register("cache:clear", Core\CLI\Commands\Cache\Clear::class);
$registry->handle($argv);