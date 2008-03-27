<?php
// $Id: global.php,v 1.2 2007/03/30 22:07:16 catzwolf Exp $
// %%%%%%	File Name mainfile.php 	%%%%%
define( "_DBUPDATED", "Duomenų bazė atnaujinta" );
define( "_DBTRUNCATED", "Duomenų bazės lentelės įrašai buvo išvalyti" );
define( "_DBOPT", "Duomenų bazės lentelė buvo optimitizuota" );
define( "_DBCREATED", "Naujas įrašas sukurtas" );
define( "_TAKINGBACK", "Gražinama jus atgal, kur tik ką buvote...." );
define( "_ERRORUPDATING", "ĮSPĖJIMAS: Klaida atnaujinant duomenų bazę. Niekas nebuvo pakeista." );
define( "_NOTHINGSELECTED", "Pranešimas: Nieko nepasirinkta. Niekas nepakeista." );
define( "_NOTHINGFOUND", "Jokios informacijos nerasta atitinkačios šį kriterijų" );
define("_LOGOUT","Atsijungti");
define("_SUBJECT","Tema");
define("_MESSAGEICON","Pranešimo paveiksliukas");
define("_COMMENTS","Komentarai");
define("_POSTANON","Rašyti anonimiškai");
define("_DISABLESMILEY","Atjungti šypsenėles");
define("_DISABLEHTML","Atjungti html");
define("_PREVIEW","Peržiūrėti");
define( "_RETURN", "Grįžti" );
define( "_ACTION", "Veiksmas" );
define( "_ALLOW", "Leisti" );
define( "_BLOCKED", "Blokuoti" );
define( "_GO", "Įrašyti" );
define("_NESTED","Struktūrinis");
define("_NOCOMMENTS","Be komentarų");
define("_FLAT","Plokščias");
define("_THREADED","Spiralinis");
define("_OLDESTFIRST","Seniausi pirmiau");
define("_NEWESTFIRST","Naujausi pirmiau");
define("_MORE","daugiau...");
define("_MULTIPAGE","Norint, kad jūsų straipsnis persikeltų per keletą puslapių į savo straipsnį įdėkite žodį <font color=red>[pagebreak]</font> (kvadratiniuose skliaustuose).");
define("_IFNOTRELOAD","Jei puslapis automatiškai nepersikrauna, paspauskite <a href='%s'>čia</a>");
define("_WARNINSTALL2","DĖMESIO: Katalogas %s egzistuoja jūsų stotyje. <br />Saugumo sumetimais prašome išimti šį katalogą.");
define("_WARNINWRITEABLE","DĖMESIO: Byla %s yra tarnybinės stoties perrašoma. <br />Prašome pakeisti priėjimą prie šios bylos dėl saugumo sumetimų.<br /> Unix (444), in Win32 (read-only/tik-skaityti)");
define( "_SELECTEDITOR", "Pasirinkite redaktorių:" );

/*Icons */
define( "_PRINT_ICON", "Spausdinti" );
define( "_PDF_ICON", "PDF" );
define( "_EMAIL_ICON", "E-paštas" );
define( "_RSS_ICON", "RSS" );

define( "_PRINTER", "Spausdinimui skirta versija" );
define( "_PRINT_PAGE", "Spausdinti puslapį" );
// %%%%%%	File Name themeuserpost.php 	%%%%%
define("_PROFILE","Profilis");
define("_POSTEDBY","Rašė");
define("_VISITWEBSITE","Aplankykite svetainę");
define("_SENDPMTO","Siųsti asmeninę žinutę %s");
define("_SENDEMAILTO","Siųsti elektroninio pašto žinutę %s");
define("_ADD","Pridėti");
define("_REPLY","Atsakyti");
define("_DATE","Data");   // Posted date
define( "_YEAR", "Metai" ); // Posted date

// %%%%%%	File Name admin_functions.php 	%%%%%
// define("_MAIN","Main");
// define("_MANUAL","Manual");
// define("_INFO","Info");
// define("_CPHOME","Control Panel Home");
// define("_YOURHOME","Home Page");
// %%%%%%	File Name misc.php (who's-online popup)	%%%%%
define("_WHOSONLINE","Kas prisijungęs");
define('_GUESTS', 'Svečiai');
define('_MEMBERS', 'Nariai');
define("_ONLINEPHRASE","<b>%s</b> nariai prisijungę");
define("_ONLINEPHRASEX","<b>%s</b> nariai naršo <b>%s</b>");
define("_CLOSE","Uždaryti");  // Close window

// %%%%%%	File Name class.textsanitizer.php 	%%%%%
define("_QUOTEC","Citata:");
// %%%%%%	File Name admin.php 	%%%%%
define("_NOPERM","Atsiprašome, jūs neturite privilegijų pasiekti šią vietą.");
// %%%%%		Common Phrases		%%%%%
define("_NO","Ne");
define("_YES","Taip");
define( "_CREATE", "Sukurti naują" );
define( "_EDIT", "Redaguoti įrašą" );
define( "_EDIT_LEG", "Redaguoją pasirinktą įrašą" );
define( "_CLONE", "Klonuoti įrašą" );
define( "_CLONE_LEG", "Klonuoja pasirinktą įrašą" );
define( "_EDITS", "Redaguoti %s" );
define( "_VIEW", "Peržiūrėti įrašą" );
define( "_VIEW_LEG", "Parodo pasirinktą įrašą" );
define( "_DELETE", "Ištrinti įrašą" );
define( "_DELETE_LEG", "Ištrina pasirinktą įrašą" );
define( "_USER", "Pakeisti vartotojo informaciją" );
define( "_USER_LEG", "Modifikuoti vartotojo teises" );
define( "_MENUS", "Rodyti meniu punktus" );
define( "_MENUS_LEG", "Rodo pasirinktus meniu punktus" );
define( "_ADDON_INSTALL", "Įdiegti priedą" );
define( "_ADDON_INSTALL_LEG", "Įdiegti pasirinktą priedą" );
define( "_ADDON_HOME", "Rodyti priedų administravimą" );
define( "_ADDON_HOME_LEG", "Rodyti priedų administravimo puslapį" );
define( "_ADDON_UPDATE", "Atnaujinti priedus" );
define( "_ADDON_UPDATE_LEG", "Atnaujina pasirinktus priedus" );
define( "_ADDON_UNINSTALL", "Išdiegti priedus" );
define( "_ADDON_UNINSTALL_LEG", "Išdiegia pasirinktus priedus" );
define( "_LIST_LEG", "Sąrašas" );
define( '_NOCACHE', 'Ne kešuoti' );

define("_INFO","Informacija");
define( '_INFO_LEG', 'Rodyti pasirinkto dalyko informaciją' );
define( '_CONTACT', 'Susisiekti su šiuo vartotoju elektroniniu paštu arba privačia žinute' );
define( '_CONTACT_LEG', 'Susisiekti su pasirinktu vartotoju' );
define( '_SUSPEND', 'Laikinai atjungti vartotoją' );
define( '_SUSPEND_LEG', 'Laikinai atjungti pasirinktą vartotoją' );
define( '_DEACTIVATE', 'Deaktyvuoti vartotoją' );
define( '_DEACTIVATE_LEG', 'Deaktyvuoti pasirinktą vartotoją' );
define( '_ACTIVATE', 'Aktyvuoti vartotoją' );
define( '_ACTIVATE_LEG', 'Aktyvuoti pasirinktą vartotoją' );
define( '_UPLOAD', 'Įkelti' );
define( '_UPLOAD_LEG', 'Įkelia media failus į serverį' );
define( '_DOWNLOAD_LEG', 'Įkelti failą' );

define( "_CANCEL", "Atšaukti" );
define( "_RESET", "Atstatyti" );
define("_SUBMIT","Pateikti");
define( "_DISPLAY", "Rodyti" );
define( "_CONTINUE", "Tęsti" );
define( "_SENDMAIL", "Siųsti laišką" );

define( "_AM_ACTION", "Veiksmas" );
define( "_MA_AD_ACTION", _AM_ACTION );
define( "_MD_AM_ACTION", _MA_AD_ACTION );

define('_MD_AM_SHORTURL', 'Trumpas adresas');

define( "_MAINMENU", "Pagrindinis meniu" );
define( "_USERMENU", "Vartotojo meniu" );
define( "_TOPMENU", "Viršutinis meniu" );
define( "_FOOTERMENU", "Apatinis emniu" );
define( "_PARENTWINDOW", "Tėvinis langas" );
define( "_NEWWINDOW", "Naujas langas" );
//define( "_SUBMIT", "Save" );
define( "_REQUIRED", "Reikalingas" );
define( "_ONLINE", "Prisijungęs(-usi)" );
define( "_OFFLINE", "Atsijungęs(-usi)" );

//define( "_CONTACT", "Contact" );
//define( "_ACTIVATE", "Activate" );
//define( "_DEACTIVATE", "De-Activate" );
//define( "_SUSPEND", "Suspend" );
define( "_SHOW", "Rodyti" );
define( "_OR", " <b>ARBA</b> " );
define( "_USEROPTIONS", "Vartotojo pasirinkimai" );

define( "_ADDONNOEXIST", "Pasirinktas priedas neegzistuoja!" );
define("_ALIGN","Rikiuoti");
define("_LEFT","Kairė");
define("_CENTER","Centras");
define("_RIGHT","Dešinė");
define("_FORM_ENTER", "Prašome įvesti %s");
// %s represents file name
define("_MUSTWABLE","Byla %s turi būti rašoma tarnybinėje stotyje!");
// Addons info
define('_PREFERENCES', 'Nustatymai');
define("_VERSION", "Versija");
define("_DESCRIPTION", "Apibūdinimas");

define("_ERRORS", "Klaidos");
define("_NONE", "Nėra");
define('_ON','');
define('_READS','perskaityta');
define('_WELCOMETO','Sveiki atvykę į %s');
define('_SEARCH','Ieškoti');
define('_ALL', 'Visi');
define('_TITLE', 'Pavadinimas');
define('_OPTIONS', 'Pasirinkimai');
define('_QUOTE', 'Citata');
define('_LIST', 'Sąrašas');

define('_LOGIN','Prisijungti');
define('_USERNAME','Vartotojas: ');
define('_PASSWORD','Slaptažodis: ');
define('_REMEMBERME','Prisiminti mane');
define( "_SELECT", "pasirinkti" );
//define( "_IMAGE", "Image" );
define( "_SEND", "Siųsti" );
define("_ASCENDING","Didėjančia tvarka");
define("_DESCENDING","Žemėjančia tvarka");
define('_BACK', 'Atgal');
define('_NOTITLE', 'Be pavadinimo');
define( '_DOWNLOAD', 'Atsisiųsti failą' );

define( '_LEGEND', 'Formos Legenda:' );
define( '_BOX_LEGEND_TITLE', 'Formos Legenda' );
// %%%%%	File Name class/zariliaform/formmatchoption.php 	%%%%%
define("_STARTSWITH", "Prasideda su");
define("_ENDSWITH", "Baigiasi su");
define("_MATCHES", "Atitinka");
define("_CONTAINS", "Turi");
// %%%%%%	File Name commentform.php 	%%%%%
define("_REGISTER","Registruotis");

define("_SIZE","DYDIS");  // font size
define("_FONT","FONTAS");  // font family
define("_COLOR","SPALVA");  // font color

define( "_EXAMPLE", "Pavyzdys" );
define("_ENTERURL","Įveskite norimą pridėti nuorodos adresą (URL):");
define("_ENTERWEBTITLE","Įveskite svetainės pavadinimą:");
define("_ENTERIMGURL","Įveskite norimą įdėti paveikslo adresą (URL).");
define("_ENTERIMGPOS","Dabar įveskite paveikslo poziciją.");
define("_IMGPOSRORL","'R' arba 'r' dėl dešinės, 'L' arba 'l' dėl kairės, arba palikite tuščią.");
define("_ERRORIMGPOS","KLAIDA! Įveskite paveikslo poziciją.");
define("_ENTEREMAIL","Įveskite el. pašto adresą.");
define("_ENTERCODE","Įveskite kodą kurį norite įdėti.");
define("_ENTERQUOTE","Įveskite tekstą kurį norite cituoti.");
define("_ENTERTEXTBOX","Prašome įvesti tekstą į teksto dėžutę.");
define("_ALLOWEDCHAR","Leistinas maksimalus ženklų skaičius: ");
define("_CURRCHAR","Dabartinis ženklų skaičius: ");
define("_PLZCOMPLETE","Prašome pabaigti temos ir žinutės žinutės laukus.");
define("_MESSAGETOOLONG","Jūsų žinutė per ilga.");
// %%%%%		TIME FORMAT SETTINGS   %%%%%
define('_SECOND', '1 sekundė');
define('_SECONDS', '%s sekundžių');
define('_MINUTE', '1 minutė');
define('_MINUTES', '%s minučių');
define('_HOUR', '1 valanda');
define('_HOURS', '%s valandų');
define('_DAY', '1 diena');
define('_DAYS', '%s dienų');
define('_WEEK', '1 savaitė');
define('_MONTH', '1 mėnuo');

define("_SHORTDATESTRING","Y.n.j");
define("_DATESTRING",_SHORTDATESTRING." G:i:s");
define("_MEDIUMDATESTRING",_SHORTDATESTRING." G:i");
/*
The following characters are recognized in the format string:
a - "am" or "pm"
A - "AM" or "PM"
d - day of the month, 2 digits with leading zeros; i.e. "01" to "31"
D - day of the week, textual, 3 letters; i.e. "Fri"
F - month, textual, long; i.e. "January"
h - hour, 12-hour format; i.e. "01" to "12"
H - hour, 24-hour format; i.e. "00" to "23"
g - hour, 12-hour format without leading zeros; i.e. "1" to "12"
G - hour, 24-hour format without leading zeros; i.e. "0" to "23"
i - minutes; i.e. "00" to "59"
j - day of the month without leading zeros; i.e. "1" to "31"
l (lowercase 'L') - day of the week, textual, long; i.e. "Friday"
L - boolean for whether it is a leap year; i.e. "0" or "1"
m - month; i.e. "01" to "12"
n - month without leading zeros; i.e. "1" to "12"
M - month, textual, 3 letters; i.e. "Jan"
s - seconds; i.e. "00" to "59"
S - English ordinal suffix, textual, 2 characters; i.e. "th", "nd"
t - number of days in the given month; i.e. "28" to "31"
T - Timezone setting of this machine; i.e. "MDT"
U - seconds since the epoch
w - day of the week, numeric, i.e. "0" (Sunday) to "6" (Saturday)
Y - year, 4 digits; i.e. "1999"
y - year, 2 digits; i.e. "99"
z - day of the year; i.e. "0" to "365"
Z - timezone offset in seconds (i.e. "-43200" to "43200")
*/

// define( '_CONFIRM_CODE_DESC', "Random confirmation code. Non-case-sensitive!" );
// define( '_CONFIRM_CODE_WRONG', "The confirm code is wrong" );
// define( '_TOO_MANY_ATTEMPTS', "<b>Possible Security Alert</b><br /><br />There where to many attempts to login to our system, As a security measure we have now disabled our system to you.<br /><br />You're IP Address has been logged.<br /><br />Please contact us if you feel this is an error." );

define( "_MA_NAV_ACTIONS", "Vartotojo meniu" );
define( "_MA_NAV_VIEWACCOUNT", "Rodyti praskyrą" );
define( "_MA_NAV_EDITACCOUNT", "Redaguoti praskyrą" );
define( "_MA_NAV_NOTIFICATIONS", "Pranešimai" );
define( "_MA_NAV_LOGOUT", "Atsijungti" );
define( "_MA_NAV_ADMINISTRATION", "Administravimas" );

/*
* login details
*/
//define( "_MA_NAV_ACTIONS", "User Menu" );
define( "_MA_NAV_LOGINENTER", "Vartotojo vardas" );
define( "_MA_NAV_LOGIN", "Vartotojo prisijungimas" );

/*
* Mimetypes
*/
define( "_PAGE", "Puslapis: " );
define( "_PREVIOUS", "Atgal" );
define( "_NEXT", "Toliau" );

?>