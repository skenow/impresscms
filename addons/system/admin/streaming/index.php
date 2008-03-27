<?php
// $Id: index.php,v 1.4 2007/05/05 11:10:35 catzwolf Exp $
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
include_once ZAR_ROOT_PATH . "/class/class.permissions.php";

$perm_handler = &zarilia_gethandler( 'groupperm' );
$stream_handler = &zarilia_gethandler( 'streaming' );
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
                if ( false == $stream_handler->doDatabase( $act ) ) {
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
        $menu_handler->render( 4 );

        require_once( ZAR_ROOT_PATH . "/class/class.about.php" );
        $zarilia_about = new ZariliaAbout();
        $zarilia_about->display();
        break;

    case 'edit':
    case 'create':
        $streaming_id = zarilia_cleanRequestVars( $_REQUEST, 'streaming_id', 0 );
        $_streaming_obj = ( $streaming_id > 0 ) ? $stream_handler->get( $streaming_id ) : $stream_handler->create();
        if ( !$_streaming_obj ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _AM_US_SECTIONNOTFOUND );
            zarilia_cp_header();
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        /**
         */

        zarilia_cp_header();
        $menu_handler->render( 2 );
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_STREAMING_CREATE, $addonversion['adminpath'] . "&amp;op=list" => _MA_AD_STREAMING_LIST ),
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $form = $_streaming_obj->formEdit();
        break;

    case 'heading':
        $section_handler = &zarilia_gethandler( 'section' );
        $_section_obj = $section_handler->getSectionWhere( 'stream' );
        if ( !$_section_obj ) {
            $_section_obj = $section_handler->create();
        }
        if ( !$_section_obj ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _AM_US_SECTIONNOTFOUND );
            zarilia_cp_header();
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        /**
         */

        zarilia_cp_header();
        $menu_handler->render( 3 );
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_STREAMING_CREATE, $addonversion['adminpath'] . "&amp;op=list" => _MA_AD_STREAMING_LIST ),
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $form = $_section_obj->formEditHeading( 'stream', 'streaming' );
        break;

    case 'clone':
        $streaming_id = zarilia_cleanRequestVars( $_REQUEST, 'streaming_id', 0 );
        $_streaming_obj = $stream_handler->get( $streaming_id );
        $_streaming_obj->setVar( 'streaming_id', '' );
        $_streaming_obj->setVar( 'streaming_title', $_streaming_obj->getVar( 'streaming_title' ) . '_cloned' );
        $_streaming_obj->setVar( 'streaming_published', time() );
        $_streaming_obj->setNew();
        if ( !$stream_handler->insert( $_streaming_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_streaming_obj->getVar( 'streaming_title' ) ) );
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, ( $_streaming_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;

    case 'save':
        $streaming_id = zarilia_cleanRequestVars( $_REQUEST, 'streaming_id', 0 );
        $_streaming_obj = ( $streaming_id > 0 ) ? $stream_handler->get( $streaming_id ) : $stream_handler->create();
        /**
         */
        $stream_handler->setUpload( $_streaming_obj );

        if ( !$stream_handler->insert( $_streaming_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_streaming_obj->getVar( 'streaming_title' ) ) );
        } else {
            $streaming_id = $_streaming_obj->getVar( 'streaming_id' );
            $mod_id = $zariliaAddon->getVar( 'mid' );
            $perm_handler = &zarilia_gethandler( 'groupperm' );
            $read_array = zarilia_cleanRequestVars( $_REQUEST, 'readgroup', array(), XOBJ_DTYPE_ARRAY );
            $readgroup = new cpPermission( '', 'streaming_read', '', $mod_id );
            $readgroup->cpPermission_save( $read_array, $streaming_id );
        }

        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, ( $_streaming_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;

    case 'delete':
        $streaming_id = zarilia_cleanRequestVars( $_REQUEST, 'streaming_id', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $_streaming_obj = $stream_handler->get( $streaming_id );
        if ( !is_object( $_streaming_obj ) ) {
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
                        'streaming_id' => $_streaming_obj->getVar( 'streaming_id' ),
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_AM_WAYSYWTDTR, $_streaming_obj->getVar( 'streaming_title' ) )
                    );
                break;
            case 1:
                $menu_mid = 1;
                $menu_streamingid = $_streaming_obj->getVar( 'streaming_id' );
                $menu_name = 'streaming';
                if ( !$stream_handler->delete( $_streaming_obj ) ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                                $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    $menus_handler = &zarilia_gethandler( 'menus' );
                    $menus_handler->deleteMenuItem( $menu_mid, $menu_name, $menu_streamingid );
                    redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
                }
                break;
        } // switch
        break;

    case 'updateall':
        $value_id = zarilia_cleanRequestVars( $_REQUEST, 'value_id', array() );
        $streaming_title = zarilia_cleanRequestVars( $_REQUEST, 'streaming_title', array() );
        $streaming_weight = zarilia_cleanRequestVars( $_REQUEST, 'streaming_weight', array() );
        $streaming_display = zarilia_cleanRequestVars( $_REQUEST, 'streaming_display', array() );
        foreach ( $value_id as $id => $streaming_id ) {
            $_streaming_obj = $stream_handler->get( $streaming_id );
            if ( isset( $streaming_title[$id] ) ) {
                $_streaming_obj->setVar( 'streaming_title', $streaming_title[$id] );
            }
            if ( isset( $streaming_weight[$id] ) ) {
                $_streaming_obj->setVar( 'streaming_weight', $streaming_weight[$id] );
            }
            if ( isset( $streaming_display[$id] ) ) {
                $_streaming_obj->setVar( 'streaming_display', $streaming_display[$id] );
            }
            /**
             */
            if ( !$stream_handler->insert( $_streaming_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_streaming_obj->getVar( 'streaming_title' ) ) );
            }
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
        } else {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case 'cloneall':
        unset( $_REQUEST['streaming_weight'] );
        unset( $_REQUEST['streaming_display'] );
        unset( $_REQUEST['value_id'] );
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $streaming_id ) {
            $_streaming_obj = $stream_handler->get( $streaming_id );
            $_streaming_obj->setVar( 'streaming_id', '' );
            $_streaming_obj->setVar( 'streaming_title', $_streaming_obj->getVar( 'streaming_title' ) . '_cloned' );
            $_streaming_obj->setVar( 'streaming_published', time() );
            $_streaming_obj->setNew();
            if ( !$stream_handler->insert( $_streaming_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_streaming_obj->getVar( 'streaming_title' ) ) );
            }
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
        } else {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case 'deleteall':
        unset( $_REQUEST['streaming_weight'] );
        unset( $_REQUEST['streaming_display'] );
        unset( $_REQUEST['value_id'] );
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $streaming_id ) {
            $_streaming_obj = $stream_handler->get( $streaming_id );
            if ( !$stream_handler->delete( $_streaming_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_streaming_obj->getVar( 'streaming_title' ) ) );
            }
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
        $type = zarilia_cleanRequestVars( $_REQUEST, 'type', 0 );
        /*
        * required for Navigation
        */
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'streaming_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
        $nav['streaming_id'] = zarilia_cleanRequestVars( $_REQUEST, 'streaming_id', 0 );
        $nav['streaming_display'] = zarilia_cleanRequestVars( $_REQUEST, 'streaming_display', 3 );

		extract($nav);

        $url = ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list";
        $form = "
		 <div class='sidetitle'>" . _MD_AD_DISPLAY_BOX . "</div>
		 <div class='sidecontent'>" . zarilia_getSelection( $display_array, $nav['streaming_display'], "streaming_display", 1, 0, false, false, "onchange=\"location='" . $url . "&amp;limit=" . $nav['limit'] . "&amp;streaming_display='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div>
		 <div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . $url . "&amp;op=list&amp;streaming_display=" . $nav['streaming_display'] . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";

        zarilia_cp_header();
        $menu_handler->render( 1 );
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_STREAMING_CREATE, $addonversion['adminpath'] . "&amp;op=list" => _MA_AD_STREAMING_LIST ),
            _MD_AD_FILTER_BOX, $form,
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );

        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'streaming_id', '5', 'center', false );
        $tlist->AddHeader( 'streaming_title', '15%', 'left', true );
        $tlist->AddHeader( 'streaming_published', '', 'center', true );
        $tlist->AddHeader( 'streaming_weight', '', 'center', true );
        $tlist->AddHeader( 'streaming_display', '', 'center', 1 );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'action', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'], 'streaming' );
        $tlist->addFooter( $stream_handler->setSubmit( $fct ) );
        $tlist->setPath( 'op=' . $op );
        $button = array( 'edit', 'delete', 'clone' );
		echo zarilia_cp_icons( $button, 'streaming_id', $streaming_id );

        $_streaming_obj = $stream_handler->getStreamObj( $nav );
        foreach ( $_streaming_obj['list'] as $obj ) {
            $streaming_id = $obj->getVar( 'streaming_id' );
            // This line is required to make the boxes work correctly//
            $tlist->addHidden( $streaming_id, 'value_id' );
            $tlist->add(
                array( $streaming_id,
                    $obj->getTextbox( 'streaming_id', 'streaming_title', '50' ),
                    $obj->getVar( 'streaming_published', 's' ),
                    $obj->getTextbox( 'streaming_id', 'streaming_weight', '5' ),
                    $obj->getYesNobox( 'streaming_id', 'streaming_display' ),
                    $obj->getCheckbox( 'streaming_id' ),
                    zarilia_cp_icons( $button, 'streaming_id', $streaming_id )
                    ) );
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_streaming_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op . '&amp;limit=' . $nav['limit'] );
        break;

    case 'index':
    case 'default':
        zarilia_cp_header();
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_STREAMING_CREATE, $addonversion['adminpath'] . "&amp;op=list" => _MA_AD_STREAMING_LIST ),
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $menu_handler->render( 0 );
        break;
} // switch
zarilia_cp_footer();

?>