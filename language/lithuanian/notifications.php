<?php
// $Id: notifications.php,v 1.1 2007/03/16 02:44:24 catzwolf Exp $

// RMV-NOTIFY

// Text for various templates...

define ('_NOT_NOTIFICATIONOPTIONS', 'Pranešimų nustatymai');
define ('_NOT_UPDATENOW', 'Atnaujinti dabar');
define ('_NOT_UPDATEOPTIONS', 'Atnaujinti pranešimų nustatymus');

define ('_NOT_CLEAR', 'Išvalyti');
define ('_NOT_CHECKALL', 'Pažymėti visus');
define ('_NOT_ADDON', 'Priedas');
define ('_NOT_CATEGORY', 'Kategorija');
define ('_NOT_ITEMID', 'ID');
define ('_NOT_ITEMNAME', 'Vardas');
define ('_NOT_EVENT', 'Įvykis');
define ('_NOT_EVENTS', 'Įvykiai');
define ('_NOT_ACTIVENOTIFICATIONS', 'Aktyvūs pranešimai');
define ('_NOT_NAMENOTAVAILABLE', 'Vardas užimtas/negaliojantis');
define ('_NOT_NONOTSFOUND', 'Jokių pranešimų nerasta');
// RMV-NEW : TODO: remove NAMENOTAVAILBLE above
define ('_NOT_ITEMNAMENOTAVAILABLE', 'Elemento pavadinimas neprieinamas');
define ('_NOT_ITEMTYPENOTAVAILABLE', 'Elemento tipas neprieinamas');
define ('_NOT_ITEMURLNOTAVAILABLE', 'Elemento URL neprieinamas');
define ('_NOT_DELETINGNOTIFICATIONS', 'Ištrinami pranešimai');
define ('_NOT_DELETESUCCESS', 'Pranešimas(-ai) sėkmingai ištrinti.');
define ('_NOT_UPDATEOK', 'Pranešimų nustatymai atnaujinti');
define ('_NOT_NOTIFICATIONMETHODIS', 'Pranešimo metodas yra');
define ('_NOT_EMAIL', 'elektroninis paštas');
define ('_NOT_PM', 'asmeninė žinutė');
define ('_NOT_DISABLE', 'atjungtas');
define ('_NOT_CHANGE', 'Pakeisti');
define ('_NOT_NOACCESS', 'Jūs neturite teisių pasiekti šį puslapį.');

// Text for addon config options
define ('_NOT_ENABLE', 'Įgalinti');
define ('_NOT_NOTIFICATION', 'Pranešimas');

define ('_NOT_CONFIG_ENABLED', 'Įgalinti pranešimus');
define ('_NOT_CONFIG_ENABLEDDSC', 'Šis modulis leidžia vartotojams pasirinkti pranešimus kai įvyksta atitinkamas įvykis. Paspauskite "Taip" norint įgalinti šią galimybę.');

define ('_NOT_CONFIG_EVENTS', 'Įgalinti specialius įvykius');
define ('_NOT_CONFIG_EVENTSDSC', 'Nustatykite pranešimus kuriuos gali pasirinkti jūsų vartotojai.');

define ('_NOT_CONFIG_ENABLE', 'Įgalinti pranešimus');
define ('_NOT_CONFIG_ENABLEDSC', 'Šis modulis leidžia vartotojams pasirinkti pranešimus kai įvyksta atitinkamas įvykis. Parinkite kokiu stiliumi vartotojams turėtų būti pranešama Bloko-stiliumi, Linijiniu-stiliumi ar abiem stiliais. Bloko-pranešimams šio modulio Pranešimų Nustatymų Blokas turi būti įgalintas.');

define ('_NOT_CONFIG_DISABLE', 'Atjungti pranešimus');
define ('_NOT_CONFIG_ENABLEBLOCK', 'Įgalinti tik Bloko-stilių');
define ('_NOT_CONFIG_ENABLEINLINE', 'Įgalinti tik Linijinį-stilių');
define ('_NOT_CONFIG_ENABLEBOTH', 'Įgalinti pranešimus (abiem stiliais)');

// For notification about comment events
define ('_NOT_COMMENT_NOTIFY', 'Pridėtas komentaras');
define ('_NOT_COMMENT_NOTIFYCAP', 'Pranešti man kai šiam straipsniui bus parašytas naujas komentaras.');
define ('_NOT_COMMENT_NOTIFYDSC', 'Gauti pranešimus kai tik bus parašytas (ar patvirtintas) šio straipsnio naujas komentaras.');
define ('_NOT_COMMENT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} automatinis-pranešimas: Komentaras pridėtas į {X_ITEM_TYPE}');

define ('_NOT_COMMENTSUBMIT_NOTIFY', 'Komentaras pateiktas');
define ('_NOT_COMMENTSUBMIT_NOTIFYCAP', 'Pranešti man kai naujas komentaras bus pateiktas (laukiantis patvirtinimo) šiam straipsniui.');
define ('_NOT_COMMENTSUBMIT_NOTIFYDSC', 'Gauti pranešimą kai naujas komentaras yra pateiktas (laukiantis patvirtinimo) šiam straipsniui.');
define ('_NOT_COMMENTSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} automatinis-pranešimas: Komentaras pateiktas {X_ITEM_TYPE}');

// For notification bookmark feature
// (Not really notification, but easy to do with this addon)
define ('_NOT_BOOKMARK_NOTIFY', 'Žymeklis');
define ('_NOT_BOOKMARK_NOTIFYCAP', 'Pažymėti šį įrašą (be pranešimo).');
define ('_NOT_BOOKMARK_NOTIFYDSC', 'Prižiūrėti šį įrašą negaunant jokių pranešimų.');

// For user profile
// FIXME: These should be reworded a little...
define ('_NOT_NOTIFYMETHOD', 'Pranešimų būdas');
define ('_NOT_METHOD_EMAIL', 'El. pašto adresas (naudoti adresą kuris yra mano profilyje)');
define ('_NOT_METHOD_PM', 'Asmeninė žinutė');
define ('_NOT_METHOD_DISABLE', 'Laikinai išjungti');

define ('_NOT_NOTIFYMODE', 'Parinktas pranešimų būdas');
define ('_NOT_MODE_SENDALWAYS', 'Pranešti man apie visus pažymėtus atnaujinimus');
define ('_NOT_MODE_SENDONCE', 'Pranešti man tik kartą');
define ('_NOT_MODE_SENDONCEPERLOGIN', 'Pranešti man vieną kartą po to atjungti kol aš vėl neprisijungsiu.');

define("_NOT_NOTIFYMETHOD_DESC", "Kai jūs prižiūrite pavyzdžiui forumą, kaip jūs norėtumėte gauti pranešimus?");

/*
define('ZAR_NOTIFICATION_MODE_SENDALWAYS', 0);
define('ZAR_NOTIFICATION_MODE_SENDONCETHENDELETE', 1);
define('ZAR_NOTIFICATION_MODE_SENDONCETHENWAIT', 2);
define('ZAR_NOTIFICATION_MODE_WAITFORLOGIN', 3);

define('ZAR_NOTIFICATION_METHOD_DISABLE', 0);
define('ZAR_NOTIFICATION_METHOD_PM', 1);
define('ZAR_NOTIFICATION_METHOD_EMAIL', 2);

define('ZAR_NOTIFICATION_DISABLE', 0);
define('ZAR_NOTIFICATION_ENABLEBLOCK', 1);
define('ZAR_NOTIFICATION_ENABLEINLINE', 2);
define('ZAR_NOTIFICATION_ENABLEBOTH', 3);
*/
?>
