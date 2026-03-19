<?php

function base_path(string $path = ''): string
{
    return dirname(__DIR__) . '/' . $path;
}

function redirect(string $url)
{
    return new \Core\Http\Response()->redirect($url);
}

function to(string $url)
{
    return redirect($url);
}

function view(string $view, array $data = [], string $layout = "layouts/main")
{
    return \Core\View\View::render($view, $data, $layout);
}
