<?php
// $Id: index.php,v 1.3 2007/05/05 11:10:17 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
if ( !is_object( $zariliaUser ) || !is_object( $zariliaAddon ) || !$zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) ) {
    exit( "Access Denied" );
}

require_once "admin_menu.php";
switch ( $op ) {
    case 'help':
        zarilia_cp_header();
        $menu_handler->render( 0 );
        @include ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php";
        break;

    case 'about':
        zarilia_cp_header();
        $menu_handler->render( 2 );
        require_once( ZAR_ROOT_PATH . "/class/class.about.php" );
        $zarilia_about = new ZariliaAbout();
        $zarilia_about->display();
        break;

    case 'list':
    case 'onlineusers':
        zarilia_cp_header();
        $menu_handler->render( 2 );
		$GLOBALS['zariliaLogger']->sysRender( E_USER_WARNING, _AM_US_EVENTNOTFOUND );
        break;

    case 'index':
    default:
        zarilia_cp_header();
        $menu_handler->render( 0 );
        $image_url = ZAR_THEME_URL . "/" . $zariliaConfig['theme_set'] . "/images/";
        echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
			    <td width="60%" class="CPindexOptions">
				<div class="cpicon">
					<a href="' . ZAR_URL . '/addons/system/index.php?fct=avatars"><br /><img src="' . ZAR_URL . '/addons/system/images/system/avatar_admin.png" hspace="10" vspace="100"/>
					<br /><span>' . _MD_AM_AVATARS . '</span></a>
				</div>
				<div class="cpicon">
					<a href="' . ZAR_URL . '/addons/system/index.php?fct=avatars"><br /><img src="' . ZAR_URL . '/addons/system/images/system/avatar_admin.png" hspace="10" vspace="100"/>
					<br /><span>' . _MD_AM_AVATARS . '</span></a>
				</div>
				</td>
			    <td width="20">&nbsp;</td>
			    <td width="48%" class="CPindexOptions"></td>
			  </tr>
			</table>';
        break;
} // switch
zarilia_cp_footer();

function showUsersData() {
    echo "User Data";
}

?>
