# MVC, Bootstrap et MySQL Workbench dans ce projet

Les trois sujets expliqués à la séance du 30.09.2026, appliqués au code du site d'emprunt.

---

## 1. MVC : qui fait quoi

MVC sépare le code en trois rôles, pour qu'on sache toujours où chercher :

| Rôle | Ce qu'il fait | Dans ce projet |
|---|---|---|
| **Modèle** | lit et écrit les données, applique les règles | `app/Services/` (`MaterialService`, `ReservationService`…). Le dossier `app/Models/` existe mais est vide : ici ce sont les services qui jouent ce rôle. |
| **Vue** | affiche une page, sans calcul | `app/Views/` (`materials/index.php`, `admin/users.php`…), toutes dans le gabarit `layouts/main.php` |
| **Contrôleur** | reçoit la demande, appelle le modèle, choisit la vue | `app/Controllers/` (`MaterialController`, `AdminController`…) |

### Le trajet d'une page

Exemple : l'élève ouvre `/materials`.

1. `public/index.php` reçoit toutes les demandes (grâce à `public/.htaccess`).
2. `routes/web.php` indique que `/materials` correspond à `MaterialController::index`.
3. Le contrôleur vérifie que l'utilisateur est connecté, puis demande la liste à `MaterialService`.
4. `MaterialService` interroge MySQL et renvoie un tableau PHP.
5. Le contrôleur passe ce tableau à la vue `materials/index.php`, qui produit le HTML.

### Où modifier quoi

- Ajouter une page : une ligne dans `routes/web.php`, une méthode dans un contrôleur, un fichier dans `app/Views/`.
- Changer une règle (durée d'emprunt, calcul du stock) : dans le service concerné, jamais dans une vue.
- Changer l'apparence d'une page : dans la vue et `public/assets/css/style.css`.

---

## 2. Bootstrap : la mise en page

Bootstrap est une bibliothèque CSS/JS : on ajoute des classes toutes faites au HTML au lieu d'écrire le CSS soi-même.

- Version **5.3.3**, chargée depuis un CDN dans `app/Views/layouts/main.php` (le CSS en haut, `bootstrap.bundle.min.js` en bas). Rien à installer.
- Les retouches propres au site sont dans `public/assets/css/style.css`, chargé après Bootstrap pour pouvoir le surcharger.

### Les classes les plus utilisées dans les vues

| Classe | Effet | Exemple dans le site |
|---|---|---|
| `container`, `row`, `col-md-6` | grille de 12 colonnes ; `col-md-6` = moitié de largeur à partir d'un écran moyen, pleine largeur sur téléphone | formulaires d'administration |
| `card` | bloc avec bordure et marge | fiches de matériel, panneaux du tableau de bord |
| `form-control` | champ de formulaire stylé | connexion, ajout de matériel (54 utilisations) |
| `btn btn-primary` | bouton principal | « Se connecter », « Ajouter au panier » |
| `table` | tableau lisible | réservations, utilisateurs |
| `modal` | fenêtre par-dessus la page | fiche détaillée d'un matériel |
| `alert alert-success` | message de confirmation | après connexion, après enregistrement |

Documentation : https://getbootstrap.com/docs/5.3/

---

## 3. MySQL Workbench : voir et manipuler la base

Workbench est un logiciel pour se connecter à une base MySQL, lire les tables, lancer des requêtes et dessiner le schéma. Il fait la même chose que phpMyAdmin, mais sur l'ordinateur.

### Se connecter à la base locale (MAMP)

MAMP doit être démarré. Dans Workbench : bouton **+** à côté de « MySQL Connections », puis :

| Champ | Valeur |
|---|---|
| Hostname | `127.0.0.1` |
| Port | `8889` |
| Username | `root` |
| Password | `root` (bouton « Store in Keychain ») |

La base du site s'appelle `cpnv_gestmat`.

### Ce qui sert dans ce projet

- **Lire les données** : double-clic sur `cpnv_gestmat` à gauche, puis clic droit sur une table > *Select Rows*.
- **Lancer un script** : *File > Open SQL Script* (par exemple `Travail/sql/maj_inventaire_excel_2026-10-07.sql`), puis l'éclair pour l'exécuter.
- **Voir le schéma** : *Database > Reverse Engineer*, choisir `cpnv_gestmat` → diagramme des tables et de leurs liens. Utile pour le lot « Conception des tables ».
- **Sauvegarder** : *Server > Data Export*, avant toute modification importante.

### Et sur le serveur de l'école

Le compte MySQL de l'école n'accepte que les connexions depuis le serveur lui-même (`localhost`) : Workbench ne peut pas s'y connecter depuis le Mac. Sur le serveur, on passe par phpMyAdmin : http://pma.eleves.mediamatique.ch.
