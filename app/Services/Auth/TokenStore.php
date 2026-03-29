<?php

namespace App\Services\Auth;

class TokenStore
{
    protected static array $validKeys = [
        'abc1234',
        'rxyz789'
    ];

    public static function validate(string $key): bool
    {
        return in_array($key, self::$validKeys, true);
    }
}
