<?php

	//loading Zarilia Configuration
	require_once 'C:/xampp/htdocs/skycommunity/mainfile.php';

// Application Settings
$Configuration['SETUP_TEST'] = '1';
$Configuration['APPLICATION_PATH'] = 'C:\\xampp\\htdocs\\skycommunity/addons/vanilla/core/';
$Configuration['DATABASE_PATH'] = $Configuration['APPLICATION_PATH'].'conf/database.php';
$Configuration['LIBRARY_PATH'] = $Configuration['APPLICATION_PATH'].'library/';
$Configuration['EXTENSIONS_PATH'] = $Configuration['APPLICATION_PATH'].'extensions/';
$Configuration['LANGUAGES_PATH'] = $Configuration['APPLICATION_PATH'].'languages/';
$Configuration['THEME_PATH'] = $Configuration['APPLICATION_PATH'].'themes/vanilla/';
$Configuration['DEFAULT_STYLE'] = ZAR_URL.'/addons/vanilla/core/themes/vanilla/styles/default/';
$Configuration['WEB_ROOT'] = '/addons/vanilla/core/';
$Configuration['BASE_URL'] = ZAR_URL.'/addons/vanilla/core/';
$Configuration['FORWARD_VALIDATED_USER_URL'] = ZAR_URL.'/addons/vanilla/core/';
$Configuration['COOKIE_PATH'] = '';
$Configuration['SETUP_COMPLETE'] = '1';

$Configuration['AUTHENTICATION_MODULE'] = 'People/People.Class.ZariliaAuthenticator.php';
//$Configuration['USER_MODULE'] = 'People/People.Class.ZariliaUser.php';
	?>