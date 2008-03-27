<?php
// $Id: admin_menu.php,v 1.2 2007/04/21 09:42:38 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//

$menu_handler = &zarilia_gethandler( 'addonmenu' );
/*
*/
$menu_handler->addMenuTop( $addonversion['adminpath'], _MD_AM_ADMININDEX );
$menu_handler->addMenuTop( $addonversion['adminpath']."&amp;op=help", "Help" );
//$menu_handler->addMenuTop( "../index.php", "Addons Home" );
//$menu_handler->addMenuTop( "about.php", $addonversion['credits'] );
/*
*/
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=index", "Index" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=list", "List RSS Feeds" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=edit", "Create RSS Feed" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=about", "About" );
/*
* Add Header
*/
$menu_handler->addHeader( $addonversion['name'] );
?>