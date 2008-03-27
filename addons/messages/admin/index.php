<?php
include 'admin_header.php';
$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'defualt' );
switch ( $op ) {
    case 'defualt':
    default:
        zarilia_cp_header();
        $menu_handler->render( 0 );
        $_numbers = $pm_handler->getMessageNums();
        echo _MA_PM_MSG_TOTAL . $_numbers['msg_total'] . "<br />";
        echo _MA_PM_MSG_TRASHTOTAL . $_numbers['is_trash_total'] . "<br />";
        echo _MA_PM_MSG_POSTED . formatTimestamp($_numbers['posted']) . "<br />";
        break;
} // switch
zarilia_cp_footer();

?>
