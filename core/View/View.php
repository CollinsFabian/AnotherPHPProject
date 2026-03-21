<?php

declare(strict_types=1);

namespace Core\View;

class View
{
    public static function render(string $view, array $data = [], string $layout = "layouts/main"): string
    {
        $viewPath = base_path("app/Views/" . str_replace('.', '/', $view) . ".php");
        $layoutPath = base_path("app/Views/" . str_replace('.', '/', $layout) . ".php");

        if (!file_exists($viewPath)) throw new \Exception("View '{$view}' not found");

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        ob_start();
        require $layoutPath;
        $output = ob_get_clean();

        return $output;
    }
}
