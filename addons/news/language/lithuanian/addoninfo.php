<?php
// $Id: modinfo.php,v 1.21 2004/09/01 17:48:07 hthouzard Exp $
// Module Info

// The name of this module
define('_MI_NEWS_NAME','Naujienos');

// A brief description of this module
define('_MI_NEWS_DESC','Sukuria Slashdot įspūdžio naujienų skyrių kur vartotojai gali rašyti naujienas/komentarus.');

// Names of blocks for this module (Not all module has blocks)
define('_MI_NEWS_BNAME1','Naujienų Temos');
define('_MI_NEWS_BNAME3','Didysis Straipsnis');
define('_MI_NEWS_BNAME4','Didžiausios Naujienos');
define('_MI_NEWS_BNAME5','Paskutinės Naujienos');
define('_MI_NEWS_BNAME6','Moderuoti Naujienas');
define('_MI_NEWS_BNAME7','Naviguoti per temas');


define('_MI_NEWS_FORM_TINYEDITOR', 'Tiny Editor');
define('_MI_NEWS_CATEGORY_NOTIFY', '');
define('_MI_NEWS_CATEGORY_NOTIFYDSC', '');
define('_MI_NEWS_CATEGORY_STORYPOSTED_NOTIFY', '');
define('_MI_NEWS_CATEGORY_STORYPOSTED_NOTIFYCAP', '');
define('_MI_NEWS_CATEGORY_STORYPOSTED_NOTIFYDSC', '');
define('_MI_NEWS_CATEGORY_STORYPOSTED_NOTIFYSBJ', '');
define('_MI_NEWS_BNAME9', '');
define('_MI_NEWS_TOPICS_DIRECTORY', '');

// Sub menus in main menu block
define('_MI_NEWS_SMNAME1','Pateikti Naujienas');
define('_MI_NEWS_SMNAME2','Archyvas');

// Names of admin menu items
define('_MI_NEWS_ADMENU2', 'Temų Tvarkiklis');
define('_MI_NEWS_ADMENU3', 'Rašyti/Redaguoti Naujienas');
define('_MI_NEWS_GROUPPERMS', 'Leidimai');
// Added by Hervé for prune option
define('_MI_NEWS_PRUNENEWS', 'Genėti naujienas');
// Added by Hervé
define('_MI_NEWS_EXPORT', 'Naujienų eksportavimas');

// Title of config items
define('_MI_STORYHOME', 'Parinkite naujienų skaičių kuris bus rodomas pagrindiniame puslapyje');
define('_MI_NOTIFYSUBMIT', 'Pažymėkite taip norint nusiūsti pranešimą webmasteriui po kiekvieno naujienų pridavimo');
define('_MI_DISPLAYNAV', 'Pažymėkite taip norint rodyti navigacijos dėžutę kiekvieno naujienų puslapio viršuje');
define('_MI_AUTOAPPROVE','Automatiškai patvirtini naujienų rašinius be administratoriaus įsikišimo?');
define("_MI_ALLOWEDSUBMITGROUPS", "Grupės kurios gali Priduoti Naujienas");
define("_MI_ALLOWEDAPPROVEGROUPS", "Grupės kurios gali Patvirtinti Naujienas");
define("_MI_NEWSDISPLAY", "Naujienų rodymo išplanavimas");
define("_MI_NAMEDISPLAY","Autoriaus vardas");
define("_MI_COLUMNMODE","Stulpeliai");
define("_MI_STORYCOUNTADMIN","Naujų straipsnių skaičius rodomas administratoriaus zonoje (šis pasirinkimas taip pat bus naudojamas norint nustatyti kiek temų bus rodoma administratoriaus zonoje ir bus naudojama statistikose.) : ");
define('_MI_UPLOADFILESIZE', 'MAX Bylos pakrovimas (KB) 1048576 = 1 Meg');
define("_MI_UPLOADGROUPS","Grupės kurios gali pakrauti bylas");


// Description of each config items
define('_MI_STORYHOMEDSC', '');
define('_MI_NOTIFYSUBMITDSC', '');
define('_MI_DISPLAYNAVDSC', '');
define('_MI_AUTOAPPROVEDSC', '');
define("_MI_ALLOWEDSUBMITGROUPSDESC", "Pažymėtos grupės galės priduoti naujienas");
define("_MI_ALLOWEDAPPROVEGROUPSDESC", "Pažymėtos grupės galės patvirtinti naujienas");
define("_MI_NEWSDISPLAYDESC", "Klasikinis rodo visas naujienas surikiuotas pagal rašymo datą. Naujienos pagal temą rodys visą paskutinį straipsnį ir visų kitų straipsnių pavadinimus.");
define('_MI_ADISPLAYNAMEDSC', 'Parinkite kaip rodyti autoriaus vardą');
define("_MI_COLUMNMODE_DESC","Jūs galite pasirinkti keliuose stulpeliuose rodyti straipsnių sarašą");
define("_MI_STORYCOUNTADMIN_DESC","");
define("_MI_UPLOADFILESIZE_DESC","");
define("_MI_UPLOADGROUPS_DESC","Parinkite grupes kurios gali pakrauti bylas į tarnybinę stotį.");

// Name of config item values
define("_MI_NEWSCLASSIC", "Klasikinis");
define("_MI_NEWSBYTOPIC", "Pagal Temą");
define("_MI_DISPLAYNAME1", "Vartotojo vardas");
define("_MI_DISPLAYNAME2", "Tikras vardas");
define("_MI_DISPLAYNAME3", "Nerodyti autoriaus");
define("_MI_UPLOAD_GROUP1","Priduodantys ir Patvirtinantys");
define("_MI_UPLOAD_GROUP2","Tik patvirtintojai");
define("_MI_UPLOAD_GROUP3","Pakrovimas atjungtas");

// Text for notifications

define('_MI_NEWS_GLOBAL_NOTIFY', 'Globaliniai');
define('_MI_NEWS_GLOBAL_NOTIFYDSC', 'Globaliniai naujienų pranešimų nustatymai.');

define('_MI_NEWS_STORY_NOTIFY', 'Straipsnis');
define('_MI_NEWS_STORY_NOTIFYDSC', 'Šio straipsnio pranešimų nustatymai.');

define('_MI_NEWS_GLOBAL_NEWCATEGORY_NOTIFY', 'Nauja Tema');
define('_MI_NEWS_GLOBAL_NEWCATEGORY_NOTIFYCAP', 'Pranešti man kai sukuriama nauja tema.');
define('_MI_NEWS_GLOBAL_NEWCATEGORY_NOTIFYDSC', 'Gauti pranešimą kai sukuriama nauja tema.');
define('_MI_NEWS_GLOBAL_NEWCATEGORY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-pranešimas : Nauja naujienų tema');

define('_MI_NEWS_GLOBAL_STORYSUBMIT_NOTIFY', 'Priduotas naujas straipsnis');
define('_MI_NEWS_GLOBAL_STORYSUBMIT_NOTIFYCAP', 'Pranešti man kai priduodamas naujas straipsnis (laukiantis patvirtinimo).');
define('_MI_NEWS_GLOBAL_STORYSUBMIT_NOTIFYDSC', 'Gauti pranešimą kaip priduodamas bet koks naujas straipsnis (laukiantis patvirtinimo).');
define('_MI_NEWS_GLOBAL_STORYSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-pranešimas : Priduotas naujas straipsnis');

define('_MI_NEWS_GLOBAL_NEWSTORY_NOTIFY', 'Naujas Straipsnis');
define('_MI_NEWS_GLOBAL_NEWSTORY_NOTIFYCAP', 'Pranešti man kai parašomas naujas straipsnis.');
define('_MI_NEWS_GLOBAL_NEWSTORY_NOTIFYDSC', 'Gauti pranešimą kai parašomas naujas straipsnis.');
define('_MI_NEWS_GLOBAL_NEWSTORY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-pranešimas : Naujas straipsnis');

define('_MI_NEWS_STORY_APPROVE_NOTIFY', 'Straipsnis Patvirtintas');
define('_MI_NEWS_STORY_APPROVE_NOTIFYCAP', 'Praneši man kai šis straipsnis bus patvirtintas.');
define('_MI_NEWS_STORY_APPROVE_NOTIFYDSC', 'Gauti pranešimą kai šis straipsnis bus patvirtintas.');
define('_MI_NEWS_STORY_APPROVE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-pranešimas : Straipnis patvirtintas');

define('_MI_RESTRICTINDEX', 'Apriboti Temas Rodyklės puslapyje?');
define('_MI_RESTRICTINDEXDSC', 'Jei nustatyta taip, vartotojai matys tik temų naujienas kurias jie gali pasiekti kaip nustatyta Naujienų Leidimuose');

define('_MI_NEWSBYTHISAUTHOR', 'To paties autoriaus kitos naujienos');
define('_MI_NEWSBYTHISAUTHORDSC', 'Jei pasirinksite taip, tada bus matoma nuoroda \'Šio autoriaus straipsniai\'');

define('_MI_NEWS_PREVNEX_LINK','Rodyti nuorodas Praėjęs ir Sekantis ?');
define('_MI_NEWS_PREVNEX_LINK_DESC','Kai šis pasirinkimas yra nustatomas \'Taip\', kiekvieno straipsnio apačioje yra matomos dvi naujos nuorodos. Šios nuorodos naudojamos pasiekti Pirmesni ar Sekantį straipsnį pagal rašymo datą');
define('_MI_NEWS_SUMMARY_SHOW','Rodyti santraukos lentelę ?');
define('_MI_NEWS_SUMMARY_SHOW_DESC','Kai naudojamas šis pasirinkimas, kiekvieno straipsnio apačioje yra rodoma lentelė su nuorodomis į visus nesenai rašytus straipsnius.');
define('_MI_NEWS_AUTHOR_EDIT','Leisti autoriams redaguit savo rašinius?');
define('_MI_NEWS_AUTHOR_EDIT_DESC','Su šiuom pasirinkimu autoriams leidžiama redaguoti savo rašinius.');
define('_MI_NEWS_RATE_NEWS','Leisti vartotojams vertinti naujienas?');
define('_MI_NEWS_TOPICS_RSS','Įgalinti RSS pateikimą pagal temą ?');
define('_MI_NEWS_TOPICS_RSS_DESC',"Jei pasiriksite 'Taip' tada temos turinys bus pasiekiamas kaip RSS pateikimas");
define('_MI_NEWS_DATEFORMAT', "Datos formatas");
define('_MI_NEWS_DATEFORMAT_DESC',"Prašome peržiūrėkite Php dokumentaciją (http://fr.php.net/manual/en/function.date.php) dėl papildomos informacijos kaip pasirinkti datos formatą. Pastaba, jei nieko neįrašysite tada bus naudojamas sisteminis datos formatas");
define('_MI_NEWS_META_DATA', "Įgalinkite meta informacijos (raktažodžių ir apibūnimo) įvedimą ?");
define('_MI_NEWS_META_DATA_DESC', "Jei nustatysite 'Taip' tada patvirtintojai galės įvesti sekančią meta informacija: raktažodžius ir apibūdinimą");
define('_MI_NEWS_BNAME8','Atsitiktinės anujienos');
define('_MI_NEWS_NEWSLETTER','Naujienų laikraštis');
define('_MI_NEWS_STATS','Statistikos');
define("_MI_NEWS_FORM_OPTIONS","Formos pasirinkimas");
define("_MI_NEWS_FORM_COMPACT","Compaktiškas");
define("_MI_NEWS_FORM_DHTML","DHTML");
define("_MI_NEWS_FORM_SPAW","Spaw Redaktorius");
define("_MI_NEWS_FORM_HTMLAREA","HtmlArea Redaktorius");
define("_MI_NEWS_FORM_FCK","FCK Redaktorius");
define("_MI_NEWS_FORM_KOIVI","Koivi Redaktorius");
define("_MI_NEWS_FORM_OPTIONS_DESC","Pasirinkite redaktorių kurį norite naudoti. Jei jūs turite 'paprastą' įdiegimą (pvz. jūs naudojate tik standartinį xoops redaktorių kuris ateina su xoops branduoliu), tada jūs galite pasirinkti tik DHTML ir Compact");
define("_MI_NEWS_KEYWORDS_HIGH","Naudoti raktažodžių  paryškinimą ?");
define("_MI_NEWS_KEYWORDS_HIGH_DESC","Jei naudosite šį pasirinkimą tada raktažodžiai kuriuos jūs įvedėta į paieškos langą straipsniuose bus paryškinami");
define("_MI_NEWS_HIGH_COLOR","Raktažodžių paryškinimo spalva ?");
define("_MI_NEWS_HIGH_COLOR_DES","Naudokite šį pasirinkimą tik tada kai pasirinkote Taip praėjusiame pasirinkime");
define("_MI_NEWS_INFOTIPS","Patarimo ilgis");
define("_MI_NEWS_INFOTIPS_DES","Jei naudosi šį pasirnkimą, nuorodos į naujienas turės pirmus (n) straipsnio ženklus. Jei jūs nustatysite 0 vertę tada informaciniai patarimai bus tušti");
define("_MI_NEWS_SITE_NAVBAR","Naudoti Mozilla ir Opera Svetainės Navigacijos Juostą ?");
define("_MI_NEWS_SITE_NAVBAR_DESC","Jei pažymėsite šį pasirinkimą Taip tada svetainės lankytojai norėdami naviguoti per jūsų straipsnius galės naudotis Svetainės Navigacijos Juosta.");
define("_MI_NEWS_TABS_SKIN","Pasirinkite apipavidalinimą naudoti kortelėse");
define("_MI_NEWS_TABS_SKIN_DESC","Šis apipavidalinimas bus naudojamas visuose blokuose kurie naudoja korteles");
define("_MI_NEWS_SKIN_1","Juostos Stiliaus");
define("_MI_NEWS_SKIN_2","Kūginis");
define("_MI_NEWS_SKIN_3","Klasikinis");
define("_MI_NEWS_SKIN_4","Aplankalo");
define("_MI_NEWS_SKIN_5","MacOS");
define("_MI_NEWS_SKIN_6","Paprastas");
define("_MI_NEWS_SKIN_7","Apvalainas");
define("_MI_NEWS_SKIN_8","ZDnet stiliaus");
?>
