<?php
// $Id$
define("_INSTALL_L0","Bienvenue dans l'assistant d'installation de XOOPS 2.0");
define("_INSTALL_L70","Merci de changer les permissions du fichier mainfile.php afin qu'il soit accessible en &eacute;criture par le serveur (ex. chmod 666 sur un serveur UNIX/LINUX, ou v&eacute;rifier les propri&eacute;t&eacute;s du fichier et s'assurer que l'option 'Lecture seule' n'est pas coch&eacute;e sur un serveur Windows). Rechargez cette page une fois les permissions chang&eacute;es.");
//define("_INSTALL_L71","Cliquez sur le bouton ci-dessous pour commencer l'installation.");
define("_INSTALL_L1","Ouvrez le fichier mainfile.php avec un &eacute;diteur de texte et cherchez le code suivant &agrave; la ligne 31 :");
define("_INSTALL_L2","Maintenant, changez cette ligne en :");
define("_INSTALL_L3","Ensuite, &agrave; la ligne 35, changez %s en %s");
define("_INSTALL_L4","OK, j'ai saisi les param&egrave;tres ci-dessus, laissez-moi essayer &agrave; nouveau !");
define("_INSTALL_L5","ATTENTION !");
define("_INSTALL_L6","Il y a une diff&eacute;rence entre votre configuration XOOPS_ROOT_PATH &agrave; la ligne 31 du fichier mainfile.php et les infos du chemin racine que nous avons d&eacute;tect&eacute;es.");
define("_INSTALL_L7","Vos param&egrave;tres :&nbsp;");
define("_INSTALL_L8","Nous avons d&eacute;tect&eacute; :&nbsp;");
define("_INSTALL_L9","( Sur les plateformes Windows, vous pouvez recevoir ce message d'erreur m&ecirc;me si votre configuration est correcte. Si c'est le cas, merci de presser le bouton ci-dessous pour continuer)");
define("_INSTALL_L10","Merci de presser le bouton ci-dessous pour continuer si tout est OK.");
define("_INSTALL_L11","Le chemin du r&eacute;pertoire racine de XOOPS sur le serveur :&nbsp;");
define("_INSTALL_L12","L'URL du r&eacute;pertoire racine de XOOPS :&nbsp;");
define("_INSTALL_L13","Si les param&egrave;tres ci-dessus sont corrects, pressez le bouton ci-dessous pour continuer.");
define("_INSTALL_L14","Suivant");
define("_INSTALL_L15","Merci d'&eacute;diter le fichier mainfile.php et d'entrer les donn&eacute;es requises pour votre base de donn&eacute;es");
define("_INSTALL_L16","%s est le nom d'h&ocirc;te de votre serveur de base de donn&eacute;es.");
define("_INSTALL_L17","%s est le nom d'utilisateur de votre compte de base de donn&eacute;es.");
define("_INSTALL_L18","%s est le mot de passe requis pour acc&eacute;der &agrave; votre base de donn&eacute;es.");
define("_INSTALL_L19","%s est le nom de votre base de donn&eacute;es dans laquelle les tables de XOOPS seront cr&eacute;&eacute;es.");
define("_INSTALL_L20","%s est le pr&eacute;fixe des tables qui seront cr&eacute;&eacute;es durant l'installation.");
define("_INSTALL_L21","La base de donn&eacute;es suivante n'a pas &eacute;t&eacute; trouv&eacute;e sur le serveur :");
define("_INSTALL_L22","Dois-je la cr&eacute;er ?");
define("_INSTALL_L23","Oui");
define("_INSTALL_L24","Non");
define("_INSTALL_L25","Nous avons d&eacute;tect&eacute; les informations de configuration suivantes pour votre base de donn&eacute;es dans mainfile.php. Merci de rectifier maintenant si ce n'est pas correct.");
define("_INSTALL_L26","Configuration de la base de donn&eacute;es");
define("_INSTALL_L51","Base de donn&eacute;es");
define("_INSTALL_L66","Choisissez la base de donn&eacute;es &agrave; utiliser");
define("_INSTALL_L27","Nom d'h&ocirc;te de la base de donn&eacute;es");
define("_INSTALL_L67","Nom d'h&ocirc;te du serveur de base de donn&eacute;es. Si vous n'&ecirc;tes pas s&ucirc;r, 'localhost' fonctionne dans la majorit&eacute; des cas.");
define("_INSTALL_L28","Nom d'utilisateur de la base de donn&eacute;es");
define("_INSTALL_L65","Nom d'utilisateur de votre compte de base de donn&eacute;es sur le serveur.");
define("_INSTALL_L29","Nom de la base de donn&eacute;es");
define("_INSTALL_L64","Le nom de la base de donn&eacute;es sur le serveur. L'assistant d'installation va cr&eacute;er la base de donn&eacute;es si elle n'existe pas.");
define("_INSTALL_L52","Mot de passe de la base de donn&eacute;es");
define("_INSTALL_L68","Mot de passe de votre compte utilisateur de base de donn&eacute;es.");
define("_INSTALL_L30","Pr&eacute;fixe des tables");
define("_INSTALL_L63","Le pr&eacute;fixe sera ajout&eacute; &agrave; toutes les tables cr&eacute;&eacute;es pour &eacute;viter les conflits de noms dans la base de donn&eacute;es. Si vous n'&ecirc;tes pas s&ucirc;r, utilisez juste par d&eacute;faut 'xoops'.");
define("_INSTALL_L54","Utiliser les connexions persistentes ?");
define("_INSTALL_L69","Par d&eacute;faut c'est 'NON'. Choisissez 'NON' si vous n'&ecirc;tes pas s&ucirc;r.");
define("_INSTALL_L55","Chemin physique de XOOPS");
define("_INSTALL_L59","Chemin physique de votre r&eacute;pertoire racine XOOPS sans le slash / de fin.");
define("_INSTALL_L56","Chemin virtuel de XOOPS (URL)");
define("_INSTALL_L58","Chemin virtuel de votre r&eacute;pertoire racine XOOPS sans le slash / de fin.");

define("_INSTALL_L31","Impossible de cr&eacute;er la base de donn&eacute;es. Contactez l'administrateur du serveur pour des d&eacute;tails.");
define("_INSTALL_L32","Installation termin&eacute;e");
define("_INSTALL_L33","Cliquez <a href='../index.php'>ICI</a> pour voir la page d'acceuil de votre site.");
define("_INSTALL_L35","Si vous avez des erreurs, merci de contacter l'&eacute;quipe de support sur <a href='http://www.frxoops.org/' target='_blank'>Xoops France</a>");
define("_INSTALL_L36","Merci de choisir votre nom d'administrateur du site et votre mot de passe.");
define("_INSTALL_L37","Nom de l'Administrateur");
define("_INSTALL_L38","E-mail de l'Administrateur");
define("_INSTALL_L39","Mot de passe de l'Administrateur");
define("_INSTALL_L74","Confirmation du mot de passe");
define("_INSTALL_L40","Créer les tables"); // pas de codage html
define("_INSTALL_L41","Merci de revenir en arri&egrave;re et de saisir toutes les informations requises.");
define("_INSTALL_L42","Retour");
define("_INSTALL_L57","Merci d'entrer %s");

// %s is database name
define("_INSTALL_L43","Base de donn&eacute;es %s cr&eacute;&eacute;e !");

// %s is table name
define("_INSTALL_L44","Impossible de cr&eacute;er %s");
define("_INSTALL_L45","Table %s cr&eacute;&eacute;e");

define("_INSTALL_L46","Pour que les modules inclus dans le package fonctionnent correctement, les fichiers suivants doivent &ecirc;tre accessible en &eacute;criture par le serveur. Merci de changer les permissions pour ces fichiers. (ex. 'chmod 666 pour les fichiers' et 'chmod 777 pour les r&eacute;pertoires' sur un serveur UNIX/LINUX, ou de contr&ocirc;ler les propri&eacute;t&eacute;s de ces fichiers et de s'assurer que l'option 'Lecture seule' n'est pas coch&eacute;e sur un serveur Windows)");
define("_INSTALL_L47","Suivant");

define("_INSTALL_L53","Merci de confirmer les donn&eacute;es soumises suivantes :");

define("_INSTALL_L60","Impossible d'ouvrir mainfile.php. Merci de v&eacute;rifier les permissions du fichier et de recommencer.");
define("_INSTALL_L61","Impossible d'&eacute;crire dans mainfile.php. Contactez l'administrateur du serveur pour des d&eacute;tails.");
define("_INSTALL_L62","Les donn&eacute;es de configuration ont &eacute;t&eacute; sauvegard&eacute;es avec succ&egrave;s dans le fichier mainfile.php.");
define("_INSTALL_L72","Les r&eacute;pertoires suivants doivent &ecirc;tre cr&eacute;&eacute;s avec une permission d'&eacute;criture par le serveur. (ex. 'chmod 777 pour les r&eacute;pertoires' sur un serveur UNIX/LINUX)");
define("_INSTALL_L73","Adresse e-mail invalide");

// add by haruki
define("_INSTALL_L80","Introduction");
define("_INSTALL_L81","Vérifier les permissions des fichiers"); // pas de codage html
define("_INSTALL_L82","V&eacute;rification des permissions des fichiers et des r&eacute;pertoires...");
define("_INSTALL_L83","Le fichier %s N'EST PAS ouvert en &eacute;criture.");
define("_INSTALL_L84","Le fichier %s est ouvert en &eacute;criture.");
define("_INSTALL_L85","Le r&eacute;pertoire %s N'EST PAS ouvert en &eacute;criture.");
define("_INSTALL_L86","Le r&eacute;pertoire %s est ouvert en &eacute;criture.");
define("_INSTALL_L87","Pas d'erreur d&eacute;tect&eacute;e.");
define("_INSTALL_L89","Paramètres généraux"); // pas de codage html
define("_INSTALL_L90","Configuration g&eacute;n&eacute;rale");
define("_INSTALL_L91","Confirmer");
define("_INSTALL_L92","Sauvegarder les paramètres"); // pas de codage html
define("_INSTALL_L93","Modifier les paramètres"); // pas de codage html
define("_INSTALL_L88","Sauvegarde des donn&eacute;es de configuration...");
define("_INSTALL_L94","Vérifier le chemin & l'URL"); // pas de codage html
define("_INSTALL_L127","V&eacute;rification du chemin des fichiers & de l'URL.");
define("_INSTALL_L95","Impossible de d&eacute;tecter le chemin physique de votre r&eacute;pertoire XOOPS.");
define("_INSTALL_L96","Il y a un conflit entre le chemin physique d&eacute;tect&eacute; (%s) et celui que vous avez saisi.");
define("_INSTALL_L97","Le <b>chemin physique</b> est correct.");

define("_INSTALL_L99","Le <b>chemin physique</b> doit &ecirc;tre un r&eacute;pertoire.");
define("_INSTALL_L100","Le <b>chemin virtuel</b> que vous avez saisi est une URL valide.");
define("_INSTALL_L101","Le <b>chemin virtuel</b> que vous avez saisi n'est pas une URL valide.");
define("_INSTALL_L102","Confirmer les paramètres de la base"); // pas de codage html
define("_INSTALL_L103","Recommencer depuis le début"); // pas de codage html
define("_INSTALL_L104","Vérifier la base de données"); // pas de codage html
define("_INSTALL_L105","Créer la base de donnéees"); // pas de codage html
define("_INSTALL_L106","Impossible de se connecter au serveur de base de donn&eacute;es.");
define("_INSTALL_L107","Merci de v&eacute;rifier le serveur de base de donn&eacute;es et sa configuration.");
define("_INSTALL_L108","La connexion au serveur de base donn&eacute;es est OK.");
define("_INSTALL_L109","La base de donn&eacute;es %s n'existe pas.");
define("_INSTALL_L110","La base de donn&eacute;es %s existe et est connectable.");
define("_INSTALL_L111","La connexion &agrave; la base de donn&eacute;es est OK.<br />Pressez le bouton ci-dessous pour cr&eacute;er les tables dans la base de donn&eacute;es.");
define("_INSTALL_L112","Paramètres de l'administrateur"); //pas de codage html
define("_INSTALL_L113","Table %s supprim&eacute;e.");
define("_INSTALL_L114","Echec de cr&eacute;ation des tables dans la base de donn&eacute;es.");
define("_INSTALL_L115","Tables de la base de donn&eacute;es cr&eacute;&eacute;es.");
define("_INSTALL_L116","Insérer les données"); // pas de codage html
define("_INSTALL_L117","Terminer");

define("_INSTALL_L118","Echec de cr&eacute;ation de la table %s.");
define("_INSTALL_L119","%d entr&eacute;e(s) ins&eacute;r&eacute;e(s) dans la table %s.");
define("_INSTALL_L120","Echec d'insertion de %d entr&eacute;es dans la table %s.");

define("_INSTALL_L121","Constante %s &eacute;crite avec %s.");
define("_INSTALL_L122","Echec d'&eacute;criture de la constante %s.");

define("_INSTALL_L123","Fichier %s stock&eacute; dans le r&eacute;pertoire cache/.");
define("_INSTALL_L124","Echec de stockage du fichier %s dans le r&eacute;pertoire cache/.");

define("_INSTALL_L125","Fichier %s &eacute;cras&eacute; par %s.");
define("_INSTALL_L126","Impossible d'&eacute;crire dans le fichier %s.");

define("_INSTALL_L130","L'installateur a d&eacute;tect&eacute; des tables pour XOOPS 1.3.x dans votre base de donn&eacute;es.<br />L'installateur va maintenant essayer de mettre &agrave; jour votre base de donn&eacute;es pour XOOPS 2.");
define("_INSTALL_L131","Les Tables pour XOOPS 2 existe d&eacute;j&agrave; dans votre base de donn&eacute;es.");
define("_INSTALL_L132","Mise à jour des tables"); // pas de codage html
define("_INSTALL_L133","Table %s mise &agrave; jour.");
define("_INSTALL_L134","Echec de mise &agrave; jour de la table %s.");
define("_INSTALL_L135","Echec de mise &agrave; jour des tables de la base de donn&eacute;es.");
define("_INSTALL_L136","Tables de la base de donn&eacute;es mises &agrave; jour.");
define("_INSTALL_L137","Mettre à jour les modules"); // pas de codage html
define("_INSTALL_L138","Mettre à jour les commentaires"); // pas de codage html
define("_INSTALL_L139","Mettre à jour les avatars"); // pas de codage html
define("_INSTALL_L140","Mettre à jour les emoticones"); // pas de codage html
define("_INSTALL_L141","L'installateur va maintenant mettre &agrave; jour chaque module pour qu'ils fonctionnent avec XOOPS 2.<br />Assurez-vous d'avoir upload&eacute; tous les fichiers du package XOOPS 2 sur votre serveur.<br />Cela peut prendre un certain temps pour finir.");
define("_INSTALL_L142","Mise &agrave; jour des modules...");
define("_INSTALL_L143","L'installateur va maintenant mettre &agrave; jour les donn&eacute;es de configuration de XOOPS 1.3.x pour &ecirc;tre utilis&eacute;es avec XOOPS 2.");
define("_INSTALL_L144","Mettre à jour la configuration"); // pas de codage html
define("_INSTALL_L145","Commentaire (ID : %s) ins&eacute;r&eacute; dans la base de donn&eacute;es.");
define("_INSTALL_L146","Impossible d'ins&eacute;rer le commentaire (ID : %s) dans la base de donn&eacute;es.");
define("_INSTALL_L147","Mise &agrave; jour des commentaires...");
define("_INSTALL_L148","Mise &agrave; jour termin&eacute;e.");
define("_INSTALL_L149","L'installateur va maintenant mettre &agrave; jour les envois de commentaires de XOOPS 1.3.x pour &ecirc;tre utilis&eacute;s dans XOOPS 2.<br />Cela peut prendre un certain temps pour finir.");
define("_INSTALL_L150","L'installateur va maintenant mettre &agrave; jour les &eacute;motic&ocirc;nes et les images de classement utilisateur pour &ecirc;tre utilis&eacute;s dans XOOPS 2.<br />Cela peut prendre un certain temps pour finir.");
define("_INSTALL_L151","L'installateur va maintenant mettre &agrave; jour les avatars utilisateurs pour &ecirc;tre utilis&eacute;s dans XOOPS 2.<br />Cela peut prendre un certain temps pour finir.");
define("_INSTALL_L155","Mise &agrave; jour des &eacute;motic&ocirc;nes/images de classement...");
define("_INSTALL_L156","Mise &agrave; jour des avatars utilisateurs...");
define("_INSTALL_L157","S&eacute;lectionnez le groupe utilisateurs par d&eacute;faut pour chaque type de groupe");
define("_INSTALL_L158","Groupes de la v1.3.x");
define("_INSTALL_L159","Webmestres");
define("_INSTALL_L160","Membres");
define("_INSTALL_L161","Anonymes");
define("_INSTALL_L162","Vous devez s&eacute;lectionner un groupe par d&eacute;faut pour chaque type de groupe.");
define("_INSTALL_L163","Table %s supprim&eacute;e.");
define("_INSTALL_L164","Echec de suppression de la table %s.");
define("_INSTALL_L165","Le site est actuellement ferm&eacute; pour maintenance. Merci de revenir plus tard.");

// %s is filename
define("_INSTALL_L152","Impossible d'ouvrir %s.");
define("_INSTALL_L153","Impossible de mettre &agrave; jour %s.");
define("_INSTALL_L154","%s mis &agrave; jour.");

define('_INSTALL_L128', "Choisissez le langage &agrave; utiliser pour la proc&eacute;dure d'installation");
define('_INSTALL_L200', 'Recharger');


define('_INSTALL_CHARSET','ISO-8859-1');
?>