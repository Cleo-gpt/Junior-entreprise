<?php

namespace App\Services;

use App\Core\Session;

class AuthService
{
    private array $config;
    private Session $session;
    private UserService $users;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../../config/app.php';
        $this->session = new Session();
        $this->users = new UserService();
    }

    public function attemptLogin(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email, true);

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        if (($user['status'] ?? 'pending') !== 'active') {
            throw new \RuntimeException('Votre compte est en attente de validation par un responsable.');
        }

        $this->session->regenerate();
        unset($user['password']);
        $this->session->set('user', $user);

        return true;
    }

    public function register(string $name, string $email, string $password, array $roles = []): array
    {
        $email = strtolower(trim($email));
        $name = trim($name);
        $password = (string) $password;

        if ($name === '') {
            throw new \InvalidArgumentException('Le nom est obligatoire.');
        }

        if ($password === '') {
            throw new \InvalidArgumentException('Le mot de passe est obligatoire.');
        }

        $allowedDomain = $this->config['auth']['allowed_domain'];
        if (!str_ends_with($email, $allowedDomain)) {
            throw new \InvalidArgumentException('L’adresse email doit se terminer par ' . $allowedDomain);
        }

        if ($this->users->findByEmail($email)) {
            throw new \InvalidArgumentException('Un compte existe déjà avec cette adresse email.');
        }

        $isAllowed = $this->users->isAllowedEmail($email);

        if (!$roles) {
            $roles = $isAllowed ? ['etudiant'] : $this->config['auth']['default_roles'];
        }

        $user = $this->users->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'status' => $isAllowed ? 'active' : 'pending',
        ], $roles);

        return $user;
    }

    public function logout(): void
    {
        $this->session->flush();
    }

    public function user(): ?array
    {
        return $this->session->get('user');
    }

    public function ensureAdminExists(): void
    {
        $this->users->ensureAdminExists();
    }
}

