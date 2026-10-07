<?php

namespace App\Core;

class View
{
    public function render(string $template, array $data = [], ?string $layout = 'layouts/main'): void
    {
        $templateFile = __DIR__ . '/../Views/' . str_replace('.', '/', $template) . '.php';

        if (!file_exists($templateFile)) {
            throw new \RuntimeException("Vue introuvable : {$template}");
        }

        extract($data);

        if ($layout) {
            $layoutFile = __DIR__ . '/../Views/' . str_replace('.', '/', $layout) . '.php';

            if (!file_exists($layoutFile)) {
                throw new \RuntimeException("Layout introuvable : {$layout}");
            }

            ob_start();
            include $templateFile;
            $content = ob_get_clean();
            include $layoutFile;
            return;
        }

        include $templateFile;
    }
}

