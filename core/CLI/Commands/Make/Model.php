<?php

namespace Core\CLI\Commands\Make;

use Core\CLI\Command;
use Core\CLI\ConsoleMessages;

class Model extends Command
{
    use ConsoleMessages;

    public function run(array $args)
    {
        $this::brand();

        throw new \Exception('Not implemented');
    }
}
