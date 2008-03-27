<?php 
// $Id: admin_header.php,v 1.2 2007/04/21 09:41:11 catzwolf Exp $
include_once '../../../include/cp_header.php';
require_once ZAR_ROOT_PATH . '/class/class.tlist.php';

$menu_handler = &zarilia_gethandler( 'addonmenu' );
$pm_handler = &zarilia_getaddonhandler( 'message', 'messages', false );
$buddy_handler = &zarilia_getaddonhandler( 'buddy', 'messages', false );
$msgsent_handler = &zarilia_getaddonhandler( 'messagesent', 'messages', false );
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
$menu_handler->addMenuTabs( "prune.php", "Prune" );
/*
* Add Header
*/ 
$menu_handler->addHeader( "Personal Messenger" );
?>
