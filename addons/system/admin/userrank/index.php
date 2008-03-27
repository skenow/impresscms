<?php
// $rank_id: index.php,v 1.2 2006/09/05 09:56:28 mekdrop Exp $
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
/**
 * load the rank management functions
 */
$rank_handler = &zarilia_gethandler( 'rank' );
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
                if ( false == $rank_handler->doDatabase( $act ) ) {
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
        $rank_id = zarilia_cleanRequestVars( $_REQUEST, 'rank_id', 0 );
        $_rank_obj = ( $rank_id > 0 ) ? $rank_handler->get( $rank_id ) : $rank_handler->create();
        if ( !$_rank_obj ) {
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
        $caption = ( !$_rank_obj->isNew() ) ? $caption = sprintf( _MA_AD_MODIFYRANK, $_rank_obj->getVar( 'rank_title' ) ) : _MA_AD_CREATE;
        $form = $_rank_obj->formEdit( $caption );
        $form->display();
        break;

    /*Clone Avatar*/
    case 'clone':
        $rank_id = zarilia_cleanRequestVars( $_REQUEST, 'rank_id', 0 );
        $_rank_obj = $rank_handler->get( $rank_id );
        $_rank_obj->setVar( 'rank_id', '' );
        $_rank_obj->setVar( 'rank_title', $_rank_obj->getVar( 'rank_title' ) . '_cloned' );
        $_rank_obj->setNew();
        if ( !$rank_handler->insert( $_rank_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_rank_obj->getVar( 'rank_title' ) ) );
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
        $rank_id = zarilia_cleanRequestVars( $_REQUEST, 'rank_id', 0 );
        $_rank_obj = ( $rank_id > 0 ) ? $rank_handler->get( $rank_id ) : $rank_handler->create();
        /**
         */
        $rank_handler->setUpload( $_rank_obj );
        /**
         */
        if ( !$rank_handler->insert( $_rank_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_rank_obj->getVar( 'rank_title' ) ) );
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, ( $_rank_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;

    case 'updateall':
        $value_id = zarilia_cleanRequestVars( $_REQUEST, 'value_id', array() );
        $rank_title = zarilia_cleanRequestVars( $_REQUEST, 'rank_title', array() );
        $rank_min = zarilia_cleanRequestVars( $_REQUEST, 'rank_min', array() );
        $rank_max = zarilia_cleanRequestVars( $_REQUEST, 'rank_max', array() );
        $rank_special = zarilia_cleanRequestVars( $_REQUEST, 'rank_special', array() );
        foreach ( $value_id as $id => $rank_id ) {
            $_rank_obj = $rank_handler->get( $rank_id );
            if ( isset( $rank_title[$id] ) ) {
                $_rank_obj->setVar( 'rank_title', $rank_title[$id] );
            }
            if ( isset( $rank_min[$id] ) ) {
                $_rank_obj->setVar( 'rank_min', $rank_min[$id] );
            }
            if ( isset( $rank_max[$id] ) ) {
                $_rank_obj->setVar( 'rank_max', $rank_max[$id] );
            }
            if ( isset( $rank_special[$id] ) ) {
                $_rank_obj->setVar( 'rank_special', $rank_special[$id] );
            }
            /**
             */
            if ( !$rank_handler->insert( $_rank_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_rank_obj->getVar( 'rank_title' ) ) );
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
        unset( $_REQUEST['avatar_weight'] );
        unset( $_REQUEST['avatar_display'] );
        unset( $_REQUEST['value_id'] );
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $rank_id ) {
            $_rank_obj = $rank_handler->get( $rank_id );
            if ( !$rank_handler->delete( $_rank_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_rank_obj->getVar( 'rank_title' ) ) );
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
        unset( $_REQUEST['avatar_weight'] );
        unset( $_REQUEST['avatar_display'] );
        unset( $_REQUEST['value_id'] );
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $rank_id ) {
            $_rank_obj = $rank_handler->get( $rank_id );
            $_rank_obj->setVar( 'rank_id', '' );
            $_rank_obj->setVar( 'rank_title', $_rank_obj->getVar( 'rank_title' ) . '_cloned' );
            $_rank_obj->setVar( 'avatar_created', time() );
            $_rank_obj->setNew();
            if ( !$rank_handler->insert( $_rank_obj, false ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_rank_obj->getVar( 'rank_title' ) ) );
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
        $rank_id = zarilia_cleanRequestVars( $_REQUEST, 'rank_id', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $_rank_obj = $rank_handler->get( $rank_id );
        if ( !is_object( $_rank_obj ) ) {
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
                        'rank_id' => $_rank_obj->getVar( 'rank_id' ),
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_AM_WAYSYWTDTR, $_rank_obj->getVar( 'rank_title' ) )
                    );
                break;
            case 1:
                $image = $_rank_obj->getVar( 'rank_image' );
                if ( !$rank_handler->delete( $_rank_obj ) ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                                $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    //  @unlink ( ZAR_UPLOAD_PATH . DIRECTORY_SEPARATOR . $image; )
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
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'rank_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );

        zarilia_cp_header();
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_CREATERANK ) );
        $menu_handler->render( 1 );

        $display = zarilia_cleanRequestVars( $_REQUEST, 'display', 3 );
        $display_array = array( '3' => _MD_AD_SHOWALL_BOX, '0' => _MD_AD_SHOWHIDDEN_BOX, '1' => _MD_AD_SHOWVISIBLE_BOX );
        $list_array = array( 5 => "5", 10 => "10", 15 => "15", 25 => "25", 50 => "50" );
		$type = 0;
        $form = "
		 <div class='sidetitle'>" . _MD_AD_DISPLAY_BOX . "</div>
		 <div class='sidecontent'>" . zarilia_getSelection( $display_array, $type, "type", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list&amp;limit=" . $nav['limit'] . "&amp;type='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div>
		 <div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list&amp;type=" . $type . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_CREATE ),
            _MD_AD_FILTER_BOX, $form
            );

        /**
         */
        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'rank_id', '5%', 'center', false );
        $tlist->AddHeader( 'rank_title', '25%', 'left', true );
        $tlist->AddHeader( 'rank_min', '', 'center', true );
        $tlist->AddHeader( 'rank_max', '', 'center', true );
        $tlist->AddHeader( 'rank_special', '', 'center', true );
        $tlist->AddHeader( 'rank_image', '', 'center', true );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'] . '&amp;op=' . $op, 'rank' );
        $tlist->setPath( 'op=' . $op );
        $tlist->addFooter( $rank_handler->setSubmit( $fct ) );

        $button = array( 'edit', 'delete', 'clone' );
        $_rank_obj = $rank_handler->getRankObj( $nav, $display );
        foreach ( $_rank_obj['list'] as $obj ) {
            $rank_id = $obj->getVar( 'rank_id' );
            $tlist->addHidden( $rank_id, 'value_id' );
            $tlist->add(
                array( $rank_id,
                    $obj->getTextbox( 'rank_id', 'rank_title', '35' ),
                    $obj->getTextbox( 'rank_id', 'rank_min', '5' ),
                    $obj->getTextbox( 'rank_id', 'rank_max', '5' ),
                    $obj->getYesNobox( 'rank_id', 'rank_special' ),
                    $obj->getRankImage(),
                    $obj->getCheckbox( 'rank_id' ),
                    zarilia_cp_icons( $button, 'rank_id', $rank_id )
                    ) );
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_rank_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op );
        break;

    case 'index':
    case 'default':
        zarilia_cp_header();
        $menu_handler->render( 0 );
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_CREATE ),
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        break;
}
zarilia_cp_footer();

?>