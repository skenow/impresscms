<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:46 catzwolf Exp $
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

$_smilies_handler = &zarilia_gethandler( 'smilie' );
$config_handler = &zarilia_gethandler( 'config' );
$zariliaConfigUser = &$config_handler->getConfigsByCat( ZAR_CONF_USER );

$id = zarilia_cleanRequestVars( $_REQUEST, 'id', 0 );
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
                if ( false == $_smilies_handler->doDatabase( $act ) ) {
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

    /*Edit Smilies*/
    case 'help':
        zarilia_cp_header();
        $menu_handler->render( 0 );
        if ( file_exists( ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php" ) ) {
            @include ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php";
        }
        break;

    /*Edit Smilies*/
    case 'about':
        zarilia_cp_header();
        $menu_handler->render( 3 );

        require_once( ZAR_ROOT_PATH . "/class/class.about.php" );
        $zarilia_about = new ZariliaAbout();
        $zarilia_about->display();
        break;

    /**
     */
    case 'create':
    case 'edit':
        $id = zarilia_cleanRequestVars( $_REQUEST, 'id', 0 );
        $_smilies_obj = ( $id > 0 ) ? $_smilies_handler->get( $id ) : $_smilies_handler->create();
        if ( !$_smilies_obj ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _MA_AD_NOTFOUND );
            zarilia_cp_header();
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        /**
         */
        zarilia_cp_header();
        $menu_handler->render( 2 );
        $caption = ( !$_smilies_obj->isNew() ) ? $caption = sprintf( _MA_AD_SMILIES_MODIFY, $_smilies_obj->getVar( 'emotion' ) ) : _MA_AD_SMILIES_CREATE;
        $form = $_smilies_obj->formEdit( $caption );
        $form->display();
        break;

    /*Clone Avatar*/
    case 'clone':
        $id = zarilia_cleanRequestVars( $_REQUEST, 'id', 0 );
        $_smilies_obj = $_smilies_handler->get( $id );
        $_smilies_obj->setVar( 'id', '' );
        $_smilies_obj->setVar( 'emotion', $_smilies_obj->getVar( 'emotion' ) . '_cloned' );
        $_smilies_obj->setNew();
        if ( !$_smilies_handler->insert( $_smilies_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_smilies_obj->getVar( 'emotion' ) ) );
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'], 1, _DBUPDATED );
        }
        break;

    /**
     */
    case 'save':
        $id = zarilia_cleanRequestVars( $_REQUEST, 'id', 0 );
        $_smilies_obj = ( $id > 0 ) ? $_smilies_handler->get( $id ) : $_smilies_handler->create();
        /**
         */
        $_smilies_handler->setUpload( $_smilies_obj );
        /**
         */
        if ( !$_smilies_handler->insert( $_smilies_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_smilies_obj->getVar( 'emotion' ) ) );
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, ( $_smilies_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;

    case 'update':
        $smile_id = zarilia_cleanRequestVars( $_REQUEST, 'smile_id', array() );
        $old_display = zarilia_cleanRequestVars( $_REQUEST, 'old_display', array() );
        $smile_display = zarilia_cleanRequestVars( $_REQUEST, 'smile_display', array() );

        $count = count( $smile_id );
        for ( $i = 0; $i < $count; $i++ ) {
            $smile_display[$i] = empty( $smile_display[$i] ) ? 0 : 1;
            if ( $old_display[$i] != $smile_display[$i] ) {
                $_smilies_obj = &$_smilies_handler->get( $smile_id[$i] );
                $_smilies_obj->setVar( 'display', $smile_display[$i] );
                if ( !$_smilies_handler->insert( $_smilies_obj ) ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $avatar->getVar( 'avatar_name' ) ) );
                }
            }
        }

        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 2 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . "&amp;op=list", 1, _DBUPDATED );
        }
        break;
    /**
     */
    case 'delete':
        $id = zarilia_cleanRequestVars( $_REQUEST, 'id', 0 );
        $_smilies_obj = $_smilies_handler->get( $id );
        if ( !is_object( $_smilies_obj ) ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        switch ( zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 ) ) {
            case 0:
            default:
                zarilia_cp_header();
                $menu_handler->render( 1 );
                zarilia_confirm(
                    array( 'op' => 'delete',
                        'id' => $_smilies_obj->getVar( 'id' ),
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_AM_WAYSYWTDTR, $_smilies_obj->getVar( 'emotion' ) )
                    );
                break;
            case 1:
                if ( !$_smilies_handler->delete( $_smilies_obj ) ) {
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

    case 'updateall':
        $value_id = zarilia_cleanRequestVars( $_REQUEST, 'value_id', array() );
        $avatar_display = zarilia_cleanRequestVars( $_REQUEST, 'display', array() );
        foreach ( $value_id as $id => $smilie_id ) {
            $_smilies_obj = $_smilies_handler->get( $smilie_id );
            if ( isset( $avatar_display[$id] ) ) {
                $_smilies_obj->setVar( 'display', $avatar_display[$id] );
            }
            /**
             */
            if ( !$_smilies_handler->insert( $_smilies_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_smilies_obj->getVar( 'emotion' ) ) );
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
        unset( $_REQUEST['value_id'] );
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $smilie_id ) {
            $_smilies_obj = $_smilies_handler->get( $smilie_id );
            if ( !$_smilies_handler->delete( $_smilies_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_smilies_obj->getVar( 'emotion' ) ) );
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
        unset( $_REQUEST['value_id'] );
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $smilie_id ) {
            $_smilies_obj = $_smilies_handler->get( $smilie_id );
            $_smilies_obj->setVar( 'id', '' );
            $_smilies_obj->setVar( 'emotion', $_smilies_obj->getVar( 'emotion' ) . '_cloned' );
            $_smilies_obj->setNew();
            if ( !$_smilies_handler->insert( $_smilies_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_smilies_obj->getVar( 'emotion' ) ) );
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

    /**
     */
    case 'list':
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        /*
        * required for Navigation
        */
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );

        zarilia_cp_header();
        $display = zarilia_cleanRequestVars( $_REQUEST, 'display', 3 );
        $display_array = array( '3' => 'Show All', '0' => 'Show Hidden', '1' => 'Show visible' );
        $list_array = array( 5 => "5", 10 => "10", 15 => "15", 25 => "25", 50 => "50" );

        $form = "
		 <div class='sidetitle'>Display:</div>
		 <div class='sidecontent'>" . zarilia_getSelection( $display_array, $display, "display", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list&amp;limit=" . $nav['limit'] . "&amp;display='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>Display Amount:</div>
		 <div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list&amp;display=" . $display . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";

		zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => 'New Smilie' ), _MD_AD_FILTER_BOX, $form );
        $menu_handler->render( 1 );
        /**
         */
        $tlist = new ZariliaTList();
        $tlist->AddHeader( '#', '3%', 'center', true );
        $tlist->AddHeader( 'smile_url', '', 'left', true );
        $tlist->AddHeader( 'icon', '', 'center', true );
        $tlist->AddHeader( 'code', '', 'center', true );
        $tlist->AddHeader( 'emotion', '', 'center', true );
        $tlist->AddHeader( 'display', '', 'center', true );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'] . '&amp;op=' . $op, 'avatars' );
        $tlist->setPrefix( '_MA_AD_' );
        $tlist->setPath( 'fct=' . $fct . '&amp;op=' . $op );
        $tlist->addFooter( $_smilies_handler->setSubmit($fct) );
        /**
         */
        $i = 1;
        $button = array( 'edit', 'delete', 'clone' );
        $_smilies_obj = $_smilies_handler->getSmiliesObj( $nav, $display );
        foreach ( $_smilies_obj['list'] as $obj ) {
            $id = $obj->getVar( 'id' );
            $tlist->addHidden( $id, 'value_id' );
            $tlist->add(
                array( $id,
                    $obj->getVar( 'smile_url' ),
                    $obj->getSmilie(),
                    $obj->getVar( 'code' ),
                    $obj->getVar( 'emotion' ),
                    $obj->getYesNobox( 'id', 'display' ),
                    $obj->getCheckbox( 'id' ),
                    zarilia_cp_icons( $button, 'id', $id )
                    ) );
            $i++;
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_smilies_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op . '&amp;limit=' . $nav['limit'] . '&amp;display=' . $display );
        break;

    /**
     */
    case 'index':
    default:
        zarilia_cp_header();
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_SMILIES_CREATE ),
			_MD_AD_MAINTENANCE_BOX, zariliaMainAction()
		 );
        $menu_handler->render( 0 );
        break;
} // switch
zarilia_cp_footer();

?>