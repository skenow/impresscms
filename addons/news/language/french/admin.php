<?php
// $Id: admin.php,v 1.8 2003/04/01 09:07:28 mvandam Exp $
// Support Francophone de Xoops (www.frxoops.org)
//%%%%%%        Admin Module Name  Articles         %%%%%
define("_AM_DBUPDATED","Base de donn&eacute;es mise &agrave; jour avec succ&egrave;s !");
define("_AM_CONFIG","Configuration des articles");
define("_AM_AUTOARTICLES","Articles automatis&eacute;s");
define("_AM_STORYID","ID de l'article");
define("_AM_TITLE","Titre");
define("_AM_TOPIC","Sujet");
define("_AM_POSTER","Auteur");
define("_AM_PROGRAMMED","Date/Heure programm&eacute;es");

define("_AM_EDIT","Editer");
define("_AM_DELETE","Effacer");
define("_AM_LAST10ARTS","Les %d derniers articles");
define("_AM_PUBLISHED","Publi&eacute; le"); // Published Date
define("_AM_GO","Ok");
define("_AM_EDITARTICLE","Editer l'article");
define("_AM_POSTNEWARTICLE","Poster un nouvel article");
define("_AM_ARTPUBLISHED","Votre article a &eacute;t&eacute; publi&eacute; !"); // mail
define("_AM_HELLO","Bonjour %s,"); // mail
define("_AM_YOURARTPUB","Votre article soumis sur notre site a &eacute;t&eacute; publi&eacute;."); // mail
define("_AM_TITLEC","Titre : "); // mail
define("_AM_URLC","URL : "); // mail
define("_AM_PUBLISHEDC","Publi&eacute; le : "); // mail
define("_AM_RUSUREDEL","Etes-vous s&ucirc;r de vouloir supprimer cet article et tous ses commentaires ?");
define("_AM_YES","Oui");
define("_AM_NO","Non");
define("_AM_INTROTEXT","Texte de l\'introduction");
define("_AM_EXTEXT","Suite du texte");
define("_AM_ALLOWEDHTML","HTML autoris&eacute; :");
define("_AM_DISAMILEY","D&eacute;sactiver les &eacute;motic&ocirc;nes");
define("_AM_DISHTML","D&eacute;sactiver le HTML");
define("_AM_APPROVE","Approuver");
define("_AM_MOVETOTOP","D&eacute;placer cet article au Top");
define("_AM_CHANGEDATETIME","Changer la date/heure de publication");
define("_AM_NOWSETTIME","C\'est maintenant mis sur : %s"); // %s is datetime of publish
define("_AM_CURRENTTIME","Actuellement il est : %s");  // %s is the current datetime
define("_AM_SETDATETIME","Param&eacute;trer la date/heure de publication");
define("_AM_MONTHC","Mois :");
define("_AM_DAYC","Jour :");
define("_AM_YEARC","Ann&eacute;e :");
define("_AM_TIMEC","Heure :");
define("_AM_PREVIEW","Pr&eacute;visualiser");
define("_AM_SAVE","Sauvegarder");
define("_AM_PUBINHOME","Publier en page d'accueil ?");
define("_AM_ADD","Ajouter");

//%%%%%%        Admin Module Name  Topics         %%%%%

define("_AM_ADDMTOPIC","Ajouter un sujet PRINCIPAL");
define("_AM_TOPICNAME","Nom du sujet");
// Attention, modifiéde 40 à 255 caractères.
define("_AM_MAX40CHAR","(maxi : 255 caract&egrave;res)");
define("_AM_TOPICIMG","Image du sujet");
define("_AM_IMGNAEXLOC","nom de l'image + extension plac&eacute; dans %s");
define("_AM_FEXAMPLE","par exemple : games.gif");
define("_AM_ADDSUBTOPIC","Ajouter un sous-sujet");
define("_AM_IN","dans");
define("_AM_MODIFYTOPIC","Modifier le sujet");
define("_AM_MODIFY","Modifier");
define("_AM_PARENTTOPIC","Sujet parent");
define("_AM_SAVECHANGE","Sauvegarder les changements");
define("_AM_DEL","Effacer");
define("_AM_CANCEL","Annuler");
define("_AM_WAYSYWTDTTAL","ATTENTION : Etes-vous s&ucirc;r de vouloir supprimer ce sujet et tous ses articles et commentaires ?");


// Added in Beta6
define("_AM_TOPICSMNGR","Gestionnaire de sujets");
define("_AM_PEARTICLES","Gestion des articles");
define("_AM_NEWSUB","Nouvelles propositions");
define("_AM_POSTED","Post&eacute; le");
define("_AM_GENERALCONF","Configuration g&eacute;n&eacute;rale");
// define("_AM_CATEGPERMS","Permissions des catégories");

// Added in RC2
define("_AM_TOPICDISPLAY","Afficher l'image du sujet ?");
define("_AM_TOPICALIGN","Position");
define("_AM_RIGHT","Droite");
define("_AM_LEFT","Gauche");

define("_AM_EXPARTS","Articles expir&eacute;s");
define("_AM_EXPIRED","Expir&eacute;");
define("_AM_CHANGEEXPDATETIME","Changer la date/heure d'expiration");
define("_AM_SETEXPDATETIME","Param&eacute;ter la date/heure d'expiration");
define("_AM_NOWSETEXPTIME","C\'est maintenant mis sur : %s");

// Added in RC3
define("_AM_ERRORTOPICNAME", "Vous devez entrer un nom de sujet !");
define("_AM_EMPTYNODELETE", "Rien &agrave; supprimer !");

// Added 240304 (Mithrandir)
define("_AM_GROUPPERM", "Permissions des groupes");
define('_AM_SELFILE','S&eacute;lectionnez un fichier');

// Added by Hervé
define('_AM_UPLOAD_DBERROR_SAVE',"Erreur pendant le rattachement d'un fichier &agrave; un article");
define('_AM_UPLOAD_ERROR',"Erreur pendant le t&eacute;l&eacute;chargement du fichier vers le serveur");
define('_AM_UPLOAD_ATTACHFILE',"Fichier(s) attach&eacute;(s)");
define('_AM_APPROVEFORM', "Permission d'approuver");
define('_AM_SUBMITFORM', "Permission de soumettre");
define('_AM_VIEWFORM', "Permissions de consulter");
define('_AM_APPROVEFORM_DESC', "Choisissez les groupes qui peuvent approuver les articles pour les sujets affichés");
define('_AM_SUBMITFORM_DESC', "Choisissez les groupes qui peuvent soumettre des articles pour les sujets affichés");
define('_AM_VIEWFORM_DESC', "Choisissez les groupes qui peuvent visualiser les sujets affichés");
define('_AM_DELETE_SELFILES', "Supprimer les fichiers s&eacute;lectionn&eacute;s");
define('_AM_TOPIC_PICTURE', "T&eacute;l&eacute;charger l'image du sujet");
define('_AM_UPLOAD_WARNING', "<b>Attention, n'oubliez pas d'appliquer les permissions d'&eacute;criture au r&eacute;pertoire suivant : %s </b>");
define('_AM_NEWS_UPGRADECOMPLETE', "Mise &agrave; jour termin&eacute;e");
define('_AM_NEWS_UPDATEMODULE', "Veuillez mettre à jour les templates et les blocs");
define('_AM_UPDATEMODULE', "Veuillez mettre &agrave; jour les templates et les blocs du module");
define('_AM_NEWS_UPGRADEFAILED', "La mise &agrave; jour a &eacute;chou&eacute;e");
define('_AM_NEWS_UPGRADE', "Mise &agrave; jour");
define('_AM_ADD_TOPIC', "Ajouter un sujet");


define('_AM_ADD_TOPIC_ERROR',"Erreur, ce sujet existe d&eacute;ja !");
define('_AM_ADD_TOPIC_ERROR1',"ERREUR: Impossible de selectionner ce sujet comme sujet parent !");
define('_AM_SUB_MENU',"Prendre ce sujet comme sous-menu");
define('_AM_SUB_MENU_YESNO',"Sous-menu ?");
define('_AM_HITS', "Hits");
define('_AM_CREATED', "Cr&eacute;e");

define('_AM_TOPIC_DESCR', "Description du sujet");
define('_AM_USERS_LIST', "Liste des utilisateurt");
define('_AM_PUBLISH_FRONTPAGE', "Publier en premi&egrave;re page ?");
define("_AM_NEWS_UPGRADEFAILED1", "Impossible de cr&eacute;er la table 'stories_files'");
define("_AM_NEWS_UPGRADEFAILED2", "Impossible de modifier la longueur du titre du sujet");
define('_AM_NEWS_UPGRADEFAILED21', "Impossible d'ajouter les nouveaux champs dans la table 'topics'");
define('_AM_NEWS_UPGRADEFAILED3', "Impossible de cr&eacute;er la table 'stories_votedata'");
define('_AM_NEWS_UPGRADEFAILED4', "Impossible de cr&eacute;er les deux champs 'rating' and 'votes' dans la table 'story'");
define('_AM_NEWS_UPGRADEFAILED0', "Merci de noter les messages et d'essayer de corriger  les probl&egrave;mes avec phpMyadmin et fichier sql de d&eacute;finition pr&eacute;sent dans le r&eacute;pertoire 'sql' du module 'news'");
define('_AM_NEWS_UPGR_ACCESS_ERROR',"Erreur pour utiliser le script de mise &agrave; jour, vous devez être administrateur du module");
define('_AM_NEWS_PRUNE_BEFORE',"Purger les articles qui ont &eacute;t&eacute; publi&eacute;s avant le");
define('_AM_NEWS_PRUNE_EXPIREDONLY',"Supprimer seulement les articles qui sont expir&eacute;s");
define('_AM_NEWS_PRUNE_CONFIRM',"Attention, vous êtes sur le point de supprimer d&eacute;finitivement les articles qui ont &eacute;t&eacute; publi&eacute;s avant le %s (cette action ne peut être annul&eacute;e). Cela repr&eacute;sente %s articles.<br />Etes vous certain(e) ?");
define('_AM_NEWS_PRUNE_TOPICS',"Se limiter aux sujets suivants");
define('_AM_NEWS_PRUNENEWS', "Purger les articles");
define('_AM_NEWS_EXPORT_NEWS', "Exporter les articles en XML");
define('_AM_NEWS_EXPORT_NOTHING', "D&eacute;sol&eacute;, il n'y a rien &agrave; exporter, merci de v&eacute;rifier vos crit&egrave;res");
define('_AM_NEWS_PRUNE_DELETED', "%d articles ont &eacute;t&eacute; supprim&eacute;s");
define('_AM_NEWS_PERM_WARNING', "<h2>Attention, il y a 3 formulaires, donc vous devez valider trois boutons</h2>");
define('_AM_NEWS_EXPORT_BETWEEN', "Exporter les articles publi&eacute;s entre le");
define('_AM_NEWS_EXPORT_AND', " et ");
define('_AM_NEWS_EXPORT_PRUNE_DSC', "Si vous ne s&eacute;lectionnez rien, alors tous les sujets seront utilis&eacute;s<br/> sinon, seuls les sujets selectionn&eacute;s seront utilis&eacute;s");
define('_AM_NEWS_EXPORT_INCTOPICS', "Inclure la description des sujets ?");
define('_AM_NEWS_EXPORT_ERROR', "Erreur pendant la cr&eacute;ation du fichier %s. Op&eacute;ration annul&eacute;e.");
define('_AM_NEWS_EXPORT_READY', "L'export du fichier au format xml est disponible pour t&eacute;l&eacute;chargement. <br /><a target='_blank' href='%s'>Cliquez sur ce lien pour le t&eacute;l&eacute;charger</a>.<br />N'oubliez pas <a href='%s'>de le supprimer </a> une fois que vous avez terminé.");
define('_AM_NEWS_RSS_URL', "URL du flux RSS");
define('_AM_NEWS_NEWSLETTER', "Newsletter");
define('_AM_NEWS_NEWSLETTER_HEADER', "Entête");
define('_AM_NEWS_NEWSLETTER_FOOTER', "Pied de page");
define('_AM_NEWS_NEWSLETTER_HTML_TAGS', "Supprimer les balises html ?");
define('_AM_NEWS_NEWSLETTER_BETWEEN', "S&eacute;lectionner les articles publi&eacute;s entre le");
define('_AM_NEWS_NEWSLETTER_READY', "Votre newsletter est disponible au t&eacute;l&eacute;chargement. <br /><a target='_blank' href='%s'>Cliquez sur ce lien pour la t&eacute;l&eacute;charger</a>.<br />N'oubliez pas de <a href='%s'>la supprimer</a> une fois que vous avez terminé.");
define('_AM_NEWS_DELETED_OK',"Fichier supprim&eacute; avec succ&egrave;s");
define('_AM_NEWS_DELETED_PB',"Un probl&egrave;me a &eacute;t&eacute; rencontr&eacute; pendant la suppression du fichier");
define('_AM_NEWS_STATS0',"Statistiques par sujet");
define('_AM_NEWS_STATS',"Statistiques");
define('_AM_NEWS_STATS1',"Auteurs");
define('_AM_NEWS_STATS2',"Total");
define('_AM_NEWS_STATS3',"Statistiques articles");
define('_AM_NEWS_STATS4',"Articles les plus lus");
define('_AM_NEWS_STATS5',"Articles les moins lus");
define('_AM_NEWS_STATS6',"Articles les mieux côt&eacute;s");
define('_AM_NEWS_STATS7',"Auteurs les plus lus");
define('_AM_NEWS_STATS8',"Auteurs les mieux côt&eacute;s");
define('_AM_NEWS_STATS9',"Meilleurs contributeurs");
define('_AM_NEWS_STATS10',"Statistiques par Auteurs");
define('_AM_NEWS_STATS11',"Nombre d'articles");
define('_AM_NEWS_HELP',"Aide");
define("_AM_NEWS_MODULEADMIN"," - Administration");
define("_AM_NEWS_GENERALSET", "Options du module" );
define('_AM_NEWS_GOTOMOD','Aller au module');
define('_AM_NEWS_NOTHING',"D&eacute;sol&eacute; mais il n'y a rien &agrave; t&eacute;l&eacute;charger, vérifiez vos crit&egrave;res");
define('_AM_NEWS_NOTHING_PRUNE',"D&eacute;sol&eacute;, mais il n'y a rien &agrave; purger, v&eacute;rifiez vos crit&egrave;res");
define('_AM_NEWS_TOPIC_COLOR',"Couleur du sujet");
define('_AM_NEWS_COLOR',"Couleur");
define('_AM_NEWS_REMOVE_BR',"Convertir les balises html &lt;br /&gt; en un retour à la ligne ?");
// Added in 1.3 RC2
define('_AM_NEWS_PLEASE_UPGRADE',"<a href='upgrade.php'><font color='#FF0000'>Veuillez mettre à jour le module s'il vous plait</font></a>");

// Added inversin 1.50
define('_AM_NEWS_VERIFY_TABLES',"Maintenir les tables");
define('_AM_NEWS_METAGEN',"Metagen");
define('_AM_NEWS_METAGEN_DESC',"Le Metagen est un système qui peut vous aider à avoir des pages mieux indexées par les moteurs de recherche.<br />Sauf si vous entrez vous même les meta keywords et meta desriptions, le module les générera automatiquement pour chaque article.");
define('_AM_NEWS_BLACKLIST',"Liste noire");
define('_AM_NEWS_BLACKLIST_DESC',"Les mots contenus dans cette liste<br />ne seront pas utilisés lors de la création des meta keywords");
define('_AM_NEWS_BLACKLIST_ADD',"Ajouter");
define('_AM_NEWS_BLACKLIST_ADD_DSC',"Entrez des mots à ajouter dans la liste<br />(un mot par ligne)");
define('_AM_NEWS_META_KEYWORDS_CNT',"Nombre maximal de meta mots clés à générer");
define('_AM_NEWS_META_KEYWORDS_ORDER',"Ordre des mots clés");
define('_AM_NEWS_META_KEYWORDS_INTEXT',"Ordre d'apparition dans le texte");
define('_AM_NEWS_META_KEYWORDS_FREQ1',"Ordre de fréquence des mots");
define('_AM_NEWS_META_KEYWORDS_FREQ2',"Ordre inverse de la fréquence des mots");
?> 