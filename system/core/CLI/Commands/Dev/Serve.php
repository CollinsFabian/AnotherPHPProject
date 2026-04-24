<?php

namespace Ziro\System\CLI\Commands\Dev;

use Ziro\System\CLI\Command;
use Ziro\System\CLI\ConsoleMessages;

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

        $publicPath = base_path('public');
        if (!$publicPath) exit("Public directory not found");
        $routerScript = base_path('system/core/Support/dev-server-router.php');
        if (!file_exists($routerScript)) exit("Dev server router not found");

        echo "Server running at \033[1;7;36mhttp://$host:$port\033[0m\n";
        echo "Serving from: \033[2;33m$publicPath\033[0m\n\n";

        $cmd = "php -S $host:$port -t $publicPath $routerScript";

        passthru($cmd);
    }
}
