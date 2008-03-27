<?php
include ZAR_ROOT_PATH . '/kernel/objectcallback.php';
$menu_handler = &zarilia_gethandler( 'addonmenu' );
$menu_handler->addMenuTop( $addonversion['adminpath'], _MD_AM_ADMININDEX );
$menu_handler->addMenuTop( $addonversion['adminpath']."&amp;op=help", "Help" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=index", "Index" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=list", "List Errors" );
$menu_handler->addMenuTabs( $addonversion['adminpath']."&amp;op=about", "About" );
$menu_handler->addHeader( $addonversion['name'] );
?>