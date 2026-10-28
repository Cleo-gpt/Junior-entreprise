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

11h31
Point sur la base Wellhosted d'après phpMyAdmin :
schéma importé (roles 4, users 1, user_roles 2), materials encore vide.
Liste des fichiers restant à importer selon la version, avec leur emplacement.
Contre-avis lancé.

11h32
Contre-avis reçu (Claude 7/10, contradicteur 9/10), ajouté dans contre-avis/2026-10-28.md.
Corrections : seul schema_TPI_pour_wellhosted.sql a pu être importé,
materials se remplit étagère par étagère, seules 3 étagères ont des données.

11h36
Import de import_materials_from_excel_pour_wellhosted.sql sur Wellhosted (fait par l'utilisateur).
Cela correspond au chemin de la version TPI : 77 types de matériel dans materials.
Ne plus importer les fichiers de la version Mathieu (risque de doublons).
Contre-avis lancé.

11h38
Contre-avis reçu (Claude 6/10, contradicteur 8/10), ajouté dans contre-avis/2026-10-28.md.
Points vérifiés : réimport dangereux après des réservations, storage/ de TPI contient des données de test,
mdp.txt est aussi sur GitHub.

11h40
Le site en ligne n'affiche rien.
Test depuis le Mac : la page d'accueil renvoie une erreur 500 vide (erreur PHP cachée).
Le CSS et le JS de la version TPI s'affichent : la racine web/ est bonne.
L'adresse /login renvoie 404 : la réécriture d'adresses (.htaccess) ne semble pas marcher.
Création de Travail/diagnostic/diag.php pour trouver la cause (à supprimer du serveur après usage).
Contre-avis lancé.

11h42
Contre-avis reçu (Claude 6/10, contradicteur 8/10), ajouté dans contre-avis/2026-10-28.md.
Piste principale : le .htaccess n'a peut-être pas été envoyé (fichier caché dans le FTP).
Diagnostic sécurisé : renommé avec un nom au hasard, protégé par un jeton (jamais écrit dans Git).
Dossier Travail/diagnostic ajouté au .gitignore.

11h42
Rangement des fichiers à la racine du dossier :
TODOLIST.md déplacé dans todolist/ (serveur.py corrigé et testé).
BASESQLREFONTE.md et analyse-colonnes-excel.md déplacés dans Travail/analyses/.
À la racine restent README.md et routine.md.
Le fichier caché .849C9593-... est laissé en place : fichier de synchronisation (Teams/OneDrive).
README.md mis à jour (arborescence et chemins).

11h43
Contre-avis reçu (Claude 6/10, contradicteur 9/10), ajouté dans contre-avis/2026-10-28.md.
.DS_Store ajouté au .gitignore et retiré de Git (8 fichiers, gardés sur le disque).

11h49
« Toujours rien » : nouveau test du site en ligne depuis le Mac.
Accueil toujours en erreur 500 vide, /login toujours 404.
Le fichier de diagnostic n'est pas trouvé sur le serveur (404 d'Apache) : il n'a pas été envoyé dans le dossier servi.
Demande à l'utilisateur d'une capture du FTP pour voir où sont les fichiers.
Contre-avis lancé.

11h50
Fichier caché .849C9593-... confirmé : synchronisation Teams/OneDrive.
Retiré de Git et ajouté au .gitignore, gardé sur le disque.

11h51
Contre-avis reçu (Claude 6/10, contradicteur 8/10), ajouté dans contre-avis/2026-10-28.md.
Le 500 vient de PHP : il manque très probablement app/ à côté de web/, ou PHP est trop ancien.
Prochaine étape : journal d'erreurs PHP Wellhosted et test.txt pour trouver le vrai dossier servi.

11h54
Tous les fichiers .md regroupés dans un sous-dossier Doc-code/ :
README.md, routine.md, TODOLIST.md, BASESQLREFONTE.md, analyse-colonnes-excel.md, contre-avis-2026-10-28.md.
Dossiers vides Travail/analyses/ et contre-avis/ supprimés.
todolist/serveur.py lit maintenant Doc-code/TODOLIST.md (testé).
README.md mis à jour (arborescence et chemins).

11h55
Contre-avis reçu (Claude 6/10, contradicteur 9/10), ajouté dans Doc-code/contre-avis-2026-10-28.md.
mdp.txt était suivi par Git et déjà sur GitHub (dépôt public).
Retiré de Git et ajouté au .gitignore, gardé sur le disque.
Mots de passe à changer : à décider.

11h55
Capture FileZilla du serveur : à la racine il y a log, private, ssl, tmp et web.
Dans web : assets, uploads, index.php.
Les dossiers app, config, routes et storage manquent à côté de web : c'est la cause de l'erreur 500.
Le panneau de gauche de FileZilla était sur /cores (dossier système du Mac) : d'où l'erreur « Permission denied ».
Création de Travail/a-envoyer-TPI : copie propre à envoyer (app, config, routes, storage vide, web avec .htaccess).
Dossier ajouté au .gitignore (il contient les accès à la base).
Contre-avis lancé.

11h57
Décision de l'utilisateur : tous les .md restent dans Doc-code/, README compris, comme demandé.
Les propositions du contre-avis sur le rangement ne sont pas suivies.

11h58
Contre-avis reçu (Claude 6/10, contradicteur 9/10), ajouté dans Doc-code/contre-avis-2026-10-28.md.
Ajouts retenus : web/uploads/materials en écriture, regarder le dossier /log du serveur en cas d'erreur, vérifier PHP 8.

À clarifier
Le PV du 07.10 dit de travailler uniquement sur la version TPI.
Aujourd'hui, les changements ont été faits sur Travail Mathieu.
Confirmer quelle version est mise en ligne.


Bilan de la journée
À écrire à 12h30.

À faire la prochaine fois
À écrire à 12h30, d'après le PV du jour.
Pas encore de PV pour le 28.10 dans Gestion de projet/PV (dernier PV : 07.10).
