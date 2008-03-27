<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:23 catzwolf Exp $
// ------------------------------------------------------------------------ //
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
/*We need to do this more global then all core addons can use it*/
if ( isset( $_REQUEST['opt'] ) ) {
    $opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0, XOBJ_DTYPE_INT );
    setcookie( 'selected_events_tab', $opt );
} else {
    $opt = zarilia_cleanRequestVars( $_COOKIE, 'selected_events_tab', 0, XOBJ_DTYPE_INT );
}

$_callback = &zarilia_gethandler( 'events' );
$do_callback = ZariliaCallback::getSingleton();
$do_callback->setCallback( $_callback );
switch ( $op ) {
    case 'delete':
    case 'maintenace':
    case 'help':
    case 'about':
    case 'edit':
    case 'cloned':
    case "optimize":
        $do_callback->setmenu( 2 );
        call_user_func( array( $do_callback, $op ) );
		redirect_header( $addonversion['adminpath'] . "&amp;op=list", 1, constant('_MD_AM_EVENT'.strtoupper($op)));
        break;

    case 'truncate':
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        switch ( $ok ) {
            case 0:
            default:
                zarilia_cp_header();
                $menu_handler->render( 1 );
                zarilia_confirm(
                    array( 'op' => 'truncate', 'ok' => 1 ),
                    $addonversion['adminpath'], _MD_AM_WAYSYWTDTR
                    );
                break;
            case 1:
                if ( !$event_handler->truncate() ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                    $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBTRUNCATED );
                }
                break;
        } // switch
        break;

    case 'save':
        $id = zarilia_cleanRequestVars( $_REQUEST, 'ID', 0 );
		$event_handler = &$_callback;
        $_obj = ( $id > 0 ) ? $event_handler->get( $id ) : $event_handler->create();
        $_obj->setVars( $_REQUEST );
        $_obj->setDate();
        if ( $event_handler->insert( $_obj, true ) ) {
            $redirect_mess = ( $_obj->isNew() ) ? _MD_AM_EVENTADDED : _DBUPDATED;
            redirect_header( $addonversion['adminpath'] . "&amp;op=list", 1, $redirect_mess );
        } else {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _MA_AD_ERRORSAVECLIENT );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        break;

    case 'list':
    default:
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 20 );

		$_obj = call_user_func( array( $_callback, 'getEventsObj' ), $nav, $opt );
        $display = zarilia_cleanRequestVars( $_REQUEST, 'display', 3 );
        $display_array = array( '3' => _MD_AD_SHOWALL_BOX, '0' => _MD_AD_SHOWHIDDEN_BOX, '1' => _MD_AD_SHOWVISIBLE_BOX );
        $list_array = array( 5 => "5", 10 => "10", 15 => "15", 25 => "25", 50 => "50" );

        $form = "<div style='padding-bottom: 5px;'>" . _MD_AD_DISPLAY_BOX . "</div><div style='padding-bottom: 5px;'>" . zarilia_getSelection( $display_array, $display, "display", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list&amp;limit=" . $nav['limit'] . "&amp;display='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";
        $form .= "<div style='padding-bottom: 5px;'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div><div style='padding-bottom: 5px;'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list&amp;display=" . $display . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";

        zarilia_cp_header();
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_CREATE ),
            _MD_AD_FILTER_BOX, $form
            );
        $menu_handler->render( 1 );

        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'id', '', 'center', true );
        $tlist->AddHeader( 'nexttime', '', 'left', true );
        $tlist->AddHeader( 'repeatnum', '', 'left', true );
        $tlist->AddHeader( 'repeatinterval', '', 'left', true );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $button = array( 'edit', 'delete', 'cloned' );
        foreach ( $_obj['list'] as $obj ) {
            $id = $obj->getVar( 'id' );
            $num = $obj->getVar( 'RepeatNum' );
            $tlist->add(
                array( $id,
                    date( "r", $obj->getVar( 'NextTime' ) ),
                    ( ( $num == 0 ) ? 'forever' : $num ),
                    $obj->getVar( 'RepeatInterval' ) . ' ' . $obj->getRepeatSystemAsText(),
                    zarilia_cp_icons( $button, 'ID', $id )
                    ) );
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_obj['count'], $nav['limit'], $nav['start'], 'start', 1, 'op=' . $op );
        break;

    /**
     */
    case 'index':
    case 'default':
        zarilia_cp_header();
        $menu_handler->render( 0 );
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_CREATE ),
            _MD_AD_MAINTENANCE_BOX, array( $addonversion['adminpath'] . "&amp;op=optimize" => _MD_AD_OPTIMIZE, $addonversion['adminpath'] . "&amp;op=truncate" => _MD_AD_CLEARENTRIES )
            );
        break;
}
zarilia_cp_footer();
exit();

function getTabs() {
    $array = array();
    $i = 1;
    while ( defined( "_MD_AM_EVENTTYPE_$i" ) )
    $array[constant( "_MD_AM_EVENTTYPE_" . ( $i++ ) )] = 'index.php?fct=events';
    return $array;
}

?>