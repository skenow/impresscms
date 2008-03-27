<?php
include 'admin_header.php';
$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'defualt' );
switch ( $op ) {
    case 'prune':
        zarilia_cp_header();
		$menu_handler->render( 1 );
        Echo "Pruning Messages";
		break;
    case 'defualt':
    default:
        zarilia_cp_header();
		$menu_handler->render( 0 );
        break;
} // switch

zarilia_cp_footer();

?>
