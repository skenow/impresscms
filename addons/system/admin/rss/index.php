<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:38 catzwolf Exp $
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
if ( !is_object( $zariliaUser ) || !is_object( $zariliaAddon ) || !$zariliaUser->isAdmin( $zariliaAddon->getVar('mid') ) ) {
    exit( "Access Denied" );
}

require_once "admin_menu.php";

$rss_handler = &zarilia_gethandler( 'rss' );
$rss_id = zarilia_cleanRequestVars( $_REQUEST, 'rss_id', 0 );
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
                if ( false == $rss_handler->doDatabase( $act ) ) {
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

    case 'edit':
        $rss_id = zarilia_cleanRequestVars( $_REQUEST, 'rss_id', 0 );
        $_rss_obj = ( $rss_id > 0 ) ? $rss_handler->get( $rss_id ) : $rss_handler->create();
        if ( !$_rss_obj ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _NOTFOUND );
            zarilia_cp_header();
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        /**
         */
        zarilia_cp_header();
        $menu_handler->render( 2 );
        $caption = ( !$_rss_obj->isNew() ) ? $caption = sprintf( _MA_AD_ERSS_MODIFY, $_rss_obj->getVar( 'age_dtitle' ) ) : _MA_AD_ERSS_CREATE;
        $form = $_rss_obj->formEdit( $caption );
        $form->display();
        break;

    case 'save':
        $rss_id = zarilia_cleanRequestVars( $_REQUEST, 'rss_id', 0 );
        $_rss_obj = ( $rss_id > 0 ) ? $rss_handler->get( $rss_id ) : $rss_handler->create();
        /**
         */
        $_rss_obj->setVars( $_REQUEST );
        /**
         */
        if ( !$rss_handler->insert( $_rss_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_rss_obj->getVar( 'rss_name' ) ) );
        } else {
            if ( $_rss_obj->getVar( 'rss_xml' ) == '' ) {
                $renderer = $rss_handler->zariliarss_getrenderer( $_rss_obj );
                $renderer->updateCache();
            }
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, ( $_rss_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;

    case 'updateall':
        $value_id = zarilia_cleanRequestVars( $_REQUEST, 'rss_id', array() );
        $rss_name = zarilia_cleanRequestVars( $_REQUEST, 'rss_name', array() );
        $rss_weight = zarilia_cleanRequestVars( $_REQUEST, 'rss_weight', array() );
        $rss_asblock = zarilia_cleanRequestVars( $_REQUEST, 'rss_asblock', array() );
        $rss_display = zarilia_cleanRequestVars( $_REQUEST, 'rss_display', array() );
        $rss_cachetime = zarilia_cleanRequestVars( $_REQUEST, 'rss_cachetime', array() );
        $rss_encoding = zarilia_cleanRequestVars( $_REQUEST, 'rss_encoding', array() );
		foreach ( $value_id as $id => $rss_id ) {
            $_rss_obj = $rss_handler->get( $rss_id );
            $_rss_obj->setVar( 'rss_name', $rss_name[$id] );
            $_rss_obj->setVar( 'rss_weight', $rss_weight[$id] );
            $_rss_obj->setVar( 'rss_asblock', $rss_asblock[$id] );
            $_rss_obj->setVar( 'rss_display', $rss_display[$id] );
            $_rss_obj->setVar( 'rss_cachetime', $rss_cachetime[$id] );
            $_rss_obj->setVar( 'rss_encoding', $rss_encoding[$id] );
            /**
             */
            if ( !$rss_handler->insert( $_rss_obj, true ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_rss_obj->getVar( 'rss_name' ) ) );
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
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $rss_id ) {
            $_rss_obj = $rss_handler->get( $rss_id );
            $_rss_obj->zariliaClone();
            $_rss_obj->setVar( 'rss_updated', time() );
            if ( !$rss_handler->insert( $_rss_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_rss_obj->getVar( 'rss_name' ) ) );
            } else {
                if ( $_rss_obj->getVar( 'rss_xml' ) == '' ) {
                    $renderer = $rss_handler->zariliarss_getrenderer( $_rss_obj );
                    $renderer->updateCache();
                }
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

    case 'delete':
        $rss_id = zarilia_cleanRequestVars( $_REQUEST, 'rss_id', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $_rss_obj = $rss_handler->get( $rss_id );
        if ( !is_object( $_rss_obj ) ) {
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
                    array( 'op' => 'delete', 'rss_id' => $_rss_obj->getVar( 'rss_id' ),
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_AM_WAYSYWTDTR, $_rss_obj->getVar( 'rss_name' ) )
                    );
                break;
            case 1:
                if ( !$rss_handler->delete( $_rss_obj ) ) {
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
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $rss_id ) {
            $_rss_obj = $rss_handler->get( $rss_id );
            if ( !$rss_handler->delete( $_rss_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_rss_obj->getVar( 'rss_name' ) ) );
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
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'rss_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
        $headlines = &$rss_handler->getObjects();
        $count = count( $headlines );

        zarilia_cp_header();
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_ERSS_CREATE ),
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $menu_handler->render( 1 );

        $tlist = new ZariliaTList();
        $tlist->AddFormStart( 'post', $addonversion['adminpath'] . '&amp;op=' . $op, 'rss_id' );
        $tlist->AddHeader( 'rss_id', '5%', 'center', false );
        $tlist->AddHeader( 'rss_name', '15%', 'left', false );
        $tlist->AddHeader( 'rss_cachetime', '15%', 'left', false );
        $tlist->AddHeader( 'rss_encoding', '', 'center', false );
        $tlist->AddHeader( 'rss_display', '', 'center', false );
        $tlist->AddHeader( 'rss_asblock', '', 'center', false );
        $tlist->AddHeader( 'rss_weight', '', 'center', false );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->addFooter( $rss_handler->setSubmit( $fct, 'fct', array( 'deleteall' => 'Delete Selected' ) ) );
        $tlist->setPath( 'op=' . $op );

        $button = array( 'edit', 'delete' );
        $_rss_obj = $rss_handler->getRssObj( $nav );
        foreach ( $_rss_obj['list'] as $obj ) {
            $rss_id = $obj->getVar( 'rss_id' );
            $tlist->addHidden( $rss_id, 'rss_id' );
            $tlist->add(
                array( $rss_id,
                    $obj->getTextbox( 'rss_id', 'rss_name', '50' ),
                    $obj->getCachetime( 'rss_id' ),
                    $obj->getEncoding( 'rss_id' ),
                    $obj->getYesNobox( 'rss_id', 'rss_display' ),
                    $obj->getYesNobox( 'rss_id', 'rss_asblock' ),
                    $obj->getTextbox( 'rss_id', 'rss_weight', '5' ),
                    $obj->getCheckbox( 'rss_id' ),
                    zarilia_cp_icons( $button, 'rss_id', $rss_id )
                    ) );
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        break;

    case 'index':
    default:
        zarilia_cp_header();
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_ERSS_CREATE ),
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $menu_handler->render( 0 );
        break;
} // switch
zarilia_cp_footer();

?>