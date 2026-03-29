<?php

declare(strict_types=1);

namespace Core\Http;

class Response
{
    public function __construct(protected string $content = "", protected int $status = 200, protected array $headers = []) {}

    public function send()
    {
        http_response_code($this->status);
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
        echo $this->content;
    }

    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');

        echo json_encode($data);
        exit;
    }

    public static function redirect(string $url, $status = 302): static
    {
        http_response_code($status);
        header("Location: {$url}");
        exit;
    }
}
