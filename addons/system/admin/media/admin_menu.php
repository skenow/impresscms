<?php
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
include ZAR_ROOT_PATH . '/kernel/objectcallback.php';
$menu_handler = &zarilia_gethandler( 'addonmenu' );
$menu_handler->addMenuTop( $addonversion['adminpath'], _MD_AM_ADMININDEX );
$menu_handler->addMenuTop( $addonversion['adminpath'] . "&amp;op=help", "Help" );
$menu_handler->addMenuTabs( $addonversion['adminpath'] . "&amp;op=media_list", "Index" );
$menu_handler->addMenuTabs( $addonversion['adminpath'] . "&amp;op=cat_list", "List Category" );
$menu_handler->addMenuTabs( $addonversion['adminpath'] . "&amp;op=cat_edit", "Create Category" );
$menu_handler->addMenuTabs( $addonversion['adminpath'] . "&amp;op=uploader", "Upload Media" );
$menu_handler->addMenuTabs( $addonversion['adminpath'] . "&amp;op=about", "About" );
$menu_handler->addHeader( $addonversion['name'] );

?>