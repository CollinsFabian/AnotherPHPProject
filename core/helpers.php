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

