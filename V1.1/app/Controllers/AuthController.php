<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Services\AuthService;

class AuthController extends BaseController
{
    private AuthService $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new AuthService();
    }

    public function showLogin(): void
    {
        if ($this->auth->user()) {
            $this->redirect('/');
        }

        $this->render('auth/login');
    }

    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        try {
            $success = $this->auth->attemptLogin($email, $password);
        } catch (\RuntimeException $e) {
            $this->session->flash('error', $e->getMessage());
            $this->redirect('/login');
            return;
        }

        if (!$success) {
            $this->session->flash('error', 'Identifiants incorrects.');
            $this->redirect('/login');
            return;
        }

        $this->session->flash('success', 'Connexion réussie.');
        $this->redirect('/');
    }

    public function showRegister(): void
    {
        $this->render('auth/register');
    }

    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirmation'] ?? '';

        if ($password !== $confirm) {
            $this->session->flash('error', 'Les mots de passe ne correspondent pas.');
            $this->redirect('/register');
        }

        try {
            $user = $this->auth->register($name, $email, $password);
        } catch (\InvalidArgumentException $e) {
            $this->session->flash('error', $e->getMessage());
            $this->redirect('/register');
            return;
        }

        $this->session->flash(
            'success',
            $user['status'] === 'active'
                ? 'Inscription validée, vous pouvez vous connecter.'
                : 'Inscription enregistrée. Un responsable validera votre compte.'
        );
        $this->redirect('/login');
    }

    public function logout(): void
    {
        $this->auth->logout();
        $this->session->flash('success', 'Vous êtes déconnecté.');
        $this->redirect('/login');
    }
}

