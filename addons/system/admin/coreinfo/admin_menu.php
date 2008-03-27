<?php
$menu_handler = &zarilia_gethandler( 'addonmenu' );
/*
*/
$menu_handler->addMenuTop( $addonversion['adminpath'], _MD_AM_ADMININDEX );
$menu_handler->addMenuTop( $addonversion['adminpath']."&amp;op=help", "Help" );
/*
*/
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=php", "Php Information" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=zarilia", "System Information" );
/*
* Add Header
*/
$menu_handler->addHeader( $addonversion['name'] );
?>