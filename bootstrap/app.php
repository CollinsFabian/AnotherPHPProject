<?php

declare(strict_types=1);

use Ziro\System\Config\Config;
use Ziro\System\Container;
use Ziro\System\Kernel;
use Ziro\System\Support\ErrorLogger;
use Ziro\System\Support\StructureValidator;

require __DIR__ . '/../vendor/autoload.php';

Config::boot();
ErrorLogger::boot();
StructureValidator::assertValid('bootstrap');

$container = new Container();
$kernel = new Kernel($container);
$kernel->registerRoutes();

return $kernel;
