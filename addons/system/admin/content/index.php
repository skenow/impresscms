<?php
// $Id: index.php,v 1.4 2007/05/05 11:10:15 catzwolf Exp $
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
include_once "admin_menu.php";
$_callback = &zarilia_gethandler( 'content' );
$do_callback = ZariliaCallback::getSingleton();
$do_callback->setCallback( $_callback );
switch ( $op ) {
    case 'maintenace':
    case 'help':
    case 'about':
    case 'edit':
    case 'delete':
    case 'cloned':
        $do_callback->setmenu( 2 );
        call_user_func( array( $do_callback, $op ) );
        break;
    /**
     */
    case 'cloneall':
    case 'deleteall':
        $do_callback->setmenu( 2 );
        $do_callback->cdall( $op );
        break;
    /**
     */
    case 'updateall':
        $do_callback->setmenu( 2 );
        $do_callback->updateAll( array( 'content_title', 'content_weight', 'content_display' ) );
        break;

    case 'save':
        $_function = ( $do_callback->getId() > 0 ) ? 'get': 'create';
        $_obj = call_user_func( array( $_callback, $_function ), $do_callback->getId( true ) );
        /**
         */
        $_obj->setVars( $_REQUEST );
        $_obj->setVar( 'content_approved', isset( $_REQUEST['content_approved'] ) );
        $_obj->setVar( 'content_display', isset( $_REQUEST['content_display'] ) );

        $content_sid = zarilia_cleanRequestVars( $_REQUEST, 'content_sid', 0 );
        if ( $content_sid ) {
            $_obj->setVar( 'content_type', call_user_func( array( $_callback, 'getcType' ), $content_sid ) );
        }

        if ( call_user_func( array( $_callback, 'insert' ), $_obj, true ) ) {
            include_once ZAR_ROOT_PATH . "/class/class.permissions.php";
            $content_id = $_obj->getVar( 'content_id' );
            $mod_id = $zariliaAddon->getVar( 'mid' );

            $read_array = zarilia_cleanRequestVars( $_REQUEST, 'readgroup', array(), XOBJ_DTYPE_ARRAY );
            if ( !is_array( $read_array ) ) {
                $cat_group = new cpPermission( '', 'section_read', '', $mod_id );
                $read_array = $cat_group->cpAdminPermission_get( $_obj->getVar( 'content_sid' ) );
            }
            $readgroup = new cpPermission( '', 'content_read', '', $mod_id );
            $readgroup->cpPermission_save( $read_array, $content_id );

            $write_array = zarilia_cleanRequestVars( $_REQUEST, 'writegroup', array(), XOBJ_DTYPE_ARRAY );
            $writegroup = new cpPermission( '', 'content_write', '', $mod_id );
            $writegroup->cpPermission_save( $write_array, $content_id );

            $menu_add = zarilia_cleanRequestVars( $_REQUEST, 'menu_add', 0 );
            if ( $menu_add ) {
                $menus_handler = &zarilia_gethandler( 'menus' );
                $_menu_obj = $menus_handler->getMenuItem( 1, 'static', $_obj->getVar( 'content_id' ) );
                if ( $_menu_obj ) {
                    $_menu_obj->setVar( 'menu_display', 0 );
                    if ( $_obj->getVar( 'content_approved' ) && $_obj->getVar( 'content_display' ) && $_obj->getVar( 'content_published' ) ) {
                        $_menu_obj->setVar( 'menu_display', 1 );
                    }
                } else {
                    $_menu_obj = $menus_handler->create();
                    $_menu_obj->setVar( 'menu_title', $_obj->getVar( 'content_title', 'e' ) );
                    $_menu_obj->setVar( 'menu_type', 'mainmenu' );
                    $_menu_obj->setVar( 'menu_mid', 1 );
                    $_menu_obj->setVar( 'menu_name', 'static' );
                    $_menu_obj->setVar( 'menu_sectionid', $_obj->getVar( 'content_id' ) );
                    $_menu_obj->setVar( 'menu_link', '{X_SITEURL}/index.php?page_type=' . $content_type . '&id=' . $_obj->getVar( 'content_id' ) );
                    $_menu_obj->setVar( 'menu_display', 1 );
                }
                if ( $menus_handler->insert( $_menu_obj ) ) {
                    $readgroup = new cpPermission( '', 'menu_read', '', 1 );
                    $readgroup->cpPermission_save( $read_array, $_menu_obj->getVar( 'menu_id' ) );
                }
            }
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            if ( $_obj->isNew() ) {
                $url = $addonversion['adminpath'] . "&op=edit&content_id=" . $_obj->getVar( 'content_id' ) . "&opt=1";
            } else {
                $url = $addonversion['adminpath'] . "&amp;op=list";
            }
            redirect_header( $url, 0, ( $_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;

    case 'list':
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        /*
        * required for Navigation
        */
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'content_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
        $nav['type'] = zarilia_cleanRequestVars( $_REQUEST, 'type', 'static' );

        $nav['content_id'] = zarilia_cleanRequestVars( $_REQUEST, 'content_id', 0 );
        $nav['content_sid'] = zarilia_cleanRequestVars( $_REQUEST, 'content_sid', 0 );
        $nav['content_display'] = zarilia_cleanRequestVars( $_REQUEST, 'content_display', 3 );

        $url = ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list";
        if ( !isset( $category_handler ) ) {
            $category_handler = &zarilia_gethandler( 'category' );
        }
        $form = "
		 <div class='sidetitle'>" . _MD_AD_DISPLAY_SECTION . "</div>
		 <div class='sidecontent'>" . zarilia_getSelection( call_user_func( array( $category_handler, 'sectionObj' ) ), $nav['content_sid'], "content_sid", 1, 0, false, false, "onchange=\"location='" . $url . "&amp;limit=" . $nav['limit'] . "&amp;content_display=" . $nav['content_display'] . "&amp;content_sid='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAY_BOX . "</div>
		 <div class='sidecontent'>" . zarilia_getSelection( $display_array, $nav['content_display'], "content_display", 1, 0, false, false, "onchange=\"location='" . $url . "&amp;limit=" . $nav['limit'] . "&amp;content_sid=" . $nav['content_sid'] . "&amp;content_display='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div>
		 <div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . $url . "&amp;op=list&amp;content_display=" . $nav['content_display'] . "&amp;content_sid=" . $nav['content_sid'] . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";

        zarilia_cp_header();
        $menu_handler->render( 1 );
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, $op_url,
            _MD_AD_FILTER_BOX, $form
            );
        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'content_id', '5', 'center', false );
        $tlist->AddHeader( 'content_title', '15%', 'left', true );
        $tlist->AddHeader( 'content_published', '', 'center', true );
        $tlist->AddHeader( 'content_weight', '', 'center', true );
        $tlist->AddHeader( 'content_display', '', 'center', true );
        $tlist->AddHeader( 'content_sid', '', 'center', true );
        $tlist->AddHeader( 'content_cid', '', 'center', true );
        $tlist->AddHeader( 'content_uid', '', 'center', true );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'], 'content' );
        $tlist->addFooter( call_user_func( array( $_callback, 'setSubmit' ), $fct ) );
        $tlist->setPath( 'op=' . $op );

        $button = array( 'edit', 'delete', 'cloned' );
        $_obj = $_callback->getContentObj( $nav, true );
        if ( $_obj['count'] ) {
            foreach ( $_obj['list'] as $obj ) {
                $content_id = $obj->getVar( 'content_id' );
                $tlist->add(
                    array( $content_id,
                        $obj->getTextbox( 'content_id', 'content_title', '25' ),
                        $obj->getVar( 'content_published', 's' ),
                        $obj->getTextbox( 'content_id', 'content_weight', '5' ),
                        $obj->getYesNobox( 'content_id', 'content_display' ),
                        $obj->getSection(),
                        $obj->getCategory(),
                        $obj->getUser(),
                        $obj->getCheckbox( 'content_id' ),
                        zarilia_cp_icons( $button, 'content_id', $content_id )
                        ) );
            }
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op . '&amp;limit=' . $nav['limit'] );
        break;

    case 'index':
    default:
        zarilia_cp_header();
        zarilia_admin_menu( _MD_AD_ACTION_BOX, $op_url, _MD_AD_MAINTENANCE_BOX, zariliaMainAction() );
        $menu_handler->render( 0 );
        break;
} // switch
zarilia_cp_footer();

?>