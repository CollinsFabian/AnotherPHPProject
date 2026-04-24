<?php

namespace Ziro\Services\Auth;

class TokenStore
{
    public static function validate(string $key): bool
    {
        $configured = (string) config('APP_API_KEYS', 'abc1234,rxyz789');
        $validKeys = array_filter(array_map('trim', explode(',', $configured)));

        return in_array($key, $validKeys, true);
    }
}
