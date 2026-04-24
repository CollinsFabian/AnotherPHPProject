<?php

namespace Ziro\Services\Auth;

class JwtService
{
    public static function generate(array $payload): string
    {
        $header = self::base64UrlEncode((string) json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload['exp'] = time() + 3600;

        $body = self::base64UrlEncode((string) json_encode($payload));
        $signature = self::base64UrlEncode(hash_hmac('sha256', "$header.$body", self::secret(), true));

        return "$header.$body.$signature";
    }

    public static function verify(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $body, $signature] = $parts;

        $valid = self::base64UrlEncode(hash_hmac('sha256', "$header.$body", self::secret(), true));
        if (!hash_equals($valid, $signature)) return null;

        $payload = json_decode(self::base64UrlDecode($body), true);
        if (!is_array($payload) || !isset($payload['exp']) || $payload['exp'] < time()) return null;

        return $payload;
    }

    protected static function secret(): string
    {
        return (string) config('APP_JWT_SECRET', 'very-secured-key');
    }

    protected static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    protected static function base64UrlDecode(string $value): string
    {
        return (string) base64_decode(strtr($value, '-_', '+/'));
    }
}
