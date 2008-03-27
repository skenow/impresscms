<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:39 catzwolf Exp $
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
$section_handler = &zarilia_gethandler( 'section' );
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
                if ( false == $section_handler->doDatabase( $act ) ) {
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
        $section_id = zarilia_cleanRequestVars( $_REQUEST, 'section_id', 0 );
        $_section_obj = ( $section_id > 0 ) ? $section_handler->get( $section_id ) : $section_handler->create();
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
        $menu_handler->render( 2 );
        zarilia_admin_menu( _MD_AD_ACTION_BOX, $op_url );
        $form = $_section_obj->formEdit();
        break;

    case 'clone':
        $section_id = zarilia_cleanRequestVars( $_REQUEST, 'section_id', 0 );
        $_section_obj = $section_handler->get( $section_id );
        $_section_obj->setVar( 'section_id', '' );
        $_section_obj->setVar( 'section_title', $_section_obj->getVar( 'section_title' ) . '_cloned' );
        $_section_obj->setVar( 'section_published', time() );
        $_section_obj->setNew();
        if ( !$section_handler->insert( $_section_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_section_obj->getVar( 'section_title' ) ) );
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, ( $_section_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;
    case 'save':
        $section_id = zarilia_cleanRequestVars( $_REQUEST, 'section_id', 0 );
        $_section_obj = ( $section_id > 0 ) ? $section_handler->get( $section_id ) : $section_handler->create();
        /**
         */
        $_section_obj->setVars( $_REQUEST );
        $_section_obj->setVar( 'section_published', time() );
        /**
         */
        if ( isset( $_REQUEST['section_image'] ) ) {
            $image = explode( '|', $_REQUEST['section_image'] );
            $_section_obj->setVar( 'section_image', $image[0] );
        }

        if ( !$section_handler->insert( $_section_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_section_obj->getVar( 'section_title' ) ) );
        } else {
            $section_id = $_section_obj->getVar( 'section_id' );
            $mod_id = $zariliaAddon->getVar( 'mid' );
            $perm_handler = &zarilia_gethandler( 'groupperm' );
            $read_array = zarilia_cleanRequestVars( $_REQUEST, 'readgroup', array(), XOBJ_DTYPE_ARRAY );
            $readgroup = new cpPermission( '', 'section_read', '', $mod_id );
            $readgroup->cpPermission_save( $read_array, $section_id );
            $write_array = zarilia_cleanRequestVars( $_REQUEST, 'writegroup', array(), XOBJ_DTYPE_ARRAY );
            $writegroup = new cpPermission( '', 'section_write', '', $mod_id );
            $writegroup->cpPermission_save( $write_array, $section_id );
        }

        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            $return_url = zarilia_cleanRequestVars( $_REQUEST, 'return_url', '', XOBJ_DTYPE_TXTBOX );
			$return_url = !empty($return_url) ? $return_url : $addonversion['adminpath'] . '&amp;op=list';
			redirect_header( $return_url, 1, ( $_section_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;
    case 'delete':
        $section_id = zarilia_cleanRequestVars( $_REQUEST, 'section_id', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $_section_obj = $section_handler->get( $section_id );
        if ( !is_object( $_section_obj ) ) {
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
                        'section_id' => $_section_obj->getVar( 'section_id' ),
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_AM_WAYSYWTDTR, $_section_obj->getVar( 'section_title' ) )
                    );
                break;
            case 1:
                $menu_mid = 1;
                $menu_sectionid = $_section_obj->getVar( 'section_id' );
                $menu_name = 'section';
                if ( !$section_handler->delete( $_section_obj ) ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                                $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    $menus_handler = &zarilia_gethandler( 'menus' );
                    $menus_handler->deleteMenuItem( $menu_mid, $menu_name, $menu_sectionid );
                    redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
                }
                break;
        } // switch
        break;
    case 'updateall':
        $value_id = zarilia_cleanRequestVars( $_REQUEST, 'value_id', array() );
        $section_title = zarilia_cleanRequestVars( $_REQUEST, 'section_title', array() );
        $section_weight = zarilia_cleanRequestVars( $_REQUEST, 'section_weight', array() );
        $section_display = zarilia_cleanRequestVars( $_REQUEST, 'section_display', array() );
        foreach ( $value_id as $id => $section_id ) {
            $_section_obj = $section_handler->get( $section_id );
            if ( isset( $section_title[$id] ) ) {
                $_section_obj->setVar( 'section_title', $section_title[$id] );
            }
            if ( isset( $section_weight[$id] ) ) {
                $_section_obj->setVar( 'section_weight', $section_weight[$id] );
            }
            if ( isset( $section_display[$id] ) ) {
                $_section_obj->setVar( 'section_display', $section_display[$id] );
            }
            /**
             */
            if ( !$section_handler->insert( $_section_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_section_obj->getVar( 'section_title' ) ) );
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
        unset( $_REQUEST['section_weight'] );
        unset( $_REQUEST['section_display'] );
        unset( $_REQUEST['value_id'] );
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $section_id ) {
            $_section_obj = $section_handler->get( $section_id );
            $_section_obj->setVar( 'section_id', '' );
            $_section_obj->setVar( 'section_title', $_section_obj->getVar( 'section_title' ) . '_cloned' );
            $_section_obj->setVar( 'section_published', time() );
            $_section_obj->setNew();
            if ( !$section_handler->insert( $_section_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_section_obj->getVar( 'section_title' ) ) );
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
        unset( $_REQUEST['section_weight'] );
        unset( $_REQUEST['section_display'] );
        unset( $_REQUEST['value_id'] );
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $section_id ) {
            $_section_obj = $section_handler->get( $section_id );
            if ( !$section_handler->delete( $_section_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_section_obj->getVar( 'section_title' ) ) );
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
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'section_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
        $nav['section_id'] = zarilia_cleanRequestVars( $_REQUEST, 'section_id', 0 );
        $nav['section_display'] = zarilia_cleanRequestVars( $_REQUEST, 'section_display', 3 );

		$url = ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list";
		$form = "
		 <div class='sidetitle'>" . _MD_AD_DISPLAY_BOX . "</div>
		 <div class='sidecontent'>" . zarilia_getSelection( $display_array, $nav['section_display'], "section_display", 1, 0, false, false, "onchange=\"location='" . $url ."&amp;limit=" . $nav['limit'] . "&amp;section_display='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div>
		 <div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . $url ."&amp;op=list&amp;section_display=" . $nav['section_display'] . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";


		zarilia_cp_header();
        $menu_handler->render( 1 );
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, $op_url,
            _MD_AD_FILTER_BOX, $form
            );

		$tlist = new ZariliaTList();
        $tlist->AddHeader( 'section_id', '5', 'center', false );
        $tlist->AddHeader( 'section_title', '15%', 'left', true );
        $tlist->AddHeader( 'section_published', '', 'center', true );
        $tlist->AddHeader( 'section_weight', '', 'center', true );
        $tlist->AddHeader( 'section_display', '', 'center', 1 );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'], 'section' );
        $tlist->addFooter( $section_handler->setSubmit( $fct ) );
        $tlist->setPath( 'op=' . $op );
        $button = array( 'edit', 'delete', 'clone' );

		$_section_obj = $section_handler->getSectionObj( $nav );
        foreach ( $_section_obj['list'] as $obj ) {
            $section_id = $obj->getVar( 'section_id' );
            // This line is required to make the boxes work correctly//
            $tlist->addHidden( $section_id, 'value_id' );
            $tlist->add(
                array( $section_id,
                    $obj->getTextbox( 'section_id', 'section_title', '50' ),
                    $obj->formatTimeStamp(),
                    $obj->getTextbox( 'section_id', 'section_weight', '5' ),
                    $obj->getYesNobox( 'section_id', 'section_display' ),
                    $obj->getCheckbox( 'section_id' ),
                    zarilia_cp_icons( $button, 'section_id', $section_id )
                    ) );
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_section_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op . '&amp;limit=' . $nav['limit'] );
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