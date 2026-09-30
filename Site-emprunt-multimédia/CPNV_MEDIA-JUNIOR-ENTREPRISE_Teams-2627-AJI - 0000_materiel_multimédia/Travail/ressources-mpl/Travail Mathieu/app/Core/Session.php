<?php

namespace App\Core;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function flash(string $key, mixed $value = null): mixed
    {
        if ($value === null) {
            if (!isset($_SESSION['_flash'][$key])) {
                return null;
            }

            $flashValue = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);

            return $flashValue;
        }

        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public function flush(): void
    {
        $_SESSION = [];
        session_destroy();
    }
}

