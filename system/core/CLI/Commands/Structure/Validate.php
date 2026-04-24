<?php

namespace Ziro\System\CLI\Commands\Structure;

use Ziro\System\CLI\Command;
use Ziro\System\CLI\ConsoleMessages;
use Ziro\System\Support\StructureValidator;

class Validate extends Command
{
    use ConsoleMessages;

    public function run(array $args)
    {
        $this::brand();

        $failures = StructureValidator::validate();

        if ($failures !== []) {
            foreach ($failures as $failure) {
                $this->errorM($failure);
            }

            exit(1);
        }

        $this->successM('Framework structure is valid.');
    }
}
