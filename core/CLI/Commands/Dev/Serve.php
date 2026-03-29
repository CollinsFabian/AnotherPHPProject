<?php

namespace Core\CLI\Commands\Dev;

use Core\CLI\Command;
use Core\CLI\ConsoleMessages;

class Serve extends Command
{
    use ConsoleMessages;

    public function run(array $args)
    {
        $this::brand();

        $host = "localhost";
        $port = "3000";

        foreach ($args as $arg) {
            if (str_starts_with($arg, "--port=")) $port = explode('=', $arg)[1];
            if (str_starts_with($arg, "--host=")) $host = explode('=', $arg)[1];
        }

        $publicPath = __DIR__ . "/../../../../public";
        if (!$publicPath) exit("Public directory not found");

        echo "Server running at \033[1;7;36mhttp://$host:$port\033[0m\n";
        echo "Serving from: \033[2;33m$publicPath\033[0m\n\n";

        $cmd = "php -S $host:$port -t $publicPath";

        passthru($cmd);
    }
}
