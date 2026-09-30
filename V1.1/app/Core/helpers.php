<?php

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $config;

        if (!$config) {
            $config = require __DIR__ . '/../../config/app.php';
        }

        $segments = explode('.', $key);
        $value = $config;

        foreach ($segments as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }
}

if (!function_exists('app_base_path')) {
    function app_base_path(): string
    {
        static $basePath;

        if ($basePath !== null) {
            return $basePath;
        }

        $configured = rtrim(config('base_url', ''), '/');

        if ($configured !== '') {
            $basePath = $configured === '/' ? '' : $configured;
            return $basePath;
        }

        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = str_replace('\\', '/', dirname($scriptName));
        $dir = rtrim($dir, '/');

        if ($dir === '.' || $dir === '/' || $dir === '\\') {
            $dir = '';
        }

        $basePath = $dir;

        return $basePath;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        $basePath = rtrim(app_base_path(), '/');
        $prefix = $basePath !== '' ? $basePath : '';

        return $prefix . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('route')) {
    function route(string $path = '/'): string
    {
        $basePath = rtrim(app_base_path(), '/');
        $normalized = '/' . ltrim($path, '/');

        if ($basePath === '') {
            return $normalized;
        }

        return $basePath . $normalized;
    }
}

if (!function_exists('currentUser')) {
    /**
     * Get the current authenticated user with fresh data from database
     * 
     * @param bool $refresh Whether to refresh from database (default: true)
     * @return array|null User data or null if not authenticated
     */
    function currentUser(bool $refresh = true): ?array
    {
        $session = new \App\Core\Session();
        $user = $session->get('user');

        if (!$user) {
            return null;
        }

        // Refresh user data from database to get latest info
        if ($refresh) {
            try {
                $userService = new \App\Services\UserService();
                $freshUser = $userService->findById($user['id']);

                if ($freshUser) {
                    $session->set('user', $freshUser);
                    return $freshUser;
                }
            } catch (\Exception $e) {
                // If refresh fails, return cached user
            }
        }

        return $user;
    }
}

