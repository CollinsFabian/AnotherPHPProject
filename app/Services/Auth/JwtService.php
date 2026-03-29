<?php

namespace App\Services\Auth;

class JwtService
{
    protected static string $secret = 'very-secured-key';

    public static function generate(array $payload): string
    {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload['exp'] = time() + 3600;

        $body = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', "$header.$body", self::$secret);

        return "$header.$body.$signature";
    }

    public static function verify(string $token): ?array
    {
        [$header, $body, $signature] = explode('.', $token);

        $valid = hash_hmac('sha256', "$header.$body", self::$secret);
        if (!hash_equals($valid, $signature)) return null;

        $payload = json_decode(base64_decode($body), true);
        if ($payload['exp'] < time()) return null;

        return $payload;
    }
}
