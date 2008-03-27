<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:52 catzwolf Exp $
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

$section_handler = &zarilia_gethandler( 'section' );
$category_handler = &zarilia_gethandler( 'category' );
$content_handler = &zarilia_gethandler( 'content' );
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
                if ( false == $content_handler->doDatabase( $act ) ) {
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

    case 'delete':
        $content_id = zarilia_cleanRequestVars( $_REQUEST, 'content_id', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $_trash_obj = $content_handler->get( $content_id );
        if ( !is_object( $_trash_obj ) ) {
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
                        'content_id' => $content_id,
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_AM_WAYSYWTDTR, $_trash_obj->getVar( 'content_title' ) )
                    );
                break;
            case 1:
                if ( !$content_handler->delete( $_trash_obj ) ) {
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

    case 'updateall':
        $value_id = zarilia_cleanRequestVars( $_REQUEST, 'value_id', array() );
        foreach ( $value_id as $id => $content_id ) {
            $_trash_obj = $content_handler->get( $content_id );
            if ( isset( $content_title[$id] ) ) {
                $_trash_obj->setVar( 'content_title', 1 );
            }
            /**
             */
            if ( !$content_handler->insert( $_trash_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_trash_obj->getVar( 'content_title' ) ) );
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
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $content_id ) {
            $_trash_obj = $content_handler->get( $content_id );
            if ( !$content_handler->delete( $_trash_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_trash_obj->getVar( 'content_title' ) ) );
            } else {
                $menus_handler = &zarilia_gethandler( 'menus' );
                $menus_handler->deleteMenuItem( 1, 'static', $content_id );
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
        /*
        * required for Navigation
        */
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'content_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
        $nav['type'] = zarilia_cleanRequestVars( $_REQUEST, 'type', 'static' );
        $nav['content_id'] = zarilia_cleanRequestVars( $_REQUEST, 'content_id', 0 );
        $nav['content_sid'] = zarilia_cleanRequestVars( $_REQUEST, 'content_sid', 0 );
        $nav['content_display'] = zarilia_cleanRequestVars( $_REQUEST, 'content_display', 3 );

        $sections_array = $section_handler->getList( null, null, null, null, 1 );
        $url = ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list";
        $form = "
		 <div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div>
		 <div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . $url . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";

        zarilia_cp_header();
        $menu_handler->render( 1 );
        zarilia_admin_menu(
            _MD_AD_FILTER_BOX, $form
            );
        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'content_id', '5', 'center', false );
        $tlist->AddHeader( 'content_title', '35%', 'left', true );
        $tlist->AddHeader( 'content_sid', '', 'center', true );
        $tlist->AddHeader( 'content_cid', '', 'center', true );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'], 'content' );
        $tlist->addFooter( $content_handler->setSubmit( $fct ) );
        $tlist->setPath( 'op=' . $op );

        $button = array( 'restore', 'delete' );
        $_trash_obj = $content_handler->getTrashObj( $nav );
        if ( $_trash_obj['count'] ) {
            foreach ( $_trash_obj['list'] as $obj ) {
                $content_id = $obj->getVar( 'content_id' );
                // This line is required to make the boxes work correctly//
                $tlist->addHidden( $content_id, 'value_id' );
                $tlist->add(
                    array( $content_id,
                        $obj->getVar( 'content_title' ),
                        $obj->getUser(),
                        $obj->getCheckbox( 'content_id' ),
                        zarilia_cp_icons( $button, 'content_id', $content_id )
                        ) );
            }
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_trash_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op . '&amp;limit=' . $nav['limit'] );
        break;

    case 'index':
    case 'default':
        zarilia_cp_header();
        zarilia_admin_menu(
            );
        $menu_handler->render( 0 );
        break;
} // switch
zarilia_cp_footer();

?>