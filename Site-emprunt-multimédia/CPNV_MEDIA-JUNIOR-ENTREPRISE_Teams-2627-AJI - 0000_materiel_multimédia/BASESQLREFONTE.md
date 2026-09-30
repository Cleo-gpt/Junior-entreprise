# BASESQLREFONTE – Base de données du site d'emprunt

Analyse de la base du dossier `Travail/ressources-mpl/Travail Mathieu`, faite le 30.09.2026. Aucune base ni aucun fichier SQL n'a été modifié : ce document ne contient que des constats et des propositions.

L'analyse repose sur les fichiers SQL (`database/`, `docs/`) et sur le code PHP (`app/Services`). Je n'ai pas eu accès à une base MySQL en fonctionnement : ce qui y est réellement chargé peut différer.

## En bref

La base fonctionne, mais l'inventaire y existe en trois exemplaires qui ne disent pas la même chose. C'est le seul vrai problème ; le reste relève du rangement.

Avec 321 objets et quelques centaines d'emprunts par an, la vitesse n'est pas un enjeu. L'optimisation utile ici, c'est d'avoir **une seule source par information** : moins de code, moins d'erreurs, et les calendriers, rappels et archives demandés par le client deviennent de simples requêtes.

Propositions, par ordre de priorité :

1. Garder un seul fichier de schéma.
2. Remplacer les 7 tables d'inventaire et les listes JSON par 3 tables : emplacements, types, exemplaires.
3. Calculer les disponibilités au lieu de les stocker.
4. Ajouter aux emprunts les dates réelles de retrait et de retour, et empêcher la perte de l'historique.
5. Porter les règles d'emprunt par rôle dans la table des rôles.

---

## 1. Ce qui existe

### Tables utilisées par le code

| Table | Rôle | Remarque |
|---|---|---|
| `users` | Comptes | identifiant texte généré par PHP |
| `roles`, `user_roles` | Rôles admin, responsable, enseignant, etudiant | correct |
| `student_whitelist` | Adresses autorisées, avec dates de validité | correct |
| `materials` | Types de matériel réservables | identifiants, objets abîmés et galerie en JSON |
| `material_spaces` | Liste des 7 emplacements | contient le **nom de la table** de chaque emplacement |
| `materials_etagere_son` + 6 autres | Une table par feuille Excel | tout en `TEXT`, colonnes différentes selon la table |
| `reservations`, `reservation_items` | Emprunts et leurs lignes | identifiants remis en JSON |
| `trash` | Corbeille (copie JSON de l'élément supprimé) | correct |
| `emails` | Journal des mails | correct |

### Fichiers hors base

`storage/users.json`, `reservations.json`, `materials.json`, `trash.json` et `students.json` datent de l'ancienne version et ne sont plus lus pour le fonctionnement normal. `users.json` contient encore l'empreinte du mot de passe admin.

---

## 2. Problèmes constatés

### 2.1 Deux schémas qui se contredisent

`database/schema.sql` et `docs/schema.sql` décrivent deux bases différentes. Le code suit `database/schema.sql`.

| Point | `database/schema.sql` | `docs/schema.sql` |
|---|---|---|
| Rôles | table `roles` + `user_roles.role_id` | `user_roles.role` en texte, pas de table `roles` |
| Statut utilisateur | `suspended` | `disabled` |
| Statut d'emprunt | `checked_out` | `picked_up` |
| Journal des mails | `emails` | `email_logs` |
| Corbeille | `trash` | absente |
| `materials.id` | 32 caractères | 64 caractères |

Quelqu'un qui installe la base avec `docs/schema.sql`, comme le README le demande, obtient un site qui ne fonctionne pas.

### 2.2 L'inventaire existe trois fois

1. **Les 7 tables par emplacement** (`import_inventory_by_space.sql`) : 321 lignes, une par objet.
2. **La table `materials`**, remplie par `import_materials_from_excel.sql` : 77 types, avec des identifiants du genre `CLAVIER-PRODUCTION-AKAI-MPK-`.
3. **La table `materials` encore**, remplie par le code : à chaque affichage d'un emplacement du catalogue, `InventorySpaceService::syncProductToMaterials` recrée ou met à jour un type avec un autre identifiant (`INV-` + 10 caractères).

Conséquences :

- Si les deux imports ont été exécutés, chaque type est présent deux fois dans `materials`, sous deux identifiants.
- Une simple consultation du catalogue écrit dans la base (jusqu'à 51 écritures pour l'étagère son).
- Les quantités ne concordent pas : l'import n° 2 se fie à la colonne NOMBRE de l'Excel, le code compte les lignes. Exemple : DJI Osmo Mobile 6, 59 d'un côté, 42 de l'autre.
- Les types synchronisés par le code perdent leurs identifiants individuels (`tracking_mode = generic`, liste vide), alors que l'Excel les contient.

### 2.3 Des tables dont le nom est une donnée

`material_spaces.table_name` indique au code quelle table lire. Le code doit donc interroger `information_schema` pour vérifier que la table et ses colonnes existent, puis construire la requête en collant le nom de table dans le SQL. Ajouter une armoire oblige à créer une table.

La liste des emplacements existe aussi en dur dans `InventorySpaceService` (5 emplacements, avec couleur et icône), en plus des 7 lignes de `material_spaces`.

### 2.4 Des données non typées

- `prix_chf` est un texte (« Prix : 90 CHF », « 90. ») relu par expression régulière à chaque affichage.
- `etat` est un texte libre ; le code devine si l'objet est abîmé en cherchant « abim », « casse », etc.
- Toutes les colonnes des 7 tables sont en `TEXT`, sans contrainte ni index.

### 2.5 Des listes en JSON là où il faudrait des lignes

`materials.identifiers`, `materials.damaged_items`, `reservation_items.identifiers` et `reservation_items.damaged_identifiers` sont des tableaux JSON. La base ne peut donc pas :

- garantir qu'un identifiant est unique ;
- empêcher qu'un même appareil soit prêté deux fois sur la même période (le contrôle est fait en PHP, en relisant tous les emprunts) ;
- répondre simplement à « où est l'appareil 250dCAN-07 ? » ou « qui l'a eu en dernier ? ».

### 2.6 Deux façons de compter la disponibilité

`materials.quantity_available` est un compteur qu'on décrémente au retrait et qu'on réincrémente au retour. En parallèle, `ReservationService::reservedQuantityForMaterial` recalcule la disponibilité à partir des emprunts qui se chevauchent. Les deux peuvent diverger, et le compteur ne sait pas répondre pour une date future.

### 2.7 L'historique peut disparaître

- `reservations.user_id` est en `ON DELETE CASCADE` : supprimer un élève efface tous ses emprunts. C'est incompatible avec la page archive par année.
- `reservation_items.material_id` est aussi en `CASCADE`, et `import_materials_from_excel.sql` commence par `DELETE FROM materials` : relancer cet import efface toutes les lignes d'emprunt.
- `import_inventory_by_space.sql` fait `DROP TABLE` sur chaque table d'emplacement.

### 2.8 Dates réelles manquantes

Un emprunt n'a que ses dates prévues (`start_date`, `end_date`) et `updated_at`. La date réelle de retrait et la date réelle de retour ne sont enregistrées nulle part, alors que le client les veut dans le calendrier et l'archive.

### 2.9 Points mineurs

- `ReservationService::hydrate` fait une requête par emprunt pour charger ses lignes : 200 emprunts affichés = 201 requêtes.
- Le compte admin par défaut et son mot de passe connu sont dans le script de création.
- Modifier un emprunt supprime puis recrée toutes ses lignes.

---

## 3. Base proposée

### 3.1 Ce qui ne change pas

`users`, `roles`, `user_roles`, `student_whitelist`, `trash`, `emails` : elles font leur travail. Les identifiants texte (`user_…`, `res_…`) restent tels quels, les changer ne rapporterait rien.

### 3.2 Inventaire : 3 tables au lieu de 9

```sql
-- Un emplacement physique (étagère, armoire). Visible Admin uniquement.
CREATE TABLE spaces (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(80) NOT NULL UNIQUE,
  label VARCHAR(120) NOT NULL,           -- « Étagère son »
  short_label VARCHAR(40) NOT NULL,      -- « Son »
  image VARCHAR(255) NULL,
  accent CHAR(7) NULL,                   -- couleur de la tuile
  sort_order INT NOT NULL DEFAULT 0,
  in_catalog BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

-- Un type de matériel : ce que l'élève voit et réserve.
-- Table existante, avec des colonnes en plus et en moins.
CREATE TABLE materials (
  id VARCHAR(32) NOT NULL PRIMARY KEY,
  space_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,            -- Excel : NOM
  designation VARCHAR(255) NULL,         -- Excel : Nom du matériel
  brand VARCHAR(120) NULL,               -- Excel : Marque
  model VARCHAR(120) NULL,               -- Excel : Modèle
  category VARCHAR(80) NULL,             -- Excel : Catégorie
  kit_content TEXT NULL,                 -- Excel : Contenu
  description TEXT NULL,                 -- Excel : Description
  replacement_cost DECIMAL(10,2) NULL,   -- Excel : Prix (CHF)
  tracking_mode ENUM('generic','numbered') NOT NULL DEFAULT 'numbered',
  quantity_generic INT NOT NULL DEFAULT 0,  -- seulement pour les objets sans identifiant
  cover_image VARCHAR(255) NULL,
  gallery JSON NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_material_space FOREIGN KEY (space_id) REFERENCES spaces(id),
  INDEX idx_material_name (name)
) ENGINE=InnoDB;

-- Un objet physique, avec son étiquette.
CREATE TABLE material_units (
  id INT AUTO_INCREMENT PRIMARY KEY,
  material_id VARCHAR(32) NOT NULL,
  identifier VARCHAR(64) NOT NULL,       -- Excel : Identifiants individuels
  state ENUM('ok','damaged','broken','lost') NOT NULL DEFAULT 'ok',
  state_note VARCHAR(255) NULL,          -- « capot pile abîmé »
  missing_parts VARCHAR(255) NULL,       -- Excel : OBJ manquant
  CONSTRAINT fk_unit_material FOREIGN KEY (material_id) REFERENCES materials(id),
  UNIQUE KEY uq_unit (material_id, identifier)
) ENGINE=InnoDB;
```

Ce qui disparaît : les 7 tables `materials_*`, `material_spaces.table_name`, `materials.identifiers`, `materials.damaged_items`, `materials.quantity_total`, `materials.quantity_available`, `materials.status`, et la synchronisation faite à chaque affichage.

Ce qui reste pratique :

- La galerie d'images reste en JSON : ce n'est qu'une liste de fichiers, jamais filtrée.
- Les objets sans identifiant (21 lignes « - » de l'Excel) restent gérés en simple quantité avec `tracking_mode = 'generic'`.
- Ajouter une armoire = ajouter une ligne dans `spaces`.

### 3.3 Emprunts

```sql
ALTER TABLE reservations
  ADD COLUMN checked_out_at DATETIME NULL,     -- retrait réel
  ADD COLUMN returned_at DATETIME NULL,        -- retour réel
  ADD COLUMN reminder_sent_at DATETIME NULL,   -- évite d'envoyer deux fois le rappel
  ADD INDEX idx_reservation_period (status, start_date, end_date);

-- Les appareils remis, un par ligne, à la place des deux colonnes JSON.
CREATE TABLE reservation_units (
  reservation_item_id INT NOT NULL,
  unit_id INT NOT NULL,
  return_state ENUM('ok','damaged','broken','lost') NULL,
  return_note VARCHAR(255) NULL,
  PRIMARY KEY (reservation_item_id, unit_id),
  CONSTRAINT fk_ru_item FOREIGN KEY (reservation_item_id) REFERENCES reservation_items(id) ON DELETE CASCADE,
  CONSTRAINT fk_ru_unit FOREIGN KEY (unit_id) REFERENCES material_units(id)
) ENGINE=InnoDB;
```

Et deux clés étrangères à passer de `CASCADE` à `RESTRICT` : `reservations.user_id` et `reservation_items.material_id`. Un élève qui quitte l'école passe en statut `suspended` au lieu d'être supprimé ; un type de matériel retiré est masqué au lieu d'être supprimé.

### 3.4 Règles d'emprunt par rôle

```sql
ALTER TABLE roles
  ADD COLUMN max_items INT NULL,            -- NULL = illimité (Prof)
  ADD COLUMN max_days INT NULL,             -- durée maximale d'un emprunt
  ADD COLUMN max_extension_days INT NULL;   -- aujourd'hui 7 jours, écrit en dur dans le code
```

Changer la limite des élèves devient une modification de donnée, pas de code.

### 3.5 Disponibilité calculée

Disponible pour une période = exemplaires en état `ok` − exemplaires pris par un emprunt actif qui chevauche la période. C'est déjà le calcul de `reservedQuantityForMaterial` ; il suffit de supprimer le compteur `quantity_available` et les deux méthodes qui le modifient.

### 3.6 Respecter les colonnes de l'Excel sans copier l'Excel

Le client demande de respecter les noms de colonnes. Une vue donne exactement le tableau Excel, pour l'affichage Admin et l'export, sans que les tables en dépendent :

```sql
CREATE VIEW v_inventaire AS
SELECT s.label            AS `Emplacement`,
       m.name             AS `NOM`,
       m.designation      AS `Nom du matériel`,
       m.brand            AS `Marque`,
       m.model            AS `Modèle`,
       m.category         AS `Catégorie`,
       u.identifier       AS `Identifiants individuels`,
       u.state            AS `État`,
       m.replacement_cost AS `Prix (CHF)`,
       m.kit_content      AS `Contenu`,
       m.description      AS `Description`,
       u.missing_parts    AS `OBJ manquant`
FROM material_units u
JOIN materials m ON m.id = u.material_id
JOIN spaces s ON s.id = m.space_id;
```

La colonne NOMBRE n'est plus saisie : c'est le nombre de lignes du type.

### 3.7 Ce que les besoins du client deviennent

| Besoin | Avec la base proposée |
|---|---|
| Calendrier Admin | `reservations` + `users`, filtré par période |
| Calendrier Prof/Élève | même requête, filtrée sur `user_id` |
| Archive par année | `WHERE YEAR(start_date) = ?`, avec `checked_out_at` et `returned_at` |
| Rappel automatique | `status = 'checked_out' AND end_date <= ? AND reminder_sent_at IS NULL` |
| Emplacement caché aux élèves | ne pas sélectionner `space_id` dans les requêtes du catalogue élève |
| Limites par rôle | colonnes de `roles` |
| Token élève | une colonne dans `users`, à définir après discussion avec l'USI |

---

## 4. Ce que je ne recommande pas

- **Remplacer les identifiants texte par des nombres** : beaucoup de code à toucher, aucun gain visible.
- **Remplacer la corbeille par des suppressions logiques partout** : la table `trash` est simple et fonctionne.
- **Ajouter des index en série** : les clés étrangères en créent déjà sur les colonnes de jointure ; à ce volume, un seul index supplémentaire est utile (période des emprunts).
- **Une table par année pour l'archive** : un filtre sur la date suffit.

---

## 5. Ordre de mise en place

Chaque étape laisse le site en état de marche.

1. **Sauvegarder** la base actuelle (`mysqldump`) avant toute chose.
2. **Un seul schéma** : garder `database/schema.sql`, supprimer `docs/schema.sql`, corriger le README. Sortir le compte admin par défaut du script.
3. **Créer** `spaces`, `material_units`, `reservation_units` et les nouvelles colonnes, à côté de l'existant.
4. **Remplir** les nouvelles tables depuis l'Excel nettoyé, pas depuis les 7 tables : le NOM y est mal associé (22 « Housse Enregistreur ZOOM » qui sont des enregistreurs). Voir `Travail/analyse-colonnes-excel.md`.
5. **Brancher le code** : `InventorySpaceService` lit `spaces` et `materials`, sans synchronisation ; `MaterialService` et `ReservationService` utilisent `material_units` et `reservation_units`.
6. **Contrôler** : 321 exemplaires, 77 types, un emprunt de test de bout en bout.
7. **Supprimer** les 7 tables `materials_*`, `material_spaces`, les colonnes JSON remplacées et les fichiers `storage/*.json`, une fois le tout validé par le client.

Les étapes 1 et 2 peuvent se faire tout de suite. Les étapes 3 à 7 correspondent aux lots 3.2 et 3.3 du planning (6 h 30 prévues) ; l'étape 5 déborde sur le lot Code.

## 6. Questions à trancher avant de commencer

1. Les deux imports ont-ils été exécutés sur la base de Mathieu ? Si oui, `materials` contient des doublons à nettoyer.
2. Y a-t-il déjà de vrais emprunts dans la base, ou seulement des essais ? S'il n'y a que des essais, on peut repartir d'une base vide et sauter la reprise des emprunts.
3. Les réponses aux 7 questions de `Travail/analyse-colonnes-excel.md` (NOMBRE, identifiants en double, objets sans identifiant).
