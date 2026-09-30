<?php

namespace App\Core;

class BaseController
{
    protected View $view;
    protected Session $session;

    public function __construct()
    {
        $this->view = new View();
        $this->session = new Session();
    }

    protected function render(string $template, array $data = [], ?string $layout = 'layouts/main'): void
    {
        $this->view->render($template, $data, $layout);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . route($path));
        exit;
    }

    protected function requireAuth(array $roles = []): ?array
    {
        $user = $this->session->get('user');

        if (!$user) {
            $this->session->flash('error', 'Veuillez vous connecter pour accéder à cette page.');
            $this->redirect('/login');
        }

        // Refresh user data from database to get latest roles and status
        $user = $this->refreshUserData($user['id']);

        if (!$user) {
            // User no longer exists, logout
            $this->session->flush();
            $this->session->flash('error', 'Votre compte n\'existe plus. Veuillez contacter un administrateur.');
            $this->redirect('/login');
        }

        if ($roles && !array_intersect($roles, $user['roles'])) {
            $this->session->flash('error', 'Vous n\'avez pas les droits suffisants.');
            $this->redirect('/');
        }

        return $user;
    }

    protected function refreshUserData(string $userId): ?array
    {
        try {
            // Import UserService dynamically to avoid circular dependencies
            $userService = new \App\Services\UserService();
            $freshUser = $userService->findById($userId);

            if ($freshUser) {
                // Update session with fresh data
                $this->session->set('user', $freshUser);
                return $freshUser;
            }

            return null;
        } catch (\Exception $e) {
            // If refresh fails, return cached user data
            return $this->session->get('user');
        }
    }
}

