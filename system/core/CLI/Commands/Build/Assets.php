<?php

namespace Ziro\System\CLI\Commands\Build;

use Ziro\System\CLI\Command;
use Ziro\System\CLI\ConsoleMessages;

class Assets extends Command
{
    use ConsoleMessages;

    public function run(array $args)
    {
        $this::brand();

        $dev = in_array('--dev', $args);
        $prod = in_array('--prod', $args);
        $builderPath = base_path('system/tools/builder');

        if (!is_dir($builderPath)) {
            $this->errorM('Builder package not found in system/tools/builder');
            return;
        }

        if ($dev) {
            passthru("node system/tools/builder/dev.js", $code);
            exit($code);
        }

        if ($prod) {
            passthru("node system/tools/builder/build.js --prod", $code);
            exit($code);
        }

        passthru("node system/tools/builder/build.js", $code);
        exit($code);
    }
}
