<?php
// $Id: admin_header.php,v 1.2 2007/04/21 09:40:17 catzwolf Exp $
include_once '../../../include/cp_header.php';
require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
require_once ZAR_ROOT_PATH.'/addons/advertisement/include/functions.php';
/*
*/
$menu_handler = &zarilia_gethandler( 'addonmenu' );
$client_handler = &zarilia_getaddonhandler( 'clients', 'advertisement', false );
$banners_handler = &zarilia_getaddonhandler( 'banners', 'advertisement', false );
$banneradds_handler = &zarilia_getaddonhandler( 'banneradds', 'advertisement', false );
/*
*
*/
$menu_handler->addMenuTop( "index.php", _MD_AM_ADMININDEX );
$menu_handler->addMenuTop( "../index.php", "Addons Home" );
$menu_handler->addMenuTop( "about.php", "About" );
/*
*
*/
$menu_handler->addMenuTabs( "index.php", "Index" );
$menu_handler->addMenuTabs( "client.php", "Client" );
$menu_handler->addMenuTabs( "banners.php", "Banners" );
$menu_handler->addMenuTabs( "banneradds.php", "Banner Adds" );
$menu_handler->addMenuTabs( "billing.php", "Billing" );
/*
* Add Header
*/
$menu_handler->addHeader( "Advertisement Manager" );

$_PHP_SELF = zarilia_getenv( 'PHP_SELF' );
?>
