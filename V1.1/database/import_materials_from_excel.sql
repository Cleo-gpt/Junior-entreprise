-- Import inventaire CPNV depuis Excel
-- Fichier source : Inventaire_CPNV_Multimedia_copie.xlsx
-- Généré le : 2026-08-25T07:29:43.165Z
-- Matériels : 77

USE `cpnv_gestmat`;

DELETE FROM materials;

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'CLAVIER-PRODUCTION-AKAI-MPK-',
  'Clavier production Akai MPK mini',
  'Clavier contrôleur MIDI USB compact destiné à la production musicale sur ordinateur. Équipé de 25 mini-touches sensibles à la vélocité, 8 pads MPC pour jouer des percussions/samples, 8 potentiomètres assignables et de commandes pour contrôler des instruments virtuels et logiciels de MAO. Connexion et alimentation par USB.
Contenu du kit : Contenu du kit : clavier AKAI MPK Mini MK3 + câble USB
Marque : AKAI Proffessional
Modèle : MPK mini laptop production keyboard
Catégorie : Son
Emplacement : Étagère son',
  5,
  5,
  'available',
  NULL,
  NULL,
  'numbered',
  '["clMPK01","clMPK02","clMPK03","clMPK04","clMPK05"]',
  90.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'ENREGISTREUR-ZOOM-H4N-ZOOM-H',
  'Enregistreur ZOOM H4N',
  'Enregistreur audio portable équipé d’entrées XLR et jack. Peut enregistrer des sons jusqu’à 140 dB SPL sans distorsion. Compatible avec les cartes mémoire SD/SDHC jusqu’à 32 Go maximum.

Identifiants repérés (1/7) : MEDIA Z1
Marque : ZOOM
Modèle : H4n
Catégorie : Son
Emplacement : Étagère son',
  7,
  7,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  100.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'ENREGISTREUR-ZOOM-H4N-PRO-ZO',
  'Enregistreur ZOOM H4N Pro',
  'Enregistreur audio portable équipé d’entrées XLR et jack. Peut enregistrer des sons jusqu’à 140 dB SPL sans distorsion. Compatible avec les cartes mémoire SD/SDHC jusqu’à 32 Go maximum.

Identifiants repérés (1/15) : MEDIA Z2
Marque : ZOOM
Modèle : H4n
Catégorie : Son
Emplacement : Étagère son',
  15,
  15,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  100.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'ENREGISTREUR-ZOOM-H5-ZOOM-H4',
  'Enregistreur ZOOM H5',
  'Enregistreur audio portable équipé d’entrées XLR et jack. Peut enregistrer des sons jusqu’à 140 dB SPL sans distorsion. Compatible avec les cartes mémoire SD/SDHC jusqu’à 32 Go maximum.

Identifiants repérés (1/4) : MEDIA Z3
Marque : ZOOM
Modèle : H4n
Catégorie : Son
Emplacement : Étagère son',
  4,
  4,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  100.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'CHARGEUR-SECTEUR-POUR-ZOOM-Z',
  'Chargeur secteur pour ZOOM',
  'Enregistreur audio portable équipé d’entrées XLR et jack. Peut enregistrer des sons jusqu’à 140 dB SPL sans distorsion. Compatible avec les cartes mémoire SD/SDHC jusqu’à 32 Go maximum.

Identifiants repérés (1/7) : MEDIA Z4
Contenu du kit : Pas de boite
Marque : ZOOM
Modèle : H4n
Catégorie : Son
Emplacement : Étagère son',
  7,
  7,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  100.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'HOUSSE-ENREGISTREUR-ZOOM-ZOO',
  'Housse Enregistreur ZOOM',
  'Enregistreur audio portable équipé d’entrées XLR et jack. Peut enregistrer des sons jusqu’à 140 dB SPL sans distorsion. Compatible avec les cartes mémoire SD/SDHC jusqu’à 32 Go maximum.

Identifiants repérés (22/16) : MEDIA Z5, MEDIA Z6, MEDIA Z8, MEDIA ZPRO1, MEDIA ZPRO2, MEDIA ZPRO3, MEDIA ZPRO4, MEDIA ZPRO5, MEDIA ZPRO6, MEDIA ZPRO7, MEDIA ZPRO8, MEDIA ZPRO9, MEDIA ZPRO10, MEDIA ZPRO11, MEDIA ZPRO12, MEDIA ZPRO13, MEDIA ZPRO14, MEDIA ZPRO15, MEDIA Z51, MEDIA Z52, MEDIA Z53, MEDIA Z54
Marque : ZOOM
Modèle : H4n
Catégorie : Son
Emplacement : Étagère son',
  16,
  16,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  100.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'KIT-WIRELESS-SENNHEISER-EW-1',
  'KIT wireless',
  'Système de réception audio sans fil permettant de recevoir le signal provenant d’un microphone ou d’un émetteur Sennheiser compatible. Principalement utilisé pour les interviews, tournages, conférences et captations audio, afin de transmettre la voix sans liaison câblée directe avec la personne équipée.eur audio portable équipé d’entrées XLR et jack. Peut enregistrer des sons jusqu’à 140 dB SPL sans distorsion. Compatible avec les cartes mémoire SD/SDHC jusqu’à 32 Go maximum.
Contenu du kit : Kit récepteur sans fil Sennheiser EW100 G2 comprenant : 1 récepteur, 2 piles, 2 antennes, 1 câble d’alimentation secteur et 1 câble XLR. Permet de recevoir et transmettre un signal audio sans fil vers une console de mixage, un enregistreur ou un autre équipement audio compatible.
Marque : Sennheiser
Modèle : EW 100 g3
Catégorie : Son
Emplacement : Étagère son',
  2,
  2,
  'available',
  NULL,
  NULL,
  'numbered',
  '["Kit wireless 1","Kit wireless 2"]',
  250.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'SONOMETRE-VELLEMAN-DEM201-ET',
  'sonomètre',
  'Sonomètre portable permettant de mesurer le niveau sonore ambiant de 30 à 130 dB. Utile pour contrôler et comparer le volume sonore lors d’enregistrements, d’événements ou dans différents environnements. Dispose d’un écran LCD, des pondérations A et C et de modes de mesure rapide ou lente. Les mesures sont indicatives et ne sont pas destinées à des mesures acoustiques officielles.

Identifiants repérés (1/2) : DEM202
Contenu du kit : 1x Sonomètre 1x tournevic 1x pile
Marque : Velleman
Modèle : DEM201
Catégorie : Son
Emplacement : Étagère son',
  2,
  2,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  100.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-CANON-APPAREIL-PHOTO-D',
  'Micro Canon Appareil photo DM-50',
  'Microphone stéréo directionnel destiné à la captation audio sur caméscope. Permet d’enregistrer principalement les sons provenant de l’avant tout en réduisant les bruits environnants. Dispose de trois modes de captation : Shotgun (mono) pour cibler le son frontal, Stereo 1 pour capter l’avant et une partie de l’environnement, et Stereo 2 pour une ambiance stéréo plus large. Utile pour les interviews, dialogues, reportages et captations vidéo.

Identifiants repérés (1/7) : mdm50-01
Contenu du kit : micro
Marque : Canon
Modèle : DM-50
Catégorie : Son
Emplacement : Étagère son',
  7,
  7,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  130.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-CANON-APPAREIL-PHOTO-D-1',
  'Micro Canon Appareil photo DM-100',
  'Microphone stéréo directionnel destiné à la captation audio sur caméscope. Permet d’enregistrer principalement les sons provenant de l’avant tout en réduisant les bruits environnants. Dispose de trois modes de captation : Shotgun (mono) pour cibler le son frontal, Stereo 1 pour capter l’avant et une partie de l’environnement, et Stereo 2 pour une ambiance stéréo plus large. Utile pour les interviews, dialogues, reportages et captations vidéo.
Contenu du kit : micro
Marque : Canon
Modèle : DM-50
Catégorie : Son
Emplacement : Étagère son',
  1,
  1,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mdm50-02"]',
  130.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-CANON-APPAREIL-PHOTO-D-2',
  'Micro Canon Appareil photo DM-E1',
  'Microphone stéréo directionnel destiné à la captation audio sur caméscope. Permet d’enregistrer principalement les sons provenant de l’avant tout en réduisant les bruits environnants. Dispose de trois modes de captation : Shotgun (mono) pour cibler le son frontal, Stereo 1 pour capter l’avant et une partie de l’environnement, et Stereo 2 pour une ambiance stéréo plus large. Utile pour les interviews, dialogues, reportages et captations vidéo.

Identifiants repérés (8/1) : mdm50-03, mdm50-04, mdm50-05, mdm50-06, mdm50-07, mdm50-08, mdm100-01, mdm-E1-01
Contenu du kit : micro
Marque : Canon
Modèle : DM-50
Catégorie : Son
Emplacement : Étagère son',
  1,
  1,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  130.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-SHURE-APPAREIL-PHOTO-V',
  'Micro SHURE Appareil photo VP83',
  'Microphone canon directionnel conçu pour être fixé sur un appareil photo ou une caméra. Permet d’améliorer la qualité sonore des vidéos en captant principalement les sons provenant de l’avant tout en réduisant les bruits environnants. Idéal pour les interviews, reportages, tournages et captations en extérieur. Dispose d’une suspension antichoc intégrée, d’un filtre coupe-bas et d’un réglage de gain à trois niveaux. Connexion à l’appareil par câble jack 3,5 mm.
Contenu du kit : micro
Marque : SHURE
Modèle : VP83
Catégorie : Son
Emplacement : Étagère son',
  11,
  11,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mVP83-01","mVP83-02","mVP83-03","mVP83-04","mVP83-05","mVP83-06","mVP83-07","mVP83-08","mVP83-09","mVP83-10","mVP83-11"]',
  200.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-SENNHEISER-APPAREIL-PH',
  'Micro SENNHEISER Appareil photo',
  'Microphone Sennheiser directionnel conçu pour être fixé sur un appareil photo ou une caméra. Permet d’améliorer la qualité sonore des vidéos en captant principalement les sons provenant de l’avant tout en réduisant les bruits environnants. Idéal pour les interviews, reportages, tournages et captations en extérieur. Dispose d’une suspension antichoc intégrée, d’un filtre coupe-bas et d’un réglage de gain à trois niveaux. Connexion à l’appareil par câble jack 3,5 mm.
Contenu du kit : micro
Marque : Sennheiser
Modèle : mke 400
Catégorie : Son
Emplacement : Étagère son',
  1,
  1,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mMKE400-01"]',
  160.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-PANASONIC-APPAREIL-PHO',
  'Micro Panasonic Appareil photo',
  'Microphone panasonic directionnel conçu pour être fixé sur un appareil photo ou une caméra. Permet d’améliorer la qualité sonore des vidéos en captant principalement les sons provenant de l’avant tout en réduisant les bruits environnants. Idéal pour les interviews, reportages, tournages et captations en extérieur. Dispose d’une suspension antichoc intégrée, d’un filtre coupe-bas et d’un réglage de gain à trois niveaux. Connexion à l’appareil par câble jack 3,5 mm.
Contenu du kit : micro
Marque : Panasonic
Modèle : DMW-MS1
Catégorie : Son
Emplacement : Étagère son',
  1,
  1,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mPANAms1-01"]',
  130.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-INTERVIEW-WIRELESS-GO-',
  'Micro  interview wireless GO',
  'Système de microphone sans fil compact composé d’un émetteur avec microphone intégré et d’un récepteur. Permet d’enregistrer la voix à distance sans câble entre la personne et la caméra. Idéal pour les interviews, présentations, reportages et tournages vidéo. L’émetteur peut être utilisé directement comme micro-cravate ou connecté à un microphone cravate externe.
Contenu du kit : 1x éméteur micro 1x récépteur 2x mousse micro 1x cable jack 3mm 2x cable USBC 1x pochette
Marque : RODE
Modèle : WIGO WHITE
Catégorie : Son
Emplacement : Étagère son',
  1,
  1,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mRODEint01"]',
  150.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'ENREGISTREUR-INTERVIEW-ZOOM-',
  'enregistreur interview ZOOM F1-LP',
  'Enregistreur audio portable avec microphone-cravate, conçu pour enregistrer directement la voix d’une personne. Se fixe à la ceinture ou se glisse dans une poche, tandis que le micro-cravate se fixe sur les vêtements. Idéal pour les interviews, reportages et tournages vidéo. L’enregistrement est effectué directement sur carte microSD, ce qui évite les problèmes d’interférences liés aux systèmes sans fil. Enregistrement jusqu’à 24 bits / 96 kHz.
Contenu du kit : 1x Enregistreur 1x micro
Marque : ZOOM
Modèle : ZOOM F1-LP
Catégorie : Son
Emplacement : Étagère son',
  2,
  2,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mZOOMint-01","mZOOMint-02"]',
  100.00,
  '[{"identifier":"mZOOMint-01","condition":"Abimé (capot pile)"}]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'ENREGISTREUR-INTERVIEW-TASCA',
  'enregistreur interview Tascam DR10-L',
  'Enregistreur audio portable avec microphone-cravate, conçu pour enregistrer directement la voix d’une personne. Se fixe à la ceinture ou se glisse dans une poche, tandis que le micro-cravate se fixe sur les vêtements. Idéal pour les interviews, reportages et tournages vidéo. L’enregistrement est effectué directement sur carte microSD, ce qui évite les problèmes d’interférences liés aux systèmes sans fil. Enregistrement jusqu’à 24 bits / 96 kHz.
Contenu du kit : 1x Enregistreur 1x micro
Marque : Tascam
Modèle : Tascam DR 10-L
Catégorie : Son
Emplacement : Étagère son',
  3,
  3,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mTASint-01","mTASint-02","mTASint-03"]',
  150.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-MAIN-SENNHEISER-E845S-',
  'Micro main SENNHEISER E845s',
  'Microphone dynamique filaire conçu principalement pour la voix, le chant, les présentations et les conférences. Sa directivité supercardioïde permet de privilégier la voix située devant le microphone tout en réduisant les bruits environnants et les risques de larsen. Construction métallique robuste, connexion XLR et interrupteur marche/arrêt intégré.
Contenu du kit : 1x micro 1x piece support
Marque : SENNHEISER
Modèle : SENNHEISER E845-S
Catégorie : Son
Emplacement : Étagère son',
  2,
  2,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mSENmain-01","mSENmain-02"]',
  90.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-MAIN-SHURE-SM58-SHURE-',
  'Micro main SHURE sm58',
  'Microphone dynamique filaire conçu principalement pour la voix, le chant, les présentations et les conférences. Sa directivité cardioïde permet de privilégier le son provenant de l’avant tout en réduisant les bruits environnants. Il intègre un filtre anti-pop pour limiter les bruits de souffle et une suspension interne réduisant les bruits de manipulation. Très robuste, il convient particulièrement à une utilisation sur scène, en studio ou lors d’événements. Connexion par câble XLR.
Contenu du kit : 1x micro 1x piece support
Marque : SHURE
Modèle : SHURE SM58
Catégorie : Son
Emplacement : Étagère son',
  10,
  10,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mSHUmain-01","mSHUmain-02","mSHUmain-03","mSHUmain-04","mSHUmain-05","mSHUmain-06","mSHUmain-07","mSHUmain-08","mSHUmain-09","mSHUmain-10"]',
  90.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-MAIN-SENNHEISER-MD-21-',
  'Micro main SENNHEISER MD 21 U',
  'Microphone dynamique omnidirectionnel conçu pour les interviews, reportages, prises de parole et captations d’ambiance. Réponse en fréquence : **40 Hz – 18 000 Hz**. Connexion filaire **XLR 3 broches**, impédance nominale de **200 Ω** et sensibilité de **1,8 mV/Pa à 1 kHz**. Ne nécessite ni pile ni alimentation fantôme. Sa directivité omnidirectionnelle capte le son provenant de toutes les directions et sa conception limite fortement les bruits de manipulation, de vent et les plosives.
Contenu du kit : 1x micro
Marque : SENNHEISER
Modèle : SENNHEISER MD 21 U
Catégorie : Son
Emplacement : Étagère son',
  1,
  1,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mSENmain-1-01"]',
  470.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-MAIN-SENNHEISER-MD-42-',
  'Micro main SENNHEISER MD 42',
  'Microphone dynamique filaire omnidirectionnel conçu principalement pour les interviews et le reportage. Réponse en fréquence : **40 Hz – 18 000 Hz**. Connexion **XLR 3 broches**, impédance nominale de **350 Ω**, impédance de charge minimale de **1 kΩ** et sensibilité de **2,0 mV/Pa à 1 kHz**. Ne nécessite ni pile ni alimentation fantôme. Sa directivité omnidirectionnelle permet de capter la voix même lorsque le microphone n’est pas parfaitement orienté vers la source. Conception limitant les bruits de manipulation, de vent et les plosives.
Contenu du kit : 1x micro
Marque : SENNHEISER
Modèle : SENNHEISER MD 42
Catégorie : Son
Emplacement : Étagère son',
  4,
  4,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mSENmain-1-02","mSENmain-1-03","mSENmain-1-04","mSENmain-1-05"]',
  170.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-PODCAST-RODE-NT-USB-MI',
  'Micro podcast RODE NT-USB MINI',
  'Microphone à condensateur USB cardioïde conçu pour l’enregistrement de voix, podcasts, streaming, visioconférences et instruments. Réponse en fréquence : 20 Hz – 20 000 Hz. Niveau SPL maximal : 121 dB. Enregistrement numérique en 24 bits / 48 kHz. Connexion USB-C pour l’alimentation et la transmission audio, avec sortie casque jack 3,5 mm permettant un monitoring sans latence. Dispose d’un filtre anti-pop intégré et d’un support de bureau magnétique amovible. Fonctionne directement sur ordinateur ou tablette compatible sans alimentation externe.
Contenu du kit : 1x micro 1x cable
Marque : RODE
Modèle : RODE NT-USB MINI
Catégorie : Son
Emplacement : Étagère son',
  5,
  5,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mRODEpod-01","mRODEpod-02","mRODEpod-03","mRODEpod-04","mRODEpod-05"]',
  80.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-PODCAST-SAMSON-CM11B-S',
  'Micro podcast SAMSON CM11B',
  'Microphone de surface à condensateur omnidirectionnel, conçu pour être posé sur une table ou une surface plane afin de capter plusieurs personnes, notamment lors de conférences, réunions ou captations. Réponse en fréquence : **30 Hz – 18 000 Hz**. Niveau SPL maximal : **127 dB**. Sensibilité : **-39 dBV/Pa**. Impédance : **600 Ω**. Connexion **Mini-XLR 3 broches**, avec câble Mini-XLR vers XLR standard. Nécessite une **alimentation fantôme de 9 à 52 V**. Dispose d’un filtre passe-haut interne et offre une plage dynamique de **103 dB**.
Contenu du kit : 1x micro 1x cable
Marque : SAMSON
Modèle : SAMSON CM11B
Catégorie : Son
Emplacement : Étagère son',
  3,
  3,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mSAMpod-01","mSAMpod-02","mSAMpod-03"]',
  80.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-PODCAST-SAMSON-UB1-SAM',
  'Micro podcast SAMSON UB1',
  'Microphone de surface à condensateur **omnidirectionnel**, conçu pour les podcasts, réunions, conférences et enregistrements de plusieurs personnes autour d’une table. Réponse en fréquence : **30 Hz – 18 000 Hz**. Niveau SPL maximal : **127 dB**, plage dynamique : **103 dB** et rapport signal/bruit : **70 dB**. Connexion **USB** directe à un ordinateur, sans interface audio ni alimentation externe. Enregistrement numérique en **16 bits / 44,1 ou 48 kHz**. Compatible **Windows et macOS** en Plug & Play. Câble USB d’environ **3 m** inclus.
Contenu du kit : 1x micro 1x cable
Marque : SAMSON
Modèle : SAMSON UB1
Catégorie : Son
Emplacement : Étagère son',
  4,
  4,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mSAMpod-01-01","mSAMpod-01-02","mSAMpod-01-03","mSAMpod-01-04"]',
  80.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-PODCAST-RCF-MT3100-RCF',
  'Micro podcast RCF MT3100',
  'Microphone de surface à électret **omnidirectionnel**, conçu principalement pour les conférences, réunions et captations de plusieurs personnes autour d’une table. Réponse en fréquence : **50 Hz – 18 000 Hz**. Impédance de sortie : **600 Ω** et rapport signal/bruit : **70 dB à 1 kHz**. Connexion par câble symétrique blindé de **5 m avec XLR 3 broches**. Nécessite une **alimentation fantôme de 15 à 52 V DC**. Dispose d’un filtre électronique réduisant les bruits et vibrations transmis par la table ainsi que d’une LED indiquant lorsque le microphone est actif.
Contenu du kit : 1x micro 1x cable
Marque : RCF
Modèle : RCF MT3100
Catégorie : Son
Emplacement : Étagère son',
  2,
  2,
  'available',
  NULL,
  NULL,
  'numbered',
  '["MI MIC 3100 1","MI MIC 3100 2"]',
  15.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MOUSSE-POUR-ZOOM-H4N-RYCOTE-',
  'Mousse pour ZOOM H4n',
  'Bonnette en mousse destinée à recouvrir les microphones stéréo intégrés du **Zoom H4n**. Permet de réduire les bruits de vent légers, les courants d’air et les plosives lors des enregistrements, tout en protégeant les capsules contre la poussière et l’humidité. Accessoire passif ne nécessitant aucune alimentation ni connexion. Pour les prises de son en extérieur avec vent important, une bonnette à poils (« deadcat ») est plus adaptée.
Contenu du kit : -
Marque : RYCOTE
Modèle : H4n
Catégorie : Son
Emplacement : Étagère son',
  4,
  4,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  9.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MOUSSE-POUR-MICRO-MAIN-RYCOT',
  'Mousse pour Micro main',
  'Bonnette en mousse destinée à recouvrir les microphones stéréo intégrés du **Zoom H4n**. Permet de réduire les bruits de vent légers, les courants d’air et les plosives lors des enregistrements, tout en protégeant les capsules contre la poussière et l’humidité. Accessoire passif ne nécessitant aucune alimentation ni connexion. Pour les prises de son en extérieur avec vent important, une bonnette à poils (« deadcat ») est plus adaptée.
Contenu du kit : -
Marque : RYCOTE
Modèle : -
Catégorie : Son
Emplacement : Étagère son',
  5,
  5,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  9.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'BONETTE-POUR-ZOOM-H4N-RYCOTE',
  'Bonette pour ZOOM H4n',
  'Bonnette en mousse destinée à recouvrir les microphones stéréo intégrés du **Zoom H4n**. Permet de réduire les bruits de vent légers, les courants d’air et les plosives lors des enregistrements, tout en protégeant les capsules contre la poussière et l’humidité. Accessoire passif ne nécessitant aucune alimentation ni connexion. Pour les prises de son en extérieur avec vent important, une bonnette à poils (« deadcat ») est plus adaptée.
Contenu du kit : -
Marque : RYCOTE
Modèle : H4n
Catégorie : Son
Emplacement : Étagère son',
  25,
  25,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  30.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'BONETTE-POUR-MICRO-RODE-RODE',
  'Bonette pour micro RODE',
  'Bonnette en mousse destinée à recouvrir les microphones stéréo intégrés du **Zoom H4n**. Permet de réduire les bruits de vent légers, les courants d’air et les plosives lors des enregistrements, tout en protégeant les capsules contre la poussière et l’humidité. Accessoire passif ne nécessitant aucune alimentation ni connexion. Pour les prises de son en extérieur avec vent important, une bonnette à poils (« deadcat ») est plus adaptée.
Contenu du kit : -
Marque : RODE
Modèle : NTG-2
Catégorie : Son
Emplacement : Étagère son',
  10,
  10,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  49.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MOUSSE-POUR-MICRO-RODE-RODE-',
  'Mousse pour micro RODE',
  'Bonnette en mousse destinée à recouvrir les microphones stéréo intégrés du **Zoom H4n**. Permet de réduire les bruits de vent légers, les courants d’air et les plosives lors des enregistrements, tout en protégeant les capsules contre la poussière et l’humidité. Accessoire passif ne nécessitant aucune alimentation ni connexion. Pour les prises de son en extérieur avec vent important, une bonnette à poils (« deadcat ») est plus adaptée.
Contenu du kit : -
Marque : RODE
Modèle : NTG-2
Catégorie : Son
Emplacement : Étagère son',
  9,
  9,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  49.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-RODE-NTG-2-RODE-NTG-2-',
  'Micro RODE NTG-2',
  'Microphone canon à condensateur **supercardioïde**, conçu pour la prise de son directionnelle en tournage, interview, télévision et reportage. Réponse en fréquence : **20 Hz – 20 000 Hz**, avec **filtre passe-haut commutable à 80 Hz** pour réduire les bruits graves. Niveau SPL maximal : **131 dB**. Sensibilité : **15 mV/Pa à 1 kHz** et bruit propre : **18 dBA**. Sortie audio symétrique **XLR 3 broches**. Peut être alimenté par **alimentation fantôme P48 (48 V)** ou par **1 pile AA 1,5 V**, pratique pour une utilisation avec un enregistreur ne fournissant pas d’alimentation fantôme.

Identifiants repérés (1/7) : NTG-2
Contenu du kit : -
Marque : RODE
Modèle : NTG-2
Catégorie : Son
Emplacement : Étagère son',
  7,
  7,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  185.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'HOUSSE-MICRO-RODE-NTG-2-RODE',
  'Housse micro RODE NTG-2',
  '-
Contenu du kit : -
Marque : RODE
Modèle : NTG-2
Catégorie : Son
Emplacement : Étagère son',
  8,
  8,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  15.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'SUPPORT-CRUSIX-POUR-MICRO-RO',
  'Support crusix pour micro RODE NTG-2',
  '-
Contenu du kit : -
Marque : RODE
Modèle : SM5
Catégorie : Son
Emplacement : Étagère son',
  9,
  9,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  35.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'SUPPORT-STANDAR-POUR-MICRO-R',
  'Support standar pour micro RODE NTG-3',
  '-
Contenu du kit : -
Marque : RODE
Modèle : RM5
Catégorie : Son
Emplacement : Étagère son',
  9,
  9,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  35.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'BAGUE-DE-SUPPORT-MICRO-RODE-',
  'Bague de support micro RODE NTG-2',
  '-
Contenu du kit : -
Marque : RODE
Modèle : NTG-2
Catégorie : Son
Emplacement : Étagère son',
  10,
  10,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  9.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'POIGNEE-DE-SUPPORT-POUR-ZOOM',
  'Poignée de support pour ZOOM H4n',
  'Accessoire pour Zoom H4n, utile pour les interviews et les enregistrements en déplacement.
Contenu du kit : -
Marque : RYCOTE
Modèle : Rycote Zoom h4n kit
Catégorie : Son
Emplacement : Étagère son',
  22,
  22,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  39.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'POIGNEE-DE-SUPPORT-POUR-ZOOM-1',
  'Poignée de support pour ZOOM H4n Version plastique',
  'Accessoire pour Zoom H4n, utile pour les interviews et les enregistrements en déplacement.
Contenu du kit : -
Marque : ZOOM
Modèle : ZOOM MA2
Catégorie : Son
Emplacement : Étagère son',
  9,
  9,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  14.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'SUPPORT-D-ATTACHE-D-ENREGIST',
  'Support d''attache d''enregistreur zoom H4n sur Appareil photo/vidéo',
  'Permet d''accrocher un ZOOM H4n sur un appareil numérique dans griffe ou sabot de flash
Contenu du kit : -
Marque : RYCOTE
Modèle : Rycote Zoom h4n kit
Catégorie : Son
Emplacement : Étagère son',
  16,
  16,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  50.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'SUPPORT-D-ATTACHE-DE-TELEPHO',
  'Support d''attache de téléphone',
  'Permet d''accrocher un telephone sur les Rycote ZOOM h4n kit
Contenu du kit : -
Marque : MANTONA
Modèle : Kit d''accroche telephone
Catégorie : Son
Emplacement : Étagère son',
  18,
  18,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  17.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'CASQUE-AUDIO-SENNHEISER-SENN',
  'Casque audio SENNHEISER',
  'Casque avec micro. Deux cables pour l''entré est sortie audio.
Contenu du kit : -
Marque : SENNHEISER
Modèle : -
Catégorie : Son
Emplacement : Étagère son',
  17,
  17,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  20.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-SAMSON-C01U-SAMSON-SAM',
  'Micro SAMSON C01U',
  'Microphone studio USB à condensateur avec diaphragme de 19 mm, conçu pour l’enregistrement de voix, podcasts, instruments et voix off. Directivité hypercardioïde, permettant de privilégier le son provenant de l’avant. Réponse en fréquence : 20 Hz – 18 000 Hz. Enregistrement numérique jusqu’à 16 bits / 48 kHz. Connexion et alimentation directement par USB, sans interface audio ni alimentation fantôme nécessaire. Compatible avec les ordinateurs Mac et PC et la plupart des logiciels d’enregistrement audio.
Contenu du kit : 1x micro 1x support micro
Marque : SAMSON
Modèle : SAMSON
Catégorie : Son
Emplacement : Étagère son',
  3,
  3,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  130.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'FILTRE-DE-REFLEXION-SE-ELECT',
  'Filtre de réfléxion',
  'Filtre acoustique à placer derrière un microphone de studio afin de réduire les réflexions sonores provenant des murs et de la pièce. Sa mousse absorbante limite la réverbération et les échos captés par le microphone, permettant d’obtenir une voix plus sèche et mieux isolée. Principalement utilisé pour l’enregistrement de voix, podcasts, doublages et chant. Se fixe généralement sur un pied de microphone derrière le micro. Accessoire acoustique passif : aucune alimentation ni connexion nécessaire.
Marque : SE ELECTRONICS
Modèle : -
Catégorie : Son
Emplacement : Étagère son',
  2,
  2,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  76.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'CABLE-MIDI-ETAGERE-SON',
  'Cable MIDI',
  'Câble destiné à transmettre des données MIDI entre des instruments et équipements compatibles, par exemple un clavier, synthétiseur, séquenceur ou interface MIDI. Connexion standard DIN 5 broches mâle vers DIN 5 broches mâle. Le MIDI transmet des informations de contrôle (notes, vélocité, tempo, commandes, etc.) et ne transmet pas directement de signal audio. Câble passif ne nécessitant aucune alimentation.
Marque : -
Modèle : -
Catégorie : Son
Emplacement : Étagère son',
  6,
  6,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  5.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'CABLE-XLR-ETAGERE-SON',
  'Cable XLR',
  'Câble audio symétrique utilisé principalement pour connecter des microphones, consoles de mixage, interfaces audio, enceintes actives et enregistreurs. Connexion standard XLR 3 broches femelle vers XLR 3 broches mâle. La transmission symétrique permet de limiter les parasites et interférences, notamment sur de longues distances. Peut également transporter une alimentation fantôme 48 V pour alimenter certains microphones à condensateu
Marque : -
Modèle : -
Catégorie : Son
Emplacement : Étagère son',
  16,
  16,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  5.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'M-AUDIO-MOBILEPRE-M-AUDIO-MO',
  'm-audio mobilepre',
  'Interface audio USB permettant de connecter des microphones, instruments et sources ligne à un ordinateur pour l’enregistrement et la lecture audio. Résolution maximale : 16 bits / 48 kHz. Dispose de 2 entrées micro XLR avec préamplificateurs, d’entrées jack 6,35 mm, de sorties ligne et d’une sortie casque. Fournit une alimentation fantôme pour les microphones à condensateur compatibles. Connexion et alimentation directement par USB, sans alimentation secteur nécessaire. Principalement destinée à l’enregistrement de voix et d’instruments sur ordinateur.
Marque : M-AUDIO
Modèle : mobilepre
Catégorie : Son
Emplacement : Étagère son',
  3,
  3,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mobile 12","mobile 17","mobile 23"]',
  29.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MIDI-MERGER-ESI-COMPACT-4X-E',
  'MIDI merger',
  'Interface audio USB permettant de connecter des microphones, instruments et sources ligne à un ordinateur pour l’enregistrement et la lecture audio. Résolution maximale : 16 bits / 48 kHz. Dispose de 2 entrées micro XLR avec préamplificateurs, d’entrées jack 6,35 mm, de sorties ligne et d’une sortie casque. Fournit une alimentation fantôme pour les microphones à condensateur compatibles. Connexion et alimentation directement par USB, sans alimentation secteur nécessaire. Principalement destinée à l’enregistrement de voix et d’instruments sur ordinateur.
Marque : ESI
Modèle : Compact 4x
Catégorie : Son
Emplacement : Étagère son',
  1,
  1,
  'available',
  NULL,
  NULL,
  'numbered',
  '["m4xMIDI-01"]',
  53.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'PHANTOM-POWER-ADAPTER-BEACHT',
  'PHANTOM POWER ADAPTER',
  'Adaptateur audio permettant de connecter jusqu’à **2 microphones XLR** à une caméra ou un appareil disposant d’une entrée audio **mini-jack 3,5 mm**. Dispose de **2 entrées XLR symétriques**, de réglages de niveau indépendants et d’une sortie mini-jack vers la caméra. Peut fournir une **alimentation fantôme 48 V** aux microphones à condensateur sur les deux entrées. Permet également d’utiliser des microphones dynamiques ne nécessitant pas d’alimentation. Fonctionne avec une **pile 9 V** pour l’alimentation fantôme. Principalement utilisé pour transformer une entrée audio caméra en solution adaptée aux microphones XLR professionnels.
Marque : BEACHTEK
Modèle : DXA-6
Catégorie : Son
Emplacement : Étagère son',
  1,
  1,
  'available',
  NULL,
  NULL,
  'numbered',
  '["mPHANT-01"]',
  33.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'BLIMP-RODE-RODE-BLIMP-MKII-E',
  'Blimp RODE',
  'Accessoire de protection anti-vent et suspension antichoc destiné aux microphones canon utilisés sur perche. Compatible avec le RØDE NTG1, NTG2, NTG3, NTG4 et NTG4+, ainsi qu’avec d’autres micros canon jusqu’à environ 325 mm de longueur. Le système de suspension Rycote Lyre isole le microphone des vibrations et des bruits de manipulation. La coque avec bonnette réduit les bruits de vent lors des prises de son en extérieur. Comprend également une bonnette à poils Dead Wombat pour les conditions venteuses plus importantes. Accessoire entièrement passif : aucune alimentation nécessaire.
Marque : RODE
Modèle : Blimp MKII
Catégorie : Son
Emplacement : Étagère son',
  6,
  6,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  200.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'BONETTE-POUR-BLIMP-RODE-RODE',
  'Bonette pour Blimp RODE',
  'Accessoire de protection anti-vent.
Marque : RODE
Modèle : Bonnette Blimp MKII
Catégorie : Son
Emplacement : Étagère son',
  6,
  6,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  50.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'PERCHE-POUR-BLIMP-RODE-RODE-',
  'Perche pour Blimp RODE',
  'Perche télescopique destinée à positionner un microphone canon équipé du RØDE Blimp au plus près de la source sonore tout en restant hors du cadre de la caméra. Utilisée principalement pour les tournages, interviews et prises de son cinéma/vidéo. Dispose d’un filetage standard 3/8" compatible avec le système de fixation du Blimp. Longueur réglable selon le modèle de perche. Accessoire mécanique passif : aucune alimentation nécessaire.
Marque : RODE
Modèle : Perche Blimp MKII
Catégorie : Son
Emplacement : Étagère son',
  6,
  6,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  43.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'MICRO-CAMERA-SHURE-SHURE-VP8',
  'Micro camera SHURE',
  'Shure VP83 LensHopper

Microphone canon compact à condensateur électret, conçu pour être monté directement sur un appareil photo ou une caméra. Directivité supercardioïde/lobaire, permettant de privilégier les sons provenant de l’avant et de réduire les bruits latéraux. Réponse en fréquence : 50 Hz – 20 000 Hz. Niveau SPL maximal : 129 dB SPL, sensibilité −36,5 dBV/Pa à 1 kHz et rapport signal/bruit d’environ 76,6 dB.

Connexion audio par mini-jack 3,5 mm TRS avec câble intégré. Dispose d’un réglage de gain −10 / 0 / +20 dB permettant d’adapter le niveau de sortie à la caméra et d’un filtre coupe-bas pour réduire les vibrations et bruits graves. Alimenté par 1 pile AA, avec une autonomie annoncée jusqu’à environ 130 heures. Suspension antichoc Rycote Lyre intégrée pour limiter les vibrations et bruits de manipulation.

Performant pour : interviews, reportages, tournages légers, captation de dialogues et prises de son directement sur caméra. Le gain +20 dB est particulièrement utile avec les appareils possédant des préamplificateurs internes assez bruyants.

Peu adapté pour : prise de son à grande distance, environnement extrêmement bruyant ou production nécessitant une liaison XLR symétrique et une perche éloignée de la caméra. Pour du cinéma ou une interview professionnelle, un microphone canon XLR placé près du sujet reste généralement préférable.
Contenu du kit : Micro+Cable
Marque : SHURE
Modèle : VP83
Catégorie : Son
Emplacement : Étagère son',
  3,
  3,
  'available',
  NULL,
  NULL,
  'numbered',
  '["VP-01","VP-02","VP-03"]',
  240.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'KIT-CANON-EOS-250D-WHIT-LENS',
  'KIT CANON EOS 250D WHIT LENS 18-55mm',
  'Canon EOS 250D + EF-S 18-55mm f/4-5.6 IS STM

Reflex numérique équipé d’un capteur APS-C CMOS 24,1 Mpx et d’un zoom stabilisé 18–55 mm f/4–5.6, équivalent à environ 29–88 mm en plein format. Plage ISO 100–25 600, extensible à 51 200. Rafale jusqu’à 5 i/s. Enregistrement vidéo jusqu’en 4K UHD 25 i/s ou Full HD jusqu’à 60 i/s (50 i/s en PAL). Objectif avec stabilisation optique IS, autofocus STM silencieux et distance minimale de mise au point de 0,25 m. Monture Canon EF-S, compatible également avec les objectifs EF.

Performant pour : photographie générale, portraits, paysages, photos de groupe, interviews, vidéos sur trépied et tournages en extérieur ou intérieur correctement éclairé. Le 18 mm est pratique pour les plans relativement larges et le 55 mm pour les portraits et cadrages plus serrés.

Peu adapté pour : faible luminosité, concerts, scènes de nuit et recherche d’un fort flou d’arrière-plan à cause de l’ouverture relativement faible f/4 à 18 mm → f/5.6 à 55 mm. La focale maximale de 55 mm est également trop courte pour la photographie animalière ou sportive à longue distance. La 4K du 250D subit un recadrage important et utilise un autofocus moins performant qu’en Full HD ; le 1080p est donc souvent plus pratique en vidéo.

Note : 1x protection capteur

Note : 1x cable usb 2.0 micro b male

Note : 1x cable usb 2.0 micro b male

Note : 1x cache objetif

Note : 1x cache objetif1x cable usb 2.0 micro b male

Note : 1x chargeur batterie1x cable usb 2.0 micro b male

Note : 1x cache objetif

Note : 1x cache objetif

Note : 1x cache objetif

Note : 1x cable usb 2.0 micro b male

Note : 1x protection capteur

Note : 1x cable usb 2.0 micro b male  1x protection capteur

Note : 1x protection capteur

Note : 1x cache objetif

Note : 1x cable usb 2.0 micro b male 1x protection capteur

Note : 1x protection capteur 1x cache objetif

Note : 1x cable usb 2.0 micro b male

Note : 1x cable usb 2.0 micro b male x cache objetif 1x protection capteur  1x chargeur batterie

Note : 1x protection capteur1x cable usb 2.0 micro b male

Note : 1x cable usb 2.0 micro b male

Note : 1x sangle canon 1x cache objetif   1x chargeur batterie1x protection capteur1x cable usb 2.0 micro b male

Note : 1x chargeur batterie

Note : 1x protection capteur1x cable usb 2.0 micro b male

Note : 1x protection capteur1x cable usb 2.0 micro b male1x chargeur batterie

Note : 1x protection capteur1x cable usb 2.0 micro b male1x chargeur batterie

Note : 1x protection capteur1x cable usb 2.0 micro b male1x chargeur batterie

Note : 1x cable usb 2.0 micro b mal1x chargeur batterie1x cache objetif 1x protection capteur

Note : 1x cable usb 2.0 micro b mal1x chargeur batterie1x cache objetif 1x protection capteur

Note : 1x cable usb 2.0 micro b mal1x chargeur batterie1x cache objetif 1x protection capteur

Note : 1x protection capteur

Note : 1x protection capteur1x cache objetif

Note : 1x cable usb 2.0 micro b mal

Identifiants repérés (34/32) : 250dCAN-01, 250dCAN-03, 250dCAN-04, 250dCAN-05, 250dCAN-07, 250dCAN-09, 250dCAN-10, 250dCAN-12, 250dCAN-15, 250dCAN-17, 250dCAN-21, 250dCAN-22, 250dCAN-23, 250dCAN-24, 250dCAN-26, 250dCAN-27, 250dCAN-28, 250dCAN-29, 250dCAN-Z31, 250dCAN-Z33, 250dCAN-Z34, 250dCAN-Z35, 250dCAN-Z36, 250dCAN-Z37, 250dCAN-Z38, 250dCAN-Z39, 250dCAN-Z40, 600dCAN-01, 600dCAN-02, 600dCAN-05, 600dCAN-07, 600dCAN-08, 600dCAN-09, EOS 250D
Contenu du kit : Contenu du kit : 1x batterie 1x chargeur batterie 1x objectif 18-55 mm 1x chiffon microfibre 1x cache objetif 1x protection capteur 1x cable usb 2.0 micro b male
Marque : CANON
Modèle : EOS 250D
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  32,
  32,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  470.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'KIT-CANON-EOS-250D-WHIT-LENS-1',
  'KIT CANON EOS 250D WHIT LENS 55-250mm',
  'Canon EOS 250D + EF-S 55-250mm 1:4-5.6 IS II 58mm

Reflex numérique équipé d’un capteur APS-C CMOS 24,1 Mpx et d’un zoom stabilisé 18–55 mm f/4–5.6, équivalent à environ 29–88 mm en plein format. Plage ISO 100–25 600, extensible à 51 200. Rafale jusqu’à 5 i/s. Enregistrement vidéo jusqu’en 4K UHD 25 i/s ou Full HD jusqu’à 60 i/s (50 i/s en PAL). Objectif avec stabilisation optique IS, autofocus STM silencieux et distance minimale de mise au point de 0,25 m. Monture Canon EF-S, compatible également avec les objectifs EF.

Performant pour : photographie générale, portraits, paysages, photos de groupe, interviews, vidéos sur trépied et tournages en extérieur ou intérieur correctement éclairé. Le 18 mm est pratique pour les plans relativement larges et le 55 mm pour les portraits et cadrages plus serrés.

Peu adapté pour : faible luminosité, concerts, scènes de nuit et recherche d’un fort flou d’arrière-plan à cause de l’ouverture relativement faible f/4 à 18 mm → f/5.6 à 55 mm. La focale maximale de 55 mm est également trop courte pour la photographie animalière ou sportive à longue distance. La 4K du 250D subit un recadrage important et utilise un autofocus moins performant qu’en Full HD ; le 1080p est donc souvent plus pratique en vidéo.

Note : 1x cable usb 2.0 micro b mal1x protection capteur 1x chargeur batterie

Note : 1x cable usb 2.0 micro b mal

Note : 1x cable usb 2.0 micro b mal1x cache objetif1x chargeur batterie

Note : 1x cache capteur1x cable usb 2.0 micro b mal

Note : 1x chargeur batterie1x cable usb 2.0 micro b mal

Note : 1x chargeur batterie

Note : 1xconnecteur d''alimentation DC verrouillable 2 broche blackmagic

Note : 1xconnecteur d''alimentation DC verrouillable 2 broche blackmagic

Note : 1xconnecteur d''alimentation DC verrouillable 2 broche blackmagic

Identifiants repérés (10/13) : 750dCAN-01, 750dCAN-02, 750dCAN-03, 550dCAN-02, 550dCAN-03, 550dCAN-04, mpc4kBM-04, mpc4kBM-05, mpc4kBM-06, EOS 250D
Contenu du kit : Contenu du kit : 1x batterie 1x objectif 55-250 mm 1x chiffon microfibre 1 1x sangle canon
Marque : CANON
Modèle : EOS 250D
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  13,
  13,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  470.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'KIT-CANON-EOS-600D-WHIT-LENS',
  'KIT CANON EOS 600D WHIT LENS 18-55mm',
  'Canon EOS 600D + EF-S 18–55mm

Reflex numérique de 2011 équipé d’un capteur APS-C CMOS 18 Mpx et d’un zoom EF-S 18–55 mm, équivalent à environ 29–88 mm en plein format. Plage ISO 100–6 400, extensible à 12 800. Rafale jusqu’à 3,7 i/s. Enregistrement vidéo jusqu’en Full HD 1920×1080 à 24/25/30 i/s ou 720p à 50/60 i/s. Écran LCD 3" orientable. Monture Canon EF/EF-S.

Performant pour : photographie générale, paysages, architecture, photos de groupe, portraits et apprentissage de la photographie. La focale 18 mm permet des plans relativement larges tandis que 55 mm convient aux portraits et cadrages plus serrés. En vidéo, le 1080p 25 i/s reste suffisant pour des captations simples, interviews ou exercices audiovisuels sur trépied.

Peu adapté pour : photographie de sujets très éloignés, sport ou animalier en raison de la focale maximale de 55 mm et de la rafale limitée à 3,7 i/s. Les performances sont également limitées en faible luminosité avec l''objectif de kit. Le 600D ne filme pas en 4K et son autofocus en vidéo est nettement moins performant que celui des reflex Canon plus récents comme le 250D ; la mise au point manuelle est souvent préférable pour un tournage maîtrisé.

Si ton objectif porte exactement l''inscription « EF-S 18-55mm 1:3.5-5.6 IS II », son ouverture est f/3.5–5.6 et je peux l''intégrer directement dans la description.

Identifiants repérés (1/9) : EOS 600D
Contenu du kit : Contenu du kit : 1x batterie  1x objectif 18-55 mm 1x chiffon microfibre  1x sangle canone
Marque : CANON
Modèle : EOS 600D
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  9,
  9,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  460.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'KIT-CANON-EOS-750D-WHIT-LENS',
  'KIT CANON EOS 750D WHIT LENS 18-55mm',
  'Canon EOS 750D

Reflex numérique équipé d’un capteur APS-C CMOS 24,2 Mpx. Plage ISO 100–12 800, extensible à 25 600. Rafale jusqu’à 5 i/s. Autofocus à 19 collimateurs, tous de type croisé, avec système Hybrid CMOS AF III en Live View. Enregistrement vidéo jusqu’en Full HD 1920×1080 à 24/25/30 i/s et 720p jusqu’à 50/60 i/s. Écran 3" tactile et orientable. Dispose d’une entrée microphone mini-jack 3,5 mm, ainsi que du Wi-Fi et NFC. Monture Canon EF/EF-S.

Performant pour : photographie générale, portraits, studio, paysages, événements et sujets en mouvement modéré. Les 24,2 Mpx permettent également un recadrage plus important que sur les anciens 550D/600D/700D de 18 Mpx. Les 19 collimateurs croisés et la rafale de 5 i/s le rendent relativement adapté à la photographie d’action. L’écran orientable est pratique pour les prises de vue à hauteur basse ou élevée et pour la vidéo.

Peu adapté pour : tournages nécessitant de la 4K, ralentis importants ou vidéo à haute fréquence d’images, puisqu’il est limité à 30 i/s en Full HD et 60 i/s en 720p. Ses performances en basse lumière restent également limitées par rapport aux appareils récents, particulièrement avec un objectif de kit peu lumineux. Pour du sport rapide ou de l''animalier, ses 5 i/s restent assez modestes.
Contenu du kit : Contenu du kit : 1x batterie  1x objectif 18-55 mm 1x chiffon microfibre  1x sangle canone 1x chargeur batterie 1x cache objetif
Marque : CANON
Modèle : EOS 750D
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  1,
  1,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  400.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'KIT-CANON-EOS-700D-WHIT-LENS',
  'KIT CANON EOS 700D WHIT LENS 18-55mm',
  'Canon EOS 700D

Reflex numérique équipé d’un capteur APS-C CMOS 18 Mpx. Plage ISO 100–12 800, extensible à 25 600. Rafale jusqu’à 5 i/s. Autofocus à 9 collimateurs, tous de type croisé, avec autofocus hybride en Live View. Enregistrement vidéo jusqu’en Full HD 1920×1080 à 24/25/30 i/s ou 720p à 50/60 i/s. Écran 3" tactile et orientable. Dispose d’une entrée microphone mini-jack 3,5 mm. Monture Canon EF/EF-S.

Performant pour : photographie générale, portraits, paysages, studio, événements et apprentissage de la photographie. La rafale de 5 i/s permet de photographier des sujets en mouvement modéré. L’écran tactile orientable facilite les prises de vue en hauteur, au ras du sol et les cadrages vidéo. Avec un objectif adapté, le capteur APS-C 18 Mpx reste suffisant pour la majorité des usages photo courants.

Peu adapté pour : tournages nécessitant de la 4K, ralentis en haute définition ou suivi autofocus performant en vidéo. Il est limité à 30 i/s en Full HD, et son autofocus vidéo est nettement moins rapide et fiable que celui des boîtiers Canon plus récents. Les performances en basse lumière sont également limitées à ISO élevé, avec une augmentation notable du bruit numérique. Pour le sport rapide et l''animalier, la rafale de 5 i/s et l''autofocus relativement ancien constituent également des limitations.

Identifiants repérés (1/3) : EOS 700D
Contenu du kit : Contenu du kit : 1x batterie  1x objectif 18-55 mm 1x chiffon microfibre  1x sangle canone 1x cache objetif
Marque : CANON
Modèle : EOS 700D
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  3,
  3,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  350.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'KIT-CANON-EOS-550D-WHIT-LENS',
  'KIT CANON EOS 550D WHIT LENS 18-55mm',
  '### Canon EOS 550D

Reflex numérique équipé d’un capteur **APS-C CMOS 18 Mpx**. Plage ISO **100–6 400**, extensible à **12 800**. Rafale jusqu’à **3,7 i/s**. Autofocus à **9 collimateurs**, dont **1 collimateur central de type croisé**. Enregistrement vidéo jusqu’en **Full HD 1920×1080 à 24/25/30 i/s** ou **720p à 50/60 i/s**. Écran fixe **3"**. Dispose d’une **entrée microphone mini-jack 3,5 mm**. Monture **Canon EF/EF-S**.

**Performant pour :** photographie générale, portraits, paysages, studio et apprentissage des réglages manuels. Le capteur APS-C de **18 Mpx** reste suffisant pour de nombreux usages photo, particulièrement avec un bon éclairage. La vidéo **1080p 25/30 i/s** convient aux interviews, plans fixes et exercices vidéo lorsque la mise au point est effectuée manuellement.

**Peu adapté pour :** photographie sportive ou sujets rapides en raison de la rafale limitée à **3,7 i/s** et de l''autofocus ancien. Les performances diminuent également en faible luminosité à ISO élevé. En vidéo, il ne dispose **ni de 4K, ni de 1080p 50/60 i/s**, ni d’un autofocus continu moderne efficace ; la **mise au point manuelle est donc généralement préférable**. L’écran fixe est également moins pratique pour la vidéo et les prises de vue sous des angles difficiles que les écrans orientables des 600D/700D/750D.

Identifiants repérés (1/4) : EOS 550D
Contenu du kit : Contenu du kit : 1x batterie  1x objectif 18-55 mm 1x chiffon microfibre  1x sangle canone 1x cache objetif1x cable usb 2.0 micro b mal1x cache objetif1x chargeur batterie
Marque : CANON
Modèle : EOS 550D
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  4,
  4,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  300.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'KIT-CANON-EOS-400D-WHIT-LENS',
  'KIT CANON EOS 400D WHIT LENS 18-55mm',
  'Reflex numérique équipé d’un capteur APS-C CMOS 10,1 Mpx. Plage ISO 100–1 600. Rafale jusqu’à 3 i/s, avec environ 27 JPEG ou 10 RAW en continu. Autofocus à 9 collimateurs, avec collimateur central de type croisé. Vitesse d’obturation de 30 s à 1/4 000 s, plus mode Bulb. Écran fixe 2,5". Monture Canon EF/EF-S. Le boîtier est exclusivement destiné à la photographie et ne permet aucun enregistrement vidéo.

Performant pour : apprentissage de la photographie, portraits, paysages, studio et photographie générale avec de bonnes conditions lumineuses. Ses réglages manuels permettent toujours de travailler correctement les bases comme ISO, ouverture et vitesse d’obturation. Avec un objectif de qualité et suffisamment de lumière, les 10,1 Mpx restent exploitables pour une utilisation web et des impressions de dimensions raisonnables.

Peu adapté pour : photographie en faible luminosité, la sensibilité étant limitée à ISO 1 600. Sa rafale de 3 i/s et son ancien système autofocus limitent également son intérêt pour le sport et les sujets rapides. Les 10,1 Mpx offrent beaucoup moins de possibilités de recadrage que les reflex plus récents. Il ne possède aucun mode vidéo, pas d’écran orientable ou tactile et pas de Live View, ce qui en fait surtout un boîtier adapté à l''apprentissage et à la photographie classique.
Contenu du kit : Contenu du kit : 1x batterie  1x objectif 18-55 mm 1x chiffon microfibre  1x sangle canone 1x cache objetif1x chargeur batterie
Marque : CANON
Modèle : EOS 400D
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  1,
  1,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  100.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'KIT-CANON-LEGRIA-HF-R68-HD-C',
  'KIT CANON Legria HF R68 HD',
  'Canon LEGRIA HF R68

Caméscope numérique Full HD équipé d’un capteur CMOS 3,28 Mpx et d’un zoom optique 32×, couvrant environ 38,5–1232 mm en équivalent 35 mm en mode standard. Ouverture maximale f/1.8–4.5. Dispose d’une stabilisation optique Intelligent IS et d’un écran tactile orientable 3". Enregistrement jusqu’en Full HD 1920×1080 à 50 i/s, aux formats AVCHD ou MP4. Mémoire interne de 8 Go, extensible par carte SD/SDHC/SDXC. Dispose également du Wi-Fi et NFC.

Performant pour : captation vidéo longue durée, conférences, présentations, spectacles, événements scolaires et sujets éloignés. Le zoom optique 32× constitue son principal avantage : il permet de passer d''un cadrage relativement large à un très gros plan sans changer d''objectif. Le 1080p 50 i/s est également intéressant pour obtenir des mouvements fluides et permet de réaliser un ralenti ×2 propre dans un montage en 25 i/s. La stabilisation optique facilite les prises de vue à main levée.

Peu adapté pour : tournages nécessitant de la 4K, photographie haute résolution ou recherche d’un rendu cinématographique avec une faible profondeur de champ. Son petit capteur limite les performances en faible luminosité et produit davantage de bruit numérique lorsque l''éclairage diminue. Le grand-angle d''environ 38,5 mm est également assez étroit pour filmer dans de petites pièces. Il privilégie donc clairement la captation pratique et le zoom important plutôt que la qualité d''image et la polyvalence d''un reflex ou hybride moderne.

Identifiants repérés (1/2) : Legria HF R68 HD
Contenu du kit : Contenu du kit : 1x batterie  1x objectif 18-55 mm 1x chiffon microfibre  1x sangle canone 1x cache objetif1x chargeur batterie
Marque : CANON
Modèle : Legria HF R68 HD
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  2,
  2,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  100.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'KIT-BLACKMAGIC-DESIGN-POCKET',
  'KIT Blackmagic Design Pocket Cinema Camera 4K WHIT LENS OLYMPUS 12-40mm',
  'Blackmagic Design Pocket Cinema Camera 4K

Caméra cinéma numérique équipée d’un capteur 4/3 de 18,96 × 10 mm d’une résolution de 4096 × 2160, avec monture Micro Four Thirds (MFT). Offre environ 13 stops de plage dynamique et une double sensibilité ISO native 400 / 3200, avec une plage allant jusqu’à ISO 25 600. Enregistrement jusqu’en DCI 4K 4096×2160 à 60 i/s et jusqu’à 120 i/s en 1080p fenêtré. Enregistre notamment en Blackmagic RAW (BRAW) et Apple ProRes. Écran tactile 5" intégré. Stockage sur cartes SD UHS-II, CFast 2.0 ou directement sur SSD via USB-C.

Connectique particulièrement complète pour la vidéo : HDMI pleine taille, entrée microphone jack 3,5 mm, entrée audio mini-XLR avec alimentation fantôme 48 V, sortie casque 3,5 mm, USB-C et alimentation externe 12 V. Fonctionne également sur batterie Canon LP-E6.

Performante pour : courts-métrages, clips, publicités, interviews, studio, fond vert et productions nécessitant un travail important en postproduction. Le BRAW, la plage dynamique d’environ 13 stops et les outils professionnels de monitoring permettent beaucoup plus de latitude en étalonnage que les Canon EOS 250D/750D/700D de ton parc. La 4K 60 i/s permet également des ralentis ×2,4 dans une timeline 25 i/s, tandis que le 1080p 120 i/s permet des ralentis plus importants.

Peu adaptée pour : photographie classique, reportage léger ou tournage nécessitant un autofocus continu performant. Elle ne possède pas de stabilisation mécanique du capteur (IBIS) et son autofocus est beaucoup plus rudimentaire que celui d’un appareil hybride moderne. Son autonomie sur batterie LP-E6 est également assez faible. Pour une utilisation à main levée sérieuse, elle bénéficie fortement d’un rig, d’une stabilisation, de batteries supplémentaires et éventuellement d’un SSD externe.
Contenu du kit : Contenu du kit : 3x batterie  1x objectif 12-40 mm olympus 1x chiffon microfibre   1x cache objetif  1x cache capteur 1x chargeur batterie 1xconnecteur d''alimentation DC verrouillable 2 broche blackmagic
Marque : BlackMagic
Modèle : Magic pocket cinema 4k
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  6,
  6,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  880.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'KIT-PANASONIC-LUMIX-GH7-WHIT',
  'KIT PANASONIC LUMIX GH7 WHIT LENS OLYMPUS 12-40mm',
  'Panasonic Lumix GH7

Appareil hybride professionnel orienté photo et surtout production vidéo, équipé d’un capteur Micro 4/3 BSI CMOS de 25,2 Mpx et d’une monture Micro Four Thirds (MFT). Plage ISO photo 100–25 600, extensible à ISO 50 ; en V-Log, la plage standard est ISO 500–12 800. Il offre plus de 13 stops de dynamique en V-Log dans les modes jusqu’à 60 i/s. Autofocus hybride à détection de phase avec suivi du sujet et stabilisation mécanique IBIS 5 axes jusqu’à 7,5 stops.

En vidéo, il peut enregistrer en 5,7K jusqu’à 60 i/s, en 5,8K Open Gate jusqu’à 30 i/s, en C4K/4K jusqu’à 120 i/s en 10 bits, et en Full HD jusqu’à 240 i/s en HFR ou 300 i/s en VFR selon le mode. Il prend en charge le V-Log, ProRes 422, ProRes RAW/RAW HQ en interne, ainsi que l''enregistrement sur SD UHS-II, CFexpress Type B ou SSD USB-C. Le refroidissement actif permet notamment l''enregistrement C4K/4K 4:2:2 10 bits 50/60p sans limite de durée liée à la chauffe.

Performant pour : courts-métrages, clips, interviews, reportages, multicaméra, fond vert, ralentis, captation d''événements et productions nécessitant un étalonnage poussé. Le 10 bits, V-Log, ProRes RAW, 5.7K/Open Gate et 4K 120p en font une caméra nettement plus avancée pour la vidéo que les Canon EOS de ton inventaire. La stabilisation 5 axes permet également de réaliser des plans à main levée particulièrement efficaces. En photo, les 25,2 Mpx, l''autofocus à détection de phase et les rafales très rapides permettent aussi de couvrir du portrait, de l''événementiel et du sport.

Peu adapté pour : situations où l''on recherche avant tout un équipement simple à prendre en main ou des fichiers légers. Les modes 5.7K, 10 bits et surtout ProRes RAW génèrent des volumes de données très importants et demandent des cartes rapides, beaucoup de stockage et une machine de montage suffisamment puissante. Le capteur Micro 4/3, plus petit qu''un APS-C ou plein format, donne également davantage de profondeur de champ à cadrage et ouverture équivalents et peut être désavantagé en très faible luminosité face à de grands capteurs récents.
Contenu du kit : Contenu du kit : 1x batterie  1x objectif 12-40 mm olympus 1x chiffon microfibre   1x cache objetif  1x cache capteur 1x chargeur batterie 1xconnecteur d''alimentation usb vers type c
Marque : PANASONIC LUMIX
Modèle : PANASONIC LUMIX GH7
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  3,
  3,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  1400.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'THE-SONY-HANDYCAM-HDR-TD30VE',
  'The Sony Handycam HDR-TD30VE',
  'Sony Handycam HDR-TD30VE

Caméscope numérique conçu pour la captation Full HD 2D et 3D, équipé de deux capteurs Exmor R CMOS rétroéclairés 1/3,91" d’environ 5,43 Mpx chacun. En mode 3D, les deux objectifs/capteurs enregistrent simultanément les images gauche et droite.

Objectif Sony G avec zoom optique 10×, focale réelle 3,2–32 mm et ouverture f/1.8–3.4. En équivalent 35 mm, la plage atteint environ 29,8–298 mm en vidéo 2D et 33,4–400,8 mm en 3D. Stabilisation optique SteadyShot avec mode Active.

Enregistrement 2D Full HD 1920×1080 jusqu’à 50p à environ 28 Mbit/s, ainsi que 25p et 50i. En 3D, enregistrement de deux flux 1920×1080/50i en MPEG4-MVC/AVCHD 2.0. Stockage sur SD/SDHC/SDXC ou Memory Stick.

Connectique : entrée microphone mini-jack, sortie casque mini-jack, Micro-HDMI et USB intégré. Microphone interne avec enregistrement Dolby Digital 5.1. Écran tactile 3,5" de 1,229 million de points. Le modèle VE intègre également un GPS pour la géolocalisation.

Performant pour : captations longues, conférences, événements, reportages et sujets relativement éloignés. Le 1080p50, le zoom optique 10× et la stabilisation sont encore parfaitement exploitables pour une captation Full HD. Sa fonction 3D stéréoscopique est surtout intéressante aujourd''hui pour des projets pédagogiques ou expérimentaux.

Peu adapté pour : production moderne nécessitant 4K, 10 bits, Log, RAW, faible profondeur de champ ou forte latitude d’étalonnage. Ses petits capteurs sont également beaucoup moins performants en basse lumière qu''un GH7, une BMPCC 4K ou un hybride APS-C/plein format. C''est donc aujourd''hui davantage une caméra de captation Full HD spécialisée, avec la 3D comme particularité, qu''une caméra de production cinéma.

Identifiants repérés (1/3) : HDR-TD30VE
Contenu du kit : Contenu du kit : 1x batterie  1x chiffon microfibre  1x chargeur batterie 1x Télécommande
Marque : SONY
Modèle : HDR-TD30VE
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  3,
  3,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  500.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'PNASONIC-HANDYCAM-WX-979-4K-',
  'PNASONIC Handycam WX 979 4k',
  'Panasonic HC-WX979 4K

Caméscope numérique 4K UHD équipé d’un capteur BSI MOS 1/2,3" d’environ 8 Mpx et d’un objectif Leica Dicomar avec zoom optique 20×. Il dispose d’une stabilisation HYBRID O.I.S.+, d’un autofocus automatique ou manuel et d’une fonction Twin Camera, avec une seconde petite caméra intégrée à l’écran permettant d’enregistrer simultanément un deuxième angle en incrustation.

Enregistrement jusqu’en 4K UHD 3840×2160 à 25 i/s, 72 Mbit/s. En Full HD, il monte jusqu’à 1920×1080 à 50 i/s progressif, avec des modes à 50 ou 28 Mbit/s. Enregistrement sur cartes SD/SDHC/SDXC, aux formats MP4 ou AVCHD selon le mode. Il possède également une entrée microphone et propose l''enregistrement audio jusqu''en 5.1 canaux en AVCHD.

Performant pour : captation d’événements, conférences, spectacles, voyages et sujets éloignés. Son zoom optique 20×, sa stabilisation et son format caméscope sont particulièrement pratiques pour filmer longtemps sans changer d''objectif. Le 1080p50 convient bien aux mouvements rapides et permet un ralenti ×2 sur une timeline 25 i/s. La 4K apporte davantage de détails et permet aussi de recadrer dans une production finale 1080p.

Peu adapté pour : courts-métrages recherchant une faible profondeur de champ, tournages en très basse lumière ou productions nécessitant 4K 50/60p, 10 bits, Log ou RAW. La 4K est limitée à 25 i/s, et le petit capteur 1/2,3" offre beaucoup moins de latitude qu''un GH7 ou une Blackmagic Pocket Cinema Camera 4K.
Contenu du kit : Contenu du kit : 1x batterie  1x chiffon microfibre  1x chargeur batterie 1x Télécommande
Marque : PANASONIC
Modèle : WX979
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  1,
  1,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  700.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'CANON-GX10-4K-PANASONIC-GX10',
  'CANON GX10 4K',
  'Canon LEGRIA GX10 4K

Caméscope semi-professionnel équipé d’un capteur CMOS 1" d’environ 13,4 Mpx, dont 8,29 Mpx effectifs en vidéo 4K. Objectif intégré avec zoom optique 15×, couvrant environ 25,5–382,5 mm en équivalent 35 mm. Il dispose du Dual Pixel CMOS AF, d’une stabilisation d’image 5 axes, d’un filtre ND intégré jusqu’à 8 stops et d’un écran tactile orientable 3,5".

Enregistrement interne jusqu’en 4K UHD 3840×2160 à 50 i/s, avec un débit maximal de 150 Mbit/s, ou en Full HD jusqu’à 50 i/s. Enregistrement interne en MP4 H.264, 8 bits 4:2:0. Il dispose de 2 emplacements SD/SDHC/SDXC, permettant notamment d’assurer de longues captations et des sauvegardes.

Performant pour : captation de conférences, spectacles, événements, interviews, reportages et sujets éloignés. Le capteur 1", le 4K 50p, le zoom 15×, le Dual Pixel AF et les filtres ND intégrés en font un caméscope nettement plus sérieux que les petits LEGRIA grand public. Il est particulièrement adapté aux situations où il faut pouvoir cadrer rapidement du 25,5 mm grand-angle jusqu’à environ 382 mm sans changer d’objectif.

Peu adapté pour : productions nécessitant du RAW, du 10 bits 4K interne, du Log moderne très poussé ou une faible profondeur de champ comparable à un grand capteur. L''enregistrement 4K interne reste en 8 bits, ce qui offre moins de latitude en étalonnage qu’un GH7 ou une Blackmagic Pocket Cinema Camera 4K. En revanche, pour de la captation longue et autonome, son ergonomie de caméscope, son zoom 15× et ses doubles cartes SD peuvent être plus pratiques que ces caméras cinéma/hybrides.
Contenu du kit : Contenu du kit : 1x batterie  1x chiffon microfibre  1x chargeur batterie 1x sangle
Marque : PANASONIC
Modèle : GX10 4K
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  1,
  1,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  1000.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'KIT-GOPRO-HERO4-SILVER-GOPRO',
  'KIT GOPRO HERO4 SILVER',
  'GoPro HERO4 Silver

Caméra d’action compacte équipée d’un capteur d’environ 12 Mpx et d’un objectif ultra grand-angle. Enregistrement jusqu’en 4K à 15 i/s, 2.7K à 30 i/s, 1080p à 60 i/s et 720p jusqu’à 120 i/s. Dispose d’un écran tactile intégré, du Wi-Fi et Bluetooth et enregistre sur carte microSD. Modes photo jusqu’à 12 Mpx, avec rafale jusqu’à 30 images/s.

Performante pour : prises de vue POV, sport, plans embarqués, véhicules, timelapses, coulisses de tournage et espaces très réduits. Son très grand-angle permet de capturer une grande partie de la scène à courte distance. Avec son caisson étanche, elle peut également être utilisée pour des prises de vue sous l’eau. Le 1080p60 permet un ralenti ×2,4 dans une timeline 25 i/s et le 720p120 jusqu’à ×4,8.

Peu adaptée pour : faible luminosité, interviews classiques, portraits et sujets éloignés. Elle ne possède pas de zoom optique ni de stabilisation électronique intégrée. La 4K à seulement 15 i/s est trop peu fluide pour la majorité des vidéos ; le 1080p 25/30/50/60 i/s est beaucoup plus pertinent en utilisation normale.

Accessoires compatibles : utilise le système de fixation GoPro classique avec pattes de montage, compatible avec de nombreux supports GoPro : fixation casque, harnais poitrine, ventouse, poignée, perche, trépied via adaptateur et différents supports adhésifs.
Contenu du kit : Contenu du kit : 1x batterie  1x chiffon microfibre  1x chargeur batterie 1x Bracelet de contraole 1x Cable usbc 1x kit pièces de fixation
Marque : GOPRO
Modèle : HEROS 4 silver
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  3,
  3,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  60.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'KIT-GOPRO-HERO3-SILVER-GOPRO',
  'KIT GOPRO HERO3+ SILVER',
  'GoPro HERO3+ Silver

Caméra d’action compacte équipée d’un capteur d’environ 10 Mpx et d’un objectif ultra grand-angle. Enregistrement vidéo jusqu’en Full HD 1080p à 60 i/s, 960p à 60 i/s et 720p jusqu’à 120 i/s. Contrairement à la HERO4 Silver, elle n’enregistre pas en 4K. Enregistrement sur carte microSD, avec Wi-Fi intégré pour le contrôle à distance depuis l’application GoPro.

Performante pour : prises de vue POV, sport, plans embarqués, véhicules, timelapses, making-of et endroits exigus. Le 1080p60 permet un ralenti ×2,4 dans une timeline 25 i/s et le 720p120 jusqu’à ×4,8. Avec son caisson étanche, elle convient également aux prises de vue aquatiques.

Peu adaptée pour : faible luminosité, interviews, portraits, sujets éloignés ou productions nécessitant de la 4K. Elle ne dispose ni de zoom optique, ni de stabilisation électronique intégrée, ni d’écran tactile intégré. Son petit capteur et son très grand-angle la destinent principalement aux plans d’action plutôt qu’à une utilisation comme caméra principale.

Accessoires compatibles : système de fixation GoPro classique à pattes, compatible avec les supports casque, harnais poitrine, ventouse, perches, poignées, fixations adhésives et adaptateurs pour trépied. Une grande partie des accessoires de fixation utilisés avec la HERO4 Silver peut donc également servir avec la HERO3+ Silver.
Contenu du kit : Contenu du kit : 1x batterie  1x chiffon microfibre  1x chargeur batterie 1x Bracelet de contraole 1x Cable usbc 1x kit pièces de fixation
Marque : GOPRO
Modèle : HEROS 3+ silver
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  1,
  1,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  30.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'KIT-GOPRO-MAX360-GOPRO-GOPRO',
  'KIT GOPRO MAX360',
  'GoPro MAX 360

Caméra d’action 360° équipée de deux objectifs ultra grand-angle permettant d’enregistrer simultanément dans toutes les directions. Enregistrement 360° jusqu’en 5.6K à 30 i/s avec assemblage des deux images, ou utilisation d’un seul objectif en mode HERO jusqu’en 1440p à 60 i/s. Photos panoramiques jusqu’à environ 16,6 Mpx. Dispose de la stabilisation électronique Max HyperSmooth, du nivellement automatique de l’horizon, d’un écran tactile, du Wi-Fi, Bluetooth et GPS. Enregistrement sur carte microSD.

Performante pour : vidéo 360°, visites virtuelles, VR, sport, plans embarqués, POV et situations où le cadrage doit pouvoir être choisi après le tournage. L’enregistrement sphérique permet de recadrer ensuite une vidéo classique dans différentes directions et de créer des mouvements de caméra artificiels en postproduction. La stabilisation Max HyperSmooth est particulièrement efficace pour les prises de vue en mouvement.

Peu adaptée pour : faible luminosité, tournages nécessitant une faible profondeur de champ ou utilisation comme caméra principale pour une production cinéma. La définition 5.6K est répartie sur l’ensemble de la sphère 360° : une fois l’image fortement recadrée en vidéo classique, la définition réellement visible est nettement inférieure à celle d’une caméra 4K filmant directement le même cadrage. Les deux objectifs très exposés sont également particulièrement sensibles aux rayures et aux chocs.

Accessoires compatibles : système de fixation GoPro standard à pattes rabattables intégrées, compatible avec perches, poignées, trépieds via adaptateur, harnais, fixations casque et ventouses. Pour exploiter l’effet de perche invisible en 360°, une perche fine alignée avec la caméra est particulièrement adaptée.

Identifiants repérés (1/2) : GOPRO MAX361
Contenu du kit : Contenu du kit : 1x batterie  1x chiffon microfibre  1x chargeur batterie  1x Cable usbc 1x kit pièces de fixation
Marque : GOPRO
Modèle : GOPRO MAX360
Catégorie : Vidéo
Emplacement : Étagère enreg. vidéo',
  2,
  2,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  160.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'DJI-RS3-MINI-DJI-RS3-MINI-ET',
  'DJI RS3 Mini',
  'Stabilisateur motorisé 3 axes destiné aux appareils photo hybrides et reflex compacts. Permet de stabiliser les mouvements de panoramique, inclinaison et roulis afin d’obtenir des plans vidéo fluides à main levée. Poids d’environ 795 g en configuration portrait et charge utile maximale de 2 kg. Il intègre les algorithmes de stabilisation DJI RS de 3ᵉ génération.

Dispose d’un écran tactile couleur 1,4", d’une connexion Bluetooth 5.1 et d’un port USB-C. La batterie intégrée de 2450 mAh offre jusqu’à environ 10 heures d’autonomie, avec une recharge d’environ 2,5 heures. Il permet également de passer en prise de vue verticale native sans accessoire supplémentaire.

Performant pour : courts-métrages, clips, interviews en mouvement, suivi de personnes, plans de marche, événements et mouvements de caméra nécessitant davantage de stabilité qu’une stabilisation optique ou IBIS seule.

Peu adapté pour : configurations dépassant 2 kg, grosses caméras cinéma, téléobjectifs lourds ou rigs comprenant de nombreux accessoires. Le boîtier et l’objectif doivent également pouvoir être correctement équilibrés sur les trois axes avant utilisation.

Compatibilité : conçu principalement pour les hybrides compacts Sony, Canon, Panasonic, Nikon et Fujifilm. Pour ton parc, un Panasonic GH7 ou un Sony A7 IV avec un objectif raisonnablement léger correspondent beaucoup mieux à son usage qu’une configuration lourde de Blackmagic Pocket Cinema Camera 4K. La compatibilité des commandes électroniques (déclenchement, Bluetooth, etc.) dépend toutefois précisément du boîtier utilisé.
Contenu du kit : 1x Stabilisateur 1x cable USBC 1x poignée triped
Marque : DJI
Modèle : RS3 Mini
Catégorie : Stabilisateur 2k
Emplacement : Étagère support vidéo',
  11,
  11,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  100.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'FEIYUTECH-G4-3-AXIS-FEIYUTEC',
  'FEIYUTECH G4 3 AXIS',
  'FeiyuTech G4 3-Axis

Stabilisateur motorisé 3 axes destiné principalement aux petites caméras d’action de type GoPro. Stabilise électroniquement les mouvements sur les axes panoramique, inclinaison et roulis afin d’obtenir des plans plus fluides lors de la marche, du suivi d’un sujet ou de prises de vue en mouvement.

Alimentation par 2 batteries rechargeables 18350. Le G4 dispose de plusieurs modes de stabilisation permettant notamment de verrouiller l’orientation de la caméra ou de suivre les mouvements de la poignée. La caméra est maintenue mécaniquement sur la nacelle ; pas de stabilisation numérique de l’image, le mouvement est compensé directement par les trois moteurs.

Performant pour : plans en mouvement, marche, suivi de personnes, POV, sport et travelling léger avec une caméra d’action. Particulièrement intéressant avec les anciennes GoPro qui ne disposent pas de stabilisation électronique intégrée.

Peu adapté pour : appareils photo hybrides/reflex ou caméras lourdes. C’est un ancien stabilisateur conçu autour du format des petites GoPro et sa compatibilité est donc beaucoup plus limitée qu’un DJI RS 3 Mini.

Compatibilité : principalement GoPro HERO3, HERO3+ et HERO4 et caméras d’action présentant des dimensions/poids similaires. Dans ton parc, il est donc particulièrement pertinent avec les GoPro HERO3+ Silver et HERO4 Silver. Il n’est pas adapté à la GoPro MAX 360, ni aux Canon EOS, GH7, A7 IV ou Blackmagic Pocket Cinema Camera 4K.
Contenu du kit : 1x Stabilisateur 1x cable USBC
Marque : FEIYUTECH
Modèle : G4 3 AXIS
Catégorie : Stabilisateur 1k
Emplacement : Étagère support vidéo',
  1,
  1,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  80.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'DJIOSMO-MOBILE-6-DJI-MOBILE-',
  'DJIOSMO MOBILE 6',
  'DJI Osmo Mobile 6

Stabilisateur motorisé 3 axes pour smartphone, conçu pour compenser les mouvements de panoramique, inclinaison et roulis lors de prises de vue vidéo. Il dispose d’une perche télescopique intégrée, d’une fixation magnétique pour smartphone, d’un joystick et d’une molette permettant notamment de contrôler le zoom ou la mise au point via l’application DJI Mimo. Connexion au smartphone en Bluetooth 5.1.

Compatible avec les smartphones pesant 170 à 290 g, d’une largeur de 67 à 84 mm et d’une épaisseur de 6,9 à 10 mm. Il n’est pas conçu pour les appareils photo ou caméras. Filetage inférieur standard 1/4"-20 permettant de monter le stabilisateur sur un trépied.

Batterie intégrée Li-Po 1000 mAh, autonomie maximale d’environ 6 h 24 min et recharge en environ 1 h 24 min avec un chargeur USB-C 10 W. Poids du stabilisateur : environ 305 g, plus 25 g pour la pince magnétique.

Performant pour : tournages au smartphone, interviews mobiles, reportages, réseaux sociaux, travelling en marchant, suivi de personnes, timelapses et plans verticaux. La fonction ActiveTrack permet au stabilisateur de suivre automatiquement un sujet via DJI Mimo.

Peu adapté pour : appareils photo, caméras d’action ou smartphones très lourds équipés de gros accessoires. Un téléphone dépassant 290 g avec sa coque, son objectif additionnel ou ses accessoires sort des spécifications recommandées. Pour les Canon EOS, GH7, A7 IV ou Blackmagic de ton parc, il faut utiliser un stabilisateur caméra comme le DJI RS 3 Mini, pas l''Osmo Mobile 6.
Contenu du kit : 1x Stabilisateur 1x cable USBC 1x pochette
Marque : DJI
Modèle : MOBILE 6.
Catégorie : Stabilisateur tél
Emplacement : Étagère support vidéo',
  59,
  59,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  90.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'FEIYUTECH-AK2000S-FEIYUTECH-',
  'FEIYUTECH AK2000s',
  'FeiyuTech AK2000S

Stabilisateur motorisé 3 axes destiné aux appareils photo hybrides et reflex. Il stabilise les mouvements de panoramique, inclinaison et roulis pour obtenir des plans fluides à main levée. Charge utile maximale : 2,2 kg lorsque l''ensemble est correctement équilibré. Poids du stabilisateur : environ 1,1 kg. Il dispose d''un écran tactile LCD, d''une molette multifonction, de verrous sur les trois axes et d''une plaque rapide compatible ARCA.

Batterie intégrée 2200 mAh, rechargeable en USB-C jusqu''à 18 W. Autonomie annoncée d''environ 7 h en utilisation, pouvant atteindre 14 h dans des conditions optimales/veille. Modes disponibles notamment : suivi, verrouillage, Inception, selfie, portrait et timelapse.

Performant pour : courts-métrages, clips, interviews en mouvement, suivi de personnes, travelling à pied et prises de vue nécessitant des mouvements de caméra fluides.

Peu adapté pour : configurations dépassant 2,2 kg, grosses caméras cinéma ou ensembles avec objectif, cage, moniteur et accessoires lourds. Le boîtier et l''objectif doivent être correctement équilibrés avant d''activer les moteurs.

Compatibilité : FeiyuTech le prévoit notamment pour différentes séries Sony A7/A9/A6xxx, Canon EOS R/M, Panasonic GH4/GH5/GH5S, Nikon Z6/Z7 et Fujifilm X-T. La compatibilité mécanique est assez large tant que l''ensemble respecte les dimensions et la charge maximale ; en revanche, les fonctions électroniques de contrôle dépendent précisément du boîtier.
Contenu du kit : 1x Stabilisateur 1x cable USBC  1x rail de gestion zoom 1x poigné étendue
Marque : FEIYUTECH
Modèle : AK2000s
Catégorie : Stabilisateur 2k
Emplacement : Étagère support vidéo',
  1,
  1,
  'available',
  NULL,
  NULL,
  'numbered',
  '["Feistab-01"]',
  100.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'FEIYUTECH-AK4000S-FEIYUTECH-',
  'FEIYUTECH AK4000s',
  'FeiyuTech AK4000

Stabilisateur motorisé 3 axes professionnel destiné aux appareils photo hybrides, reflex et certaines caméras cinéma. Il compense les mouvements de panoramique, inclinaison et roulis pour obtenir des plans fluides à main levée. Sa charge utile atteint 4 kg avec une caméra correctement équilibrée, ce qui le rend nettement plus adapté aux configurations lourdes que l’AK2000S. Le stabilisateur pèse environ 1,44 kg hors batteries.

Il dispose d’un écran tactile LCD, d’une molette multifonction pour contrôler notamment le focus/zoom, du Bluetooth et Wi-Fi, d’une plaque rapide compatible Manfrotto PL501 et de plusieurs filetages 1/4" pour accessoires. Alimentation par 4 batteries Li-ion 18650 de 2200 mAh, avec une autonomie théorique pouvant atteindre 12 heures lorsque l’ensemble est correctement équilibré.

Performant pour : courts-métrages, clips, interviews en mouvement, travelling, suivi de sujets et surtout configurations relativement lourdes comprenant un gros objectif ou certains accessoires. Sa capacité de 4 kg permet également l''utilisation de certaines caméras cinéma compactes.

Peu adapté pour : tournages très légers ou longs à bout de bras, puisqu’il est relativement lourd avant même d’ajouter la caméra. Il n’est également pas résistant aux éclaboussures.

Compatibilité : FeiyuTech indique notamment les Canon 5D III/IV, 6D, 80D, 1D X II, les séries Sony α7/α9, les Panasonic GH5/GH5S, le Nikon D850 et même certaines configurations Canon C300. D’autres boîtiers de dimensions et poids compatibles peuvent être montés mécaniquement, mais les commandes électroniques dépendent du modèle exact
Contenu du kit : 1x Stabilisateur 1x cable USBC   kit batterie et recharge
Marque : FEIYUTECH
Modèle : AK4000s
Catégorie : Stabilisateur 4k
Emplacement : Étagère support vidéo',
  1,
  1,
  'available',
  NULL,
  NULL,
  'numbered',
  '["Feistab-01"]',
  200.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'FLYCAM-U-FLYCAM-FLYCAM-U-FLY',
  'FLYCAM-U-flycam',
  'Stabilisateur mécanique à contrepoids pour caméras et appareils photo, de type Steadicam. Contrairement aux DJI RS ou FeiyuTech, il ne possède aucun moteur ni électronique : la stabilisation repose sur un gimbal mécanique, l''équilibrage de la caméra et des contrepoids réglables. La plateforme caméra peut être déplacée pour régler le centre de gravité, tandis que les masses inférieures permettent d''ajuster l''équilibre vertical.

Performant pour : travelling à pied, suivi de personnes, mouvements autour d''un sujet, escaliers et plans nécessitant une stabilisation sans batterie. Une fois correctement équilibré, il permet d''atténuer fortement les mouvements de l''opérateur.

Peu adapté pour : utilisation rapide par un débutant. Chaque combinaison boîtier + objectif nécessite un équilibrage manuel précis, et il n''offre évidemment aucun suivi automatique, contrôle caméra ou verrouillage motorisé des axes. Bref, contrairement à un RS3 Mini, il faut que l''opérateur fasse une partie du boulot — technologie scandaleuse.

Compatibilité : plateforme de fixation universelle, principalement destinée aux caméscopes compacts et configurations DSLR/hybrides suffisamment légères pour être correctement équilibrées. Des sources d''époque mentionnent notamment son utilisation avec des caméras DV et des DSLR.
Contenu du kit : 1x Stabilisateur 1x cable USBC   kit batterie et recharge
Marque : FLYCAM
Modèle : U-flycam
Catégorie : Stabilisateur manuelle
Emplacement : Étagère support vidéo',
  1,
  1,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  44.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'VELBONDV-7000-VELBON-DV-7000',
  'VelbonDV-7000',
  'Velbon DV-7000N

Trépied vidéo professionnel en aluminium, conçu pour supporter des caméras, caméscopes et appareils photo relativement lourds. Il est équipé d’une tête fluide PH-368 à 2 axes, permettant d’effectuer des mouvements de panoramique et d’inclinaison fluides, particulièrement adaptés à la captation vidéo. Plaque de fixation rapide QB-6RL avec vis caméra standard 1/4"-20.

Hauteur réglable de 56,8 à 162,5 cm, longueur repliée 70 cm et poids d’environ 3,37 kg. Charge recommandée : 4,5 kg ; charge maximale : 6 kg. Les jambes comportent 3 sections et la colonne centrale se règle par manivelle.

Performant pour : captation de conférences, interviews, spectacles, événements et plans vidéo nécessitant des panoramiques fluides. Sa capacité de charge permet d''accueillir aussi bien des caméscopes que des configurations hybrides relativement lourdes.

Compatibilité : grâce à sa fixation standard 1/4", il peut accueillir la majorité des appareils de ton parc : Canon EOS, Panasonic GH7, Sony A7 IV, Blackmagic Pocket Cinema Camera 4K, Canon LEGRIA, caméscopes Panasonic/Sony et GoPro avec adaptateur trépied approprié, tant que la configuration reste sous la charge maximale.

Identifiants repérés (1/13) : DV-7000n
Marque : Velbon
Modèle : DV-7000n
Catégorie : Trépied lourd
Emplacement : Étagère support vidéo',
  13,
  13,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  504.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'HAMA-STAR-62-3D-HAMA-STAR-62',
  'HAMA STAR 62 3D',
  'Hama Star 62 3D

Trépied photo/vidéo en aluminium équipé d’une tête 3D à 3 axes, permettant de régler séparément l’orientation horizontale, verticale et le cadrage portrait/paysage. Hauteur réglable de 64 à 160 cm, poids d’environ 1,5 kg et charge maximale annoncée de 4 kg. Fixation caméra standard 1/4" avec plateau rapide.

Dispose de 3 sections de jambes, d’une colonne centrale réglable par manivelle, de niveaux à bulle, d’une poignée de transport et d’un crochet permettant d’ajouter du poids pour améliorer la stabilité.

Performant pour : photographie, interviews, plans fixes, studio, conférences et captations vidéo simples. La tête 3D permet des réglages précis du cadrage et la charge de 4 kg accepte la majorité des reflex, hybrides et caméscopes légers.

Peu adapté pour : mouvements vidéo très fluides comme les panoramiques professionnels, car il ne possède pas une véritable tête fluide vidéo comme le Velbon DV-7000N. Il est également moins adapté aux grosses configurations cinéma ou aux appareils équipés de rigs lourds.

Compatibilité : grâce au filetage standard 1/4", compatible avec la majorité des appareils de ton parc : Canon EOS 250D/400D/550D/600D/700D/750D, Panasonic GH7, Sony A7 IV, Blackmagic Pocket Cinema Camera 4K, Canon LEGRIA et différents caméscopes Sony/Panasonic, tant que la configuration reste sous 4 kg. Les GoPro nécessitent un adaptateur de fixation approprié.

Identifiants repérés (24/25) : 63 3D, 64 3D, 65 3D, 66 3D, 67 3D, 68 3D, 69 3D, 70 3D, 71 3D, 72 3D, 73 3D, 74 3D, 75 3D, 76 3D, 77 3D, 78 3D, 79 3D, 80 3D, 81 3D, 82 3D, 83 3D, 84 3D, 85 3D, 86 3D
Marque : HAMA STAR
Modèle : 62 3D
Catégorie : Trépied Moyen
Emplacement : Étagère support vidéo',
  25,
  25,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  30.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'NAVITECH-NAVITECH-INCONNU-ET',
  'Navitech',
  'Navitech Lightweight Aluminium Tripod

Trépied léger en aluminium destiné aux appareils photo et petits caméscopes. Il dispose de jambes télescopiques à 3 sections avec verrouillage par leviers, permettant un déploiement rapide et un transport facile. Pour certaines variantes compactes Navitech actuellement référencées, la hauteur est d’environ 28 cm replié à 56 cm déployé.

Performant pour : photographie, plans vidéo fixes, petites caméras, caméscopes et utilisation mobile nécessitant un trépied léger et peu encombrant.

Peu adapté pour : grosses configurations, Blackmagic équipée d’un rig, longs téléobjectifs ou mouvements vidéo professionnels. Sa construction légère privilégie la portabilité plutôt que la rigidité d’un trépied vidéo lourd.

Compatibilité : les trépieds Navitech de cette gamme sont commercialisés pour de nombreux appareils photo et caméscopes, y compris des Canon EOS, Sony et caméscopes Canon/Panasonic
Marque : Navitech
Modèle : INCONNU
Catégorie : Trépied Leger
Emplacement : Étagère support vidéo',
  5,
  5,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  20.00,
  '[]'
);

INSERT INTO materials (id, name, description, quantity_total, quantity_available, status, cover_image, gallery, tracking_mode, identifiers, replacement_cost, damaged_items) VALUES (
  'DORR-MONOPOD-170-DORR-170-ET',
  'Dorr monopod 170',
  'DÖRR Monopod 170

Monopode professionnel 4 sections en aluminium, conçu pour stabiliser un appareil photo ou une caméra tout en conservant davantage de mobilité qu’avec un trépied. Hauteur réglable de 62 à 179 cm, poids d’environ 480 g et charge maximale de 3 kg. Fixation standard 1/4" pour appareil photo/caméra.

Il dispose d’une poignée en mousse, d’une dragonne et d’un pied combinant embout en caoutchouc et pointe métallique (spike) pour s’adapter aux sols intérieurs ou extérieurs. Une housse de transport et un support pour téléobjectif sont prévus avec le monopode.

Performant pour : photographie sportive, événementielle, animalier, utilisation de téléobjectifs lourds et captations nécessitant davantage de stabilité tout en restant mobile. Il permet notamment de soulager le poids d''un appareil équipé d''un 55–250 mm ou d''un autre téléobjectif.

Peu adapté pour : plans vidéo totalement fixes ou mouvements panoramiques fluides nécessitant une véritable tête vidéo. Un monopode stabilise principalement les mouvements verticaux et supporte le poids de la caméra, mais ne remplace pas un trépied.

Compatibilité : grâce au filetage 1/4" et à sa charge maximale de 3 kg, compatible avec la majorité des Canon EOS, Panasonic GH7, Sony A7 IV, caméscopes légers et autres appareils standards tant que l''ensemble boîtier + objectif reste sous la charge maximale.
Marque : Dorr
Modèle : 170
Catégorie : Monopod
Emplacement : Étagère support vidéo',
  5,
  5,
  'available',
  NULL,
  NULL,
  'generic',
  '[]',
  25.00,
  '[]'
);
