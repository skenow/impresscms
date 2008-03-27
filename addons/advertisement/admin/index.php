<?php
include 'admin_header.php';
$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'default' );
switch ( $op ) {
    case 'default':
    default:
        zarilia_cp_header();
        $menu_handler->render( 0 );
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array(
			"client.php?op=edit" => _MA_AD_ECLIENT_CREATE,
			"banners.php?op=edit" => _MA_AD_EBANNER_CREATE,
			"banneradds.php?op=edit" => _MA_AD_EBANNERADS_CREATE,
			)
		);
		break;
} // switch
zarilia_cp_footer();

?>
