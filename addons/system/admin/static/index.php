<?php
// $Id: index.php,v 1.4 2007/05/05 11:10:32 catzwolf Exp $
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
include_once ZAR_ROOT_PATH . "/class/class.permissions.php";

$perm_handler = &zarilia_gethandler( 'groupperm' );
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

    case 'edit':
        $section_handler = &zarilia_gethandler( 'section' );
        $category_handler = &zarilia_gethandler( 'category' );

        $content_id = zarilia_cleanRequestVars( $_REQUEST, 'content_id', 0 );
        $_content_obj = ( $content_id > 0 ) ? $content_handler->get( $content_id ) : $content_handler->create();
        $_content_obj->setVar('content_type', 'static');
		if ( !$_content_obj ) {
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
            _MD_AD_ACTION_BOX, $op_url
            );
        $form = $_content_obj->formEdit( 'static' );
        break;

    case 'clone':
        $content_id = zarilia_cleanRequestVars( $_REQUEST, 'content_id', 0 );
        $_content_obj = $content_handler->get( $content_id );
        $_content_obj->setVar( 'content_id', '' );
        $_content_obj->setVar( 'content_title', $_content_obj->getVar( 'content_title' ) . '_cloned' );
        $_content_obj->setVar( 'content_published', time() );
        $_content_obj->setNew();
        if ( !$content_handler->insert( $_content_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_content_obj->getVar( 'content_title' ) ) );
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, ( $_content_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;

    case 'save':
        $content_id = zarilia_cleanRequestVars( $_REQUEST, 'content_id', 0 );
        $_content_obj = ( $content_id > 0 ) ? $content_handler->get( $content_id ) : $content_handler->create();
        $_content_obj->setVars( $_REQUEST );

        /**
         */
        $opt = zarilia_cleanRequestVars( $_REQUEST, 'content_opt', 0 );
        switch ( $opt ) {
            case 0:
            default:
                $content_approved = ( isset( $_REQUEST['content_approved'] ) ) ? 1 : 0;
                $_content_obj->setVar( 'content_approved', $content_approved );

                $content_display = ( isset( $_REQUEST['content_display'] ) ) ? 1 : 0;
                $_content_obj->setVar( 'content_display', $content_display );

                if ( $_content_obj->isNew() ) {
                    $_content_obj->setVar( 'content_created', time() );
                }
                $content_published = ( $content_approved ) ? time() : null;
                $_content_obj->setVar( 'content_published', $content_published );
                break;

            case 1:
                $_content_created = zarilia_cleanRequestVars( $_REQUEST, 'content_created', '' );
                $_content_published = zarilia_cleanRequestVars( $_REQUEST, 'content_published', '' );
                $_content_expired = zarilia_cleanRequestVars( $_REQUEST, 'content_expired', '' );
                if ( $_content_created > $_content_published ) {
                    $_content_published = $_content_created;
                }
                if ( !$_content_obj->isNew() ) {
                    $_content_updated = zarilia_cleanRequestVars( $_REQUEST, 'content_updated', time() );
                    $_content_obj->setVar( 'content_updated', strtotime( $_content_updated ) );
                }
                $_content_obj->setVar( 'content_created', strtotime( $_content_created ) );
                $_content_obj->setVar( 'content_published', strtotime( $_content_published ) );
                $_content_obj->setVar( 'content_expired', strtotime( $_content_expired ) );

                if ( !strtotime( $_content_published ) ) {
                    $_content_obj->setVar( 'content_approved', 0 );
                }
                break;
            case 2:
                break;
            case 3:
                break;
            case 4:
                break;
        } // switch
        /*
        if ( $_content_obj->isNew() ) {
            $_content_obj->setVar( 'content_created', time() );
            if ( isset( $_REQUEST['content_approved'] ) && intval( $_REQUEST['content_approved'] ) == 1 ) {
                $_content_obj->setVar( 'content_approved', 1 );
                $_content_obj->setVar( 'content_display', 1 );
                $_content_obj->setVar( 'content_published', time() );
            } else {
                $_content_obj->setVar( 'content_approved', 0 );
                $_content_obj->setVar( 'content_display', 0 );
            }

            $content_approved = ( isset( $_REQUEST['content_approved'] ) ) ? 1 : 0;
            $_content_obj->setVar( 'content_approved', $content_approved );

            $content_published = ( $content_approved ) ? time() : null;
            $_content_obj->setVar( 'content_published', $content_published );

            $content_display = ( isset( $_REQUEST['content_display'] ) ) ? 1 : 0;
            $_content_obj->setVar( 'content_display', $content_display );
        } else {
            $_content_created = ( isset( $_REQUEST['content_created'] ) && $_REQUEST['content_created'] != '' ) ? strtotime( $_REQUEST['content_created'] ) : '';
            $_content_published = ( isset( $_REQUEST['content_published'] ) && $_REQUEST['content_published'] != '' ) ? strtotime( $_REQUEST['content_published'] ) : '';
            $_content_updated = ( isset( $_REQUEST['content_updated'] ) && $_REQUEST['content_updated'] != '' ) ? strtotime( $_REQUEST['content_updated'] ) : time();

            if ( $_content_created > $_content_published ) {
                $_content_published = $_content_created;
            }
            $_content_obj->setVar( 'content_created', $_content_created );
            $_content_obj->setVar( 'content_published', $_content_published );
            $_content_obj->setVar( 'content_updated', $_content_updated );

            $content_expired = ( isset( $_REQUEST['content_expired'] ) && !empty( $_REQUEST['content_expired'] ) ) ? $_REQUEST['content_expired'] : '';
            $_content_obj->setVar( 'content_expired', $content_expired );

            if ( isset( $_REQUEST['is_hidden'] ) ) {
                $content_approved = ( isset( $_REQUEST['content_approved'] ) ) ? 1 : 0;
                $_content_obj->setVar( 'content_approved', $content_approved );

                $content_display = ( isset( $_REQUEST['content_display'] ) ) ? 1 : 0;
                $_content_obj->setVar( 'content_display', $content_display );
            }
        }
*/

        /**
         */
        if ( !$content_handler->insert( $_content_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_content_obj->getVar( 'content_title' ) ) );
        } else {
            $content_id = $_content_obj->getVar( 'content_id' );
            $mod_id = $zariliaAddon->getVar( 'mid' );
            $perm_handler = &zarilia_gethandler( 'groupperm' );
            $read_array = zarilia_cleanRequestVars( $_REQUEST, 'readgroup', array(), XOBJ_DTYPE_ARRAY );
            $readgroup = new cpPermission( '', 'content_read', '', $mod_id );
            $readgroup->cpPermission_save( $read_array, $content_id );
            $write_array = zarilia_cleanRequestVars( $_REQUEST, 'writegroup', array(), XOBJ_DTYPE_ARRAY );
            $writegroup = new cpPermission( '', 'content_write', '', $mod_id );
            $writegroup->cpPermission_save( $write_array, $content_id );

            /**
             */
            $menus_handler = &zarilia_gethandler( 'menus' );
            $_menu_obj = $menus_handler->getMenuItem( 1, 'static', $_content_obj->getVar( 'content_id' ) );
            if ( $_menu_obj ) {
                $_menu_obj->setVar( 'menu_display', 0 );
                if ( $_content_obj->getVar( 'content_approved' ) && $_content_obj->getVar( 'content_display' ) && $_content_obj->getVar( 'content_published' ) ) {
                    $_menu_obj->setVar( 'menu_display', 1 );
                }
            } else {
                $_menu_obj = $menus_handler->create();
                $_menu_obj->setVar( 'menu_title', $_content_obj->getVar( 'content_title', 'e' ) );
                $_menu_obj->setVar( 'menu_type', 'mainmenu' );
                $_menu_obj->setVar( 'menu_mid', 1 );
                $_menu_obj->setVar( 'menu_name', 'static' );
                $_menu_obj->setVar( 'menu_sectionid', $_content_obj->getVar( 'content_id' ) );
                $_menu_obj->setVar( 'menu_link', '{X_SITEURL}/index.php?page_type=static&id=' . $_content_obj->getVar( 'content_id' ) );
                $_menu_obj->setVar( 'menu_display', 1 );
            }

            if ( !$menus_handler->insert( $_menu_obj ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_menu_obj->getVar( 'menu_title' ) ) );
                continue;
            } else {
                $readgroup = new cpPermission( '', 'menu_read', '', 1 );
                $readgroup->cpPermission_save( $read_array, $_menu_obj->getVar( 'menu_id' ) );
            }
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            if ( $_content_obj->isNew() ) {
                $url = $addonversion['adminpath'] . "&op=edit&content_id=" . $_content_obj->getVar( 'content_id' ) . "&opt=1";
            } else {
                $url = $addonversion['adminpath'] . "&amp;op=list";
            }
            redirect_header( $url, 1, ( $_content_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;

    case 'delete':
        $content_id = zarilia_cleanRequestVars( $_REQUEST, 'content_id', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $_content_obj = $content_handler->get( $content_id );
        if ( !is_object( $_content_obj ) ) {
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
                        ), $addonversion['adminpath'], sprintf( _MD_AM_WAYSYWTDTR, $_content_obj->getVar( 'content_title' ) )
                    );
                break;
            case 1:
                if ( !$content_handler->delete( $_content_obj ) ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                                $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    $menus_handler = &zarilia_gethandler( 'menus' );
                    $menus_handler->deleteMenuItem( 1, 'static', $content_id );
                    redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
                }
                break;
        } // switch
        break;

    case 'updateall':
        $value_id = zarilia_cleanRequestVars( $_REQUEST, 'value_id', array() );
        $content_title = zarilia_cleanRequestVars( $_REQUEST, 'content_title', array() );
        $content_weight = zarilia_cleanRequestVars( $_REQUEST, 'content_weight', array() );
        $content_display = zarilia_cleanRequestVars( $_REQUEST, 'content_display', array() );
        foreach ( $value_id as $id => $content_id ) {
            $_content_obj = $content_handler->get( $content_id );
            if ( isset( $content_title[$id] ) ) {
                $_content_obj->setVar( 'content_title', $content_title[$id] );
            }
            if ( isset( $content_weight[$id] ) ) {
                $_content_obj->setVar( 'content_weight', $content_weight[$id] );
            }
            if ( isset( $content_display[$id] ) ) {
                $_content_obj->setVar( 'content_display', $content_display[$id] );
            }
            /**
             */
            if ( !$content_handler->insert( $_content_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_content_obj->getVar( 'content_title' ) ) );
            } else {
                $menus_handler = &zarilia_gethandler( 'menus' );
                $_menu_obj = $menus_handler->getMenuItem( 1, 'static', $_content_obj->getVar( 'content_id' ) );
                if ( $_menu_obj ) {
                    $_menu_obj->setVar( 'menu_display', 0 );
                    if ( $_content_obj->getVar( 'content_approved' ) && $_content_obj->getVar( 'content_display' ) && $_content_obj->getVar( 'content_published' ) ) {
                        $_menu_obj->setVar( 'menu_display', 1 );
                    }
                    if ( !$menus_handler->insert( $_menu_obj ) ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_menu_obj->getVar( 'menu_title' ) ) );
                        continue;
                    }
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

    case 'cloneall':
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $content_id ) {
            $_content_obj = $content_handler->get( $content_id );
            $_content_obj->setVar( 'content_id', '' );
            $_content_obj->setVar( 'content_title', $_content_obj->getVar( 'content_title' ) . '_cloned' );
            $_content_obj->setVar( 'content_published', time() );
            $_content_obj->setNew();
            if ( !$content_handler->insert( $_content_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_content_obj->getVar( 'content_title' ) ) );
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
            $_content_obj = $content_handler->get( $content_id );
            if ( !$content_handler->delete( $_content_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_content_obj->getVar( 'content_title' ) ) );
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
        $nav['content_type'] = zarilia_cleanRequestVars( $_REQUEST, 'content_type', 'static' );
        $nav['content_id'] = zarilia_cleanRequestVars( $_REQUEST, 'content_id', 0 );
        $nav['content_display'] = zarilia_cleanRequestVars( $_REQUEST, 'content_display', 3 );

		$url = ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list";
        $form = "
		 <div class='sidetitle'>" . _MD_AD_DISPLAY_BOX . "</div>
		 <div class='sidecontent'>" . zarilia_getSelection( $display_array, $nav['content_display'], "content_display", 1, 0, false, false, "onchange=\"location='" . $url . "&amp;limit=" . $nav['limit'] . "&amp;content_display='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div>
		 <div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . $url . "&amp;op=list&amp;content_display=" . $nav['content_display'] . "&amp;&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";

		//**//
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
        $tlist->AddHeader( 'content_display', '', 'center', 1 );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'], 'content' );
        $tlist->addFooter( $content_handler->setSubmit( $fct ) );
        $tlist->setPath( 'op=' . $op );
        $button = array( 'edit', 'delete', 'clone' );
        $_content_obj = $content_handler->getContentObj( $nav, true );
        if ( $_content_obj['count'] ) {
            foreach ( $_content_obj['list'] as $obj ) {
                $content_id = $obj->getVar( 'content_id' );
                // This line is required to make the boxes work correctly//
                $tlist->addHidden( $content_id, 'value_id' );
                $tlist->add(
                    array( $content_id,
                        $obj->getTextbox( 'content_id', 'content_title', '50' ),
                        $obj->getVar( 'content_published' ),
                        $obj->getTextbox( 'content_id', 'content_weight', '5' ),
                        $obj->getYesNobox( 'content_id', 'content_display' ),
                        $obj->getCheckbox( 'content_id' ),
                        zarilia_cp_icons( $button, 'content_id', $content_id )
                        ) );
            }
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_content_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op . '&amp;limit=' . $nav['limit'] );
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