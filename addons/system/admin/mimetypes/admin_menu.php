<?php
$menu_handler = &zarilia_gethandler( 'addonmenu' );
/*
*/
$menu_handler->addMenuTop( $addonversion['adminpath'], _MD_AM_ADMININDEX );
$menu_handler->addMenuTop( $addonversion['adminpath']."&amp;op=help", "Help" );
/*
*/
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=index", "Index" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=list", "List Mimetypes" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=create", "Create Mimetypes" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=find", "Find Mimetype" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=permissions", "Set Permissions" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=about", "About" );
/*
* Add Header
*/
$menu_handler->addHeader( $addonversion['name'] );
?>