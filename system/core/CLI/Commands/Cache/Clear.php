<?php

namespace Ziro\System\CLI\Commands\Cache;

use Ziro\System\Cache\Cache;
use Ziro\System\CLI\Command;
use Ziro\System\CLI\ConsoleMessages;

class Clear extends Command
{
    use ConsoleMessages;

    public function run(array $args)
    {
        $this::brand();
        $this->normalM("Cleaning storage...");

        try {
            $cache = new Cache();
            if ($cache->clear()) $this->successM("Cache storage has been wiped clean.");
            else throw new \ErrorException("Could not fully clear the cache directory.");
        } catch (\Throwable $e) {
            $this->errorM($e->getMessage());
            exit(1);
        }
    }
}
