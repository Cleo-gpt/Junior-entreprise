<?php

return [
    'name' => 'CPNV Gestion de Matériel',
    // Laisser vide pour auto-détection (dirname($_SERVER['SCRIPT_NAME'])).
    // Renseigner manuellement lors du déploiement si besoin (ex: '/gestmat').
    'base_url' => '',
    'storage' => [
        'path' => __DIR__ . '/../storage',
    ],
    // Sur le serveur, les accès viennent de config/database.local.php (ignoré par Git).
    // Sans ce fichier, on utilise la base XAMPP locale.
    'database' => file_exists(__DIR__ . '/database.local.php')
        ? require __DIR__ . '/database.local.php'
        : [
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'cpnv_gestmat',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
        ],
    'mail' => [
        'driver' => 'log', // log | smtp
        'from_address' => 'no-reply@cpnv-gestmat.ch',
        'from_name' => 'Gestion de Matériel',
        'smtp' => [
            'transport' => 'mail', // mail | custom
            'host' => 'smtp.example.com',
            'port' => 587,
            'username' => 'user@example.com',
            'password' => 'change-me',
            'encryption' => 'tls',
        ],
    ],
    'auth' => [
        'allowed_domain' => '@eduvaud.ch',
        'default_roles' => ['etudiant'],
        'role_labels' => [
            'admin' => 'Administrateur',
            'responsable' => 'Responsable de matériel',
            'enseignant' => 'Enseignant',
            'etudiant' => 'Étudiant',
        ],
    ],
];

