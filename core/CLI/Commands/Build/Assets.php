<?php

namespace Core\CLI\Commands\Build;

use Core\CLI\Command;
use Core\CLI\ConsoleMessages;

class Assets extends Command
{
    use ConsoleMessages;

    public function run(array $args)
    {
        $this::brand();

        $dev = in_array('--dev', $args);
        $prod = in_array('--prod', $args);

        if ($dev) {
            passthru("node tools/builder/dev.js");
            return;
        }

        if ($prod) {
            passthru("node tools/builder/build.js --prod");
            return;
        }

        passthru("node tools/builder/build.js");
    }
}
