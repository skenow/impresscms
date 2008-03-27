<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:33 catzwolf Exp $
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
require_once "admin_menu.php";

$mimetype_handler = &zarilia_gethandler( 'mimetype' );
$mime_id = zarilia_cleanRequestVars( $_REQUEST, 'mime_id', 0 );
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
                if ( false == $mimetype_handler->doDatabase( $act ) ) {
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
        $menu_handler->render( 5 );
        require_once( ZAR_ROOT_PATH . "/class/class.about.php" );
        $zarilia_about = new ZariliaAbout();
        $zarilia_about->display();
        break;

    case 'edit':
    case 'create':
        $mime_id = zarilia_cleanRequestVars( $_REQUEST, 'mime_id', 0 );
        $_mimetype_obj = ( $mime_id > 0 ) ? $mimetype_handler->get( $mime_id ) : $mimetype_handler->create();
        if ( !$_mimetype_obj ) {
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
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_MIME_CREATE_NEW,
                $addonversion['adminpath'] . "&amp;op=permissions" => _MA_AD_MIME_PERMISSION
                ),
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $form = $_mimetype_obj->formEdit();
        break;

    /*Clone Avatar*/
    case 'clone':
        $mime_id = zarilia_cleanRequestVars( $_REQUEST, 'mime_id', 0 );
        $_mimetype_obj = $mimetype_handler->get( $mime_id );
        $_mimetype_obj->setVar( 'mime_id', '' );
        $_mimetype_obj->setVar( 'mime_name', $_mimetype_obj->getVar( 'mime_name' ) . '_cloned' );
        $_mimetype_obj->setNew();
        if ( !$mimetype_handler->insert( $_mimetype_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_mimetype_obj->getVar( 'mime_name' ) . '_cloned' ) );
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

    case 'save':
        $mime_id = zarilia_cleanRequestVars( $_REQUEST, 'mime_id', 0 );
        $_mimetype_obj = ( $mime_id > 0 ) ? $mimetype_handler->get( $mime_id ) : $mimetype_handler->create();
        /**
         */
        $_mimetype_obj->setVars( $_REQUEST );
        if ( !$mimetype_handler->insert( $_mimetype_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_mimetype_obj->getVar( 'mime_name' ) ) );
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

    case 'openurl':
        $file_ext = zarilia_cleanRequestVars( $_REQUEST, 'fileext' );
        $mimetype_handler->open_url( $file_ext );
        break;

    case 'delete':
        $mime_id = zarilia_cleanRequestVars( $_REQUEST, 'mime_id', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $_mimetype_obj = $mimetype_handler->get( $mime_id );
        if ( !is_object( $_mimetype_obj ) ) {
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
                        'mime_id' => $_mimetype_obj->getVar( 'mime_id' ),
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_MA_AD_WAYSYWTDTR, $_mimetype_obj->getVar( 'mime_name' ) )
                    );
                break;
            case 1:
                if ( !$mimetype_handler->delete( $_mimetype_obj ) ) {
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

    case 'savepermissions':
        $permGroup = zarilia_cleanRequestVars( $_REQUEST, 'permGroup', 0 );
        $perms = zarilia_cleanRequestVars( $_REQUEST, 'perms', array(), XOBJ_DTYPE_ARRAY );

        $gperm_handler = &zarilia_gethandler( 'groupperm' );
        $criteria = new CriteriaCompo( new Criteria( 'gperm_groupid', $permGroup ) );
        $criteria->add( new Criteria( 'gperm_modid', 1 ) );
        $criteria = new CriteriaCompo( new Criteria( 'gperm_name', 'mime_read' ) );
        $gperm_handler->deleteAll( $criteria );
        if ( isset( $perms['mime_read']['groups'] ) ) {
            foreach ( $perms['mime_read']['groups'] as $group_id => $item_ids ) {
                if ( count( $item_ids ) ) {
                    foreach ( $item_ids as $item_id => $selected ) {
                        $gperm = &$gperm_handler->create();
                        $gperm->setVar( 'gperm_groupid', $group_id );
                        $gperm->setVar( 'gperm_name', 'mime_read' );
                        $gperm->setVar( 'gperm_modid', 1 );
                        $gperm->setVar( 'gperm_itemid', $item_id );
                        $gperm_handler->insert( $gperm );
                    }
                }
            }
        }
        redirect_header( $addonversion['adminpath'] . "&amp;op=permissions", 1, _MA_AD_MIME_PERMISSIONSSAVED );
        break;

    case 'permissions':
        include_once ZAR_ROOT_PATH . '/class/zariliaform/grouppermform.php';

        $selgrp = zarilia_cleanRequestVars( $_REQUEST, 'selgrp', ZAR_GROUP_ADMIN );
        $selmod = zarilia_cleanRequestVars( $_REQUEST, 'selmod', 1 );

        $criteria = new CriteriaCompo( new Criteria( 'hasmain', 1 ) );
        $criteria->add( new Criteria( 'isactive', 1 ) );
        // $criteria->add( new Criteria( 'hasmimetype', 1 ) );
        $addon_list = &$addon_handler->getList( $criteria );
        $toponlyblock = false;
        $addon_list[1] = "System";
        $addon_list[0] = "Custom Blocks";
        natcasesort( $addon_list );

        zarilia_cp_header();
        $menu_handler->render( 4 );
        $extra[0] = "onchange=\"location='" . $addonversion['adminpath'] . "&amp;op=permissions&amp;selgrp=" . $selgrp . "&amp;selmod='+this.options[this.selectedIndex].value\" style=\"width: 90%;\"";
        /*
		*
		*/
        $member_handler = &zarilia_gethandler( 'member' );
        $group_list = &$member_handler->getGroupList();
        $extra[1] = "onchange=\"location='" . $addonversion['adminpath'] . "&amp;op=permissions&amp;selmod=" . $selmod . "&amp;selgrp='+this.options[this.selectedIndex].value\" style=\"width: 90%;\"";
        $filter_content = "
		   <div class='sidetitle'>" . _MA_AD_MIME_ADDON . "</div>
		   <div class='sidecontent'>" . zarilia_getSelection( $addon_list, $selmod, 'selmod', 1, 0 , false, "", $extra[0], 0, false ) . "</div>
		   <div class='sidetitle'>" . _MA_AD_MIME_GROUP . "</div>
		   <div class='sidecontent'>" . zarilia_getSelection( $group_list, $selgrp, 'selgrp', 1, 0 , false, "", $extra[1], 0 , false );
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_MIME_CREATE_NEW, $addonversion['adminpath'] => _MA_AD_MIME_LISTMIME ), _MD_AD_FILTER_BOX, $filter_content );

        /*
		*
		*/
        $mimetype_form = new ZariliaGroupPermForm( '', $selmod, 'mime_read', _MA_AD_MIME_CSELECTPERMISSIONS, $selgrp, $addonversion['adminpath'], $addonversion['adminpath'] . "&amp;op=savepermissions" );
        $result = $zariliaDB->Execute( "SELECT mime_id, mime_name FROM " . $zariliaDB->prefix( "mimetypes" ) );
        if ( $result->RecordCount() ) {
            while ( $cat_row = $result->FetchRow() ) {
                $mimetype_form->addItem( $cat_row['mime_id'], $cat_row['mime_name'], 0 );
            }
            echo $mimetype_form->render();
        } else {
            echo "<div><b>" . _MA_AD_WFD_PERM_CNOCATEGORY . "</b></div>";
        }
        unset ( $mimetype_form );
        break;

    case 'find':
        /**
         * find mimetype
         */
        zarilia_cp_header();
        $menu_handler->render( 3 );

        require_once ZAR_ROOT_PATH . "/class/zariliaformloader.php";
        $find_form = new ZariliaThemeForm( _MA_AD_MIME_FINDMIMETYPE, "open_url", $addonversion['adminpath'] );
        $find_form->addElement( new ZariliaFormText( _MA_AD_MIME_EXTFIND, 'fileext', 5, 60, "" ), true );
        $find_form->addElement( new ZariliaFormButton( '', 'submit', 'Open Url', 'submit' ) );
        $find_form->display();
        break;

    case 'updateall':
        $value_id = zarilia_cleanRequestVars( $_REQUEST, 'value_id', array() );
        $mime_name = zarilia_cleanRequestVars( $_REQUEST, 'mime_name', array() );
        $mime_ext = zarilia_cleanRequestVars( $_REQUEST, 'mime_ext', array() );
        $mime_display = zarilia_cleanRequestVars( $_REQUEST, 'mime_display', array() );
        foreach ( $value_id as $id => $mime_id ) {
            $_mimetype_obj = $mimetype_handler->get( $mime_id );
            if ( isset( $mime_name[$id] ) ) {
                $_mimetype_obj->setVar( 'mime_name', $mime_name[$id] );
            }
            if ( isset( $mime_ext[$id] ) ) {
                $_mimetype_obj->setVar( 'mime_ext', $mime_ext[$id] );
            }
            if ( isset( $mime_display[$id] ) ) {
                $_mimetype_obj->setVar( 'mime_display', $mime_display[$id] );
            }
            /**
             */
            if ( !$mimetype_handler->insert( $_mimetype_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_mimetype_obj->getVar( 'mime_name' ) ) );
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
        foreach ( array_keys( $checkbox ) as $mime_id ) {
            $_mimetype_obj = $mimetype_handler->get( $mime_id );
            $_mimetype_obj->setVar( 'mime_id', '' );
            $_mimetype_obj->setVar( 'mime_name', $_mimetype_obj->getVar( 'mime_name' ) . '_cloned' );
            $_mimetype_obj->setNew();
            if ( !$mimetype_handler->insert( $_mimetype_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_mimetype_obj->getVar( 'mime_name' ) ) );
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
        foreach ( array_keys( $checkbox ) as $mime_id ) {
            $_mimetype_obj = $mimetype_handler->get( $mime_id );
            if ( !$mimetype_handler->delete( $_mimetype_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_mimetype_obj->getVar( 'mime_name' ) ) );
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

    case "search";
    case 'list':
    default:
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';

        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'mime_name', XOBJ_DTYPE_TXTBOX );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 25 );
        $nav['search_by'] = zarilia_cleanRequestVars( $_REQUEST, 'search_by', '', XOBJ_DTYPE_TXTBOX );
        $nav['search_text'] = zarilia_cleanRequestVars( $_REQUEST, 'search_text', '', XOBJ_DTYPE_TXTBOX );
        $nav['mime_display'] = zarilia_cleanRequestVars( $_REQUEST, 'mime_display', 3 );
        $nav['mime_safe'] = zarilia_cleanRequestVars( $_REQUEST, 'mime_safe', 3 );

        $search_content = "
		   <form op='" . $addonversion['adminpath'] . "&amp;op=search' style='margin:0; padding:0;' method='post'>
		   <div class='sidetitle'>" . _MA_AD_MIME_SEARCH_BY . "</div>
		   <div class='sidecontent'>" . zarilia_getSelection( array( 'mime_id' => _MA_AD_MIME_ID, 'mime_name' => _MA_AD_MIME_NAME, 'mime_ext' => _MA_AD_MIME_EXT ), $nav['search_by'], 'search_by', 1, 0 , false, "", "", 0, false ) . "</div>
		   <div class='sidetitle'>" . _MA_AD_MIME_SEARCH_TEXT . "</div>
		   <div class='sidecontent'><input type='text' name='search_text' size='3' mime_id='search_text' value='' style='width: 100%'/></div>
		   <div style='text-align: right; padding-right: 10px;'><input type='submit'class='formbutton' name='mime_search' mime_id='mime_search' value='" . _SUBMIT . "' /></div>
		   </form>";

		$url = ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list";

		$safe_array = array( '3' => _MD_AD_SHOWSAFEALL_BOX, '0' => _MD_AD_SHOWSAFENOT_BOX, '1' => _MD_AD_SHOWSAFEIS_BOX );
		$search_content .= "
		 <div class='sidetitle'>" . _MD_AD_DISPLAY_BOX . "</div>
		 <div class='sidecontent'>" . zarilia_getSelection( $display_array, $nav['mime_display'], "mime_display", 1, 0, false, false, "onchange=\"location='" . $url ."&amp;limit=" . $nav['limit'] . "&amp;mime_safe=" . $nav['mime_safe'] . "&amp;mime_display='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div>
		 <div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . $url ."&amp;op=list&amp;mime_display=" . $nav['mime_display'] . "&amp;mime_safe=" . $nav['mime_safe'] . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAY_SAFE_BOX . "</div>
		 <div class='sidecontent'>" . zarilia_getSelection( $safe_array, $nav['mime_safe'], "mime_safe", 1, 0, false, false, "onchange=\"location='" . $url ."&amp;limit=" . $nav['limit'] . "&amp;mime_display=" . $nav['mime_display'] . "&amp;mime_safe='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		";

        zarilia_cp_header();
        $menu_handler->render( 1 );

        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_MIME_CREATE_NEW,
                $addonversion['adminpath'] . "&amp;op=permissions" => _MA_AD_MIME_PERMISSION
                ),
            _MD_AD_FILTER_BOX, $search_content,
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );

        $tlist = new ZariliaTList();
        $tlist->AddHeader( '#', '3%', 'center', true );
        $tlist->AddHeader( 'mime_name', '', 'left', true );
        $tlist->AddHeader( 'mime_ext', '', 'center', true );
        $tlist->AddHeader( 'mime_category', '', 'center', true );
        $tlist->AddHeader( 'mime_display', '', 'center', true );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', 'index.php', 'mimetypes' );
        $tlist->setPath( 'op=' . $op );
        $tlist->addFooter( $mimetype_handler->setSubmit( $fct ) );
        /**
         */
        $button = array( 'edit', 'delete', 'clone' );
        $_mimetype_obj = $mimetype_handler->getMimetypeObj( $nav );
        foreach ( $_mimetype_obj['list'] as $obj ) {
            $mime_id = $obj->getVar( 'mime_id' );
            // This line is required to make the boxes work correctly//
            $tlist->addHidden( $mime_id, 'value_id' );
            $tlist->add(
                array( $mime_id,
                    $obj->getTextbox( 'mime_id', 'mime_name', '50' ),
                    $obj->getTextbox( 'mime_id', 'mime_ext', '5' ),
                    $obj->mimeCategory(),
                    $obj->getYesNobox( 'mime_id', 'mime_display' ),
                    $obj->getCheckbox( 'mime_id' ),
                    zarilia_cp_icons( $button, 'mime_id', $mime_id )
                    ) );
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        $url = $addonversion['adminpath'] . '&amp;op=' . $op . '&amp;limit=' . $nav['limit'];
        if ( $nav['search_by'] ) {
            $url .= '&search_by=' . $nav['search_by'] . '&search_text=' . $nav['search_text'];
        }
		zarilia_pagnav( $_mimetype_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $url );
        break;

    case 'index':
    case 'default':
        zarilia_cp_header();
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_MIME_CREATE_NEW,
                $addonversion['adminpath'] . "&amp;op=list" => _MA_AD_MIME_LISTMIME,
                $addonversion['adminpath'] . "&amp;op=permissions" => _MA_AD_MIME_PERMISSION
                ),
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $menu_handler->render( 0 );
        break;
}
zarilia_cp_footer();
exit();

/* */
function edit_mimetype( $mime_id = 0 ) {
}

?>
