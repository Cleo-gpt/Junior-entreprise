# Mise en ligne sur eleves.mediamatique.ch

Adresse du site une fois en ligne : **https://cforclaz.eleves.mediamatique.ch/emprunt/public/**

Contenu de ce dossier :

| Élément | Rôle |
|---|---|
| `emprunt/` | le site de Mathieu prêt à envoyer : configuration pour le serveur de l'école, `.htaccess` de protection, sans `database/`, `docs/` ni les anciens fichiers JSON |
| `base_pour_serveur_ecole.sql` | la base complète (comptes, inventaire mis à jour depuis l'Excel), sans `CREATE DATABASE` ni `USE` |
| `racine-www/.htaccess` | seulement si le site est mis directement dans `www/` au lieu de `www/emprunt/` |
| `sous-dossier/.htaccess` | modèle, déjà copié dans `emprunt/` |

## 1. Nettoyer le serveur (FileZilla)

Supprimer `www/Travail TPI/` : son nom contient une espace (toutes les pages répondraient « 404 - Page non trouvée ») et il laisse télécharger `storage/users.json` et les scripts SQL.

## 2. Envoyer le site

1. Dans FileZilla, menu Serveur > **Forcer l'affichage des fichiers cachés** (sinon les deux `.htaccess` ne partent pas).
2. Glisser le dossier `emprunt/` dans `www/`.
3. Clic droit sur `www/emprunt/storage/` > Droits d'accès au fichier > `775`, en cochant « Récursion dans les sous-dossiers ». Pareil pour `www/emprunt/public/uploads/`.

## 3. Importer la base

1. http://pma.eleves.mediamatique.ch, sélectionner la base `cforclaz` à gauche, onglet **Importer**.
2. Choisir `base_pour_serveur_ecole.sql`, Exécuter.

Ne pas importer les fichiers SQL de Mathieu : ils essaient de créer la base `cpnv_gestmat`, ce que le compte élève n'a pas le droit de faire.

## 4. Mettre le mot de passe MySQL

Sur le serveur, `www/emprunt/config/app.php` (FileZilla : clic droit > Afficher/Éditer) : remplacer `MOT_DE_PASSE_MYSQL` par le mot de passe MySQL du compte AutoWeb. Si le nom de la base affiché dans phpMyAdmin n'est pas `cforclaz`, le corriger aussi (`database` et `username`).

Le faire sur le serveur seulement : le vrai mot de passe ne doit pas finir dans le dépôt GitHub.

## 5. Vérifier

- Ouvrir https://cforclaz.eleves.mediamatique.ch/emprunt/ : on doit arriver sur la page de connexion.
- Se connecter avec `admin@eduvaud.ch` / `Admin@123`, puis **changer ce mot de passe tout de suite** : il est écrit dans le README de Mathieu.
- https://cforclaz.eleves.mediamatique.ch/emprunt/storage/ doit répondre « Forbidden ».
- En cas d'erreur 500 : télécharger `logs/error.log` (à côté de `www/`) et lire les dernières lignes. Si elles parlent de `syntax error`, la version de PHP du compte est trop ancienne (il faut 8.2) : demander à l'enseignant de la changer.
