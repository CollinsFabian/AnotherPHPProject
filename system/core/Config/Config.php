<?php

declare(strict_types=1);

namespace Ziro\System\Config;

class Config
{
    protected static array $loaded = [];

    public static function boot(): void
    {
        if (self::$loaded !== []) {
            return;
        }

        $envPath = base_path('.env');
        if (!is_file($envPath)) {
            return;
        }

        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");

            self::$loaded[$name] = $value;
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::boot();

        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }

        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }

        return self::$loaded[$key] ?? $default;
    }
}
