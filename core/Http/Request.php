<?php

declare(strict_types=1);

namespace Core\Http;

class Request
{
    public string $method;
    public string $uri;

    public static function capture(): self
    {
        $instance = new self();

        $instance->method = $_SERVER['REQUEST_METHOD'] ?? "GET";
        $uri = $_SERVER['REQUEST_URI'] ?? "/";
        if (($pos = strpos($uri, "?")) !== false) $uri = substr($uri, 0, $pos);
        $uri = str_replace("/public/index.php", '', $uri);

        $instance->uri = rtrim($uri, '/') ?: '/';
        return $instance;
    }
}
