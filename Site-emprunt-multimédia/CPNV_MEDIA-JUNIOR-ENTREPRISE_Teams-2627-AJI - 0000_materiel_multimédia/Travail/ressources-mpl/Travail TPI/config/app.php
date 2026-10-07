<?php

return [
    'name' => 'CPNV Gestion de Matériel',
    // Laisser vide pour auto-détection (dirname($_SERVER['SCRIPT_NAME'])).
    // Renseigner manuellement lors du déploiement si besoin (ex: '/gestmat').
    'base_url' => '',
    'storage' => [
        'path' => __DIR__ . '/../storage',
    ],
    'database' => [
        'host' => 'loutre.mysql',
        'port' => 3306,
        'database' => 'c167_studiomultimedia',
        'username' => 'c167_studiomultimedia',
        'password' => 'JND6!spms',
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

