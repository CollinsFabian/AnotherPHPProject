<?php

namespace Core\CLI;

trait ConsoleMessages
{
    public static function successM($msg)
    {
        sleep(1);
        echo "\e[32m";
        $chars = str_split($msg);

        foreach ($chars as $c) {
            echo $c;
            usleep(33000);
        }

        echo "\e[0m\n";
    }

    public static function errorM($msg)
    {
        echo "\e[31m$msg\e[0m\n";
    }

    public static function normalM($msg)
    {
        echo "\e[33m$msg\e[0m\n";
    }

    public static function brand()
    {
        usleep(100000);
        echo "\e[1;38;5;75m[ Z I R O ]\e[0m \e[2m" . date("H:i:s") . "\e[0m\n\n";
    }
}
