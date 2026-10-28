# Routine

Journal de chaque étape, avec le jour et l'heure.
Le bilan de la journée se fait à 12h30, ou en fin de session.


## Mercredi 28.10.2026

Les heures avant 10h47 sont approximatives : le journal a commencé à 10h47.


Vers 10h30
Dossier racine public renommé en web dans Travail Mathieu, pour l'Apache du serveur.
Chemin des uploads corrigé dans AdminController.php.
README de Travail Mathieu mis à jour.

Vers 10h35
Vérification des bases de données.
Travail Mathieu pointait encore sur la base locale (root, sans mot de passe).
Les nouveaux accès Wellhosted étaient seulement dans Travail TPI/config/app.php.

Vers 10h35
Problème trouvé : le dépôt GitHub est public, et le mot de passe SQL du serveur est dans Travail TPI/config/app.php et dans l'historique Git depuis le 30.09.
À changer chez Wellhosted.

Vers 10h40
Travail Mathieu branché sur la base Wellhosted (loutre.mysql).
Les accès sont dans config/database.local.php, ignoré par Git.
Sans ce fichier, le site revient à la base locale.

10h47
Création de README.md (arborescence, règles à lire, fin de journée) et de ce fichier routine.md.

10h50
Test de connexion au serveur SQL depuis le Mac, sans rien écrire.
Le port MySQL 3306 est fermé depuis l'extérieur : impossible d'importer directement.
Le port SSH 22 de bison.web.wellhosted.ch est ouvert : possible avec un accès SSH.
Sinon, import à faire par phpMyAdmin.

10h53
Arborescence du README.md refaite sous forme d'arbre (├── └──), sur le modèle donné.

10h56
Relecture des changements public → web et base de données, dans les deux versions.
Les deux versions ont bien web/, plus aucune référence à public/ dans le code.
Points relevés : base locale MAMP mal réglée par défaut, mot de passe public sur GitHub, version à choisir, base du serveur à remplir.

11h04
Ajout dans README.md de l'emplacement du dossier du projet sur l'ordinateur.

11h19
Erreur #1044 à l'import dans phpMyAdmin Wellhosted (accès refusé à la base cpnv_gestmat).
Cause : les scripts contiennent CREATE DATABASE et USE cpnv_gestmat, or le login n'a des droits que sur c167_studiomultimedia.
Création dans Travail/sql de deux copies sans ces lignes :
inventaire_par_emplacement_pour_wellhosted.sql
maj_inventaire_excel_2026-10-07_pour_wellhosted.sql
Les fichiers d'origine ne sont pas modifiés (ils servent pour MAMP).

11h24
Ajout dans README.md (section 2) de la règle du contre-avis :
un agent spécialiste en informatique contredit Claude, note sa solution sur 10,
propose une meilleure solution et la note aussi.
Sauvegarde dans contre-avis/, un fichier par jour.

11h25
Base c167_studiomultimedia sur Wellhosted : les 9 tables du schéma sont bien importées.
Reste à importer les tables d'emplacement si on met en ligne la version Mathieu.
Contre-avis lancé.

11h27
Contre-avis reçu (Claude 7/10, contradicteur 8.5/10), enregistré dans contre-avis/2026-10-28.md.
Création de Travail/sql/import_materials_from_excel_pour_wellhosted.sql, pour remplir materials en version TPI.

À clarifier
Le PV du 07.10 dit de travailler uniquement sur la version TPI.
Aujourd'hui, les changements ont été faits sur Travail Mathieu.
Confirmer quelle version est mise en ligne.


Bilan de la journée
À écrire à 12h30.

À faire la prochaine fois
À écrire à 12h30, d'après le PV du jour.
Pas encore de PV pour le 28.10 dans Gestion de projet/PV (dernier PV : 07.10).
