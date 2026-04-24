<?php

namespace Ziro\System\CLI;

abstract class Command
{
    abstract public function run(array $args);
}
