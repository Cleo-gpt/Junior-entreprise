# Modifications du projet – Junior Entreprise 2026-2027

Journal de ce qui a été fait sur le site d'emprunt depuis la reprise par la Junior Entreprise (chef de projet : Cléo Forclaz). Le guide d'origine reste dans `README.md` ; les points où il n'est plus juste sont signalés à la fin.

Chemins donnés depuis le dossier du projet `CPNV_MEDIA-JUNIOR-ENTREPRISE_Teams-2627-AJI - 0000_materiel_multimédia/`.

---

## 30.09.2026

### Organisation

- Dépôt GitHub `Junior-entreprise`, dossier `Site-emprunt-multimédia/`, copie du dossier Teams.
- Les deux versions du site reçues de Mathieu Pézeril sont renommées :

  | Avant | Après | Contenu |
  |---|---|---|
  | `V1.1` | `Travail/ressources-mpl/Travail Mathieu` | version la plus récente : catalogue par étagère, imports de l'Excel (août 2026) |
  | `V1.1 2` | `Travail/ressources-mpl/Travail TPI` | site du TPI, identique à `original.zip` (novembre 2025) |

- `TODOLIST.md` : les étapes du planning en version courte. La page `todolist/` (lancer `python3 todolist/serveur.py`) permet de cocher les tâches ; chaque case cochée est écrite dans `TODOLIST.md`.

### Analyses (aucun fichier de Mathieu modifié)

- `Travail/analyse-colonnes-excel.md` : feuilles, colonnes et incohérences de l'Excel d'inventaire (321 objets, 77 types, 3 feuilles remplies sur 7).
- `BASESQLREFONTE.md` : constats sur la base de Mathieu et proposition de refonte en 5 fichiers SQL.

### Installation locale (MAMP)

Fait en suivant la section 3 de `README.md`, sur une copie du site dans `/Applications/MAMP/htdocs/`.

| Étape | Ce qui a été fait |
|---|---|
| PHP | rien à changer : PHP 8.3 de MAMP, `pdo_mysql` actif, envois jusqu'à 48 Mo |
| Apache | `mod_rewrite` activé dans `/Applications/MAMP/conf/apache/httpd.conf` (ligne 179) ; original sauvegardé dans `httpd.conf.avant-mod_rewrite.bak`. Sans lui, le formulaire de connexion renvoie une erreur 404. |
| Racine du site | réglée dans MAMP sur `htdocs/Travail Mathieu/public` → site sur http://localhost:8888/ |
| Base | `database/schema.sql` exécuté sur le MySQL de MAMP → base `cpnv_gestmat` ; 3 adresses d'exemple dans `student_whitelist` |
| Inventaire | `database/import_inventory_by_space.sql` de Mathieu exécuté (7 tables `materials_*`), sinon le catalogue affiche « 0 types » |
| Connexion | `config/app.php` **de la copie dans htdocs** : port `8889`, utilisateur `root`, mot de passe `root` |

Piège rencontré : chaque dossier recollé dans `htdocs` revient avec le `config/app.php` d'origine (port `3306`, mot de passe vide) et affiche « SQLSTATE[HY000] [2002] Connection refused ».

---

## 07.10.2026

### Décisions du client

- **L'Excel d'inventaire fait foi** sur les tables de la base.
- La version de référence est celle de Mathieu, modifiée le 24.08.2026 (`Travail Mathieu/storage/inventaire.zip`).

### Base locale mise à jour depuis l'Excel

- Sauvegarde avant modification : `~/Documents/sauvegardes-cpnv_gestmat/cpnv_gestmat_avant-maj-excel_2026-10-07.sql`.
- Script : `Travail/sql/maj_inventaire_excel_2026-10-07.sql`. Il remplace le contenu des 3 tables remplies (son 131, enreg. vidéo 85, support vidéo 105 lignes) par l'Excel **à la lettre** ; la structure des tables ne change pas.
- Différence avec l'import de Mathieu : son script ne lisait modèle, prix et description que sur la première ligne de chaque groupe. Désormais chaque ligne garde ses propres valeurs ; seul le NOM (étiquette de groupe) est recopié vers le bas.
- Effets visibles : les enregistreurs ZPRO1 à ZPRO15 passent en « H4n PRO » à 160–170 CHF. Les erreurs de recopie de l'Excel entrent aussi dans la base (SHURE SM59 à SM67, HAMA « 63 3D » à « 86 3D », RODE NTG-3 à NTG-8…) : elles sont à corriger dans l'Excel, puis le script se relance.

### Mise en ligne sur le serveur de l'école

Serveur AutoWeb `eleves.mediamatique.ch`. Le tout avait été préparé dans `Travail/deploiement-ecole/`, supprimé du dépôt le 07.10 (il reste dans l'historique Git).

| Élément | Rôle |
|---|---|
| `emprunt/` | site de Mathieu prêt à envoyer dans `www/` : `config/app.php` pour le serveur (`localhost`, base et utilisateur `cforclaz`, mot de passe à saisir sur le serveur), `.htaccess` qui renvoie vers `public/` et bloque `app/`, `config/`, `storage/`… ; sans `database/`, `docs/` ni les anciens JSON |
| `base_pour_serveur_ecole.sql` | export de la base locale sans `CREATE DATABASE` ni `USE`, à importer dans phpMyAdmin (http://pma.eleves.mediamatique.ch) |

Adresse : https://cforclaz.eleves.mediamatique.ch/emprunt/

Problèmes rencontrés et corrigés :

- erreur 500 : `config/app.php` pointait vers la base de MAMP ;
- nom de dossier avec une espace (`Travail TPI`) : le routeur compare l'adresse encodée (`%20`) au chemin décodé et répond 404 sur toutes les pages ;
- dossier entier envoyé dans `www/` : la base et les JSON étaient téléchargeables publiquement ;
- première version du `.htaccess` : la redirection partait vers le chemin disque du serveur (`/data/chroot/home/…`), corrigée avec `RewriteCond %{REQUEST_URI}`.

Restait à faire au 07.10 : importer la base sur le serveur, saisir le mot de passe MySQL dans `config/app.php`, changer le mot de passe de `admin@eduvaud.ch`.

### Documents de gestion

- `Gestion de projet/PV/` aligné sur le dossier Teams : PV du 16.09 corrigé, PV du 30.09, PDF dans `PDF (Backup)/` ; modèle de PV retiré.
- Planning Excel recopié depuis Teams (version du 30.09).

---

## Ce qui n'est plus juste dans `README.md`

- Section 3 : il faut exécuter `database/schema.sql`, **pas** `docs/schema.sql` (autre structure : pas de table `roles`, statuts différents ; le site ne fonctionne pas avec).
- Adresse locale : `http://localhost:8888/` avec MAMP, pas `http://localhost/agm/V1/public/`.
- Le compte `admin@eduvaud.ch` / `Admin@123` est écrit dans le README : à changer sur toute installation accessible depuis l'extérieur.
- Le site ne doit pas être placé dans un dossier dont le nom contient une espace ou un accent.
- Pour la version de Mathieu, l'inventaire vient des tables `materials_*` (une par étagère), qui ne sont pas décrites dans le README.
