<?php

// Warning: Some of the constant values here contain the sprintf format code "%s".  That format code must not be removed.

// header text for initial page
$bchk['LANG_HEADER_INITIAL_PAGE'] = "
	<h3>Les caractéristiques suivantes du navigateur vont être testées :</h3>
";

// footer text for initial page
$bchk['LANG_FOOTER_INITIAL_PAGE'] = "
";

// header text for test results page
$bchk['LANG_HEADER_RESULTS_PAGE'] = "
	<h3>Résultats des tests de votre navigateur :</h3>
";

// footer text for test results page
$bchk['LANG_FOOTER_RESULTS_PAGE'] = "
	<!-- exemple
	<p>Si l'un de ces tests échoue, sa référence peut être utile pour corriger le problème :
	<a href='http://example.com/' target='_blank'>Exemple</a>
	</p>
	-->
";

// text for link
$bchk['LANG_CLICK_HERE1'] = 'Cliquez';
$bchk['LANG_CLICK_HERE2'] = 'ICI';
$bchk['LANG_CLICK_HERE3'] = 'pour lancer les tests.';

$bchk['LANG_SELECT_LANGUAGE'] = 'Choisissez la langue';

// text for link
$bchk['LANG_CLICK_HERE4'] = 'Cliquez';
$bchk['LANG_CLICK_HERE5'] = 'ICI';
$bchk['LANG_CLICK_HERE6'] = 'pour relancer les tests.';

$bchk['LANG_DO_NOT_RELOAD'] = "
	Veuillez ne <em>pas</em> utiliser les boutons de retour vers la page précédente, de rafraîchissement ou de rechargement de la page car ils peuvent produire des résultats incorrects.
";

// errors
$bchk['LANG_ERROR_MISSING_POST_VALUE'] = "Erreur interne : valeur POST manquante '%s'";
$bchk['LANG_ERROR_INTERNAL']           = 'Erreur interne : %s';

// test results column headers
$bchk['LANG_FEATURE']     = 'Fonctionnalités';
$bchk['LANG_DESCRIPTION'] = 'Description';
$bchk['LANG_TEST_RESULT'] = 'Résultats';

// test names and descriptions
$bchk['LANG_COOKIES']                     = 'Cookies';
$bchk['LANG_COOKIES_DESC']                = 'Les cookies peuvent être écrits et lus. (via les entêtes HTTP)';
$bchk['LANG_REFERRER_H']                  = 'Referrer-H';
$bchk['LANG_REFERRER_H_DESC']             = 'L\'adresse de la page de provenance peut être lue. (nom de l\'hôte)';
$bchk['LANG_REFERRER_HS']                 = 'Referrer-HS';
$bchk['LANG_REFERRER_HS_DESC']            = 'L\'adresse de la page de provenance peut être lue. (nom de l\'hôte + nom du script)';
$bchk['LANG_REFERRER_HSQ']                = 'Referrer-HSQ';
$bchk['LANG_REFERRER_HSQ_DESC']           = 'L\'adresse de la page de provenance peut être lue. (nom de l\'hôte + nom du script + chaîne de requête)';
$bchk['LANG_JAVASCRIPT']                  = 'Javascript';
$bchk['LANG_JAVASCRIPT_DESC']             = 'Le code Javascript peut être exécuté dans la page.';
$bchk['LANG_JAVASCRIPT_READ_COOKIE']      = 'Lecture des cookies dans Javascript';
$bchk['LANG_JAVASCRIPT_READ_COOKIE_DESC'] = 'Les cookies peuvent être lus depuis Javascript.';
$bchk['LANG_JAVASCRIPT_SET_COOKIE']       = 'Création des cookies dans Javascript';
$bchk['LANG_JAVASCRIPT_SET_COOKIE_DESC']  = 'Les cookies peuvent être écris depuis Javascript.';
$bchk['LANG_CLOCK']                       = 'Horloge';
$bchk['LANG_CLOCK_DESC']                  = 'Le serveur et le client sont d\'accord sur l\'heure de %s minutes. (utilisation de Javascript)';

$bchk['LANG_NO_TESTS'] = 'Aucun test effectué';

// test results
$bchk['LANG_PASS'] = 'Réussi';
$bchk['LANG_FAIL'] = 'Echec';

// clock test details
$bchk['LANG_SECONDS']                = 'secondes';
$bchk['LANG_MINUTES']                = 'minutes';
$bchk['LANG_HOURS']                  = 'heures';
$bchk['LANG_DAYS']                   = 'jours';
$bchk['LANG_SERVER_CLOCK']           = 'Heure du serveur';
$bchk['LANG_CLIENT_CLOCK']           = 'Heure du client';
$bchk['LANG_DIFFERENCE']             = 'Différence';
$bchk['LANG_SIMULATING_CLOCK_ERROR'] = 'Simulation de l\'horloge du client, erreur de %s secondes.';
$bchk['LANG_NOTE_INTERNET_LAG']      = 'Notez que même si les horloges sont réglées avec précision, des différences entre les horloges sont normales, cela est dûe au décalage d\'internet.';

$bchk['LANG_'] = '';

?>