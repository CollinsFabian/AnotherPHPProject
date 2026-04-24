<?php

namespace Ziro\Support;

class Context
{
    protected static array $store = [];

    public static function set(string $key, $value): void
    {
        self::$store[$key] = $value;
    }

    public static function get(string $key)
    {
        return self::$store[$key] ?? null;
    }
}
