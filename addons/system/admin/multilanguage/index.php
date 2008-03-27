<?php
// $lang_id: index.php,v 1.2 2006/09/05 09:56:28 mekdrop Exp $
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

require_once( 'admin_menu.php' );
require_once ZAR_ROOT_PATH . '/class/class.menubar.php';
include_once( 'vars.php' );
include_once( 'functions.php' );

$xlanguage_handler = &zarilia_gethandler( 'language' );
$xlanguage_handler->loadConfig();
$type = zarilia_cleanRequestVars( $_REQUEST, 'type', '', XOBJ_DTYPE_TXTBOX );
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

    case 'edit':
    case 'create':
        $isBase = ( isset( $type ) && $type == 'ext' ) ? false : true;
		$lang_id = zarilia_cleanRequestVars( $_REQUEST, 'lang_id', 0 );
        if ( $lang_id > 0 ) {
            $lang = &$xlanguage_handler->get( $lang_id, $isBase );
        } elseif ( isset( $lang_name ) ) {
            $lang = &$xlanguage_handler->getByName( $lang_name, $isBase );
        } else {
            $lang = &$xlanguage_handler->create( true, $isBase );
        }
        if ( !$lang ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _AM_US_EVENTNOTFOUND );
            zarilia_cp_header();
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        /**
         */
        zarilia_cp_header();
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . '&amp;op=edit&type=base' => _AM_XLANG_ADDBASE, $addonversion['adminpath'] . '&amp;op=edit&type=ext' => _AM_XLANG_ADDEXT ) );
        $menu_handler->render( 2 );

        $lang_name = $lang->getVar( 'lang_name', 'e' );
        $lang_desc = $lang->getVar( 'lang_desc', 'e' );
        $lang_code = $lang->getVar( 'lang_code', 'e' );
        $lang_charset = $lang->getVar( 'lang_charset', 'e' );
        $lang_image = $lang->getVar( 'lang_image', 'e' );
        $weight = $lang->getVar( 'weight' );
        if ( $isBase == false ) {
            $lang_base = $lang->getVar( 'lang_base' );
        }
        include "langform.inc.php";
        break;

    case 'clone':
        $lang_id = zarilia_cleanRequestVars( $_REQUEST, 'lang_id', 0 );
        $isBase = ( isset( $type ) && $type == 'ext' ) ? false : true;
        $_xlang_obj = $xlanguage_handler->get( $lang_id, $isBase );
        print_r_html( $_xlang_obj );
        $_xlang_obj->setVar( 'lang_id', '' );
        $_xlang_obj->setVar( 'lang_name', $_xlang_obj->getVar( 'lang_name' ) . '_cloned' );
        $_xlang_obj->setNew();
        if ( !$xlanguage_handler->insert( $_xlang_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_xlang_obj->getVar( 'lang_name' ) ) );
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
        }
        break;

    case 'save':
        $type = zarilia_cleanRequestVars( $_REQUEST, 'type', '', XOBJ_DTYPE_TXTBOX );
        $lang_id = zarilia_cleanRequestVars( $_REQUEST, 'lang_id', 0 );
        $isBase = ( isset( $type ) && $type == 'ext' ) ? false : true;
        $_xlang_obj = ( $lang_id > 0 ) ? $xlanguage_handler->get( $lang_id, $isBase ) : $xlanguage_handler->create( true, $isBase );
        /**
         */
        $_xlang_obj->setVars( $_REQUEST );
        /**
         */
        if ( !$xlanguage_handler->insert( $_xlang_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_xlang_obj->getVar( 'lang_name' ) ) );
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, ( $_xlang_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;
    // case 'updateall':
    // $value_id = zarilia_cleanRequestVars( $_REQUEST, 'value_id', array() );
    // $lang_name = zarilia_cleanRequestVars( $_REQUEST, 'lang_name', array() );
    // $rank_min = zarilia_cleanRequestVars( $_REQUEST, 'rank_min', array() );
    // $rank_max = zarilia_cleanRequestVars( $_REQUEST, 'rank_max', array() );
    // $rank_special = zarilia_cleanRequestVars( $_REQUEST, 'rank_special', array() );
    // foreach ( $value_id as $id => $lang_id ) {
    // $_xlang_obj = $xlanguage_handler->get( $lang_id );
    // if ( isset( $lang_name[$id] ) ) {
    // $_xlang_obj->setVar( 'lang_name', $lang_name[$id] );
    // }
    // if ( isset( $rank_min[$id] ) ) {
    // $_xlang_obj->setVar( 'rank_min', $rank_min[$id] );
    // }
    // if ( isset( $rank_max[$id] ) ) {
    // $_xlang_obj->setVar( 'rank_max', $rank_max[$id] );
    // }
    // if ( isset( $rank_special[$id] ) ) {
    // $_xlang_obj->setVar( 'rank_special', $rank_special[$id] );
    // }
    // /**
    // */
    // if ( !$xlanguage_handler->insert( $_xlang_obj, false ) ) {
    // $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_xlang_obj->getVar( 'lang_name' ) ) );
    // }
    // }
    // if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
    // redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
    // } else {
    // zarilia_cp_header();
    // $menu_handler->render( 1 );
    // $GLOBALS['zariliaLogger']->setSysError();
    // $GLOBALS['zariliaLogger']->sysRender();
    // }
    // break;
    // case 'deleteall':
    // print_r_html( $_REQUEST );
    // $type = zarilia_cleanRequestVars( $_REQUEST, 'type', '', XOBJ_DTYPE_TXTBOX );
    // $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
    // $type = zarilia_cleanRequestVars( $_REQUEST, 'type', array() );
    // echo $checkbox;
    // foreach ( array_keys( $checkbox ) as $lang_id ) {
    // $isBase = ( isset( $type ) && $type == 'ext' ) ? false : true;
    // $_xlang_obj = $xlanguage_handler->get( $lang_id, $isBase );
    // // if ( !$xlanguage_handler->delete( $_xlang_obj, false ) ) {
    // // $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_xlang_obj->getVar( 'lang_name' ) ) );
    // // }
    // }
    // $GLOBALS['zariliaLogger']->getSysErrorCount();
    // if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
    // // redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
    // } else {
    // zarilia_cp_header();
    // $menu_handler->render( 1 );
    // // $GLOBALS['zariliaLogger']->setSysError();
    // $GLOBALS['zariliaLogger']->sysRender();
    // }
    // break;
    // case 'cloneall':
    // unset( $_REQUEST['avatar_weight'] );
    // unset( $_REQUEST['avatar_display'] );
    // unset( $_REQUEST['value_id'] );
    // $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
    // foreach ( array_keys( $checkbox ) as $lang_id ) {
    // $_xlang_obj = $xlanguage_handler->get( $lang_id );
    // $_xlang_obj->setVar( 'lang_id', '' );
    // $_xlang_obj->setVar( 'lang_name', $_xlang_obj->getVar( 'lang_name' ) . '_cloned' );
    // $_xlang_obj->setVar( 'avatar_created', time() );
    // $_xlang_obj->setNew();
    // if ( !$xlanguage_handler->insert( $_xlang_obj, false ) ) {
    // $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_xlang_obj->getVar( 'lang_name' ) ) );
    // }
    // }
    // if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
    // redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
    // } else {
    // zarilia_cp_header();
    // $menu_handler->render( 1 );
    // $GLOBALS['zariliaLogger']->setSysError();
    // $GLOBALS['zariliaLogger']->sysRender();
    // }
    // break;
    case 'delete':
        $lang_id = zarilia_cleanRequestVars( $_REQUEST, 'lang_id', 0 );
        echo $lang_id;
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $isBase = ( isset( $type ) && $type == 'ext' ) ? false : true;
        $_xlang_obj = $xlanguage_handler->get( $lang_id, $isBase );
        if ( !is_object( $_xlang_obj ) ) {
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
                        'lang_id' => $_xlang_obj->getVar( 'lang_id' ),
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_AM_WAYSYWTDTR, $_xlang_obj->getVar( 'lang_name' ) )
                    );
                break;
            case 1:
                $xlanguage_handler->delete( $_xlang_obj );
                if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                                $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    redirect_header( $addonversion['adminpath'] . "&amp;op=list", 1, _DBUPDATED );
                }
                break;
        } // switch
        break;

    case 'list':
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        /*
        * required for Navigation
        */
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'lang_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );

        $display = zarilia_cleanRequestVars( $_REQUEST, 'display', 3 );
        $display_array = array( '3' => _MD_AD_SHOWALL_BOX, '0' => _MD_AD_SHOWHIDDEN_BOX, '1' => _MD_AD_SHOWVISIBLE_BOX );
        $list_array = array( 5 => "5", 10 => "10", 15 => "15", 25 => "25", 50 => "50" );
        $form = "<div style='padding-bottom: 5px;'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div><div style='padding-bottom: 5px;'>" . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list&amp;display=" . $display . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";

        zarilia_cp_header();
        $menu_handler->render( 1 );
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . '&amp;op=edit&type=base' => _AM_XLANG_ADDBASE, $addonversion['adminpath'] . '&amp;op=edit&type=ext' => _AM_XLANG_ADDEXT ),
            _MD_AD_FILTER_BOX, $form
            );

        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'lang_id', '5%', 'center', false );
        $tlist->AddHeader( 'lang_name', '25%', 'left', true );
        $tlist->AddHeader( 'lang_charset', '', 'center', true );
        $tlist->AddHeader( 'lang_code', '', 'center', true );
        $tlist->AddHeader( 'lang_image', '', 'center', true );
        $tlist->AddHeader( 'weight', '', 'center', true );
        $tlist->AddHeader( 'lang_base', '', 'center', true );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'] . '&amp;op=' . $op, 'multilanguage' );
        $tlist->addFooter();
        $tlist->setPath( 'op=' . $op );

        $button = array( 'edit', 'delete', 'clone' );
        $_xlang_obj = $xlanguage_handler->getAllList();
        foreach ( $_xlang_obj as $lang_name => $obj ) {
            $isOrphan = true;
            if ( isset( $obj['base'] ) ) {
                if ( is_readable( ZAR_ROOT_PATH . '/images/flags/' . $obj['base']->getVar( 'lang_image' ) ) ) {
                    $lang_image = $obj['base']->getVar( 'lang_image' );
                } else {
                    $lang_image = 'noflag.gif';
                }
                $lang_id = $obj['base']->getVar( 'lang_id' );
                $tlist->addHidden( $lang_id, 'value_id' );
                $buttons = "<a href='" . $addonversion['adminpath'] . "&amp;op=edit&amp;type=base&amp;lang_id=" . $obj['base']->getVar( 'lang_id' ) . "'>" . zarilia_img_show( 'edit', _EDIT ) . "</a><a href='" . $addonversion['adminpath'] . "&amp;op=delete&amp;type=base&amp;lang_id=" . $obj['base']->getVar( 'lang_id' ) . "'>" . zarilia_img_show( 'delete', _DELETE ) . "</a><a href='" . $addonversion['adminpath'] . "&amp;op=clone&amp;type=base&amp;lang_id=" . $obj['base']->getVar( 'lang_id' ) . "'>" . zarilia_img_show( 'clone', _CLONE ) . "</a>";
                $tlist->add(
                    array( $lang_id,
                        $obj['base']->getVar( 'lang_name' ),
                        $obj['base']->getVar( 'lang_charset' ),
                        $obj['base']->getVar( 'lang_code' ),
                        "<img src='" . ZAR_URL . '/images/flags/' . $lang_image . "' alt='" . $obj['base']->getVar( 'lang_desc' ) . "' />",
                        $obj['base']->getVar( 'weight' ),
                        "------------",
                        $buttons
                        ) );
            }
            $isOrphan = false;
            if ( !isset( $obj['ext'] ) || count( $obj['ext'] ) < 1 ) {
                continue;
            }
            /**
             */
            foreach( $obj['ext'] as $ext ) {
                if ( is_readable( ZAR_ROOT_PATH . '/images/flags/' . $ext->getVar( 'lang_image' ) ) ) {
                    $lang_image = $ext->getVar( 'lang_image' );
                } else {
                    $lang_image = 'noflag.gif';
                }
                $lang_base = ( $isOrphan ) ? "<font color='red'>" . $ext->getVar( 'lang_base' ) . "</font>" : $ext->getVar( 'lang_base' );
                $buttons = "<a href='" . $addonversion['adminpath'] . "&amp;op=edit&amp;type=ext&amp;lang_id=" . $ext->getVar( 'lang_id' ) . "'>" . zarilia_img_show( 'edit', _EDIT ) . "</a><a href='" . $addonversion['adminpath'] . "&amp;op=delete&amp;type=ext&amp;lang_id=" . $ext->getVar( 'lang_id' ) . "'>" . zarilia_img_show( 'delete', _DELETE ) . "</a><a href='" . $addonversion['adminpath'] . "&amp;op=clone&amp;type=ext&amp;lang_id=" . $ext->getVar( 'lang_id' ) . "'>" . zarilia_img_show( 'clone', _CLONE ) . "</a>";
                $tlist->add(
                    array( $lang_id,
                        $ext->getVar( 'lang_name' ),
                        $ext->getVar( 'lang_charset' ),
                        $ext->getVar( 'lang_code' ),
                        "<img src = '" . ZAR_URL . '/images/flags/' . $lang_image . "' alt = '" . $ext->getVar( 'lang_desc' ) . "' / >",
                        $ext->getVar( 'weight' ),
                        $lang_base,
                        $buttons
                        ) );
            }
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_xlang_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op );
        break;

    case 'index':
    case 'default':
        zarilia_cp_header();
        $menu_handler->render( 0 );
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . '&amp;op=edit&type=base' => _AM_XLANG_ADDBASE, $addonversion['adminpath'] . '&amp;op=edit&type=ext' => _AM_XLANG_ADDEXT ) );
        break;
}
zarilia_cp_footer();

?>