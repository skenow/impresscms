<?php
// $Id: zarilia_version.php,v 1.1 2007/03/16 02:34:23 catzwolf Exp $

$addonversion['name'] = _MI_ADVERTISEMENT;
$addonversion['version'] = 1.0;
$addonversion['description'] = _MI_ADVERTISEMENT_DESC;
$addonversion['author'] = "John Neill";
$addonversion['credits'] = "Zarilia";
$addonversion['license'] = "GPL see LICENSE";
$addonversion['official'] = 1;
$addonversion['dirname'] = "advertisement";

// Admin things
$addonversion['hasAdmin'] = 1;
$addonversion['adminindex'] = "admin/index.php";
$addonversion['adminmenu'] = "admin/menu.php";

/*
* Mysql file
*/ 
//$addonversion['sqlfile']['mysql'] = "sql/mysql.sql";

/*
* Mysql Tables
*/ 
$addonversion['tables'][0] = "messages";
$addonversion['tables'][1] = "messages_buddy";
$addonversion['tables'][2] = "messages_sent";

//install
//$addonversion['onInstall'] = 'include/install.php';
//update
//$addonversion['onUpdate'] = 'include/update.php';

// Templates
$addonversion['templates'][1]['file'] = 'ads_index.html';
$addonversion['templates'][1]['description'] = '';
// Menu
$addonversion['hasMain'] = 1;

// Blocks
?>