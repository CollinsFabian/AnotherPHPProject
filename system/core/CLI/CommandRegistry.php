<?php

namespace Ziro\System\CLI;

class CommandRegistry
{
    use ConsoleMessages;
    public array $commands = [];

    public function register(string $name, string $class): void
    {
        $this->commands[$name] = $class;
    }

    public function handle(array $argv)
    {
        $command = $argv[1] ?? null;

        if (!$command) {
            $this::normalM("Available commands");

            foreach ($this->commands as $name => $class) $this::normalM(" $name");
            return;
        }

        if (!isset($this->commands[$command])) {
            $this::errorM("Unknown command: $command\n");
            return;
        }

        $class = $this->commands[$command];
        if (!class_exists($class)) {
            $this::errorM("Command class not found: $class\n");
            return;
        }

        $instance = new $class;
        if (!method_exists($instance, "run")) {
            $this::errorM("Invalid command structure\n");
            return;
        }

        $instance->run(array_slice($argv, 2));
    }
}
