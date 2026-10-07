# BASESQLREFONTE – Base de données du site d'emprunt

Analyse de la base du dossier `Travail/ressources-mpl/Travail Mathieu`, faite le 30.09.2026.

- Aucune base ni aucun fichier de Mathieu n'a été modifié : ce document ne contient que des constats et des propositions.
- L'analyse repose sur les fichiers SQL (`database/`, `docs/`) et sur le code PHP (`app/Services`). Je n'ai pas eu accès à une base MySQL en fonctionnement.
- Le SQL proposé ici n'a **pas été exécuté** (pas de MySQL sur le poste) : à tester sur une base vide avant usage.

## En bref

La base fonctionne, mais l'inventaire y existe en trois versions qui ne disent pas la même chose. C'est le seul vrai problème ; le reste est du rangement.

Avec 321 objets et quelques centaines d'emprunts par an, la vitesse n'est pas un enjeu. L'optimisation utile est d'avoir **une seule source par information**, et de porter les règles dans la base plutôt que dans le PHP.

La refonte tient en **5 fichiers SQL** à exécuter dans l'ordre, sans script JS ni import PHP :

| Fichier | Contenu | Remplace |
|---|---|---|
| `01_schema.sql` | tables, clés, contraintes | `database/schema.sql` et `docs/schema.sql` |
| `02_reference.sql` | rôles avec leurs limites, emplacements | la liste d'emplacements écrite en dur dans le PHP |
| `03_inventaire.sql` | reprise de l'inventaire, avec les corrections | `import_excel_by_space.js` et les 2 imports SQL |
| `04_vues.sql` | catalogue, inventaire, calendrier, archive, rappels | des calculs faits aujourd'hui en PHP |
| `05_controles.sql` | requêtes de vérification (lecture seule) | rien, c'est nouveau |

Ce qui reste hors SQL : l'envoi des mails, le hachage des mots de passe, l'envoi des photos, et les pages PHP qui lisent les vues.

---

## 1. Ce qui existe

| Table | Rôle | Remarque |
|---|---|---|
| `users`, `roles`, `user_roles` | comptes et rôles (admin, responsable, enseignant, etudiant) | correct |
| `student_whitelist` | adresses autorisées, avec dates de validité | correct |
| `materials` | types de matériel réservables | identifiants, objets abîmés et galerie en JSON |
| `material_spaces` | les 7 emplacements | contient le **nom de la table** de chaque emplacement |
| `materials_etagere_son` + 6 autres | une table par feuille Excel | tout en `TEXT`, colonnes différentes selon la table |
| `reservations`, `reservation_items` | emprunts et leurs lignes | identifiants remis en JSON |
| `trash` | corbeille (copie JSON de l'élément supprimé) | correct |
| `emails` | journal des mails | correct |

Contenu de l'inventaire : 321 lignes dans 3 tables (son 131, enreg. vidéo 85, support vidéo 105). Les 4 autres tables sont vides.

Les fichiers `storage/*.json` datent de l'ancienne version. Seul `ImportService` les lit, et il n'est appelé nulle part.

## 2. Problèmes constatés

1. **Deux schémas contradictoires.** `database/schema.sql` (suivi par le code) et `docs/schema.sql` (recommandé par le README) diffèrent : table `roles` absente, `suspended`/`disabled`, `checked_out`/`picked_up`, `emails`/`email_logs`, pas de corbeille. Installer avec `docs/schema.sql` donne un site qui ne fonctionne pas.
2. **L'inventaire existe trois fois.**
   - les 7 tables par emplacement (`import_inventory_by_space.sql`), une ligne par objet ;
   - `materials` rempli par `import_materials_from_excel.sql` : 77 types, quantités tirées de la colonne NOMBRE ;
   - `materials` rempli par le code : chaque affichage d'un emplacement du catalogue recrée ou met à jour les types sous un autre identifiant (`INV-…`), sans les identifiants individuels. Jusqu'à 51 écritures pour afficher l'étagère son.
3. **Les 7 tables ne correspondent pas à l'Excel.** Comparées ligne à ligne avec l'Excel de `storage/` : 65 modèles, 37 noms de matériel et 21 prix diffèrent. Exemple : les enregistreurs `MEDIA ZPRO1` à `ZPRO15` sont en modèle « H4n » dans la base et « H4n PRO » dans l'Excel. À l'inverse, l'Excel contient des erreurs de recopie que la base n'a pas (« 62 3D », « 63 3D », « 64 3D »…). **Décision du client (07.10.2026) : l'Excel fait foi.** Les 7 tables ne servent plus de source ; les erreurs de l'Excel se corrigent dans l'Excel.
4. **Le NOM est mal associé dans l'étagère son.** 22 enregistreurs y sont nommés « Housse Enregistreur ZOOM ». Détail dans `Travail/analyse-colonnes-excel.md`.
5. **Des tables dont le nom est une donnée.** Le code lit `material_spaces.table_name`, interroge `information_schema`, puis colle le nom de table dans la requête. Ajouter une armoire oblige à créer une table.
6. **Des données non typées.** Le prix est un texte (« Prix : 90 CHF ») relu par expression régulière à chaque affichage ; l'état est un texte libre où le code cherche « abim », « casse ».
7. **Des listes en JSON.** La base ne peut ni garantir qu'un identifiant est unique, ni dire où se trouve un appareil précis, ni qui l'a eu en dernier.
8. **Une disponibilité stockée qui ne sert pas.** `materials.quantity_available` n'est jamais mis à jour lors d'un emprunt (les deux fonctions prévues pour cela ne sont appelées nulle part). La vraie disponibilité est recalculée à partir des emprunts.
9. **L'historique peut disparaître.** Supprimer un élève efface ses emprunts (`ON DELETE CASCADE`). `import_materials_from_excel.sql` commence par `DELETE FROM materials`, ce qui efface en cascade toutes les lignes d'emprunt.
10. **Dates réelles manquantes.** Un emprunt n'a que ses dates prévues ; le retrait et le retour réels ne sont pas enregistrés, alors que le client les veut dans le calendrier et l'archive.
11. **Mineur.** Une requête par emprunt pour charger ses lignes (200 emprunts = 201 requêtes). Le compte admin et son mot de passe connu sont dans le script de création.

---

## 3. `01_schema.sql` – les tables

Inchangées : `users`, `user_roles`, `student_whitelist`, `trash`, `emails`. Les identifiants texte (`user_…`, `res_…`, `INV-…`) restent tels quels.

### Inventaire : 3 tables au lieu de 9

```sql
-- Un emplacement physique. Jamais montré aux élèves.
CREATE TABLE spaces (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(80) NOT NULL UNIQUE,
  label VARCHAR(120) NOT NULL,             -- « Étagère son »
  short_label VARCHAR(40) NOT NULL,        -- « Son »
  image VARCHAR(255) NULL,
  accent CHAR(7) NULL,                     -- couleur de la tuile
  sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

-- Un type de matériel : ce que l'élève voit et réserve.
CREATE TABLE materials (
  id VARCHAR(32) NOT NULL PRIMARY KEY,
  space_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,              -- Excel : NOM
  designation VARCHAR(255) NULL,           -- Excel : Nom du matériel
  brand VARCHAR(120) NULL,                 -- Excel : Marque
  model VARCHAR(120) NULL,                 -- Excel : Modèle
  category VARCHAR(80) NULL,               -- Excel : Catégorie
  kit_content TEXT NULL,                   -- Excel : Contenu
  description TEXT NULL,                   -- Excel : Description
  replacement_cost DECIMAL(10,2) NULL,     -- Excel : Prix (CHF)
  cover_image VARCHAR(255) NULL,
  gallery JSON NULL,
  is_active BOOLEAN NOT NULL DEFAULT TRUE, -- masquer au lieu de supprimer
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_material_space FOREIGN KEY (space_id) REFERENCES spaces(id),
  UNIQUE KEY uq_material_name (space_id, name)
) ENGINE=InnoDB;

-- Un objet étiqueté (quantity = 1), ou un lot d'objets sans étiquette (quantity = N).
CREATE TABLE material_units (
  id INT AUTO_INCREMENT PRIMARY KEY,
  material_id VARCHAR(32) NOT NULL,
  identifier VARCHAR(64) NULL,             -- Excel : Identifiants individuels
  quantity INT NOT NULL DEFAULT 1,
  state ENUM('ok','damaged','broken','lost') NOT NULL DEFAULT 'ok',
  state_note VARCHAR(255) NULL,            -- « capot pile abîmé »
  missing_parts VARCHAR(255) NULL,         -- Excel : OBJ manquant
  CONSTRAINT fk_unit_material FOREIGN KEY (material_id) REFERENCES materials(id),
  UNIQUE KEY uq_unit (material_id, identifier),
  CONSTRAINT chk_unit_qty CHECK (quantity >= 1 AND (identifier IS NULL OR quantity = 1))
) ENGINE=InnoDB;
```

Ce qui disparaît : les 7 tables `materials_*`, `material_spaces`, et dans `materials` les colonnes `identifiers`, `damaged_items`, `quantity_total`, `quantity_available`, `status`, `tracking_mode`.

Ce qui reste pratique :

- Une seule façon de compter : le stock d'un type est `SUM(quantity)` de ses lignes en état `ok`.
- Les 25 bonnettes sans étiquette tiennent en une ligne, pas en 25.
- L'unicité est par type, pas globale : les identifiants `L-01` à `L-13` sont portés par plusieurs types de trépieds.
- La galerie d'images reste en JSON : c'est une simple liste de fichiers, jamais filtrée.
- Ajouter une armoire = une ligne dans `spaces`.

### Emprunts

```sql
ALTER TABLE reservations
  ADD COLUMN checked_out_at DATETIME NULL,     -- retrait réel
  ADD COLUMN returned_at DATETIME NULL,        -- retour réel
  ADD COLUMN reminder_sent_at DATETIME NULL,   -- évite d'envoyer deux fois le rappel
  ADD INDEX idx_reservation_period (status, start_date, end_date),
  ADD CONSTRAINT chk_reservation_dates CHECK (end_date >= start_date);

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

Deux clés étrangères passent de `CASCADE` à `RESTRICT` : `reservations.user_id` et `reservation_items.material_id`. Un élève qui quitte l'école passe en `suspended`, un type retiré passe en `is_active = FALSE` : l'archive reste complète.

### Règles d'emprunt par rôle

```sql
ALTER TABLE roles
  ADD COLUMN max_items INT NULL,            -- NULL = illimité (Prof)
  ADD COLUMN max_days INT NULL,             -- durée maximale d'un emprunt
  ADD COLUMN max_extension_days INT NULL;   -- aujourd'hui 7 jours, écrit en dur dans le PHP
```

Changer la limite des élèves devient un `UPDATE`, plus une modification de code.

## 4. `02_reference.sql` – les données fixes

```sql
INSERT INTO roles (name, max_items, max_days, max_extension_days) VALUES
  ('admin', NULL, NULL, NULL),
  ('responsable', NULL, NULL, NULL),
  ('enseignant', NULL, 30, 14),     -- valeurs à confirmer avec le client
  ('etudiant', 5, 7, 7);

INSERT INTO spaces (slug, label, short_label, image, accent, sort_order) VALUES
  ('etagere_son', 'Étagère son', 'Son', 'img/spaces/son.svg', '#1e5699', 1),
  ('etagere_lumiere', 'Étagère lumière', 'Lumière', 'img/spaces/lumiere.svg', '#f0a500', 2),
  ('etagere_enreg_video', 'Étagère enreg. vidéo', 'Enreg. vidéo', 'img/spaces/enreg-video.svg', '#2a9d8f', 3),
  ('etagere_support_video', 'Étagère support vidéo', 'Support vidéo', 'img/spaces/support-video.svg', '#e76f51', 4),
  ('etagere_prod_studio', 'Étagère prod. studio', 'Prod. studio', 'img/spaces/prod-studio.svg', '#6c5ce7', 5),
  ('armoire_rouge', 'Armoire rouge', 'Armoire rouge', NULL, NULL, 6),
  ('armoire_bleue', 'Armoire bleue', 'Armoire bleue', NULL, NULL, 7);
```

Le compte admin ne va pas dans ce fichier : un fichier à part, non versionné, avec un mot de passe propre à l'installation.

## 5. `03_inventaire.sql` – la reprise depuis l'Excel

> **Mise à jour 07.10.2026 : l'Excel de Mathieu ne fait finalement pas foi.** Ce point est mis de côté ; la reprise de l'inventaire décrite ici est à revoir.

Version de référence : `Inventaire_CPNV_Multimedia_copie.xlsx`, dernière modification par Mathieu le 24.08.2026, conservé dans `Travail Mathieu/storage/inventaire.zip` (confirmé comme la plus récente le 07.10.2026).

Une règle en découle : **les corrections de données se font dans l'Excel, pas dans le SQL.** Si un modèle, un nom ou un prix est faux, Mathieu (ou toi, avec son accord) le corrige dans l'Excel. Le fichier SQL ne fait que du nettoyage technique, toujours le même ; on peut donc le relancer à chaque nouvelle version de l'Excel.

### Préparer l'Excel (à la main, une fois par version)

1. Dans la feuille son, écrire le NOM sur la **première ligne de chaque groupe** (aujourd'hui la liste NOM / NOMBRE est décalée par rapport au détail, voir la question 3 de `Travail/analyse-colonnes-excel.md`).
2. Dans la feuille support vidéo, supprimer la colonne A vide pour que le tableau commence en A comme les autres.
3. Enregistrer chaque feuille remplie en **CSV UTF-8** : `etagere_son.csv`, `etagere_enreg_video.csv`, `etagere_support_video.csv`.

### Le fichier SQL

```sql
-- 1. Transit : une ligne par ligne de l'Excel, tout en texte
CREATE TABLE import_inventaire (
  emplacement VARCHAR(80) NOT NULL,          -- feuille d'origine
  ligne INT NOT NULL,                        -- numéro de ligne dans la feuille
  nom VARCHAR(255), nombre VARCHAR(20), designation VARCHAR(255), marque VARCHAR(120),
  modele VARCHAR(120), categorie VARCHAR(80), identifiant VARCHAR(64), etat VARCHAR(255),
  prix VARCHAR(60), contenu TEXT, description TEXT,
  id_manquant VARCHAR(64), obj_manquant VARCHAR(255),   -- colonnes « OBJ manquant »
  PRIMARY KEY (emplacement, ligne)
);

-- Chargement d'une feuille (à répéter pour chaque CSV, ou via Importer > CSV dans phpMyAdmin)
SET @n = 1;
LOAD DATA LOCAL INFILE 'etagere_enreg_video.csv' INTO TABLE import_inventaire
  CHARACTER SET utf8mb4 FIELDS TERMINATED BY ';' OPTIONALLY ENCLOSED BY '"' IGNORE 1 LINES
  (nom, nombre, designation, marque, modele, categorie, identifiant, etat, prix, contenu,
   description, id_manquant, obj_manquant)
  SET emplacement = 'etagere_enreg_video', ligne = (@n := @n + 1);

-- 2. Nettoyage technique, identique à chaque reprise
DELETE FROM import_inventaire WHERE nom = 'NOM';                         -- en-têtes répétés
UPDATE import_inventaire SET nom = NULL WHERE TRIM(nom) = '';
UPDATE import_inventaire SET identifiant = NULL WHERE TRIM(identifiant) IN ('', '-');

-- Le NOM n'est écrit que sur la 1re ligne d'un groupe : on le recopie vers le bas
CREATE TEMPORARY TABLE noms AS
SELECT a.emplacement, a.ligne,
       (SELECT b.nom FROM import_inventaire b
         WHERE b.emplacement = a.emplacement AND b.ligne < a.ligne AND b.nom IS NOT NULL
         ORDER BY b.ligne DESC LIMIT 1) AS nom
FROM import_inventaire a
WHERE a.nom IS NULL;

UPDATE import_inventaire i
JOIN noms n ON n.emplacement = i.emplacement AND n.ligne = i.ligne
SET i.nom = n.nom;

-- 3. Les types
INSERT INTO materials (id, space_id, name, designation, brand, model, category,
                       kit_content, description, replacement_cost)
SELECT CONCAT('INV-', UPPER(LEFT(MD5(CONCAT(i.emplacement, '|', LOWER(MIN(TRIM(i.nom))))), 10))),
       s.id, MIN(TRIM(i.nom)), MIN(i.designation), MIN(i.marque), MIN(i.modele), MIN(i.categorie),
       MIN(i.contenu), MIN(i.description),
       MIN(CAST(REPLACE(REGEXP_SUBSTR(i.prix, '[0-9]+([.,][0-9]+)?'), ',', '.') AS DECIMAL(10,2)))
FROM import_inventaire i
JOIN spaces s ON s.slug = i.emplacement
GROUP BY i.emplacement, s.id, TRIM(i.nom);

-- 4. Les exemplaires : 1 ligne par objet étiqueté, 1 ligne avec la quantité NOMBRE sinon
INSERT INTO material_units (material_id, identifier, quantity, state, state_note)
SELECT CONCAT('INV-', UPPER(LEFT(MD5(CONCAT(i.emplacement, '|', LOWER(TRIM(i.nom)))), 10))),
       i.identifiant,
       IF(i.identifiant IS NULL, GREATEST(1, CAST(i.nombre AS UNSIGNED)), 1),
       IF(i.etat REGEXP 'abim|endomag|cass', 'damaged', 'ok'),
       IF(i.etat REGEXP 'abim|endomag|cass', i.etat, NULL)
FROM import_inventaire i;

-- 5. Objets manquants : liste à part dans l'Excel, rattachée par l'identifiant
UPDATE material_units u
JOIN import_inventaire i ON i.id_manquant = u.identifier
SET u.missing_parts = i.obj_manquant
WHERE i.obj_manquant IS NOT NULL;

DROP TABLE import_inventaire;
```

À savoir :

- La feuille son n'a pas les colonnes « OBJ manquant » : pour elle, la liste de colonnes du chargement s'arrête à `description`.
- `LOAD DATA LOCAL` doit être autorisé par le serveur (`local_infile`). Sinon, l'import CSV de phpMyAdmin fait la même chose.
- L'identifiant d'un type est calculé à partir de son nom (même formule que le PHP actuel). Si un nom change dans l'Excel, l'identifiant change aussi : sans importance tant que la base ne contient que des emprunts d'essai (question 3 ci-dessous).
- Le SQL de cette section n'a pas encore été exécuté : il sera testé sur une base locale vide pendant le lot 3.2.

## 6. `04_vues.sql` – les règles dans la base

Chaque besoin du client devient une vue ; le PHP n'a plus qu'à faire `SELECT * FROM vue WHERE …`.

```sql
-- Stock par type
CREATE VIEW v_stock AS
SELECT m.id AS material_id,
       COALESCE(SUM(u.quantity), 0) AS total,
       COALESCE(SUM(IF(u.state = 'ok', u.quantity, 0)), 0) AS en_etat
FROM materials m
LEFT JOIN material_units u ON u.material_id = m.id
GROUP BY m.id;

-- Catalogue élève : aucun emplacement, aucun prix
CREATE VIEW v_catalogue AS
SELECT m.id, m.name, m.designation, m.brand, m.model, m.category,
       m.kit_content, m.description, m.cover_image, m.gallery, s.en_etat AS quantite
FROM materials m
JOIN v_stock s ON s.material_id = m.id
WHERE m.is_active;

-- Inventaire Admin : les colonnes de l'Excel, dans l'ordre de l'Excel
CREATE VIEW v_inventaire AS
SELECT sp.label AS `Emplacement`, m.name AS `NOM`, st.total AS `NOMBRE`,
       m.designation AS `Nom du matériel`, m.brand AS `Marque`, m.model AS `Modèle`,
       m.category AS `Catégorie`, u.identifier AS `Identifiants individuels`,
       u.state AS `État`, m.replacement_cost AS `Prix (CHF)`,
       m.kit_content AS `Contenu`, m.description AS `Description`,
       u.missing_parts AS `OBJ manquant`
FROM material_units u
JOIN materials m ON m.id = u.material_id
JOIN spaces sp ON sp.id = m.space_id
JOIN v_stock st ON st.material_id = m.id;

-- Calendrier et archive : une ligne par matériel emprunté
CREATE VIEW v_emprunts AS
SELECT r.id AS reservation_id, r.status, r.user_id, us.name AS emprunteur, us.email,
       r.start_date, r.end_date, r.checked_out_at, r.returned_at,
       YEAR(r.start_date) AS annee, m.id AS material_id, m.name AS materiel, ri.quantity
FROM reservations r
JOIN users us ON us.id = r.user_id
JOIN reservation_items ri ON ri.reservation_id = r.id
JOIN materials m ON m.id = ri.material_id;

-- Rappels à envoyer : sorti, échéance atteinte, pas encore rappelé
CREATE VIEW v_rappels AS
SELECT r.id AS reservation_id, us.name, us.email, r.end_date
FROM reservations r
JOIN users us ON us.id = r.user_id
WHERE r.status = 'checked_out' AND r.end_date <= CURRENT_DATE AND r.reminder_sent_at IS NULL;
```

| Besoin du client | Requête |
|---|---|
| Calendrier Admin | `v_emprunts` filtré par période |
| Calendrier Prof/Élève | `v_emprunts WHERE user_id = ?` |
| Archive par année | `v_emprunts WHERE annee = ?` |
| Rappel automatique | `v_rappels`, lue par une tâche planifiée qui envoie le mail puis remplit `reminder_sent_at` |
| Emplacement caché aux élèves | les pages élève ne lisent que `v_catalogue` |
| Limites par rôle | colonnes de `roles` |
| Token élève | une colonne dans `users`, à définir avec l'USI |

La disponibilité sur une période dépend de deux dates, donc elle ne peut pas être une vue. Elle reste une requête, celle que le code fait déjà :

```sql
SELECT s.en_etat - COALESCE(SUM(ri.quantity), 0) AS disponible
FROM v_stock s
LEFT JOIN reservation_items ri ON ri.material_id = s.material_id
LEFT JOIN reservations r ON r.id = ri.reservation_id
     AND r.status IN ('pending', 'approved', 'checked_out')
     AND r.start_date <= :fin AND r.end_date >= :debut
WHERE s.material_id = :id
GROUP BY s.material_id, s.en_etat;
```

## 7. `05_controles.sql` – vérifier après chaque reprise

```sql
SELECT COUNT(*) AS types, (SELECT SUM(quantity) FROM material_units) AS objets FROM materials;
SELECT name FROM materials m WHERE NOT EXISTS (SELECT 1 FROM material_units u WHERE u.material_id = m.id);
SELECT identifier, COUNT(*) FROM material_units WHERE identifier IS NOT NULL GROUP BY identifier HAVING COUNT(*) > 1;
SELECT id, name FROM materials WHERE replacement_cost IS NULL;
```

Attendu : 321 lignes d'exemplaires avant correction des quantités, aucun type sans exemplaire, et seuls `L-01` à `L-13` en double.

---

## 8. Ce que je ne recommande pas

- **Triggers, procédures stockées, événements planifiés.** Ce serait « encore plus de SQL », mais c'est de la logique cachée, difficile à relire et souvent bloquée ou mal exportée chez les hébergeurs mutualisés. Les vues et les contraintes suffisent.
- **Empêcher en base qu'un même appareil soit prêté deux fois sur la même période.** MySQL n'a pas de contrainte pour cela ; ce contrôle reste une requête faite avant l'enregistrement.
- **Remplacer les identifiants texte par des nombres** : beaucoup de code à toucher, aucun gain visible.
- **Ajouter des index en série** : les clés étrangères en créent déjà ; à ce volume, seul l'index sur la période des emprunts est utile.
- **Une table par année pour l'archive** : un filtre sur l'année suffit.

## 9. Ordre de mise en place

1. **Sauvegarder** la base actuelle (export phpMyAdmin ou `mysqldump`).
2. **Faire valider** par Mathieu les questions ci-dessous.
3. **Écrire et tester** les 5 fichiers sur une base vide, en local.
4. **Brancher le PHP** sur les nouvelles tables et les vues ; supprimer `syncProductToMaterials` et la lecture de `information_schema`.
5. **Contrôler** avec `05_controles.sql`, puis un emprunt de test de bout en bout.
6. **Supprimer** les 7 tables `materials_*`, `material_spaces`, `docs/schema.sql`, les 2 anciens imports, le script JS et `storage/*.json`, une fois le tout validé.

Version requise : MySQL 8.0.16 ou MariaDB 10.2 au minimum (contraintes `CHECK`, `REGEXP_SUBSTR`). À vérifier auprès de l'hébergeur.

## 10. Questions à trancher avant de commencer

1. ~~Quelle source fait foi quand la base et l'Excel diffèrent ?~~ **L'Excel** (réponse du client, 07.10.2026). Reste à savoir : l'Excel reste-t-il la référence après la mise en ligne, ou le site prend-il le relais ? Dans le premier cas, chaque modification devra être faite dans l'Excel puis réimportée ; dans le second, l'Excel ne sert qu'à la reprise et le site fournit l'export.
2. Les quantités des objets sans étiquette : la colonne NOMBRE de l'Excel est-elle juste pour eux ?
3. Y a-t-il déjà de vrais emprunts dans la base, ou seulement des essais ? S'il n'y a que des essais, on repart d'une base vide.
4. Les limites par rôle (nombre d'objets, durée, prolongation) pour Élève et Prof.
5. Les 7 questions de `Travail/analyse-colonnes-excel.md`.
