<?php
include ZAR_ROOT_PATH . '/kernel/objectcallback.php';
$menu_handler = &zarilia_gethandler( 'addonmenu' );
$menu_handler->addMenuTop( $addonversion['adminpath'], _MD_AM_ADMININDEX );
$menu_handler->addMenuTop( $addonversion['adminpath'] . '&amp;op=help', 'Help' );
$menu_handler->addMenuTabs( $addonversion['adminpath'] . '&amp;op=index', 'Index' );
$menu_handler->addMenuTabs( $addonversion['adminpath'] . '&amp;op=list', 'List Groups' );
$menu_handler->addMenuTabs( $addonversion['adminpath'] . '&amp;op=create', 'Create Group' );
$menu_handler->addMenuTabs( $addonversion['adminpath'] . '&amp;op=about', 'About' );
$menu_handler->addHeader( $addonversion['name'] );

$zariliaOption['non_delete_groups'] = array( ZAR_GROUP_ADMIN, ZAR_GROUP_USERS, ZAR_GROUP_ANONYMOUS, ZAR_GROUP_MODERATORS, ZAR_GROUP_SUBMITTERS, ZAR_GROUP_SUBSCRIPTION, ZAR_GROUP_BANNED );

?>