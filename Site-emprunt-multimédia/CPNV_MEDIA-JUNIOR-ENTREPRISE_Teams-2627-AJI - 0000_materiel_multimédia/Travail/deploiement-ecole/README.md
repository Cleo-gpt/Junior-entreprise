# Mise en ligne sur eleves.mediamatique.ch

Serveur de l'école (AutoWeb). Chaque élève a :

- un dossier `www/` dans son espace FTP : c'est la racine du site ;
- un dossier `logs/` avec `error.log` : les erreurs PHP du site y sont écrites ;
- une base MySQL qui porte **le nom de son identifiant**, gérée sur http://pma.eleves.mediamatique.ch.

## 1. Les fichiers (FileZilla)

1. Envoyer **le contenu** du dossier `Travail Mathieu` dans `www/` : `app/`, `config/`, `public/`, `routes/`, `storage/`, `uploads/` (pas `database/` ni `docs/`).
2. Envoyer aussi `racine-www/.htaccess` de ce dossier dans `www/`.
3. Vérifier que `public/.htaccess` est bien sur le serveur. Les fichiers qui commencent par un point sont cachés : dans FileZilla, menu Serveur > Forcer l'affichage des fichiers cachés.
4. Droits d'écriture (clic droit > Droits d'accès au fichier) : `775` sur `storage/` et ses sous-dossiers, et sur `public/uploads/materials/`.

### Variante : site dans un sous-dossier de `www/`

- Nom de dossier **sans espace ni accent** (ex. `emprunt`) : avec `Travail TPI`, l'adresse contient `%20` et toutes les pages, accueil compris, répondent « 404 - Page non trouvée » (le routeur compare l'adresse encodée au chemin décodé).
- Mettre `sous-dossier/.htaccess` dans ce dossier (pas `racine-www/.htaccess`) : l'adresse `…/emprunt/` renvoie vers `…/emprunt/public/`, et `storage/`, `database/`, `config/`… ne sont plus téléchargeables.

## 2. La base (phpMyAdmin de l'école)

1. Se connecter sur http://pma.eleves.mediamatique.ch, sélectionner **sa** base (à gauche), onglet Importer.
2. Importer `base_pour_serveur_ecole.sql`. Ce fichier ne contient ni `CREATE DATABASE` ni `USE` : il se charge dans la base sélectionnée.

Ne pas importer `database/schema.sql` ni `import_inventory_by_space.sql` de Mathieu : ils essaient de créer la base `cpnv_gestmat`, ce que le compte élève n'a pas le droit de faire.

## 3. La connexion (`www/config/app.php`)

```php
'database' => [
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'IDENTIFIANT',        // nom de la base, visible dans phpMyAdmin
    'username' => 'IDENTIFIANT',        // le même
    'password' => 'MOT DE PASSE MYSQL', // celui reçu avec le compte AutoWeb
    'charset' => 'utf8mb4',
],
```

Modifier ce fichier directement sur le serveur (FileZilla : clic droit > Afficher/Éditer), pas dans le dépôt : le mot de passe ne doit pas finir sur GitHub.

## 4. Vérifier

- Ouvrir l'adresse du site : la page de connexion doit s'afficher.
- Se connecter avec `admin@eduvaud.ch` / `Admin@123`, puis **changer ce mot de passe tout de suite** : le site est public.
- En cas d'erreur 500 ou de page blanche : télécharger `logs/error.log` avec FileZilla et lire les dernières lignes.
- Le site demande PHP 8.2 ou plus récent. Si l'erreur parle de `syntax error`, demander à l'enseignant de changer la version de PHP du compte.
