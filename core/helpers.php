<?php

function base_path(string $path = ''): string
{
    return dirname(__DIR__) . '/' . $path;
}

function redirect(string $url)
{
    return \Core\Http\Response::redirect($url);
}

function json(array $data, int $status = 200)
{
    return \Core\Http\Response::json($data, $status);
}

function to(string $url)
{
    return redirect($url);
}

function view(string $view, array $data = [], string $layout = "layouts/main")
{
    return \Core\View\View::render($view, $data, $layout);
}

function asset(string $path): string
{
    if (getenv('APP_ENV') === 'dev') {
        $manifest = json_decode(file_get_contents(__DIR__ . '/../public/manifest.json'), true);
        return '/' . ($manifest[$path] ?? $path);
    }


    static $manifest = null;

    if ($manifest === null) {
        $manifestFile = __DIR__ . '/../public/manifest.json';

        $manifest = file_exists($manifestFile)
            ? json_decode(file_get_contents($manifestFile), true)
            : [];
    }

    return '/' . ($manifest[$path] ?? $path);
}
