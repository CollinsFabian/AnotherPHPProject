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

    public function redirect(string $url, $status = 302): static
    {
        $this->content = "";
        $this->status = $status;
        $this->headers['Location'] = $url;
        return $this;
    }
}
