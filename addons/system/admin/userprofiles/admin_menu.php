<?php
$menu_handler = &zarilia_gethandler( 'addonmenu' );
/*
*/
$menu_handler->addMenuTop( $addonversion['adminpath'], _MD_AM_ADMININDEX );
$menu_handler->addMenuTop( $addonversion['adminpath']."&amp;op=help", "Help" );
/*
*/
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=index", "Index" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=list_category", "List Category" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=edit_category", "Create Category" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=list_profile", "List Profiles" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=edit_profile", "Create Profile" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=about", "About" );
/*
* Add Header
*/
$menu_handler->addHeader( $addonversion['name'] );
?>