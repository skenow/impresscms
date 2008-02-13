<?php
// $Id$
// Support Francophone de Xoops (www.frxoops.org)
//%%%%%%        File Name mainfile.php         %%%%%
define("_PLEASEWAIT","Merci de patienter");
define("_FETCHING","Chargement...");
define("_TAKINGBACK","Retour l&agrave; o&ugrave; vous &eacute;tiez...");
define("_LOGOUT","D&eacute;connexion");
define("_SUBJECT","Sujet");
define("_MESSAGEICON","Ic&ocirc;ne de message");
define("_COMMENTS","Commentaires");
define("_POSTANON","Poster en anonyme");
define("_DISABLESMILEY","D&eacute;sactiver les &eacute;motic&ocirc;nes");
define("_DISABLEHTML","D&eacute;sactiver le html");
define("_PREVIEW","Pr&eacute;visualiser");

define("_GO","Ok !");
define("_NESTED","Embo&icirc;t&eacute;");
define("_NOCOMMENTS","Pas de commentaires");
define("_FLAT","A plat");
define("_THREADED","Par conversation");
define("_OLDESTFIRST","Les + anciens en premier");
define("_NEWESTFIRST","Les + r&eacute;cents en premier");
define("_MORE","plus...");
define("_MULTIPAGE","Pour avoir votre article sur des pages multiples, ins&eacute;rer le mot <font color=red>[pagebreak]</font> (avec les crochets) dans l'article.");
define("_IFNOTRELOAD","Si la page ne se recharge pas automatiquement, merci de cliquer <a href=%s>ici</a>");
define("_WARNINSTALL2","ATTENTION : Le r&eacute;pertoire %s est pr&eacute;sent sur votre serveur. <br />Merci de supprimer ce r&eacute;pertoire pour des raisons de s&eacute;curit&eacute;.");
define("_WARNINWRITEABLE","ATTENTION : Le fichier %s est ouvert en &eacute;criture sur le serveur. <br />Merci de changer les permissions de ce fichier pour des raisons de s&eacute;curit&eacute;.<br /> sous Unix (444), sous Win32 (lecture seule)");

//%%%%%%        File Name themeuserpost.php         %%%%%
define("_PROFILE","Profil");
define("_POSTEDBY","Post&eacute; par");
define("_VISITWEBSITE","Visiter le site Web");
define("_SENDPMTO","Envoyer un message priv&eacute; &agrave; %s");
define("_SENDEMAILTO","Envoyer un courriel &agrave; %s");
define("_ADD","Ajouter");
define("_REPLY","R&eacute;pondre");
define("_DATE","Date");   // Posted date

//%%%%%%        File Name admin_functions.php         %%%%%
define("_MAIN","Principal");
define("_MANUAL","Manuel");
define("_INFO","Info");
define("_CPHOME","Accueil de l'admin");
define("_YOURHOME","Page d'accueil");

//%%%%%%        File Name misc.php (who's-online popup)        %%%%%
define("_WHOSONLINE","Qui est en ligne");
define('_GUESTS', 'Invit&eacute;(s)');
define('_MEMBERS', 'Membre(s)');
define("_ONLINEPHRASE","<b>%s</b> utilisateur(s) en ligne");
define("_ONLINEPHRASEX","dont <b>%s</b> sur <b>%s</b>");
define("_CLOSE","Fermer");  // Close window

//%%%%%%        File Name module.textsanitizer.php         %%%%%
define("_QUOTEC","Citation :");

//%%%%%%        File Name admin.php         %%%%%
define("_NOPERM","D&eacute;sol&eacute;, vous n'avez pas les droits pour acc&eacute;der &agrave; cette zone.");

//%%%%%                Common Phrases                %%%%%
define("_NO","Non");
define("_YES","Oui");
define("_EDIT","Editer");
define("_DELETE","Effacer");
define("_SUBMIT","Envoyer");
define("_MODULENOEXIST","Le module s&eacute;lectionn&eacute; n'existe pas !");
define("_ALIGN","Alignement");
define("_LEFT","Gauche");
define("_CENTER","Centre");
define("_RIGHT","Droite");
define("_FORM_ENTER", "Merci d'entrer %s");
// %s represents file name
define("_MUSTWABLE","Le fichier %s doit &ecirc;tre accessible en &eacute;criture sur le serveur !");
// Module info
define('_PREFERENCES', 'Pr&eacute;f&eacute;rences');
define("_VERSION", "Version");
define("_DESCRIPTION", "Description");
define("_ERRORS", "Erreurs");
define("_NONE", "Aucun");
define('_ON','le');
define('_READS','lectures');
define('_WELCOMETO','Bienvenue sur %s');
define('_SEARCH','Cherche');
define('_ALL', 'Tous');
define('_TITLE', 'Titre');
define('_OPTIONS', 'Options');
define('_QUOTE', 'Citation');
define('_LIST', 'Liste');
define('_LOGIN','Entrez');
define('_USERNAME','Membre :&nbsp;');
define('_REMEMBERME','Auto-Connexion');  // autologin hack GIJ
define('_PASSWORD','Mot de passe :&nbsp;');
define("_SELECT","S&eacute;lectionner");
define("_IMAGE","Image");
define("_SEND","Envoyer");
define("_CANCEL","Annuler");
define("_ASCENDING","Ordre ascendant");
define("_DESCENDING","Ordre d&eacute;scendant");
define('_BACK', 'Retour');
define('_NOTITLE', 'Aucun titre');

/* Image manager */
define('_IMGMANAGER',"Gestionnaire d'images");
define('_NUMIMAGES', '%s images');
define('_ADDIMAGE','Ajouter un fichier image');
define('_IMAGENAME','Nom :');
define('_IMGMAXSIZE','Taille maxi autoris&eacute;e (ko) :');
define('_IMGMAXWIDTH','Largeur maxi autoris&eacute;e (pixels) :');
define('_IMGMAXHEIGHT','Hauteur maxi autoris&eacute;e (pixels) :');
define('_IMAGECAT','Cat&eacute;gorie :');
define('_IMAGEFILE','Fichier image ');
define('_IMGWEIGHT',"Ordre d'affichage dans le gestionnaire d'images :");
define('_IMGDISPLAY','Afficher cette image ?');
define('_IMAGEMIME','Type MIME :');
define('_FAILFETCHIMG', "Impossible d'uploader le fichier %s");
define('_FAILSAVEIMG', "Impossible de stocker l'image %s dans la base de donn&eacute;es");
define('_NOCACHE', 'Pas de Cache');
define('_CLONE', 'Cloner');

//%%%%%        File Name class/xoopsform/formmatchoption.php         %%%%%
define("_STARTSWITH", "Commen&ccedil;ant par");
define("_ENDSWITH", "Finissant par");
define("_MATCHES", "Correspondant &agrave;");
define("_CONTAINS", "Contenant");

//%%%%%%        File Name commentform.php         %%%%%
define("_REGISTER","Enregistrement");

//%%%%%%        File Name xoopscodes.php         %%%%%
define("_SIZE","TAILLE");  // font size
define("_FONT","POLICE");  // font family
define("_COLOR","COULEUR");  // font color
define("_EXAMPLE","EXEMPLE");
define("_ENTERURL","Entrez l'URL du lien que vous voulez ajouter :");
define("_ENTERWEBTITLE","Entrez le titre du site web :");
define("_ENTERIMGURL","Entrez l'URL de l'image que vous voulez ajouter.");
define("_ENTERIMGPOS","Maintenant, entrez la position de l'image.");
define("_IMGPOSRORL","'R' ou 'r' pour droite, 'L' ou 'l' pour gauche, ou laisser vide.");
define("_ERRORIMGPOS","ERREUR ! Entrez la position de l'image.");
define("_ENTEREMAIL","Entrez l'adresse courriel que vous voulez ajouter.");
define("_ENTERCODE","Entrez les codes que vous voulez ajouter.");
define("_ENTERQUOTE","Entrez le texte que vous voulez citer.");
define("_ENTERTEXTBOX","Merci de saisir le texte dans la bo&icirc;te.");
define("_ALLOWEDCHAR","Longueur maximum autoris&eacute;e de caract&egrave;res :&nbsp;");
define("_CURRCHAR","Longueur de caract&egrave;res actuelle :&nbsp;");
define("_PLZCOMPLETE","Merci de compl&eacute;ter le sujet et le champ message.");
define("_MESSAGETOOLONG","Votre message est trop long.");

//%%%%%                TIME FORMAT SETTINGS   %%%%%
define('_SECOND', '1 seconde');
define('_SECONDS', '%s secondes');
define('_MINUTE', '1 minute');
define('_MINUTES', '%s minutes');
define('_HOUR', '1 heure');
define('_HOURS', '%s heures');
define('_DAY', '1 jour');
define('_DAYS', '%s jours');
define('_WEEK', '1 semaine');
define('_MONTH', '1 mois');

define("_DATESTRING","Y-m-d");
define("_MEDIUMDATESTRING","Y-m-d G:i");
define("_SHORTDATESTRING","Y-m-d");

/*
The following characters are recognized in the format string:
a - "am" or "pm"
A - "AM" or "PM"
d - jour du mois, 2 digits avec les zéros devant; i.e. "01" to "31"
D - jour de la semaine, textual, 3 lettres; i.e. "Fri"
F - mois, textual, long; i.e. "January"
h - heure, format 12-heures; i.e. "01" to "12"
H - heure, format 24-heures; i.e. "00" to "23"
g - heure, format 12-heures sans les zéros devant; i.e. "1" to "12"
G - heure, format 24-heures sans les zéros devant; i.e. "0" to "23"
i - minutes; i.e. "00" à "59"
j - jour du mois sans les zéros devant; i.e. "1" to "31"
l (lowercase 'L') - jour de la semaine, textual, long; i.e. "Friday"
L - booleen pour année bissextile; i.e. "0" or "1"
m - mois; i.e. "01" to "12"
n - mois sans les zéros devant; i.e. "1" to "12"
M - mois, textual, 3 letters; i.e. "Jan"
s - secondes; i.e. "00" to "59"
S - English ordinal suffix, textual, 2 characters; i.e. "th", "nd"
t - nombre de jours dans le mois donné ; i.e. "28" to "31"
T - Timezone setting of this machine; i.e. "MDT"
U - seconds since the epoch
w - jour de la semaine, numeric, i.e. "0" (Sunday) to "6" (Saturday)
Y - année, 4 positions; i.e. "1999"
y - année, 2 positions; i.e. "99"
z - jour de l'année; i.e. "0" to "365"
Z - timezone offset en secondes (i.e. "-43200" to "43200")
*/


//%%%%%                LANGUAGE SPECIFIC SETTINGS   %%%%%
define('_CHARSET', 'ISO-8859-1');
define('_LANGCODE', 'fr');

// change 0 to 1 if this language is a multi-bytes language
define("XOOPS_USE_MULTIBYTES", "0");

// Secutiry Image by DuGris
define("_SECURITYIMAGE_CODE","Code de sécurité");
define("_SECURITYIMAGE_GETCODE","Entrez le code de sécurité");
define("_SECURITYIMAGE_ERROR","Code de sécurité invalide");
define("_SECURITYIMAGE_GDERROR","<b><font color='#CC0000'>L'extension GD, pour PHP doit être installée</font> : <a target='php' href='http://fr2.php.net/manual/fr/ref.image.php'>Manuel PHP</a></b><br>");
define("_SECURITYIMAGE_FONTERROR","<b><font color='#CC0000'>Aucune fichier fontes trouvées</font>, vérifier votre installation</b><br>");
// Secutiry Image by DuGris
define("_MENU_USER_MENU", "Profil");
define("_MENU_DOWNLOADS", "Downloads");
define("_MENU_LIBRARY", "Infothèque");
define("_MENU_HOME", "Accueil");
define("_MENU_VACNT", "Voir mon compte");
define("_MENU_EACNT", "Editer mon compte");
define("_MENU_NOTIF", "Notifications");
define("_MENU_LOUT", "Déconnexion");
define("_MENU_INBOX", "Mes messages");

define("_SELECT_LANGUAGE", "Langue:");
define('_INBOX_TECHNOLOGY_DEVELOPED_BY', 'La Technologie INBOX est développée par <a href="http://inboxinternational.com" target="_blank">INBOX Solutions</a> basé sur ' . XOOPS_VERSION . '' );
define("_COPY_XOOPS", "XOOPS &copy; 2001-2006 <a href='http://www.xoops.org/' target='_blank'>The XOOPS Project</a>");
define("_COPY_INBOX", "Technologie INBOX &copy; 2004-2006 <a href='http://inboxinternational.com/' target='_blank'>INBOX International</a>");

?>