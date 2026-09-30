# CPNV Gestion de Matériel – Guide de prise en main

Ce document est destiné aux futurs développeurs (humains ou IA) amenés à faire évoluer l’application. Il décrit l’architecture actuelle, les points d’extension clés, ainsi que les étapes pour installer le projet sur un nouvel environnement.

---

## 1. Aperçu rapide

- **Stack principale** : PHP 8.2+, MySQL 8+, Bootstrap 5, JavaScript vanilla (avec Bootstrap bundle).
- **Organisation** : pattern MVC léger (`app/Controllers`, `app/Services`, `app/Views`) associé à un routeur interne (`app/Core/Router.php`).
- **Persistance** :
  - MySQL (`materials`, `users`, `roles`, `student_whitelist`, etc.).
  - JSON (transitoire) pour certaines fonctionnalités encore en chantier (`storage/reservations.json`, `storage/emails`).
- **Front-end** : Layout responsive inspiré de la charte CPNV, situé dans `app/Views` et `public/assets`.
- **Fichiers publics** : `public/` est la racine exposée par Apache/Nginx.

---

## 2. Structure des dossiers

```text
V1/
├─ app/
│  ├─ Controllers/         → Logique des pages (AuthController, AdminController, …)
│  ├─ Core/                → Router, View, Session, Database helpers
│  ├─ Services/            → Accès métier (AuthService, MaterialService, UserService, …)
│  └─ Views/               → Templates PHP (layouts + pages)
├─ config/                 → Configuration globale (`app.php`)
├─ database/               → Scripts SQL (schéma complet, seeds de base)
├─ docs/                   → Documentation développeur (ce fichier)
├─ public/                 → Point d’entrée (`index.php`), assets, uploads
├─ storage/                → Données JSON héritées (réservations, emails de log, …)
└─ uploads/                → Stockage des médias envoyés (couvertures & galeries)
```

---

## 3. Installation locale (XAMPP / LAMP)

1. **Cloner ou copier** le dossier `V1` dans le répertoire web (`htdocs` sur XAMPP).
2. **Configurer Apache** :
   - Point d’entrée : `http://localhost/agm/V1/public/`
   - Activer `mod_rewrite` (`httpd.conf` → `LoadModule rewrite_module modules/mod_rewrite.so`).
3. **Paramétrer PHP** (si nécessaire) :
   - Activer `pdo_mysql` dans `php.ini` (`extension=pdo_mysql`).
   - Vérifier `file_uploads = On`, `upload_max_filesize` et `post_max_size` (>= 10M recommandés).
4. **Créer la base MySQL** :
   - Ouvrir `V1/database/schema.sql` et exécuter le script (phpMyAdmin, MySQL Workbench ou CLI).
   - Le script crée la base `cpnv_gestmat`, les tables (`users`, `roles`, `materials`, etc.), un administrateur par défaut (`admin@eduvaud.ch` / `Admin@123`) et les rôles essentiels.
   - Adapter le nom de la base si besoin (modifier les deux premières instructions du script).
5. **Configurer la connexion** :
   - Éditer `config/app.php` (section `database`) avec l’hôte, l’utilisateur et le mot de passe MySQL de l’environnement.
6. **Renseigner la liste des étudiants autorisés** :
   - La méthode `AuthService::register` vérifie que les emails appartiennent au domaine `@eduvaud.ch` et, si l’adresse figure dans `student_whitelist`, active immédiatement le compte.
   - Insérer les entrées depuis l’ancien fichier JSON si besoin :
     ```sql
     INSERT IGNORE INTO student_whitelist (email) VALUES
       ('alice.durant@eduvaud.ch'),
       ('benoit.leroy@eduvaud.ch'),
       ('carla.moreau@eduvaud.ch');
     ```
   - Les autres inscriptions restent en statut `pending` jusqu’à validation manuelle (fonctionnalité à implémenter côté interface admin).
7. **Lancer l’application** :
   - Naviguer vers `http://localhost/agm/V1/public/`.
   - Se connecter avec `admin@eduvaud.ch / Admin@123` puis changer ce mot de passe.

---

## 4. Fonctionnalités clés & points d’extension

### 4.1 Authentification & gestion des utilisateurs
- `App\Services\AuthService` : logique d’inscription/connexion basée sur `UserService` et la session PHP.
- `App\Services\UserService` :
  - lecture/écriture MySQL (`users`, `roles`, `user_roles`, `student_whitelist`),
  - création d’utilisateurs, affectation des rôles,
  - vérification du compte admin par défaut (`ensureAdminExists()`).
- Interface d’administration (`/admin/users`) pour :
  - créer/supprimer un utilisateur, définir ses rôles et son statut ;
  - gérer la liste blanche (`student_whitelist`) pour autoriser automatiquement les inscriptions d’étudiants (ajout unitaire avec plage de dates ou import CSV `email,start_date,end_date`).
- Pour ajouter un nouveau rôle ou modifier la politique d’activation, intervenir ici.

### 4.2 Matériel & inventaire
- `App\Services\MaterialService` : CRUD côté base (`materials`), stockage des images (couverture + galerie), conversions JSON.
- `App\Controllers\AdminController` :
  - formulaire simple d’ajout/modification de matériel avec gestion unifiée des images (prévisualisation, suppression unitaire/globale, sélection de la couverture),
  - aperçu des éléments existants (tableau).
- `Administration → Gestion des réservations` : permet aux responsables de modifier les demandes, valider le retrait (`checked_out`), la restitution (`returned`) et d’annuler ou supprimer une réservation. Les ajustements de stock sont appliqués automatiquement.
- Les suppressions réalisées depuis l’interface déplacent désormais l’élément dans une **corbeille** (`/admin/trash`). Seuls les administrateurs peuvent restaurer ou effacer définitivement ces éléments.
- **Planification par plage de dates** :
  - la disponibilité est calculée dynamiquement en fonction des réservations existantes (statuts `pending`, `approved`, `checked_out`);
  - le catalogue propose un filtre « du… au… » afin d’anticiper les besoins à venir, y compris lorsque le stock du jour est épuisé.
- **Matériels numérotés** :
  - activez le mode « numéroté » dans le formulaire d’ajout/édition et renseignez un identifiant par exemplaire ;
  - lors du retrait, les responsables doivent sélectionner les identifiants remis afin de tracer précisément quel appareil a été prêté et éviter les doublons sur une même période.
- **Prix indicatif** :
  - chaque matériel peut stocker un montant de remplacement (non visible côté étudiant) pour faciliter l’évaluation en cas de perte ou de casse.
- **Réservations multi-matériels** :
  - Les utilisateurs peuvent constituer un panier (`/cart`) depuis le catalogue, ajuster les quantités et valider une réservation groupée.
  - Les réservations JSON contiennent désormais un tableau `items` (`material_id`, `quantity`). Le champ historique `material_id` reste présent pour compatibilité (premier item).
  - Côté admin, les stocks sont réajustés en fonction des statuts (en attente/retirée/restaurée) pour chacun des matériels.
- `Mon panier` : page utilisateur permettant de regrouper plusieurs matériels avant de confirmer une réservation unique (vérification du stock lors du checkout).
- **Prolongations côté utilisateur** :
  - Chaque réservation peut être prolongée une seule fois par l’utilisateur (max. 7 jours) si aucun autre prêt ne s’y oppose dans la fenêtre choisie ;
  - le formulaire depuis le tableau de bord propose directement la dernière date possible ;
  - au-delà, seules les personnes disposant des rôles `admin` ou `responsable` peuvent ajuster la réservation via l’espace d’administration.
- `App\Views\materials/index.php` : catalogue côté étudiant, badge indiquant le nombre d’images disponibles.

### 4.3 Notifications & emails
- `App\Services\NotificationService` : deux drivers disponibles (`log` par défaut, `smtp` si configuré).
  - En mode log, les emails sont sauvegardés sous `storage/emails/` (fichier JSON + `.txt` lisible).
  - En mode SMTP (`config/app.php` → `mail.driver = 'smtp'`), remplir les informations du serveur courriel.

### 4.4 Réservations
- Actuellement persistées dans `storage/reservations.json` via `ReservationService`.
- Une table `reservations` figure déjà dans `database/schema.sql` pour préparer la migration complète vers MySQL.
- Pour finaliser cette transition : remplacer `ReservationService` par une version SQL et mettre à jour les contrôleurs associés (`DashboardController`, `ReservationController`).

---

## 5. Fichiers & dossiers importants pour une IA

| Zone | Fichiers à consulter | Description |
|------|----------------------|-------------|
| Routage | `public/index.php`, `routes/web.php` | Déclare les routes HTTP et l’initialisation globale. |
| Services métier | `app/Services/*.php` | Contiennent la logique d’accès aux données et aux règles métiers. |
| Vues | `app/Views/` | Templates PHP (utilisent `route()` et `asset()` pour générer les URLs). |
| Assets | `public/assets/css/style.css`, `public/assets/js/app.js` | Styles et JS spécifiques à l’application. |
| Uploads | `public/uploads/materials` | Répertoire d’upload (créé automatiquement). |
| Config | `config/app.php` | Paramétrage central (base, mail, règles d’authentification). |
| Documentation | `docs/README.md` (ce fichier) | Guide d’architecture et d’installation. |

Astuce : pour localiser rapidement la logique d’un écran, chercher le contrôleur correspondant dans `routes/web.php`, puis remonter vers la vue et le service associé.

---

## 6. Procédure de déploiement sur un nouveau serveur

1. Copier les sources (`V1/`) sur le nouveau serveur.
2. Configurer le VirtualHost pour pointer vers `V1/public`.
3. Exécuter le script SQL (`database/schema.sql`) sur le MySQL cible.
4. Mettre à jour le fichier `config/app.php` avec les paramètres de l’environnement (base, SMTP, etc.).
5. Ajouter les adresses autorisées dans `student_whitelist`.
6. Vérifier les permissions d’écriture sur `public/uploads/materials` et `storage/`.
7. Tester rapidement :
   - connexion en admin (`/login`),
   - ajout d’un matériel avec images,
   - consultation du catalogue,
   - génération d’un email (prévisualisation). 

---

## 7. Pistes d’amélioration

- Migrer les réservations et signatures vers MySQL (structure prête dans le script).
- Ajouter une interface d’approbation des comptes `pending` pour les responsables.
- Implémenter un scheduler (cron) pour les rappels automatiques de restitution (utiliser `NotificationService`). 
- Gérer des statuts supplémentaires : matériel endommagé, historique des imports.
- Intégrer des tests automatisés (`PHPUnit`) pour les services critiques (auth, matériel, réservations).

---

## 8. Ressources complémentaires

- **Charte graphique de référence** : [Site du CPNV](https://www.cpnv.ch/) (couleurs et typographies).
- **Bootstrap 5** : https://getbootstrap.com/
- **Documentation PHP** : https://www.php.net/manual/

---

Bon développement ! Toute modification importante devrait être décrite dans ce dossier (`docs/`). Pensez à maintenir le script SQL synchronisé avec les évolutions de la base de données et à mettre à jour la documentation lors de changements structurels. 
# Gestion du matériel CPNV – Documentation technique

## Vue d’ensemble
Application PHP (MVC léger) permettant de gérer l’inventaire multimédia du CPNV : catalogue, réservations étudiantes, notifications et administration. Le projet cible un hébergement type XAMPP (Apache/PHP 8/MySQL) et s’appuie sur Bootstrap 5 pour l’interface inspirée de [cpnv.ch](https://www.cpnv.ch/).

Technologies principales :
- PHP 8.2+, Apache 2.4 (mod_rewrite activé)
- MySQL/MariaDB (PDO, UTF-8)
- Bootstrap 5 + Sass, JavaScript vanilla
- Stockage complémentaire JSON (reservations/emails) et uploads locaux

## Structure du projet
```
V1/
├── app/
│   ├── Controllers/    # Logique HTTP (Auth, Dashboard, Materials, Admin…)
│   ├── Core/           # Router, View, Session, helpers, Database (PDO)
│   ├── Services/       # Couche métier (Auth, User, Material, Reservation, Notifications)
│   └── Views/          # Templates PHP (layouts, pages)
├── config/             # Fichiers de configuration applicative
├── docs/               # Documentation et scripts SQL
├── public/             # Point d’entrée web (index.php, assets, uploads)
├── routes/             # Déclaration des routes web
├── storage/            # Données JSON (réservations, emails log, signatures)
└── uploads/            # Fichiers téléversés par les utilisateurs
```

Conventions :
- Les contrôleurs restent minces et délèguent aux services (ne pas mettre de SQL direct dans un contrôleur).
- Les services manipulent la donnée (MySQL ou JSON) et renvoient des tableaux prêts à l’emploi pour les vues.
- Les vues n’effectuent aucune logique métier, uniquement de l’affichage.

## Flux applicatifs
- **Authentification** : `AuthController` s’appuie sur `AuthService` et `UserService` (MySQL). Les comptes sont activés automatiquement si l’adresse figure dans `allowed_emails`, sinon ils restent « pending » jusqu’à validation manuelle.
- **Matériel** : `MaterialService` lit/écrit dans la table `materials`. Les images de couverture et galeries sont gérées via uploads locaux (`public/uploads/materials`) ou URLs.
- **Réservations** : `ReservationService` persiste encore en JSON (`storage/reservations.json`). Une migration vers MySQL est prévue : voir `docs/schema.sql` pour la structure proposée.
- **Notifications** : `NotificationService` log les emails dans `storage/emails/` (mode `log`) ou passe par SMTP selon `config/app.php`.
- **Administration** : `/admin/tools` regroupe l’ajout de matériel (formulaire structuré) et la prévisualisation des emails. L’accès est réservé aux rôles `admin` & `responsable`.

## Base de données
Configuration dans `config/app.php` (`database.*`). Utiliser l’encodage `utf8mb4`.  
Le script `docs/schema.sql` automatise la création de la base et des tables principales :
- `users` : comptes applicatifs (status `pending|active|disabled`)
- `user_roles` : association N-N entre utilisateurs et rôles (`admin`, `responsable`, `enseignant`, `etudiant`…)
- `allowed_emails` : adresses autorisées à être activées immédiatement (liste importée depuis l’école)
- `materials` : inventaire (quantités, couverture, galerie JSON)
- `reservations` / `reservation_items` : structure de migration pour remplacer le stockage JSON
- `email_logs` : table optionnelle si l’on souhaite historiser les envois (actuellement logs fichiers)

> **Important**
> - Activer `pdo_mysql` dans `php.ini` et redémarrer Apache après modification.
> - Vérifier `upload_max_filesize` et `post_max_size` pour autoriser les uploads de galeries.
> - Le dossier `public/uploads/materials` doit être accessible en écriture par le serveur web.
> - Le champ `materials.gallery` utilise le type `JSON` (MySQL ≥ 5.7 / MariaDB ≥ 10.2.7). Si la version cible ne le supporte pas, remplacer par `LONGTEXT` et encoder/décoder manuellement côté PHP.

## Mise en place rapide (dev local XAMPP)
1. Cloner/copier le dossier `V1` dans `C:\xampp\htdocs\agm` (ou chemin équivalent).
2. Créer la base MySQL : `mysql -u root -p < docs/schema.sql` (adapter si besoin).
3. Ajuster `config/app.php` : `database` (host, port, database, username, password), `mail` (driver `log` par défaut).
4. Vérifier que `RewriteModule` est activé (`httpd.conf`). Le `.htaccess` dans `public/` redirige les URLs vers `index.php`.
5. Accéder à `http://localhost/agm/V1/public/`. Un compte admin (`admin@eduvaud.ch` / `Admin@123`) est auto-créé si absent. **Changez ce mot de passe immédiatement en production.**

## Gestion des utilisateurs
- `UserService` centralise l’accès MySQL (`app/Services/UserService.php`).
- Activation immédiate : ajouter les adresses autorisées dans `allowed_emails` (via SQL ou en important la liste). Exemple SQL :
  ```sql
  INSERT INTO allowed_emails (email) VALUES
    ('alice.durant@eduvaud.ch'),
    ('benoit.leroy@eduvaud.ch');
  ```
- Validation manuelle : mettre `status = 'active'` et attribuer les rôles dans `user_roles`.
- Pour créer un nouveau rôle, il suffit de l’insérer en base (aucune table de référence obligatoire, mais gardez la cohérence avec `config/app.php['auth']['role_labels']`). Les badges affichés se basent sur ces libellés.

## Gestion du matériel
- Ajout via `/admin/tools` : uploads multiples, URLs librement saisissables. Les fichiers sont stockés sous `public/uploads/materials/` avec un nom unique.
- Le catalogue public utilise l’image de couverture (`cover_image`). À défaut, la première image de la galerie est utilisée, puis un fallback (`assets/img/sample-camera.svg`).
- `MaterialService` (MySQL) se trouve dans `app/Services/MaterialService.php`. Les contrôleurs ou services tiers doivent passer par cette classe pour toute manipulation.

## Notifications / emails
- Mode développement (`log`) : chaque envoi génère un JSON (`storage/emails/emails.json`) et un fichier texte daté (`storage/emails/mail_*.txt`).
- Pour passer en SMTP, renseigner `config/app.php['mail']` et définir `driver => 'smtp'`. Le service utilise `mail()` lorsqu’`smtp.transport = mail`. Pour un vrai SMTP authentifié, implémentez l’envoi via une librairie dédiée (PHPMailer, Symfony Mailer…) dans `NotificationService::sendViaSmtp`.

## Scripts SQL
- `docs/schema.sql` : création complète de la base `cpnv_gestmat`, tables, index, données de référence.
- Utiliser ce fichier pour initialiser une instance (staging/prod) ou régénérer rapidement la base en local.
- Pour migrer depuis le stockage JSON existant (par ex. `storage/users.json`, `storage/materials.json`), importer les données avec un script ponctuel ou des requêtes INSERT adaptées. `materials.json` peut servir de source (`INSERT` direct dans la table `materials`).

## Bonnes pratiques & repères pour les prochaines itérations (IA ou dev humain)
- Toujours privilégier les services existants (`UserService`, `MaterialService`, `ReservationService`, `NotificationService`) pour encapsuler la logique métier.
- Les contrôleurs doivent rester « fins » : validation simple, appel de service, rendu de vue.
- Ajouter de nouvelles routes via `routes/web.php`, puis créer l’action correspondante dans un contrôleur dédié.
- Les vues se trouvent dans `app/Views/`. Respecter la convention `dossier/fichier.php` et utiliser le layout principal `layouts/main.php`.
- Toute nouvelle donnée persistante devrait passer par MySQL ; `storage/*.json` doit être considéré comme temporaire ou legacy.
- Documenter chaque évolution dans `docs/` (ajout de fichiers README annexes, scripts SQL, guides d’utilisation). Garder ce dossier à jour facilite la reprise par une future IA.
- Pour introduire des tests automatisés, placer le code dans un dossier `tests/` et envisager PHPUnit. Documenter le setup dans `docs/tests.md`.

## Tests & validation
- Tests manuels recommandés après modification :
  - Inscription d’un étudiant autorisé (`allowed_emails`) → activation immédiate et connexion.
  - Inscription d’un nouvel email non autorisé → statut pending + message lors de la connexion.
  - Ajout de matériel avec upload + galerie → affichage correct dans l’inventaire et le catalogue public.
  - Réservation → décrémentation du stock et apparition dans le tableau de bord.
  - Génération d’un email → apparition d’un fichier dans `storage/emails/`.
- Pensez à vérifier les erreurs PHP (`logs/`) et MySQL en cas de comportement inattendu.

## Comptes et accès par défaut
- **Administrateur** : `admin@eduvaud.ch` / `Admin@123` (créé au premier démarrage). Changer immédiatement ce mot de passe après installation.
- Les étudiants doivent posséder une adresse `@eduvaud.ch`. Ajoutez leurs emails dans `allowed_emails` pour une activation automatique ou validez-les manuellement.

## Ressources supplémentaires
- `docs/schema.sql` : script SQL complet.
- `storage/students.json` : liste historique des emails d’étudiants (peut servir à alimenter `allowed_emails`).
- `storage/materials.json` : données legacy actuellement non utilisées (MySQL fait foi).

Pour toute évolution, mettre à jour cette documentation afin que la prochaine génération (humaine ou IA) comprenne rapidement l’architecture et les points d’extension du projet.

