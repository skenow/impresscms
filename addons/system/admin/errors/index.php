<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:17 catzwolf Exp $
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

$_callback = &zarilia_gethandler( 'errors' );
$do_callback = ZariliaCallback::getSingleton();
$do_callback->setCallback( $_callback );
switch ( $op ) {
    case 'maintenace':
    case 'help':
    case 'about':
    case 'edit':
    case 'delete':
        $do_callback->setmenu( 2 );
        call_user_func( array( $do_callback, $op ) );
        break;
    case 'deleteall':
        $do_callback->setmenu( 2 );
        $do_callback->cdall( $op );
        break;
    case 'list':
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        $errors_id = zarilia_cleanRequestVars( $_REQUEST, 'errors_id', 0 );
        $type = zarilia_cleanRequestVars( $_REQUEST, 'type', 0 );
        /*
        * required for Navigation
        */
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'errors_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );

        $pulldate = zarilia_cleanRequestVars( $_REQUEST, 'date1', 0 );
        $pulldate = ( is_string( $pulldate ) ) ? strtotime( $pulldate ) : $pulldate;

        zarilia_cp_header();
        $content = '<form action="' . $addonversion['adminpath'] . '" method="get" >';
        $content .= "<div class='sidetitle'>" . _MA_AD_SELECT_DATE . "</div>";
        $content .= "<div class='sidecontent'>" . showHtmlCalendar( false, $pulldate ) . "</div>";
        $content .= '<input align="right" type="submit" class="formbutton" value="' . _GO . '" name="selsubmit" />
			<input type="hidden" value="list" name="op" />
			<input type="hidden" value="' . $fct . '" name="fct" />
		</form>';
        $content .= "<br /><div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div><div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";

        $menu_handler->render( 1 );
        zarilia_admin_menu( _MD_AD_FILTER_BOX, $content );
        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'errors_id', '5', 'center', false );
        $tlist->AddHeader( 'errors_no', '', 'center', true );
        $tlist->AddHeader( 'errors_ip', '', 'center', true );
        $tlist->AddHeader( 'errors_date', '', 'center', true );
        $tlist->AddHeader( 'errors_report', '15%', 'left', 1 );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'], 'errors' );
        $tlist->addFooter( call_user_func( array( $_callback, 'setSubmit' ), $fct ) );
        $tlist->setPath( 'op=' . $op );

        $button = array( 'edit', 'delete' );
        $_this_obj = call_user_func( array( $_callback, 'getErrorsObj' ), $nav, $pulldate );
        foreach ( $_this_obj['list'] as $obj ) {
            $errors_id = $obj->getVar( 'errors_id' );
            $tlist->add(
                array( $errors_id,
                    $obj->getVar( 'errors_no' ),
                    $obj->getVar( 'errors_ip' ),
                    $obj->getVar( 'errors_date' ),
                    $obj->getReports(),
                    $obj->getCheckbox( 'errors_id' ),
                    zarilia_cp_icons( $button, 'errors_id', $errors_id )
                    ) );
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_this_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op . '&amp;limit=' . $nav['limit'] );
        break;

    case 'index':
    case 'default':
        zarilia_cp_header();
        zarilia_admin_menu(
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $menu_handler->render( 0 );
        break;
} // switch
zarilia_cp_footer();

?>