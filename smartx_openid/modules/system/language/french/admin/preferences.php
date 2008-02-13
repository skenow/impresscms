<?php
// $Id$
//%%%%%%        Admin Module Name  AdminGroup         %%%%%
// dont change
define("_AM_DBUPDATED",_MD_AM_DBUPDATED);

define("_MD_AM_SITEPREF","Pr&eacute;f&eacute;rences du site");
define("_MD_AM_SITENAME","Nom du site");
define("_MD_AM_SLOGAN","Slogan pour votre site");
define("_MD_AM_ADMINML","Addresse mail Administrateur");
define("_MD_AM_LANGUAGE","Langage par d&eacute;faut");
define("_MD_AM_STARTPAGE","Module pour votre page d'accueil");
define("_MD_AM_NONE","Aucun");
define("_MD_AM_SERVERTZ","Fuseau horaire du serveur");
define("_MD_AM_DEFAULTTZ","Fuseau horaire par d&eacute;faut");
define("_MD_AM_DTHEME","Th&egrave;me par d&eacute;faut");
define("_MD_AM_THEMESET","Jeu de th&egrave;mes");
define("_MD_AM_ANONNAME","Nom de membre pour les utilisateurs anonymes");
define("_MD_AM_MINPASS","Longueur minimum requise pour le mot de passe");
define("_MD_AM_NEWUNOTIFY","Notifier par mail lorsqu'un nouvel utilisateur s'est enregistr&eacute;?");
define("_MD_AM_SELFDELETE","Autoriser les membres &agrave; supprimer leur compte?");
define("_MD_AM_LOADINGIMG","Afficher l'image : Chargement...?");
define("_MD_AM_USEGZIP","Utiliser la compression gzip?");
define("_MD_AM_UNAMELVL","S&eacute;lectionner le niveau de restriction pour le filtrage des noms de membres");
define("_MD_AM_STRICT","Strict (uniquement alpha-num&eacute;rique)");
define("_MD_AM_MEDIUM","Moyen");
define("_MD_AM_LIGHT","Permissif (recommand&eacute; pour les caract&egrave;res multi-bits)");
define("_MD_AM_USERCOOKIE","Nom pour le cookie utilisateur.");
define("_MD_AM_USERCOOKIEDSC","Ce cookie contient uniquement un nom utilisateur et est sauvegard&eacute; pour un an sur le PC de l'utilisateur (s'il le d&eacute;sire). Si un utilisateur a ce cookie, son nom de membre sera automatiquement ins&eacute;r&eacute; dans la boite de connexion.");
define("_MD_AM_USEMYSESS","Utiliser une session personnalis&eacute;e");
define("_MD_AM_USEMYSESSDSC","Choisissez OUI pour personnaliser la session des valeurs li&eacute;es.");
define("_MD_AM_SESSNAME","Nom de la session.");
define("_MD_AM_SESSNAMEDSC","Le nom de la session (Valide uniquement lorsque l'option 'Utiliser une session personnalis&eacute;e' est active)");
define("_MD_AM_SESSEXPIRE","Expiration de la session");
define("_MD_AM_SESSEXPIREDSC","Dur&eacute;e maximum de la session en minutes (Valide uniquement lorsque l'option 'Utiliser une session personnalis&eacute;e' est active. Fonctionne seulement quand vous utilisez PHP 4.2.0 ou sup&eacute;rieur.)");
define("_MD_AM_BANNERS","Activer l'affichage des banni&egrave;res?");
define("_MD_AM_MYIP","Votre adresse IP");
define("_MD_AM_MYIPDSC","Cette IP ne sera pas compt&eacute;e pour l'affichage des banni&egrave;res");
define("_MD_AM_ALWDHTML","Balises HTML autoris&eacute;es dans tous les envois.");
define("_MD_AM_INVLDMINPASS","Valeur invalide pour la longueur minimum du mot de passe.");
define("_MD_AM_INVLDUCOOK","Valeur invalide pour le nom du cookie utilisateur.");
define("_MD_AM_INVLDSCOOK","Valeur invalide pour le nom du cookie de session.");
define("_MD_AM_INVLDSEXP","Valeur invalide pour l'expiration de la session.");
define("_MD_AM_ADMNOTSET","Le mail de l'administrateur n'est pas saisi.");
define("_MD_AM_YES","Oui");
define("_MD_AM_NO","Non");
define("_MD_AM_DONTCHNG","Ne pas changer!");
define("_MD_AM_REMEMBER","Rappelez-vous de faire un chmod 666 sur ce fichier pour permettre au syst&egrave;me d'y &eacute;crire correctement.");
define("_MD_AM_IFUCANT","Si vous ne changez pas les permissions vous pouvez &eacute;diter le reste de ce fichier manuellement.");


define("_MD_AM_COMMODE","Mode d'affichage par d&eacute;faut des commentaires");
define("_MD_AM_COMORDER","Ordre d'affichage par d&eacute;faut des commentaires");
define("_MD_AM_ALLOWHTML","Autoriser les balises HTML dans les commentaires utilisateurs?");
define("_MD_AM_DEBUGMODE","Mode de mise au point (Debug)");
define("_MD_AM_DEBUGMODEDSC","Vous pouvez choisir entre plusieurs options de debuggage. Un site Web courant doit avoir ceci sur inactif, puisque tout le monde pourra visualiser les messages affich&eacute;s.");
define("_MD_AM_AVATARALLOW","Autoriser l'upload d'avatar personnalis&eacute; ?");
define('_MD_AM_AVATARMP','Envois minimum requis');
define('_MD_AM_AVATARMPDSC',"Entrez le nombre minimum d'envois requis pour uploader un avatar personnalis&eacute;");
define("_MD_AM_AVATARW","Largeur maxi de l'image avatar (pixels)");
define("_MD_AM_AVATARH","Hauteur maxi de l'image avatar (pixels)");
define("_MD_AM_AVATARMAX","Taille maxi de l'image avatar (octets)");
define("_MD_AM_AVATARCONF","Param&egrave;tres des avatars personnalis&eacute;s");
define("_MD_AM_CHNGUTHEME","Changer tous les th&egrave;mes utilisateurs");
define("_MD_AM_NOTIFYTO","Choisissez le groupe auquel le mail de notification d'un nouveau membre sera envoy&eacute;");
define("_MD_AM_ALLOWTHEME","Autoriser les utilisateurs &agrave; s&eacute;lectionner un th&egrave;me ?");
define("_MD_AM_ALLOWIMAGE","Autoriser les utilisateurs &agrave; afficher des fichiers images dans les envois ?");

define("_MD_AM_USERACTV","Activation par l'utilisateur requise (recommand&eacute;)");
define("_MD_AM_AUTOACTV","Activation automatique");
define("_MD_AM_ADMINACTV","Activation par les administrateurs");
define("_MD_AM_ACTVTYPE","S&eacute;lectionnez le type d'activation des membres nouvellement enregistr&eacute;s");
define("_MD_AM_ACTVGROUP","Choisissez le groupe auquel le mail d'activation doit &ecirc;tre envoy&eacute;");
define("_MD_AM_ACTVGROUPDSC","Valide uniquement lorsque l'option 'Activation par les administrateurs' est s&eacute;lectionn&eacute;e");
define('_MD_AM_USESSL', 'Utiliser le SSL pour se connecter ?');
define('_MD_AM_SSLPOST', 'Nom de la variable SSL');
define('_MD_AM_SSLPOSTDSC', 'Nom de la variable utilis&eacute;e une valeur de session en mode POST. Si vous ne savez pas quoi mettre, inventez un nom difficilement reconnaissable.');
define('_MD_AM_DEBUGMODE0','Inactif');
define('_MD_AM_DEBUGMODE1','Activer mode debug en ligne');
define('_MD_AM_DEBUGMODE2','Activer mode debug en popup');
define('_MD_AM_DEBUGMODE3','Mise au point des templates Smarty');
define('_MD_AM_MINUNAME', 'Longueur minimum requise pour le nom de membre');
define('_MD_AM_MAXUNAME', 'Longueur maximum requise pour le nom de membre');
define('_MD_AM_GENERAL', 'Param&egrave;tres g&eacute;n&eacute;raux');
define('_MD_AM_USERSETTINGS', 'Param&egrave;tres des infos utilisateurs');
define('_MD_AM_ALLWCHGMAIL', 'Autoriser les utilisateurs &agrave; changer leur adresse e-mail?');
define('_MD_AM_ALLWCHGMAILDSC', '');
define('_MD_AM_IPBAN', 'IP Interdites');
define('_MD_AM_BADEMAILS', "Entrez les e-mails qui ne doivent pas &ecirc;tre employ&eacute;s dans les profils utilisateurs");
define('_MD_AM_BADEMAILSDSC', 'Les s&eacute;parer par un <b>|</b>, casse insensible, regex activ&eacute;. Ne jamais terminer par |');
define('_MD_AM_BADUNAMES', 'Entrez les noms qui ne doivent pas &ecirc;tre s&eacute;lectionn&eacute;s nom de membre');
define('_MD_AM_BADUNAMESDSC', 'Les s&eacute;parer par un <b>|</b>, casse insensible, regex activ&eacute;.');
define('_MD_AM_DOBADIPS', "Activer le bannissement d'IP?");
define('_MD_AM_DOBADIPSDSC', "Les utilisateurs des adresses IP indiqu&eacute;es seront bannis de votre site");
define('_MD_AM_BADIPS', 'Entrez les adresses IP qui seront bannies de ce site.<br />Les s&eacute;parer par un <b>|</b>, casse insensible, regex activ&eacute;.');
define('_MD_AM_BADIPSDSC', "^aaa.bbb.ccc bannira les visiteurs dont l'IP commence par aaa.bbb.ccc<br />aaa.bbb.ccc$ bannira les visiteurs dont l'IP se termine par aaa.bbb.ccc<br />aaa.bbb.ccc bannira les visiteurs dont l'IP contient aaa.bbb.ccc");
define('_MD_AM_PREFMAIN', 'Pr&eacute;f&eacute;rences principales');
define('_MD_AM_METAKEY', 'M&eacute;ta keywords');
define('_MD_AM_METAKEYDSC', 'La balise keywords est une s&eacute;rie de mots-cl&eacute;s qui repr&eacute;sente le contenu de votre site. Tapez les mots-cl&eacute;s s&eacute;par&eacute;s par une virgule ou un espace au milieu. (Ex. XOOPS, PHP, mySQL, portal system)');
define('_MD_AM_METARATING', 'M&eacute;ta rating');
define('_MD_AM_METARATINGDSC', "La balise rating d&eacute;finie l'&acirc;ge minimum d'acc&egrave;s &agrave; votre site et une &eacute;valuation de son contenu");
define('_MD_AM_METAOGEN', 'G&eacute;n&eacute;ral');
define('_MD_AM_METAO14YRS', '14 ans');
define('_MD_AM_METAOREST', 'Limit&eacute;');
define('_MD_AM_METAOMAT', 'Adulte');
define('_MD_AM_METAROBOTS', 'M&eacute;ta robots');
define('_MD_AM_METAROBOTSDSC', 'La balise robots d&eacute;clare aux moteurs de recherche quel contenu indexer');
define('_MD_AM_INDEXFOLLOW', 'Indexer, suivre');
define('_MD_AM_NOINDEXFOLLOW', 'Ne pas indexer, suivre');
define('_MD_AM_INDEXNOFOLLOW', 'Indexer, ne pas suivre');
define('_MD_AM_NOINDEXNOFOLLOW', 'Ne pas indexer, ne pas suivre');
define('_MD_AM_METAAUTHOR', 'M&eacute;ta auteur');
define('_MD_AM_METAAUTHORDSC', "La balises auteur d&eacute;finit le nom de l'auteur des documents qui seront lus. Les formats de donn&eacute;es support&eacute;s incluent le nom, l'adresse e-mail du Webmestre, le nom de l'entreprise ou l'URL.");
define('_MD_AM_METACOPYR', 'M&eacute;ta copyright');
define('_MD_AM_METACOPYRDSC', "La balise copyright d&eacute;finit n'importe quelle d&eacute;claration de droit d'auteur que vous voulez appliquer &agrave; vos documents Web.");
define('_MD_AM_METADESC', 'M&eacute;ta description');
define('_MD_AM_METADESCDSC', 'La balise description est une description g&eacute;n&eacute;rale de ce qui est contenu dans vos pages web');
define('_MD_AM_METAFOOTER', 'M&eacute;ta balises et pied de page');
define('_MD_AM_FOOTER', 'Pied de page');
define('_MD_AM_FOOTERDSC', 'Assurez-vous de taper les liens avec le chemin complet commen&ccedil;ant par http://, autrement les liens ne fonctionneront pas correctement dans les pages des modules.');
define('_MD_AM_CENSOR', 'Options des mots &agrave; censurer');
define('_MD_AM_DOCENSOR', 'Activer la censure des mots ind&eacute;sirables ?');
define('_MD_AM_DOCENSORDSC', 'Les mots qui doivent &ecirc;tre censur&eacute;s si cette option est activ&eacute;e. Cette option peut &ecirc;tre arr&ecirc;t&eacute;e pour accro&icirc;tre la vitesse de votre site.');
define('_MD_AM_CENSORWRD', 'Mots &agrave; censurer');
define('_MD_AM_CENSORWRDDSC', 'Entrez les mots qui seront censur&eacute;s dans les envois utilisateurs.<br />Les s&eacute;parer par un <b>|</b>, casse insensible.');
define('_MD_AM_CENSORRPLC', 'Les mots censur&eacute;s seront remplac&eacute;s par :');
define('_MD_AM_CENSORRPLCDSC', 'Les mots censur&eacute;s seront remplac&eacute;s par les caract&egrave;res entr&eacute;s dans cette zone de texte');

define('_MD_AM_SEARCH', 'Options de recherche');
define('_MD_AM_DOSEARCH', 'Activer la recherche globale ?');
define('_MD_AM_DOSEARCHDSC', "Autorise la recherche d'envois/&eacute;l&eacute;ments dans tout votre site.");
define('_MD_AM_MINSEARCH', 'Longueur minimum des mots-cl&eacute;s');
define('_MD_AM_MINSEARCHDSC', 'Entrez la longueur minimum des mot-cl&eacute;s requis par les utilisateurs pour ex&eacute;cuter la recherche');
define('_MD_AM_MODCONFIG', 'Options de configuration des modules');
define('_MD_AM_DSPDSCLMR', 'Afficher un disclaimer ?');
define('_MD_AM_DSPDSCLMRDSC', "Choisissez OUI pour afficher le disclaimer dans la page d'enregistrement");
define('_MD_AM_REGDSCLMR', "Disclaimer d'enregistrement");
define('_MD_AM_REGDSCLMRDSC', "Entrez le texte qui sera affich&eacute; dans le disclaimer d'enregistrement");
define('_MD_AM_ALLOWREG', "Autoriser l'enregistrement de nouveaux utilisateurs ?");
define('_MD_AM_ALLOWREGDSC', "Choisissez OUI pour accepter l'enregistrement de nouveaux utilisateurs");
define('_MD_AM_THEMEFILE', 'Mise &agrave; jour des fichiers du th&egrave;mes &agrave; partir du r&eacute;pertoire themes/ ?');
define('_MD_AM_THEMEFILEDSC', "Si cette option est activ&eacute;e, les fichiers du th&egrave;mes seront mis &agrave; jour automatiquement s'il y a des fichiers plus r&eacute;cents dans le r&eacute;pertoire  themes/ pour le th&egrave;me actuel. Remettre cette option à non lorsque vos modifications sont opérationnelles pour une meilleure performance. Cette option doit &ecirc;tre inactive lorsque le site est accessible au public.");
define('_MD_AM_CLOSESITE', 'Arr&ecirc;ter votre site ?');
define('_MD_AM_CLOSESITEDSC', "Choisissez oui pour arr&ecirc;ter votre site pour que seuls les utilisateurs d'un des groupes choisis aient acc&egrave;s au site. ");
define('_MD_AM_CLOSESITEOK', "S&eacute;lectionnez les groupes qui seront autoris&eacute;s &agrave; acc&eacute;der au site lorsqu'il est arr&ecirc;t&eacute;");
define('_MD_AM_CLOSESITEOKDSC', "On accorde toujours aux utilisateurs du groupe administrateurs l'acc&egrave;s par d&eacute;faut.");
define('_MD_AM_CLOSESITETXT', "Raison de l'arr&ecirc;t du site");
define('_MD_AM_CLOSESITETXTDSC', 'Le texte qui sera pr&eacute;sent&eacute; quand le site est ferm&eacute;.');
define('_MD_AM_SITECACHE', 'Cache large du site');
define('_MD_AM_SITECACHEDSC', "Mise en cache du contenu du site pour un temps indiqu&eacute; afin d'augmenter les performances. La mise en cache large du site ignorera le cache au niveau des modules, le cache au niveau des blocs et le cache au niveau du module des articles.");
define('_MD_AM_MODCACHE', 'Cache large des modules');
define('_MD_AM_MODCACHEDSC', 'Mettre en cache le contenu des modules pour un temps indiqu&eacute; afin augmenter les performances. <br>La mise en cache large des modules ignorera le cache au niveau du module des articles.');
define('_MD_AM_NOMODULE', "Il n'y a pas de modules qui peuvent &ecirc;tre mis en cache.");
define('_MD_AM_DTPLSET', 'Choix du jeu de templates par d&eacute;faut');
define('_MD_AM_SSLLINK', 'URL pour la page de la connexion SSL');

// added for mailer
define("_MD_AM_MAILER","Param&egrave;tre mail");
define("_MD_AM_MAILER_MAIL","");
define("_MD_AM_MAILER_SENDMAIL","");
define("_MD_AM_MAILER_","");
define("_MD_AM_MAILFROM","DE adresse");
define("_MD_AM_MAILFROMDESC","");
define("_MD_AM_MAILFROMNAME","DE nom");
define("_MD_AM_MAILFROMNAMEDESC","");
// RMV-NOTIFY
define("_MD_AM_MAILFROMUID","DE utilisateur");
define("_MD_AM_MAILFROMUIDDESC","Quand le syst&egrave;me envoie un message priv&eacute;, avec quel utilisateur doit-il sembler avoir &eacute;t&eacute; envoy&eacute; ?");
define("_MD_AM_MAILERMETHOD","M&eacute;thode d'envoi du mail");
define("_MD_AM_MAILERMETHODDESC","La m&eacute;thode utilis&eacute;e pour envoyer le mail. Par d&eacute;faut c'est 'mail', utiliser une autre uniquement en cas de probl&egrave;mes.");
define("_MD_AM_SMTPHOST","H&ocirc;te(s) SMTP");
define("_MD_AM_SMTPHOSTDESC","Liste des serveurs SMTP pour essayer de se connecter.");
define("_MD_AM_SMTPUSER","Nom utilisateur SMTPAuth");
define("_MD_AM_SMTPUSERDESC","Nom utilisateur pour se connecter &agrave; l'h&ocirc;te STMP avec SMTPAuth.");
define("_MD_AM_SMTPPASS","Mot de passe SMTPAuth");
define("_MD_AM_SMTPPASSDESC","Mot de passe pour se connecter &agrave; l'h&ocirc;te STMP avec SMTPAuth.");
define("_MD_AM_SENDMAILPATH","Chemin pour l'envoi du mail");
define("_MD_AM_SENDMAILPATHDESC","Chemin du programe d'envoi du mail (ou substitut) sur le serveur.");
define("_MD_AM_THEMEOK","Th&egrave;mes s&eacute;lectionnables");
define("_MD_AM_THEMEOKDSC","Choisissez les th&egrave;mes que les utilisateurs peuvent choisir comme th&egrave;me par d&eacute;faut dans le bloc th&egrave;mes");


// Xoops Authentication constants
define("_MD_AM_AUTH_CONFOPTION_XOOPS", "Base de donn&eacute;es XOOPS");
define("_MD_AM_AUTH_CONFOPTION_LDAP", "Annuaire Standard LDAP");
define("_MD_AM_AUTH_CONFOPTION_AD", "Annuaire Active Microsoft &copy");
define("_MD_AM_AUTHENTICATION", "Options d'authentification");
define("_MD_AM_AUTHMETHOD", "M&eacute;thode d'authentification");
define("_MD_AM_AUTHMETHODDESC", "Quelle m&eacute;thode voulez-vous utiliser pour authentifier vos utilisateurs");
define("_MD_AM_LDAP_MAIL_ATTR", "Attribut mail");
define("_MD_AM_LDAP_MAIL_ATTR_DESC", "Le nom de l'attribut repr&eacute;sentant le mail dans votre annuaire");
define("_MD_AM_LDAP_NAME_ATTR", "Attribut nom complet de la personne");
define("_MD_AM_LDAP_NAME_ATTR_DESC", "Le nom de l'attribut repr&eacute;sentant le nom complet de la personne (en g&eacute;n&eacute;ral 'cn')");
define("_MD_AM_LDAP_SURNAME_ATTR", "Attribut nom de famille de la personne");
define("_MD_AM_LDAP_SURNAME_ATTR_DESC", "Le nom de l'attribut repr&eacute;sentant le nom de famille de la personne (en g&eacute;n&eacute;ral 'sn')");
define("_MD_AM_LDAP_GIVENNAME_ATTR","Attribut pr&eacute;nom de la personne");
define("_MD_AM_LDAP_GIVENNAME_ATTR_DSC", "Le nom de l'attribut repr&eacute;sentant le nom de famille de la personne (en g&eacute;n&eacute;ral 'givenname')");
define("_MD_AM_LDAP_BASE_DN", "DN de base");
define("_MD_AM_LDAP_BASE_DN_DESC", "Nom du DN de base pour les utilisateurs (ou=users,dc=xoops,dc=org)");
define("_MD_AM_LDAP_PORT","Port LDAP");
define("_MD_AM_LDAP_PORT_DESC","Port d'&eacute;coute de votre annuaire LDAP (par d&eacute;faut 389 )");
define("_MD_AM_LDAP_SERVER","Nom du serveur");
define("_MD_AM_LDAP_SERVER_DESC","Nom ou adresse IP du serveur LDAP");

define("_MD_AM_LDAP_MANAGER_DN", "DN de recherche");
define("_MD_AM_LDAP_MANAGER_DN_DESC", "DN de la personne autoris&eacute;e &agrave; faire des recherches (par exemple cn=manager,dc=xoops,dc=org) ");
define("_MD_AM_LDAP_MANAGER_PASS", "Mot de passe de recherche");
define("_MD_AM_LDAP_MANAGER_PASS_DESC", "Mot de passe de la personne autoris&eacute;e &agrave; faire des recherches");
define("_MD_AM_LDAP_VERSION", "Version LDAP");
define("_MD_AM_LDAP_VERSION_DESC", "Version du protocole LDAP : 2 ou 3");
define("_MD_AM_LDAP_USERS_BYPASS", " Utilisateurs contournant l'authentication LDAP");
define("_MD_AM_LDAP_USERS_BYPASS_DESC", "Authentification directe dans base de donn&eacute;es XOOPS.<br>Noms utilisateurs s&eacute;par&eacute;s par | ");

define("_MD_AM_LDAP_LOGINLDAP_ATTR","Attribut utilis&eacute; pour rechercher un utilisateur");
define("_MD_AM_LDAP_LOGINLDAP_ATTR_D","Quand l'utilisation du nom de connexion dans l'option DN est plac&eacute;e &agrave; oui, il doit correspondre &agrave; celui de XOOPS");
define("_MD_AM_LDAP_LOGINNAME_ASDN", "Nom de login pr&eacute;sent dans le DN");
define("_MD_AM_LDAP_LOGINNAME_ASDN_D", "Le nom de login XOOPS est utilis&eacute; dans le DN (eg : uid=<loginname>,dc=xoops,dc=org)<br>L'entr&eacute;e est directement lue dans le serveur LDAP sans recherche");

define("_MD_AM_LDAP_FILTER_PERSON", "Filtre de recherche");
define("_MD_AM_LDAP_FILTER_PERSON_DESC", "Filtre sp&eacute;cial pour la recherche de personne. @@loginname@@ est remplac&eacute; par le nom de login<br> Laisser en blanc par d&eacute;faut !" .
		"<br>Ex : (&(objectclass=person)(samaccountname=@@loginname@@)) pour AD" .
		"<br>Ex : (&(objectclass=inetOrgPerson)(uid=@@loginname@@)) pour LDAP");

define("_MD_AM_LDAP_DOMAIN_NAME", "Nom de domaine");
define("_MD_AM_LDAP_DOMAIN_NAME_DESC", "Nom de domaine Windows. Pour ADS et serveur NT");

define("_MD_AM_LDAP_PROVIS", "Provisionnement automatique du compte XOOPS");
define("_MD_AM_LDAP_PROVIS_DESC", "Cr&eacute;&eacute; le compte XOOPS automatiquement si l'authentification est correcte");

define("_MD_AM_LDAP_PROVIS_GROUP", "Affectation par d&eacute;faut au(x) groupe(s)");
define("_MD_AM_LDAP_PROVIS_GROUP_DSC", "S&eacute;lectionner les groupes auquels l'utilisateur cr&eacute;&eacute; sera affect&eacute;");

?>