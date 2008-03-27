<?php
// $Id: admin_menu.php,v 1.3 2007/05/05 11:10:27 catzwolf Exp $
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
$menu_handler->addMenuTop( $addonversion['adminpath'], _MD_AM_ADMININDEX );
$menu_handler->addMenuTop( $addonversion['adminpath'].'&amp;op=help', _MD_AM_ADMINHELP);
$menu_handler->addMenuTabs( $addonversion['adminpath'].'&amp;op=index', _MD_AM_ADMININDEX );
$menu_handler->addMenuTabs( $addonversion['adminpath'].'&amp;op=list', _MD_AM_ADMINLIST );
$menu_handler->addMenuTabs( $addonversion['adminpath'].'&amp;op=create', _MD_AM_ADMINCREATE );
$menu_handler->addMenuTabs( $addonversion['adminpath'].'&amp;op=about', _MD_AM_ADMINABOUT);
$menu_handler->addHeader( $addonversion['name'] );
?>