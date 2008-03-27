<?php
$menu_handler = &zarilia_gethandler( 'addonmenu' );
/*
*/
$menu_handler->addMenuTop( $addonversion['adminpath'], _MD_AM_ADMININDEX );
$menu_handler->addMenuTop( ZAR_URL.'/index.php?page_type=stream', "Home Page" );
$menu_handler->addMenuTop( $addonversion['adminpath']."&amp;op=help", "Help" );

/*
*/
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=index", "Index" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=list", "List Streams" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=create", "Create Stream" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=heading", "Create Heading" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=about", "About" );
/*
* Add Header
*/
$menu_handler->addHeader( $addonversion['name'] );
?>