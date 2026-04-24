#!/usr/bin/env php
<?php

require __DIR__ . "/../../../vendor/autoload.php";

use Ziro\System\CLI\CommandRegistry;

$registry = new CommandRegistry();

$registry->register("make:controller", Ziro\System\CLI\Commands\Make\Controller::class);
$registry->register("make:model", Ziro\System\CLI\Commands\Make\Model::class);
$registry->register("make:migration", Ziro\System\CLI\Commands\Make\Migration::class);
$registry->register("migrate", Ziro\System\CLI\Commands\DB\Migrate::class);
$registry->register("serve", Ziro\System\CLI\Commands\Dev\Serve::class);

$registry->register("build:assets", Ziro\System\CLI\Commands\Build\Assets::class);
$registry->register("cache:clear", Ziro\System\CLI\Commands\Cache\Clear::class);
$registry->register("structure:validate", Ziro\System\CLI\Commands\Structure\Validate::class);
$registry->handle($argv);
