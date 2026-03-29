<?php

namespace Core\CLI;

abstract class Command
{
    abstract public function run(array $args);
}
