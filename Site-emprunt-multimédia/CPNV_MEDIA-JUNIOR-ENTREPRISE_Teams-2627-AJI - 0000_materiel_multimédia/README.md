# Site emprunt multimédia

Projet Junior Entreprise CPNV 2026-2027.

Chef de projet : Cléo Forclaz
Coach : Alain Jaccobi
Client : Mathieu Pézeril


## 1. Arborescence des dossiers

Emplacement du dossier sur l'ordinateur :

    /Users/cleoforclaz/Documents/GitHub/Junior-entreprise/Site-emprunt-multimédia/
    └── CPNV_MEDIA-JUNIOR-ENTREPRISE_Teams-2627-AJI - 0000_materiel_multimédia/


Contenu du dossier :

    CPNV_MEDIA-JUNIOR-ENTREPRISE_Teams-2627-AJI - 0000_materiel_multimédia/
    ├── README.md                 ce fichier
    ├── routine.md                journal des étapes et bilan de fin de journée
    ├── TODOLIST.md               liste des tâches à cocher
    ├── BASESQLREFONTE.md         analyse et refonte de la base de données
    ├── contre-avis/              avis du spécialiste contradicteur, un fichier par jour
    ├── todolist/                 page web pour cocher la TODOLIST
    │   ├── index.html
    │   ├── style.css
    │   └── serveur.py            lancer : python3 todolist/serveur.py
    ├── Gestion de projet/
    │   ├── 2627_00_plannification_site_emprunt_multimédia_ex.xlsx
    │   ├── Cahier des charges/
    │   ├── Devis/
    │   ├── Factures/
    │   ├── PV/                   un PV par séance (.docx)
    │   │   └── PDF (Backup)/     copie PDF des PV
    │   └── Prise de note/
    ├── Travail/
    │   ├── analyse-colonnes-excel.md
    │   ├── sql/                  scripts SQL écrits pendant le projet
    │   └── ressources-mpl/
    │       ├── Travail TPI/      version de référence du site
    │       ├── Travail Mathieu/  version de Mathieu, plus récente
    │       ├── original.zip      copie d'origine du site TPI
    │       ├── capture changement/
    │       └── mdp.txt
    └── Rendu/                    livrables finaux, à la fin du projet


Structure d'un site (Travail TPI et Travail Mathieu) :

    Travail TPI/
    ├── app/                      code PHP
    │   ├── Controllers/
    │   ├── Core/
    │   ├── Services/
    │   └── Views/
    ├── config/
    │   ├── app.php
    │   └── database.local.php    accès à la base du serveur, ignoré par Git
    ├── database/                 schéma SQL et imports
    ├── docs/
    ├── routes/
    ├── storage/
    ├── uploads/
    └── web/                      racine visible par Apache
        ├── index.php
        ├── .htaccess
        ├── assets/
        │   ├── css/
        │   ├── js/
        │   └── img/
        └── uploads/


## 2. À lire à chaque nouvelle consigne

Écrire en français.

Après chaque étape, ajouter une ligne dans routine.md avec le jour et l'heure.

Suivre l'ordre de TODOLIST.md et cocher ce qui est fait.

Garder un code simple à maintenir : c'est une demande du client.


Contre-avis d'un spécialiste :

Après chaque consigne, Claude crée un agent spécialiste en informatique
qui joue le rôle de contradicteur.

Cet agent :
relit ce que Claude a dit ou fait,
le contredit et donne une note sur 10 à la solution de Claude,
propose une meilleure solution,
donne une note sur 10 à sa propre solution.

Le résultat est sauvegardé à chaque fois dans contre-avis/,
un fichier par journée de travail sur le dossier : contre-avis/AAAA-MM-JJ.md
Chaque contre-avis est ajouté à la suite, avec l'heure et la consigne.


Décisions déjà prises :

L'Excel d'inventaire fait foi sur la base de données (07.10.2026).
Version de référence : l'Excel de Mathieu du 24.08.2026.

Pour le moment, on travaille uniquement sur la version TPI (07.10.2026).
Ne pas modifier Travail TPI sans demande explicite.


Hébergement :

Hôte web : bison.web.wellhosted.ch
Hôte SQL : loutre.mysql
phpMyAdmin : https://phpmyadmin.wellhosted.ch/
Apache pointe vers le dossier web/.

La base loutre.mysql n'est accessible que depuis le serveur, pas depuis le Mac.


Site en local :

MAMP, Apache sur le port 8888, MySQL sur le port 8889.
Base locale : cpnv_gestmat.


Mots de passe :

Le dépôt GitHub est public.
Ne jamais écrire un mot de passe dans un fichier suivi par Git, ni dans ce README.
Les accès à la base du serveur vont dans config/database.local.php, que Git ignore.
Ce fichier s'envoie à la main sur le serveur (FTP ou SFTP).


Ne pas commiter ni pousser sur GitHub sans accord.


## 3. Obligatoire en fin de journée (12h30) ou en fin de session (plus de tokens)

Ajouter dans routine.md le bilan de la journée :
ce qui a été fait, en quelques lignes.

Lire le PV du jour dans Gestion de projet/PV.
S'il n'existe pas encore, le signaler et prendre le dernier PV.

Écrire dans routine.md ce qu'il faut faire la prochaine fois,
d'après les actions à mener et les décisions du PV.

Cocher dans TODOLIST.md les tâches terminées.

Vérifier qu'aucun mot de passe n'est dans un fichier suivi par Git.

Lister ce qui n'est pas commité et demander s'il faut faire un commit.
