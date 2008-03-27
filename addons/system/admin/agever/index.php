<?php
// $Id: index.php,v 1.3 2007/04/21 09:41:52 catzwolf Exp $
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

$_handler = &zarilia_gethandler( 'age' );
$do_callback = &ZariliaCallback::getSingleton();
$do_callback->setCallback( @$_callback );
switch ( $op ) {
    case 'maintenace':
    case 'help':
    case 'about':
    case 'edit':
    case 'delete':
    case 'save':
        $do_callback->setmenu( 2 );
        call_user_func( array( $do_callback, $op ) );
        break;

    case 'deleteall':
        $do_callback->setmenu( 2 );
        $do_callback->cdall( $op );
        break;

    case 'list':
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'age_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
        /**
         */
        $mod_id = zarilia_cleanRequestVars( $_REQUEST, 'mod_id', 0 );
        $pulldate = zarilia_cleanRequestVars( $_REQUEST, 'date1', 0 );
        $pulldate = ( is_string( $pulldate ) ) ? strtotime( $pulldate ) : $pulldate;
        /**
         */
        $addon_list = &call_user_func( array( $do_callback, 'getAddon' ) );
        $addon_list[0] = _MA_AD_ALLMODS;
        ksort( $addon_list );
        /**
         */
        zarilia_cp_header();
        $extra = "style=\"width: 90%;\" onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list&amp;mod_id='+this.options[this.selectedIndex].value\"";
        $content = '<form action="' . $addonversion['adminpath'] . '" method="get" >';
        $content .= "<div class='sidetitle'>" . _MA_AD_SELECT_ADDON . "</div>";
        $content .= "<div class='sidecontent'>" . zarilia_getSelection( $addon_list, $mod_id, 'mod_id', 1, 0 , false, "", $extra, 0, false ) . "</div>";
        $content .= "<div class='sidetitle'>" . _MA_AD_SELECT_DATE . "</div>";
        $content .= "<div class='sidecontent'>" . showHtmlCalendar( false, $pulldate ) . "</div>";
        $content .= '<input align="right" type="submit" class="formbutton" value="' . _GO . '" name="selsubmit" /><input type="hidden" value="list" name="op" /><input type="hidden" value="' . $fct . '" name="fct" /></form>';
        $content .= "<br /><div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div><div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";
        /**
         */
        zarilia_admin_menu( _MD_AD_FILTER_BOX, $content );
        $menu_handler->render( 1 );
        $tlist = new ZariliaTList();
        $tlist->AddFormStart( 'post', $addonversion['adminpath'] . '&amp;op=' . $op, 'agever' );
        $tlist->AddHeader( 'age_id', '5%', 'center', true );
        $tlist->AddHeader( 'age_uid', '15%', 'left', true );
        $tlist->AddHeader( 'age_ip', '', 'center', true );
        $tlist->AddHeader( 'age_date', '', 'center', true );
        $tlist->AddHeader( 'age_agreed', '', 'center', false );
        $tlist->AddHeader( 'age_gdate', '', 'center', false );
        $tlist->AddHeader( 'age_mid', '', 'center', false );
        $tlist->AddHeader( 'age_itemid', '5%', 'center', false );
        $tlist->AddHeader( 'age_dtitle', '', 'center', false );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'action', '', 'center', false );
        $tlist->addFooter( call_user_func( array( $do_callback, 'setSubmit' ), $fct, 'fct', array( 'deleteall' => 'Delete Selected' ) ) );
        $tlist->setPath( 'op=' . $op );
        $button = array( 'edit', 'delete' );
        $_age_obj = &call_user_func( array( $do_callback, 'getAgeObj' ), $nav, $mod_id, $pulldate );
        foreach ( $_age_obj['list'] as $obj ) {
            $age_id = $obj->getVar( 'age_id' );
            $tlist->addHidden( $age_id, 'value_id' );
            $tlist->add(
                array( $age_id,
                    $obj->getLinkedUserName( 1 ),
                    $obj->getVar( 'age_ip' ),
                    $obj->getVar( 'age_date' ),
                    $obj->getAgreed(),
                    $obj->getVar( 'age_gdate' ),
                    $addon_list[$obj->getMid()],
                    $obj->getVar( 'age_itemid' ),
                    $obj->getVar( 'age_dtitle' ),
                    $obj->getCheckbox( 'age_id' ),
                    zarilia_cp_icons( $button, 'age_id', $age_id )
                    ) );
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( @$_age_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op );
        break;
    /**
     */
    case 'index':
    default:
        zarilia_cp_header();
        zarilia_admin_menu( _MD_AD_MAINTENANCE_BOX, zariliaMainAction() );
        $menu_handler->render( 0 );
        break;
} // switch
zarilia_cp_footer();

?>