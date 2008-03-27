<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:42 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
if ( !is_object( $zariliaUser ) || !is_object( $zariliaAddon ) || !$zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) ) {
    exit( "Access Denied" );
}
/**
 */
require_once "admin_menu.php";
$maintenance = array( $addonversion['adminpath'] . "&amp;op=maintenace&amp;act=optimize" => _MD_AD_OPTIMIZE, $addonversion['adminpath'] . "&amp;op=maintenace&amp;act=analyze" => _MD_AD_ANALYZE, $addonversion['adminpath'] . "&amp;op=maintenace&amp;act=repair" => _MD_AD_REPAIR, $addonversion['adminpath'] . "&amp;op=maintenace&amp;act=truncate" => _MD_AD_CLEARENTRIES );
$security_handler = &zarilia_gethandler( 'tokens' );
switch ( $op ) {
    case 'maintenace':
        $act = zarilia_cleanRequestVars( $_REQUEST, 'act', '', XOBJ_DTYPE_TXTBOX );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        switch ( $ok ) {
            case 0:
            default:
                zarilia_cp_header();
                $menu_handler->render( 1 );
                zarilia_confirm(
                    array( 'op' => 'maintenace',
                        'act' => $act,
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_AD_DOTABLE, $act )
                    );
                break;
            case 1:
                $act = zarilia_cleanRequestVars( $_REQUEST, 'act', '', XOBJ_DTYPE_TXTBOX );
                if ( false == $security_handler->doDatabase( $act ) ) {
                    zarilia_cp_header();
                    $menu_handler->render( 0 );
                    $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                }
                redirect_header( $addonversion['adminpath'], 1, sprintf( _MD_AD_DOTABLEFINSHED, $act ) );
                break;
        } // switch
        break;

    case 'help':
        zarilia_cp_header();
        $menu_handler->render( 0 );
        if ( file_exists( ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php" ) ) {
            @include ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php";
        }
        break;

    case 'about':
        zarilia_cp_header();
        $menu_handler->render( 3 );

        require_once( ZAR_ROOT_PATH . "/class/class.about.php" );
        $zarilia_about = new ZariliaAbout();
        $zarilia_about->display();
        break;

    case 'save':
        $security_id = zarilia_cleanRequestVars( $_REQUEST, 'security_id', 0 );
        $_security_obj = ( $security_id > 0 ) ? $security_handler->get( $security_id ) : $security_handler->create();
        /**
         */
        $_security_obj->setVars( $_REQUEST );
        /**
         */
        if ( !$security_handler->insert( $_security_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_security_obj->getVar( 'security_title' ) ) );
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, ( $_security_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;

    case 'delete':
        $security_id = zarilia_cleanRequestVars( $_REQUEST, 'security_id', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $_security_obj = $security_handler->get( $security_id );
        if ( !is_object( $_security_obj ) ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }

        switch ( $ok ) {
            case 0:
            default:
                zarilia_cp_header();
                $menu_handler->render( 1 );
                zarilia_confirm(
                    array( 'op' => 'delete',
                        'security_id' => $_security_obj->getVar( 'security_id' ),
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_AM_WAYSYWTDTR, $_security_obj->getVar( 'security_title' ) )
                    );
                break;
            case 1:
                if ( !$security_handler->delete( $_security_obj ) ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                                $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
                }
                break;
        } // switch
        break;

    case 'deleteall':
        $menus_handler = &zarilia_gethandler( 'menus' );
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        $menu_mid = 1;
        $menu_name = 'category';
        foreach ( array_keys( $checkbox ) as $security_id ) {
            $_security_obj = $security_handler->get( $security_id );
            if ( !$security_handler->delete( $_security_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_security_obj->getVar( 'security_title' ) ) );
            }
            // $menus_handler->deleteMenuItem( $menu_mid, $menu_name, $security_id );
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
        } else {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case 'list':
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        /*
        * required for Navigation
        */
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'security_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DSC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
        $nav['security_id'] = zarilia_cleanRequestVars( $_REQUEST, 'security_id', 0 );

/*
        $url = ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list";
        $form = "
		 <div class='sidetitle'>" . _MD_AD_DISPLAY_SECTION . "</div>
		 <div class='sidecontent'>" . zarilia_getSelection( $sections_array, $nav['section_id'], "section_id", 1, 0, false, false, "onchange=\"location='" . $url . "&amp;limit=" . $nav['limit'] . "&amp;security_display=" . $nav['security_display'] . "&amp;section_id='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAY_BOX . "</div>
		 <div class='sidecontent'>" . zarilia_getSelection( $display_array, $nav['security_display'], "security_display", 1, 0, false, false, "onchange=\"location='" . $url . "&amp;section_id=" . $nav['section_id'] . "&amp;limit=" . $nav['limit'] . "&amp;security_display='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div>
		 <div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . $url . "&amp;section_id=" . $nav['section_id'] . "&amp;security_display=" . $nav['security_display'] . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";
*/
        zarilia_cp_header();
        $menu_handler->render( 1 );
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, $op_url//,
            //_MD_AD_FILTER_BOX, $form
            );

        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'security_id', '5', 'center', false );
        $tlist->AddHeader( 'security_title', '15%', 'left', true );
        $tlist->AddHeader( 'security_login', '', 'center', true );
        $tlist->AddHeader( 'security_ip', '', 'center', true );
        $tlist->AddHeader( 'security_date', '', 'center', 1 );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'], 'category' );
        $tlist->addFooter( $security_handler->setSubmit( $fct ) );
        $tlist->setPath( 'op=' . $op );

        $button = array( 'edit', 'delete', 'clone' );
        $_security_obj = $security_handler->getSecurityObj( $nav );
		if ( $_security_obj['count'] ) {
            foreach ( $_security_obj['list'] as $obj ) {
                $security_id = $obj->getVar( 'security_id' );
                // This line is required to make the boxes work correctly//
                $tlist->addHidden( $security_id, 'value_id' );
                $tlist->add(
                    array( $security_id,
                        $obj->getTextbox( 'security_id', 'security_title', '50' ),
                        $obj->getVar( 'security_login' ),
                        $obj->getVar( 'security_ip' ),
                        $obj->getVar('security_date'),
                        $obj->getCheckbox( 'security_id' ),
                        zarilia_cp_icons( $button, 'security_id', $security_id )
                        ) );
            }
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_security_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op . '&amp;limit=' . $nav['limit'] );
        break;

    case 'index':
    case 'default':
        zarilia_cp_header();
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, $op_url,
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $menu_handler->render( 0 );
        break;
} // switch
zarilia_cp_footer();

?>