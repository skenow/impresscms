<?php
// $Id: addoninfo.php,v 1.2 2004/01/29 17:15:54 mithyt2 Exp $
// Module Info

// The name of this module
define('_MI_NEWS_NAME','Articles');

// A brief description of this module
define('_MI_NEWS_DESC','Cr&eacute;e une section d\'articles, o&ugrave; les utilisateurs peuvent poster des articles/commentaires.');

// Names of blocks for this module (Not all module has blocks)
define('_MI_NEWS_BNAME1','Sujets d\'articles');
define('_MI_NEWS_BNAME3','Article du jour');
define('_MI_NEWS_BNAME4','Top articles');
define('_MI_NEWS_BNAME5','Articles r&eacute;cents');
define('_MI_NEWS_BNAME6','Mod&eacute;ration des articles');
define('_MI_NEWS_BNAME7','Navigation dans les sujets');

// Sub menus in main menu block
define('_MI_NEWS_SMNAME1','Proposer un article');
define('_MI_NEWS_SMNAME2','Archives');

// Names of admin menu items
define('_MI_NEWS_ADMENU2', 'Sujets');
define('_MI_NEWS_ADMENU3', 'Articles');
define('_MI_NEWS_GROUPPERMS', 'Permissions');
// Added by Hervé for prune option
define('_MI_NEWS_PRUNENEWS', 'Purge');
// Added by Hervé
define('_MI_NEWS_EXPORT', 'Export');

// Title of config items
define('_MI_STORYHOME', 'Combien d\'article(s) sur la page principale ?');
define('_MI_NOTIFYSUBMIT', 'Notifier par mail d\'une nouvelle proposition ?');
define('_MI_DISPLAYNAV', 'Afficher la bo&icirc;te de navigation en haut de chaque page ?');
//define('_MI_ANONPOST','Autoriser les utilisateurs anonymes &agrave; envoyer de nouveaux articles ?');
define('_MI_AUTOAPPROVE','Approuver automatiquement les nouveaux articles sans l\'intervention d\'un administrateur ?');
define("_MI_ALLOWEDSUBMITGROUPS", "Groupes pouvant soumettre des articles");
define("_MI_ALLOWEDAPPROVEGROUPS", "Groupes pouvant approuver des articles");
define("_MI_NEWSDISPLAY", "Mise en page des articles");
define("_MI_NAMEDISPLAY","Nom d'auteur &agrave; utiliser");
define("_MI_COLUMNMODE","Colonnes");
define("_MI_STORYCOUNTADMIN","Nombre d'articles à afficher dans l'administration : ");
define("_MI_UPLOADFILESIZE", "Taille maximale des fichiers joints en Ko (1048576 = 1 Méga)");
define("_MI_UPLOADGROUPS","Groupes autorisés &agrave; joindre des fichiers aux articles");

// Description of each config items
define('_MI_STORYHOMEDSC', '');
define('_MI_NOTIFYSUBMITDSC', '');
define('_MI_DISPLAYNAVDSC', '');
define('_MI_AUTOAPPROVEDSC', '');
define("_MI_ALLOWEDSUBMITGROUPSDESC", "Les groupes s&eacute;lectionn&eacute;s seront autoris&eacute;s &agrave; soumettre des articles");
define("_MI_ALLOWEDAPPROVEGROUPSDESC", "Les groupes s&eacute;lectionn&eacute;s seront autoris&eacute;s &agrave; approuver les nouveaux articles");
define("_MI_NEWSDISPLAYDESC", "Le mode \"Classique\" affiche tous les nouveaux articles tri&eacute;s par date de publication. Le mode \"Articles par sujets\" groupera les articles par sujet avec le dernier article d&eacute;velopp&eacute; et les autres avec juste le titre");
define('_MI_ADISPLAYNAMEDSC', 'Permet de choisir sous quelle forme le nom de l\'auteur doit être affich&eacute;');
define("_MI_COLUMNMODE_DESC","Choisissez le nombre de colonnes &agrave; utiliser pour afficher les articles (cette option n'est utilisable qu'avec le mode d'affichage 'classique')");
define("_MI_STORYCOUNTADMIN_DESC","");
define("_MI_UPLOADFILESIZE_DESC","");
define("_MI_UPLOADGROUPS_DESC","Choisissez les groupes qui peuvent télécharger vers le serveur");


// Name of config item values
define("_MI_NEWSCLASSIC", "Classique");
define("_MI_NEWSBYTOPIC", "Articles par sujets");
define('_MI_DISPLAYNAME1', 'Pseudo');
define('_MI_DISPLAYNAME2', 'Nom complet');
define('_MI_DISPLAYNAME3', 'Aucun');
define("_MI_UPLOAD_GROUP1","Editeurs et Approbateurs");
define("_MI_UPLOAD_GROUP2","Approbateurs uniquement");
define("_MI_UPLOAD_GROUP3","Téléchargement désactivé");


// Text for notifications

define('_MI_NEWS_GLOBAL_NOTIFY', 'Globale');
define('_MI_NEWS_GLOBAL_NOTIFYDSC', 'Options de notification globale des articles.');

define('_MI_NEWS_STORY_NOTIFY', 'Articles');
define('_MI_NEWS_STORY_NOTIFYDSC', 'Options de notification s\'appliquant &agrave; l\'article actuel.');

define('_MI_NEWS_GLOBAL_NEWCATEGORY_NOTIFY', 'Nouveau sujet');
define('_MI_NEWS_GLOBAL_NEWCATEGORY_NOTIFYCAP', 'Notifiez-moi quand un nouveau sujet est cr&eacute;&eacute;.');
define('_MI_NEWS_GLOBAL_NEWCATEGORY_NOTIFYDSC', 'Recevoir une notification quand un nouveau sujet est cr&eacute;&eacute;.');
define('_MI_NEWS_GLOBAL_NEWCATEGORY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} notification automatique : Nouvel article');

define('_MI_NEWS_GLOBAL_STORYSUBMIT_NOTIFY', 'Nouvel article propos&eacute;');
define('_MI_NEWS_GLOBAL_STORYSUBMIT_NOTIFYCAP', 'Notifiez-moi lorsqu\'un nouvel article est propos&eacute; (attente d\'&ecirc;tre approuv&eacute;).');
define('_MI_NEWS_GLOBAL_STORYSUBMIT_NOTIFYDSC', 'Recevoir une notification lorsqu\'un nouvel article est propos&eacute; (attente d\'&ecirc;tre approuv&eacute;).');
define('_MI_NEWS_GLOBAL_STORYSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} notification automatique : Nouvel article propos&eacute;');

define('_MI_NEWS_GLOBAL_NEWSTORY_NOTIFY', 'Nouvel article');
define('_MI_NEWS_GLOBAL_NEWSTORY_NOTIFYCAP', 'Notifiez-moi quand un nouvel article est post&eacute;.');
define('_MI_NEWS_GLOBAL_NEWSTORY_NOTIFYDSC', 'Recevoir une notification quand un nouvel article est post&eacute;.');
define('_MI_NEWS_GLOBAL_NEWSTORY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} notification automatique : Nouvel article');

define('_MI_NEWS_STORY_APPROVE_NOTIFY', 'Article approuv&eacute;');
define('_MI_NEWS_STORY_APPROVE_NOTIFYCAP', 'Notifiez-moi quand cet article est approuv&eacute;.');
define('_MI_NEWS_STORY_APPROVE_NOTIFYDSC', 'Recevoir une notification quand cet article est approuv&eacute;.');
define('_MI_NEWS_STORY_APPROVE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} notification automatique : Article approuv&eacute;');

define('_MI_RESTRICTINDEX', 'Restreindre les sujets sur la page d\'index ?');
define('_MI_RESTRICTINDEXDSC', 'Si l\'option est à Oui, les utilisateurs ne verront que les articles pour lesquels ils ont les permissions de lecture');

define('_MI_NEWSBYTHISAUTHOR', 'Articles du même auteur');
define('_MI_NEWSBYTHISAUTHORDSC', "Si vous mettez cette option à OUI, alors un lien 'Articles du même auteur' sera affich&eacute;");

define('_MI_NEWS_PREVNEX_LINK','Afficher les liens vers les articles pr&eacute;c&eacute;dents et suivants ?');
define("_MI_NEWS_PREVNEX_LINK_DESC","Si cette option est activ&eacute;e, deux nouveaux liens seront visibles en bas de chaque article. Ces liens seront utiles pour lire l'article pr&eacute;c&eacute;dent et suivant en fonction de la date de publication");
define('_MI_NEWS_SUMMARY_SHOW','Afficher la table de sommaire ?');
define('_MI_NEWS_SUMMARY_SHOW_DESC','Quand vous utilisez cette option, une table sommaire contenant les liens vers tous les articles r&eacute;cents publi&eacute;s sur le même thème sera visible en bas de chaque article');
define('_MI_NEWS_AUTHOR_EDIT','Permettre aux auteurs d\'&eacute;diter leurs articles ?');
define('_MI_NEWS_AUTHOR_EDIT_DESC','Avec cette option, les auteurs pourront eux-même &eacute;diter leurs contributions.');
define('_MI_NEWS_RATE_NEWS','Permettre aux utilisateurs de noter les articles ?');
define('_MI_NEWS_TOPICS_RSS','Activer le flux RSS par sujet?');
define('_MI_NEWS_TOPICS_RSS_DESC',"Si vous positionnez cette option sur 'Oui', alors les articles pour ce sujet seront disponibles sous la forme d'un flux RSS");
define('_MI_NEWS_DATEFORMAT', "Format de Date");
define('_MI_NEWS_META_DATA', "Permettre la saisie des donn&eacute;es meta (keywords et description) ?");
define('_MI_NEWS_META_DATA_DESC', "Si vous positionnez cette option sur 'Oui', alors les approbateurs pourront entrer les donn&eacute;es meta suivantes : keywords et description");
define('_MI_NEWS_BNAME8','Articles Al&eacute;atoires');
define('_MI_NEWS_NEWSLETTER','Newsletter');
define('_MI_NEWS_STATS','Statistiques');
define("_MI_NEWS_FORM_OPTIONS","Option de formulaire");
define("_MI_NEWS_FORM_COMPACT","Compact");
define("_MI_NEWS_FORM_DHTML","DHTML");
define("_MI_NEWS_FORM_SPAW","Spaw Editor");
define("_MI_NEWS_FORM_HTMLAREA","HtmlArea Editor");
define("_MI_NEWS_FORM_FCK","FCK Editor");
define("_MI_NEWS_FORM_KOIVI","Koivi Editor");
define("_MI_NEWS_FORM_OPTIONS_DESC","S&eacute;lectionnez l'éditeur à utiliser. Si vous avez une installation 'simple' (i.e vous utilisez seulement l'&eacute;diteur xoops fourni en standard), alors vous ne pouvez que s&eacute;lectionner DHTML et Compact");
define("_MI_NEWS_KEYWORDS_HIGH","Activer le surlignage des mots clefs de recherche ?");
define("_MI_NEWS_KEYWORDS_HIGH_DESC","Si vous utilisez cette option, alors les mots clefs saisis dans la boite Recherche seront surlign&eacute;s dans les articles");
define("_MI_NEWS_HIGH_COLOR","Couleur utilis&eacute;e pour le surlignage des mots de recherche ?");
define("_MI_NEWS_HIGH_COLOR_DES","Utilisez cette option seulement si vous avez choisi Oui à l'option pr&eacute;c&eacute;dente");
define("_MI_NEWS_INFOTIPS","Nombre de caractères pris en compte dans les infobulles");
define("_MI_NEWS_INFOTIPS_DES","Si vous utilisez cette option, les liens relatifs à des articles contiendront une infobulle reprennant les premiers (n) caractères de chaque article. Si vous param&eacute;trez cette valeur à 0, alors l'infobulle sera vide");
define("_MI_NEWS_SITE_NAVBAR","Utiliser la barre de navigation Mozilla et Opera ?");
define("_MI_NEWS_SITE_NAVBAR_DESC","Si vous positionnez cette option à Oui, alors les visiteurs de votre site pourront utiliser la barre de navigation de site pour se d&eacute;placer entre vos articles.");
define("_MI_NEWS_TABS_SKIN","Sélectionnez l'apparence à utiliser dans les onglets");
define("_MI_NEWS_TABS_SKIN_DESC","Cet habillage sera utilisé dans tous les blocs qui utilisent des onglets");
define("_MI_NEWS_SKIN_1","Style barre");
define("_MI_NEWS_SKIN_2","Incliné");
define("_MI_NEWS_SKIN_3","Classique");
define("_MI_NEWS_SKIN_4","Dossiers");
define("_MI_NEWS_SKIN_5","MacOs");
define("_MI_NEWS_SKIN_6","Plat");
define("_MI_NEWS_SKIN_7","Arrondi");
define("_MI_NEWS_SKIN_8","Style ZDnet");

// Added in version 1.50
define('_MI_NEWS_BNAME9','Archives');
define("_MI_NEWS_FORM_TINYEDITOR","TinyEditor");
define("_MI_NEWS_FOOTNOTES","Voir les liens sur les versions imprimables ?");
define("_MI_NEWS_DUBLINCORE","Activer les Dublin Core Metadata ?");
define("_MI_NEWS_DUBLINCORE_DSC","Pour plus d'informations, <a href='http://dublincore.org/'>consultez ce lien</a>");
define("_MI_NEWS_BOOKMARK_ME","Afficher un bloc 'Ajouter cette page sur ces sites' ?");
define("_MI_NEWS_BOOKMARK_ME_DSC","Ce bloc sera visible sur la page des articles");
define("_MI_NEWS_FF_MICROFORMAT","Activer les micro résumés de Firefox 2 ?");
define("_MI_NEWS_FF_MICROFORMAT_DSC","Pour plus d'informations, voir <a href='http://wiki.mozilla.org/Microsummaries' target='_blank'>cette page</a>");
define("_MI_NEWS_WHOS_WHO","Annuaire des auteurs");
define("_MI_NEWS_METAGEN","Metagen");
define("_MI_NEWS_TOPICS_DIRECTORY","Répertoire des sujets");
define("_MI_NEWS_ADVERTISEMENT","Publicité");
define("_MI_NEWS_ADV_DESCR","Entrez un texte ou du code javascript à afficher dans vos articles");
define("_MI_NEWS_MIME_TYPES","Entrez les types mime autorisés pour le téléchargement des pièces jointes dans les articles (séparez les par un retour à la ligne)");
define("_MI_NEWS_ENHANCED_PAGENAV","Utiliser le séparateur de pages amélioré ?");
define("_MI_NEWS_ENHANCED_PAGENAV_DSC","Avec cette option vous pouvez séparer vos pages avec quelque chose comme cela [pagrebreak:Titre page], les liens vers les pages sont remplacés par une liste déroulante et vous pouvez utiliser [summary] pour créer un sommaire automatique des pages");

// Added in version 1.54
define('_MI_NEWS_CATEGORY_NOTIFY','Catégorie');
define('_MI_NEWS_CATEGORY_NOTIFYDSC',"Options de notification pour la catégorie en cours");

define('_MI_NEWS_CATEGORY_STORYPOSTED_NOTIFY', "Nouvel article publié");
define('_MI_NEWS_CATEGORY_STORYPOSTED_NOTIFYCAP', "Notifiez-moi lorsqu'un nouvel article est publié dans cette catégorie.");
define('_MI_NEWS_CATEGORY_STORYPOSTED_NOTIFYDSC', "Recevoir une notification lorsqu'un article est publié dans cette catégorie.");
define('_MI_NEWS_CATEGORY_STORYPOSTED_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} notification automatique : Nouvel article');


?>  