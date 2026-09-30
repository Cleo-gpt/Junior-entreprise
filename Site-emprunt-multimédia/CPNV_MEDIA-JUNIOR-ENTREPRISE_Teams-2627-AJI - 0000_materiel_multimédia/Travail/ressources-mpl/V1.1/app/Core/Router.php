<?php

namespace App\Core;

use function app_base_path;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => [],
    ];

    public function get(string $path, callable|array $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function delete(string $path, callable|array $handler): void
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        $basePath = rtrim(app_base_path(), '/');
        if ($basePath && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        $path = '/' . ltrim($path, '/');

        if ($path === '/index.php') {
            $path = '/';
        }

        $path = rtrim($path, '/') ?: '/';
        $routes = $this->routes[$method] ?? [];

        if (!array_key_exists($path, $routes)) {
            http_response_code(404);
            echo '404 - Page non trouvée';
            return;
        }

        $handler = $routes[$path];

        if (is_callable($handler)) {
            $handler();
            return;
        }

        if (is_array($handler) && count($handler) === 2) {
            [$class, $methodName] = $handler;
            if (class_exists($class)) {
                $instance = new $class();
                if (method_exists($instance, $methodName)) {
                    $instance->{$methodName}();
                    return;
                }
            }
        }

        http_response_code(500);
        echo '500 - Gestionnaire de route invalide';
    }

    private function addRoute(string $method, string $path, callable|array $handler): void
    {
        $path = rtrim($path, '/') ?: '/';
        $this->routes[$method][$path] = $handler;
    }
}

