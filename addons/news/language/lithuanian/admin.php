<?php
// $Id: admin.php,v 1.18 2004/07/26 17:51:25 hthouzard Exp $
//%%%%%%	Admin Module Name  Straipsniai 	%%%%%
define("_AM_DBUPDATED","Duomenų bazė atnaujinta sėkmingai!");
define("_AM_CONFIG","Naujienų parinktys");
define("_AM_AUTOARTICLES","Automatizuoti straipsniai");
define("_AM_STORYID","Straipsnio ID");
define("_AM_TITLE","Pavadinimas");
define("_AM_TOPIC","Temas");
define("_AM_POSTER","Rašytojas");
define("_AM_PROGRAMMED","Programuota Data/Laikas");
define("_AM_ACTION","Veiksmas");
define("_AM_EDIT","Redaguoti");
define("_AM_DELETE","Trinti");
define("_AM_LAST10ARTS","Paskutinius %d straipsnių");
define("_AM_PUBLISHED","Publikuota"); // Published Date
define("_AM_GO","Tęsti!");
define("_AM_EDITARTICLE","Redaguoti straipsnį");
define("_AM_POSTNEWARTICLE","Rašyti naują straipsnį");
define("_AM_ARTPUBLISHED","Jūsų straipsnis buvo publikuotas!");
define("_AM_HELLO","Sveiki %s,");
define("_AM_YOURARTPUB","Jūsų straipsnis priduotas į mūsų svetainę buvo publikuotas.");
define("_AM_TITLEC","Tema: ");
define("_AM_URLC","URL: ");
define("_AM_PUBLISHEDC","Publikuota: ");
define("_AM_RUSUREDEL","Ar jūs tikrai norite ištrinti šį straipsnį ir visus jo komentarus?");
define("_AM_YES","Taip");
define("_AM_NO","Ne");
define("_AM_INTROTEXT","Įžanginis tekstas");
define("_AM_EXTEXT","Papildomas tekstas");
define("_AM_ALLOWEDHTML","Leistinas HTML:");
define("_AM_DISAMILEY","Atjungti šypsniukus");
define("_AM_DISHTML","Atjungti HTML");
define("_AM_APPROVE","Patvirtinti");
define("_AM_MOVETOTOP","Pervesti šį straipsnį viršun");
define("_AM_CHANGEDATETIME","Pakeisti publikacijos datą/laiką");
define("_AM_NOWSETTIME","Dabar nustatyta yra: %s"); // %s is datetime of publish
define("_AM_CURRENTTIME","Dabartinis laikas yra: %s");  // %s is the current datetime
define("_AM_SETDATETIME","Nustatyti pubikacijos datą/laiką");
define("_AM_MONTHC","Mėnuo:");
define("_AM_DAYC","Diena:");
define("_AM_YEARC","Metai:");
define("_AM_TIMEC","Laikas:");
define("_AM_PREVIEW","Peržiūrėti");
define("_AM_SAVE","Saugoti");
define("_AM_PUBINHOME","Publikuoti Namų puslapyje?");
define("_AM_ADD","Pridėti");

//%%%%%%	Admin Module Name  Temos 	%%%%%

define("_AM_ADDMTOPIC","Pridėti PAGRINDINĘ Temą");
define("_AM_TOPICNAME","Temos pavadinimas");
// Warning, changed from 40 to 255 characters.
define("_AM_MAX40CHAR","(maksimumas: 255 ženklai)");
define("_AM_TOPICIMG","Temos paveikslas");
define("_AM_IMGNAEXLOC","paveikslo pavadinimas + pratęsimas randasi %s");
define("_AM_FEXAMPLE","pavyzdžiuo: žaidimai.gif");
define("_AM_ADDSUBTOPIC","Pridėti potemę");
define("_AM_IN","esančią");
define("_AM_MODIFYTOPIC","Keisti temą");
define("_AM_MODIFY","Keisti");
define("_AM_PARENTTOPIC","Pagrindinė tema");
define("_AM_SAVECHANGE","Išsaugoti pakeitimus");
define("_AM_DEL","Trinti");
define("_AM_CANCEL","Anuliuoti");
define("_AM_WAYSYWTDTTAL","PERSPĖJIMAS: Ar jūs tikrai norite ištrinti šią temą ir VISUS šios temos straipsnius su komentarais?");


// Added in Beta6
define("_AM_TOPICSMNGR","Temų tvarkiklis");
define("_AM_PEARTICLES","Rašyti/Redaguoti straipsnius");
define("_AM_NEWSUB","Nauji pateikimai");
define("_AM_POSTED","Posted");
define("_AM_GENERALCONF","Bendros parinktys");

// Added in RC2
define("_AM_TOPICDISPLAY","Rodyti temos paveikslą?");
define("_AM_TOPICALIGN","Pozicija");
define("_AM_RIGHT","Dešinė");
define("_AM_LEFT","Kairė");

define("_AM_EXPARTS","Nebegaliojantys straipsniai");
define("_AM_EXPIRED","Nustojo galioti");
define("_AM_CHANGEEXPDATETIME","Pakeisti galiojimo datą/laiką");
define("_AM_SETEXPDATETIME","Nurodyti galiojimo datą/laiką");
define("_AM_NOWSETEXPTIME","Dabar yra nustatytas: %s");

// Added in RC3
define("_AM_ERRORTOPICNAME", "Jūs turite įvesti temos pavadinimą!");
define("_AM_EMPTYNODELETE", "Nėra ką ištrinti!");

// Added 240304 (Mithrandir)
define('_AM_GROUPPERM', 'Pateikimo/Patvirtinimo/Rodymo Leidimai');
define('_AM_SELFILE','Pasirinkite bylą pakrauti');

// Added by Hervé
define('_AM_UPLOAD_DBERROR_SAVE','Pakraunant prie straipsnio bylą įvyko klaida');
define('_AM_UPLOAD_ERROR','Pakrauntant bylą įvyko klaida');
define('_AM_UPLOAD_ATTACHFILE','Pridėta byla(os)');
define('_AM_APPROVEFORM', 'Patvirtinimo Leidimai');
define('_AM_SUBMITFORM', 'Pateikimo Leidimai');
define('_AM_VIEWFORM', 'Rodymo Leidimai');
define('_AM_APPROVEFORM_DESC', 'Pažymėkite kas gali patvirtinti naujienas');
define('_AM_SUBMITFORM_DESC', 'Pažymėkite kas gali pateikti naujienas');
define('_AM_VIEWFORM_DESC', 'Pažymėkite kas gali matyti kurias temas');
define('_AM_DELETE_SELFILES', 'Trinkti pažymėtas bylas');
define('_AM_TOPIC_PICTURE', 'Pakrauti paveikslą');
define('_AM_UPLOAD_WARNING', '<B>Perspėjimas, neužmirškite suteikti rašymo leidimo sekančiam katalogui: %s</B>');

define('_AM_NEWS_UPGRADECOMPLETE', 'Atnaujinimas baigtas');
define('_AM_NEWS_UPDATEMODULE', 'Atnaujinti modulio šablonus ir blokus');
define('_AM_NEWS_UPGRADEFAILED', 'Atnaujinti nepavyko');
define('_AM_NEWS_UPGRADE', 'Atnaujinti');
define('_AM_ADD_TOPIC','Pridėti temą');
define('_AM_ADD_TOPIC_ERROR','Klaida, tema jau egzistuoja!');
define('_AM_ADD_TOPIC_ERROR1','KLAIDA: Šios temos pasirinkti pagrindinei temai negalima!');
define('_AM_SUB_MENU','Publikuoti šią temą kaip pomeniu');
define('_AM_SUB_MENU_YESNO','pomeniu?');
define('_AM_HITS', 'Kirčių');
define('_AM_CREATED', 'Sukurtas');

define('_AM_TOPIC_DESCR', "Temos apibūdinimas");
define('_AM_USERS_LIST', "Vartotojų sąrašas");
define('_AM_PUBLISH_FRONTPAGE', "Publikuoti priekiniame puslapyje?");
define('_AM_NEWS_UPGRADEFAILED1', 'Nebuvo galimybės sukurti lentelę stories_files');
define('_AM_NEWS_UPGRADEFAILED2', "Nebuvo galimybės pakeisti temos pavadinimo ilgumą");
define('_AM_NEWS_UPGRADEFAILED21', "Nebuvo galimybės pridėti naujus laukus į temos lentelę");
define('_AM_NEWS_UPGRADEFAILED3', 'Nebuvo galimybės sukurti lentelę stories_votedata');
define('_AM_NEWS_UPGRADEFAILED4', "Nebuvo galimybės sukurti du laukus 'rating' ir 'votes' lentelėje 'story'");
define('_AM_NEWS_UPGRADEFAILED0', "Prašome pasižymėti žinutes ir bandyti ištaisyti problemas su phpMyadmin ir sql apibrėžimų byla esančia naujienų modulio 'sql' kataloge");
define('_AM_NEWS_UPGR_ACCESS_ERROR',"Klaida, norint naudoti atnaujinimo skriptą, jūs turite būti šio modulio administratoriumi");
define('_AM_NEWS_PRUNE_BEFORE',"Genėti straipsnius kurie buvo rašyti prieš");
define('_AM_NEWS_PRUNE_EXPIREDONLY',"Išimti tik straipsnius kurie yra pasibaigę");
define('_AM_NEWS_PRUNE_CONFIRM',"Perspėjimas, jūs norinte visiškai ištrinti straipsnius kurie buvo rašyti prieš %s (šis veiksmas negali būti atitaisomas). Tai reprezentuoja %s straipsnių.<br />Ar jūs tikras ?");
define('_AM_NEWS_PRUNE_TOPICS',"Limituoti tik sekančioms temoms");
define('_AM_NEWS_PRUNENEWS', 'Genėti naujienas');
define('_AM_NEWS_EXPORT_NEWS', 'Eksportuoti najienas');
define('_AM_NEWS_EXPORT_NOTHING', "Atsiprašome bet nėra ką eksportuoti, prašome pasitikrinti savo kriterijus");
define('_AM_NEWS_PRUNE_DELETED', '%d naujienos buvo ištrintos');
define('_AM_NEWS_PERM_WARNING', '<h2>Perspėjimas, jūs turite 3 formas todėl jūs turite 3 pridavimo mygtukus</h2>');
define('_AM_NEWS_EXPORT_BETWEEN', 'Eksportuoti naujienas spausdintas tarp');
define('_AM_NEWS_EXPORT_AND', ' ir ');
define('_AM_NEWS_EXPORT_PRUNE_DSC', "Jei jūs nieko nepažymėsite tada bus naudojamos visos temos <br/> kitaip bus naudojamos tik pažymėtos temos");
define('_AM_NEWS_EXPORT_INCTOPICS', 'Įtraukti temų definicijas?');
define('_AM_NEWS_EXPORT_ERROR', 'Įvyko klaida bandand sukurti bylą %s. Operacija sustabdyta.');
define('_AM_NEWS_EXPORT_READY', "Jūsų xml exporto byla yra paruošta parsiuntimui. <br /><a href='%s'>Spragtelkite šią nuorodą parsisiūsti ją</a>.<br />Neužmirškite <a href='%s'>išimti tai</a> kai viską pabaigsite.");
define('_AM_NEWS_RSS_URL', "RSS padavimo URL");
define('_AM_NEWS_NEWSLETTER', "Informacinis biuletenis");
define('_AM_NEWS_NEWSLETTER_BETWEEN', 'Parinkite naujienas spausdintas tarp');
define('_AM_NEWS_NEWSLETTER_READY', "Jūsų biuletenio byla yra paruošta parsisiuntimui. <br /><a href='%s'>Spragtelkite šią nuorodą parsisiūsti ją</a>.<br /> Neužmirškite <a href='%s'>išimti tai</a> kai viską pabaigsite.");
define('_AM_NEWS_DELETED_OK',"Byla sėkmingai ištrinta");
define('_AM_NEWS_DELETED_PB',"Nepavyko ištrinti bylos");
define('_AM_NEWS_STATS0','Temų statistikos');
define('_AM_NEWS_STATS','Statistikos');
define('_AM_NEWS_STATS1','Unikalių autorių');
define('_AM_NEWS_STATS2','Viso');
define('_AM_NEWS_STATS3','Straipsnių statistikos');
define('_AM_NEWS_STATS4','Labiausiai skaitomi straipsniai');
define('_AM_NEWS_STATS5','Mažiau skaityti straipsniai');
define('_AM_NEWS_STATS6','Geriausiai įvertinti straipsniai');
define('_AM_NEWS_STATS7','Labiausiai skaitomi autoriai');
define('_AM_NEWS_STATS8','Geriausiai įvertinti autoriai');
define('_AM_NEWS_STATS9','Didžiausi pagalbininkai');
define('_AM_NEWS_STATS10','Autorių statistikos');
define('_AM_NEWS_STATS11',"Viso straipsnių");
define('_AM_NEWS_HELP',"Pagalba");
define("_AM_NEWS_MODULEADMIN","Modulio administracija");
define("_AM_NEWS_GENERALSET", "Modulio nustatymai" );
define('_AM_NEWS_GOTOMOD','Pasiekti modulį');
define('_AM_NEWS_NOTHING',"Atsiprašome tačiau nėra ką parsisiūsti, prašome patikrinti savo kriterijus!");
define('_AM_NEWS_NOTHING_PRUNE',"Atsiprašome tačiau nėra genimų naujienų, prašome patikrinti savo kriterijus!");
define('_AM_NEWS_TOPIC_COLOR',"Temos spalva");
define('_AM_NEWS_COLOR',"Spalva");
define('_AM_NEWS_REMOVE_BR',"Paversti html &lt;br&gt; priedėli į naują eilutę?");
// Added in 1.3 RC2
define('_AM_NEWS_PLEASE_UPGRADE',"<a href='upgrade.php'><font color='#FF0000'>Prašome atnaujinti modulį !</font></a>");
?>