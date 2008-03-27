<?php
// $Id: category.php,v 1.3 2007/04/21 09:42:31 catzwolf Exp $
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
/**
 */
switch ( $op ) {
    case 'cat_maintenace':
        $act = zarilia_cleanRequestVars( $_REQUEST, 'act', '', XOBJ_DTYPE_TXTBOX );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        switch ( $ok ) {
            case 0:
            default:
                zarilia_cp_header();
                $menu_handler->render( 1 );
                zarilia_confirm(
                    array( 'op' => 'cat_maintenace',
                        'act' => $act,
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_AD_DOTABLE, $act )
                    );
                break;
            case 1:
                $act = zarilia_cleanRequestVars( $_REQUEST, 'act', '', XOBJ_DTYPE_TXTBOX );
                if ( false == $media_cat_handler->doDatabase( $act ) ) {
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

    case 'cat_help':
        zarilia_cp_header();
        $menu_handler->render( 0 );
        if ( file_exists( ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php" ) ) {
            @include ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php";
        }
        break;

    case 'cat_about':
        zarilia_cp_header();
        $menu_handler->render( 3 );
        require_once( ZAR_ROOT_PATH . "/class/class.about.php" );
        $zarilia_about = new ZariliaAbout();
        $zarilia_about->display();
        break;

    case 'cat_edit':
        $media_cid = zarilia_cleanRequestVars( $_REQUEST, 'media_cid', 0 );
        $_media_cat_obj = ( $media_cid > 0 ) ? $media_cat_handler->get( $media_cid ) : $media_cat_handler->create();
        if ( !$_media_cat_obj ) {
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
        zarilia_admin_menu( _MD_AD_ACTION_BOX, getActionMenu( $media_cid ), _MD_AD_MAINTENANCE_BOX, zariliaMainAction() );
        $_media_cat_obj->formEdit();
        break;

    case 'cat_clone':
        $media_cid = zarilia_cleanRequestVars( $_REQUEST, 'media_cid', 0 );
        $_media_cat_obj = $media_cat_handler->get( $media_cid );
        $_media_cat_obj->setVar( 'media_cid', '' );
        $_media_cat_obj->setVar( 'media_ctitle', $_media_cat_obj->getVar( 'media_ctitle' ) . '_cloned' );
        $_media_cat_obj->setNew();
        if ( !$media_cat_handler->insert( $_media_cat_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_media_cat_obj->getVar( 'media_ctitle' ) ) );
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=cat_list', 1, ( $_media_cat_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;

    case 'cat_save':
        $media_cid = zarilia_cleanRequestVars( $_REQUEST, 'media_cid', 0 );
        $_media_cat_obj = ( $media_cid > 0 ) ? $media_cat_handler->get( $media_cid ) : $media_cat_handler->create();
        /**
         */
        $_media_cat_obj->setVars( $_REQUEST );
        /**
         */
        if ( isset( $_REQUEST['section_image'] ) ) {
            $image = explode( '|', $_REQUEST['section_image'] );
            $_media_cat_obj->setVar( 'section_image', $image[0] );
        }
        if ( !$media_cat_handler->insert( $_media_cat_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_media_cat_obj->getVar( 'media_ctitle' ) ) );
        } else {
            $media_cid = $_media_cat_obj->getVar( 'media_cid' );
            $mod_id = $zariliaAddon->getVar( 'mid' );
            $perm_handler = &zarilia_gethandler( 'groupperm' );
            $read_array = zarilia_cleanRequestVars( $_REQUEST, 'readgroup', array(), XOBJ_DTYPE_ARRAY );
            $readgroup = new cpPermission( '', 'mediacategory_read', '', $mod_id );
            $readgroup->cpPermission_save( $read_array, $media_cid );
            $write_array = zarilia_cleanRequestVars( $_REQUEST, 'writegroup', array(), XOBJ_DTYPE_ARRAY );
            $writegroup = new cpPermission( '', 'mediacategory_write', '', $mod_id );
            $writegroup->cpPermission_save( $write_array, $media_cid );
        }

        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            $return_url = zarilia_cleanRequestVars( $_REQUEST, 'return_url', '', XOBJ_DTYPE_TXTBOX );
            $return_url = !empty( $return_url ) ? $return_url : $addonversion['adminpath'] . '&amp;op=cat_list';
            redirect_header( $return_url, 1, ( $_media_cat_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;

    case 'cat_delete':
        $media_cid = zarilia_cleanRequestVars( $_REQUEST, 'media_cid', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $_media_cat_obj = $media_cat_handler->get( $media_cid );
        if ( !is_object( $_media_cat_obj ) ) {
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
                    array( 'op' => 'cat_delete',
                        'media_cid' => $_media_cat_obj->getVar( 'media_cid' ),
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_AM_WAYSYWTDTR, $_media_cat_obj->getVar( 'media_ctitle' ) )
                    );
                break;
            case 1:
                $menu_mid = 1;
                $menu_sectionid = $_media_cat_obj->getVar( 'media_cid' );
                $menu_name = 'section';
                if ( !$media_cat_handler->cat_delete( $_media_cat_obj ) ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                    $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    $menus_handler = &zarilia_gethandler( 'menus' );
                    $menus_handler->deleteMenuItem( $menu_mid, $menu_name, $menu_sectionid );
                    redirect_header( $addonversion['adminpath'] . '&amp;op=cat_list', 1, _DBUPDATED );
                }
                break;
        } // switch
        break;

    case 'cat_updateall':
        $value_id = zarilia_cleanRequestVars( $_REQUEST, 'value_id', array() );
        $media_ctitle = zarilia_cleanRequestVars( $_REQUEST, 'media_ctitle', array() );
        $media_cweight = zarilia_cleanRequestVars( $_REQUEST, 'media_cweight', array() );
        $media_cdisplay = zarilia_cleanRequestVars( $_REQUEST, 'media_cdisplay', array() );
        foreach ( $value_id as $id => $media_cid ) {
            $_media_cat_obj = $media_cat_handler->get( $media_cid );
            if ( isset( $media_ctitle[$id] ) ) {
                $_media_cat_obj->setVar( 'media_ctitle', $media_ctitle[$id] );
            }
            if ( isset( $media_cweight[$id] ) ) {
                $_media_cat_obj->setVar( 'media_cweight', $media_cweight[$id] );
            }
            if ( isset( $media_cdisplay[$id] ) ) {
                $_media_cat_obj->setVar( 'media_cdisplay', $media_cdisplay[$id] );
            }
            /**
             */
            if ( !$media_cat_handler->insert( $_media_cat_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_media_cat_obj->getVar( 'media_ctitle' ) ) );
            }
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            redirect_header( $addonversion['adminpath'] . '&amp;op=cat_list', 1, _DBUPDATED );
        } else {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case 'cat_cloneall':
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $media_cid ) {
            $_media_cat_obj = $media_cat_handler->get( $media_cid );
            $_media_cat_obj->setVar( 'media_cid', '' );
            $_media_cat_obj->setVar( 'media_ctitle', $_media_cat_obj->getVar( 'media_ctitle' ) . '_cloned' );
            $_media_cat_obj->setNew();
            if ( !$media_cat_handler->insert( $_media_cat_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_media_cat_obj->getVar( 'media_ctitle' ) ) );
            }
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            redirect_header( $addonversion['adminpath'] . '&amp;op=cat_list', 1, _DBUPDATED );
        } else {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case 'cat_deleteall':
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $media_cid ) {
            $_media_cat_obj = $media_cat_handler->get( $media_cid );
            if ( !$media_cat_handler->delete( $_media_cat_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_media_cat_obj->getVar( 'media_ctitle' ) ) );
            }
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            redirect_header( $addonversion['adminpath'] . '&amp;op=cat_list', 1, _DBUPDATED );
        } else {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case 'cat_list':
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        $type = zarilia_cleanRequestVars( $_REQUEST, 'type', 0 );
        /*
        * required for Navigation
        */
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'media_cid' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
        $nav['media_cid'] = zarilia_cleanRequestVars( $_REQUEST, 'media_cid', 0 );
        $nav['media_cdisplay'] = zarilia_cleanRequestVars( $_REQUEST, 'media_cdisplay', 3 );

        $url = ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=cat_list";
        $form = "
		 <div class='sidetitle'>" . _MD_AD_DISPLAY_BOX . "</div>
		 <div class='sidecontent'>" . zarilia_getSelection( $display_array, $nav['media_cdisplay'], "media_cdisplay", 1, 0, false, false, "onchange=\"location='" . $url . "&amp;limit=" . $nav['limit'] . "&amp;media_cdisplay='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div>
		 <div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . $url . "&amp;op=cat_list&amp;media_cdisplay=" . $nav['media_cdisplay'] . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";

        zarilia_cp_header();
        $menu_handler->render( 1 );
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, getActionMenu( $media_cid ),
            _MD_AD_FILTER_BOX, $form,
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );

        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'media_cid', '5', 'center', false );
        $tlist->AddHeader( 'media_ctitle', '15%', 'left', true );
        $tlist->AddHeader( 'media_cweight', '', 'center', true );
        $tlist->AddHeader( 'media_cdisplay', '', 'center', 1 );
        $tlist->AddHeader( 'media_ccount', '', 'center', false );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'], 'section' );
        $tlist->addFooter( $media_cat_handler->setSubmit( $fct, 'fct', array( 'cat_updateall' => 'Update Selected', 'cat_deleteall' => 'Delete Selected', 'cat_cloneall' => 'Clone Selected' ) ) );
        $tlist->setPath( 'op=' . $op );

        $button = array( 'cat_edit' => 'edit', 'cat_delete' => 'delete', 'cat_clone' => 'clone', 'media_list' => 'view', 'uploader' => 'upload' );
        $_media_cat_obj = $media_cat_handler->getMediaCatObj( $nav );
        foreach ( $_media_cat_obj['list'] as $obj ) {
            $media_cid = $obj->getVar( 'media_cid' );
            $count = $media_handler->getCount( new Criteria( 'media_cid', $media_cid ) );
            // This line is required to make the boxes work correctly//
            $tlist->addHidden( $media_cid, 'value_id' );
            $tlist->add(
                array( $media_cid,
                    $obj->getTextbox( 'media_cid', 'media_ctitle', '50' ),
                    $obj->getTextbox( 'media_cid', 'media_cweight', '5' ),
                    $obj->getYesNobox( 'media_cid', 'media_cdisplay' ),
                    $count,
                    $obj->getCheckbox( 'media_cid' ),
                    zarilia_cp_icons( $button, 'media_cid', $media_cid )
                    ) );
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_media_cat_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op . '&amp;limit=' . $nav['limit'] );
        break;
} // switch

?>