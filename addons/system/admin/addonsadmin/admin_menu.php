<?php
$menu_handler = &zarilia_gethandler( 'addonmenu' );
/*
*/
$menu_handler->addMenuTop( $addonversion['adminpath'], _MD_AM_ADMININDEX );
$menu_handler->addMenuTop( $addonversion['adminpath']."&amp;op=help", "Help" );
/*
*/
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=index", "Index" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=list&amp;act=0", _MD_AM_ACTIVE );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=list&amp;act=1", _MD_AM_INACTIVE );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=list&amp;act=2", _MD_AM_NOTINSTALLED );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=uploadform", _MA_AD_UPLOAD );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=about", "About" );
/*
* Add Header
*/
$menu_handler->addHeader( $addonversion['name'] );
?>