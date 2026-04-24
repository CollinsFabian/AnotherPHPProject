<?php

namespace Core\CLI\Commands\Make;

use Core\CLI\Command;
use Core\CLI\ConsoleMessages;

class Controller extends Command
{
    use ConsoleMessages;

    public function run(array $args)
    {
        $this::brand();

        $name = $args[0] ?? null;

        if (!$name) {
            $this::errorM("Controller name required");
            return;
        }

        $name = str_replace(['/', '\\'], '', $name);
        $path = __DIR__ . "/../../../../app/Controllers/Api/";

        if (!is_dir($path)) mkdir($path, 0777, true);

        $filepath = $path . "{$name}.php";

        if (file_exists($filepath)) {
            $this::errorM("Controller already exists");
            return;
        }


        $stub = file_get_contents(__DIR__ . "/../../stubs/controller/_className.php");
        $stub = str_replace("_className", $name, $stub);

        file_put_contents($filepath, $stub);
        $this::successM("API controller created: {$name}");
    }
}
