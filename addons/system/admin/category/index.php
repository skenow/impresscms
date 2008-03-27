<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:00 catzwolf Exp $
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
require_once "admin_menu.php";
$maintenance = array( $addonversion['adminpath'] . "&amp;op=maintenace&amp;act=optimize" => _MD_AD_OPTIMIZE, $addonversion['adminpath'] . "&amp;op=maintenace&amp;act=analyze" => _MD_AD_ANALYZE, $addonversion['adminpath'] . "&amp;op=maintenace&amp;act=repair" => _MD_AD_REPAIR, $addonversion['adminpath'] . "&amp;op=maintenace&amp;act=truncate" => _MD_AD_CLEARENTRIES );

$_callback = &zarilia_gethandler( 'category' );
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
    case 'updateall':
        $do_callback->setmenu( 2 );
        $do_callback->updateAll( array( 'category_title', 'category_weight', 'category_display' ) );
        break;
    /**
     */
    case 'cloneall':
    case 'deleteall':
        $do_callback->setmenu( 2 );
        $do_callback->cdall( $op );
        break;

    case 'save':
        $_function = ( $do_callback->getId() > 0 ) ? 'get': 'create';
        $_obj = call_user_func( array( $_callback, $_function ), $do_callback->getId() );
        if ( !$_obj ) {
            // do error check here
            exit();
        }
        /**
         */
        $_obj->setVars( $_REQUEST );
        if ( call_user_func( array( $_callback, 'insert' ), $_obj, true ) ) {
            $_id = $_obj->getVar( 'category_id' );
            $mod_id = $zariliaAddon->getVar( 'mid' );
            $perm_handler = &zarilia_gethandler( 'groupperm' );
            /**
             */
            include ZAR_ROOT_PATH . '/class/class.permissions.php';
            $read_array = zarilia_cleanRequestVars( $_REQUEST, 'readgroup', array(), XOBJ_DTYPE_ARRAY );
            $readgroup = new cpPermission( '', 'category_read', '', $mod_id );
            $readgroup->cpPermission_save( $read_array, $_id );
            /**
             */
            $write_array = zarilia_cleanRequestVars( $_REQUEST, 'writegroup', array(), XOBJ_DTYPE_ARRAY );
            $writegroup = new cpPermission( '', 'category_write', '', $mod_id );
            $writegroup->cpPermission_save( $write_array, $_id );
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 0, ( $_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
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
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'category_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
        $nav['section_id'] = zarilia_cleanRequestVars( $_REQUEST, 'section_id', '' );
        $nav['category_id'] = zarilia_cleanRequestVars( $_REQUEST, 'category_id', 0 );
        $nav['category_display'] = zarilia_cleanRequestVars( $_REQUEST, 'category_display', 3 );

        $url = ZAR_URL . '/addons/system/' . $addonversion['adminpath'] . '&amp;op=list';
        $form = "
		 <div class='sidetitle'>" . _MD_AD_DISPLAY_SECTION . "</div>
		 <div class='sidecontent'>" . zarilia_getSelection( call_user_func( array( $_callback, 'sectionObj' ) ), $nav['section_id'], 'section_id', 1, 0, false, false, "onchange=\"location='" . $url . "&amp;limit=" . $nav['limit'] . "&amp;category_display=" . $nav['category_display'] . "&amp;section_id='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAY_BOX . "</div>
		 <div class='sidecontent'>" . zarilia_getSelection( $display_array, $nav['category_display'], 'category_display', 1, 0, false, false, "onchange=\"location='" . $url . "&amp;section_id=" . $nav['section_id'] . "&amp;limit=" . $nav['limit'] . "&amp;category_display='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div>
		 <div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], 'limit', 1, 0, false, false, "onchange=\"location='" . $url . "&amp;section_id=" . $nav['section_id'] . "&amp;category_display=" . $nav['category_display'] . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";
        zarilia_cp_header();
        $menu_handler->render( 1 );
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, $op_url,
            _MD_AD_FILTER_BOX, $form
            );
        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'category_id', '5', 'center', false );
        $tlist->AddHeader( 'category_title', '15%', 'left', true );
        $tlist->AddHeader( 'category_published', '', 'center', true );
        $tlist->AddHeader( 'category_weight', '', 'center', true );
        $tlist->AddHeader( 'category_display', '', 'center', 1 );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'], 'category' );
        $tlist->addFooter( call_user_func( array( $_callback, 'setSubmit' ), $fct ) );
        $tlist->setPath( 'op=' . $op );
        $button = array( 'edit', 'delete', 'cloned' );
        $_obj = $_callback->getCategoryObj( $nav, $nav['category_id'], $nav['section_id'], $nav['category_display'] );
        if ( $_obj['count'] > 0 ) {
            foreach ( $_obj['list'] as $obj ) {
                $_id = $obj->getVar( 'category_id' );
                $tlist->add(
                    array( $_id,
                        $obj->getTextbox( 'category_id', 'category_title', '50' ),
                        $obj->getVar( 'category_published', 's' ),
                        $obj->getTextbox( 'category_id', 'category_weight', '5' ),
                        $obj->getYesNobox( 'category_id', 'category_display' ),
                        $obj->getCheckbox( 'category_id' ),
                        zarilia_cp_icons( $button, 'category_id', $_id )
                        ) );
            }
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op . '&amp;limit=' . $nav['limit'] );
        break;

    case 'default':
        zarilia_cp_header();
        zarilia_admin_menu( _MD_AD_ACTION_BOX, $op_url, _MD_AD_MAINTENANCE_BOX, zariliaMainAction() );
        $menu_handler->render( 0 );
        break;
} // switch
zarilia_cp_footer();

?>