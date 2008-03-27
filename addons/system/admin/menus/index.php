<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:32 catzwolf Exp $
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
    exit( 'Access Denied' );
}

require_once 'admin_menu.php';
$_callback = &zarilia_gethandler( 'menus' );
$do_callback = &ZariliaCallback::getSingleton();
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
    case 'updateall':
        $do_callback->setmenu( 2 );
        $do_callback->updateAll( array( 'menu_title', 'menu_weight', 'menu_display' ) );
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
    case 'save':
        unset( $_SESSION['user'] );
        require_once ZAR_ROOT_PATH . '/class/class.permissions.php';
        $menu_id = zarilia_cleanRequestVars( $_REQUEST, 'menu_id', 0 );
        $_menu_obj = ( $menu_id > 0 ) ? $_callback->get( $menu_id ) : $_callback->create();
        if ( isset( $_REQUEST['menu_mid'] ) && intval( $_REQUEST['menu_mid'] ) > 1 ) {
            $addon = $addon_handler->get( $_REQUEST['menu_mid'] );
            $_REQUEST['menu_title'] = $addon->getVar( 'name' );
            $_REQUEST['menu_link'] = ZAR_URL . '/addons/' . $addon->getVar( 'dirname' ) . '/';
        }
        if ( isset( $_REQUEST['menu_mid'] ) && intval( $_REQUEST['menu_mid'] ) == -1 ) {
            $_REQUEST['menu_title'] = 'hr';
            $_REQUEST['menu_link'] = '';
        }
        if ( !$_REQUEST['menu_type'] ) {
            if ( !$_callback->delete( $_menu_obj ) ) {
                zarilia_cp_header();
                $menu_handler->render( 1 );
                $GLOBALS['zariliaLogger']->setSysError();
                $GLOBALS['zariliaLogger']->sysRender();
                zarilia_cp_footer();
                exit();
            } else {
                redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
            }
        }
        $_menu_obj->setVars( $_REQUEST );
        $_menu_obj->setVar( 'menu_title', $_REQUEST['menu_title'] );
        $_menu_obj->setVar( 'menu_type', $_REQUEST['menu_type'] );
        $_menu_obj->setVar( 'menu_link', $_REQUEST['menu_link'] );
        if ( isset( $_REQUEST['menu_image'] ) ) {
            $image = explode( '|', $_REQUEST['menu_image'] );
            if ( $image[0] == '' || $image[0] == 'blank.png' ) {
                $image[0] = '';
            }
            $_menu_obj->setVar( 'menu_image', $image[0] );
        }
        if ( !$_callback->insert( $_menu_obj ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_menu_obj->getVar( 'menu_title' ) ) );
            continue;
        } else {
            $read_array = zarilia_cleanRequestVars( $_REQUEST, 'menu_read', array(), XOBJ_DTYPE_ARRAY );
            $readgroup = new cpPermission( '', 'menu_read', '', 1 );
            $readgroup->cpPermission_save( $read_array, $_menu_obj->getVar( 'menu_id' ) );
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, ( $_menu_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;

    case 'menus':
        require ZAR_ROOT_PATH . '/class/class.tlist.php';
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'menu_weight' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
		$menu_type = zarilia_cleanRequestVars( $_REQUEST, 'menu_type', 0 );

		zarilia_cp_header();
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_EMENU_CREATE ), _MD_AD_MAINTENANCE_BOX, zariliaMainAction() );
        $menu_handler->render( 1 );
        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'menu_id', '5', 'center', false );
        $tlist->AddHeader( 'menu_type', '10%', 'center', true );
        $tlist->AddHeader( 'menu_title', '', 'left', true );
        $tlist->AddHeader( 'menu_image', '', 'center', true );
        $tlist->AddHeader( 'menu_weight', '', 'center', true );
        $tlist->AddHeader( 'menu_display', '', 'center', 1 );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'] . '&amp;op=' . $op, 'menues' );
        $tlist->addFooter( $_callback->setSubmit( $fct ) );
        $tlist->addHidden( $menu_type, 'menu_type' );
        $tlist->setPath( 'op=' . $op . '&amp;&menu_type=' . $menu_type );

        $button = array( 'edit', 'delete', 'clone' );
        $_avt_obj = $_callback->getMenus( $nav, $menu_type, true, true );
        foreach ( $_avt_obj['list'] as $obj ) {
            $menu_id = $obj->getVar( 'menu_id' );
            $tlist->addHidden( $menu_id, 'value_id' );
            $tlist->add(
                array( $menu_id,
                    $obj->getVar( 'menu_type' ),
                    $obj->getTextbox( 'menu_id', 'menu_title', '35' ),
                    $obj->getVar( 'menu_image' ),
                    $obj->getTextbox( 'menu_id', 'menu_weight', '5' ),
                    $obj->getYesNobox( 'menu_id', 'menu_display' ),
                    $obj->getCheckbox( 'menu_id' ),
                    zarilia_cp_icons( $button, 'menu_id', $menu_id )
                    ) );
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_avt_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op . '&menu_type=' . $menu_type . '&amp;limit=' . $nav['limit'] );
        break;

    case 'list':
        $menu_type = zarilia_cleanRequestVars( $_REQUEST, 'menu_type', 0 );

		require ZAR_ROOT_PATH . '/class/class.tlist.php';
        zarilia_cp_header();
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_EMENU_CREATE ), _MD_AD_MAINTENANCE_BOX, zariliaMainAction() );

        $menu_handler->render( 1 );
        $tlist = new ZariliaTList();
        $tlist->AddFormStart( 'post', $addonversion['adminpath'] . '&amp;op=' . $op, 'menus' );
        $tlist->AddHeader( 'menu_id', '5%', 'center', false );
        $tlist->AddHeader( 'menu_type', '15%', 'left', false );
        $tlist->AddHeader( 'menu_title', '15%', 'left', false );
        $tlist->AddHeader( 'menu_count', '', 'center', false );
        $tlist->AddHeader( 'menu_published', '', 'center', false );
        $tlist->AddHeader( 'action', '10%', 'center', false );
        $tlist->addFooter();
        $tlist->setPath( 'op=' . $op . '&amp;&menu_type=' . $menu_type );

        $i = 1;
        $button = array( 'menus' );
        $_menus = &call_user_func( array( $_callback, 'getMenuList' ) );
        foreach ( array_keys( $_menus ) as $k ) {
            $criteria = new CriteriaCompo();
            $criteria->add( new Criteria( 'menu_type', $k ) );
            $total_array = call_user_func( array( $_callback, 'getCount' ), $criteria );
            $criteria->add( new Criteria( 'menu_display', 1 ) );
            $published_array = call_user_func( array( $_callback, 'getCount' ), $criteria );
            $tlist->add( array( $i, $k, $_menus[$k], intval( $total_array ), intval( $published_array ), zarilia_cp_icons( $button, 'menu_type', $k ) ) );
            $i++;
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        break;

    case 'index':
    default:
        zarilia_cp_header();
        zarilia_admin_menu( _MD_AD_MAINTENANCE_BOX, zariliaMainAction() );
        $menu_handler->render( 0 );
        break;
} // switch
zarilia_cp_footer();

?>