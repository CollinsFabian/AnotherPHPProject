<?php

namespace Core\CLI\Commands\Make;

use Core\CLI\Command;
use Core\CLI\ConsoleMessages;

class Migration extends Command
{
    use ConsoleMessages;

    public function run(array $args)
    {
        $this::brand();

        $name = $args[0] ?? null;

        if (!$name) {
            $this::errorM("Migration name required");
            return;
        }

        $timestamp = date("Y_m_d_His");

        $path = __DIR__ . "/../../../../database/migrations/";

        if (!is_dir($path)) mkdir($path, 0777, true);
        $filepath = "{$path}{$timestamp}_{$name}.php";

        $stub = file_get_contents(__DIR__ . "/../../stubs/migration.php");
        file_put_contents($filepath, $stub);

        $this::errorM("Migration created: {$filepath}");
    }
}
