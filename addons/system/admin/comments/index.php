<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:02 catzwolf Exp $
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
require_once "admin_menu.php";

$comment_handler = &zarilia_gethandler( 'comment' );
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
                if ( false == $category_handler->doDatabase( $act ) ) {
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
        $menu_handler->render( 2 );
        require_once( ZAR_ROOT_PATH . "/class/class.about.php" );
        $zarilia_about = new ZariliaAbout();
        $zarilia_about->display();
        break;

    case 'edit':
    case 'create':
        zarilia_cp_header();
        $menu_handler->render( 2 );
        require_once ZAR_ROOT_PATH . '/include/comment_edit.php';
        break;

    case 'post':
        zarilia_cp_header();
        $menu_handler->render( 2 );
        require_once ZAR_ROOT_PATH . '/include/comment_post.php';
        break;

    case 'delete':
        zarilia_cp_header();
        $menu_handler->render( 2 );
        require_once ZAR_ROOT_PATH . '/include/comment_delete.php';
        break;

    case 'jump':
        $com_id = ( isset( $_GET['com_id'] ) ) ? intval( $_GET['com_id'] ) : 0;
        if ( $com_id > 0 ) {
            $comment = &$comment_handler->get( $com_id );
            if ( is_object( $comment ) ) {
                $addon_handler = &zarilia_gethandler( 'addon' );
                $addon = &$addon_handler->get( $comment->getVar( 'com_modid' ) );
                $comment_config = $addon->getInfo( 'comments' );
                header( 'Location: ' . ZAR_URL . '/addons/' . $addon->getVar( 'dirname' ) . '/' . $comment_config['pageName'] . '?' . $comment_config['itemName'] . '=' . $comment->getVar( 'com_itemid' ) . '&com_id=' . $comment->getVar( 'com_id' ) . '&com_rootid=' . $comment->getVar( 'com_rootid' ) . '&com_mode=thread&' . str_replace( '&amp;', '&', $comment->getVar( 'com_exparams' ) ) . '#comment' . $comment->getVar( 'com_id' ) );
                exit();
            }
        }
        redirect_header( $addonversion['adminpath'], 1 );
        break;

    case 'list':
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        include_once ZAR_ROOT_PATH . '/include/comment_constants.php';
        include_once ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/comment.php';

        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'com_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
        $com_modid = zarilia_cleanRequestVars( $_REQUEST, 'com_modid', 0 );
        $status = zarilia_cleanRequestVars( $_REQUEST, 'status', 0 );

        $limit_array = array( 10, 20, 50, 100 );

        $status_array = array( ZAR_COMMENT_PENDING => _CM_PENDING, ZAR_COMMENT_ACTIVE => _CM_ACTIVE, ZAR_COMMENT_HIDDEN => _CM_HIDDEN );
        $status_array2 = array(
            ZAR_COMMENT_PENDING => '<span style="text-decoration: none; font-weight: bold; color: #00ff00;">' . _CM_PENDING . '</span>',
            ZAR_COMMENT_ACTIVE => '<span style="text-decoration: none; font-weight: bold; color: #ff0000;">' . _CM_ACTIVE . '</span>',
            ZAR_COMMENT_HIDDEN => '<span style="text-decoration: none; font-weight: bold; color: #0000ff;">' . _CM_HIDDEN . '</span>'
            );

        $addon_array = &$addon_handler->getList( new Criteria( 'hascomments', 1 ) );

        $form = '<form action="index.php" method="get">';
        $form .= '<div class="sidetitle">Addons:</div>';
        $form .= '<div class="sidecontent"><select name="addon" style="width: 90%">';
        $addon_array[0] = _MD_AM_ALLMODS;
        foreach ( $addon_array as $k => $v ) {
            $sel = '';
            if ( $k == $com_modid ) {
                $sel = ' selected="selected"';
            }
            $form .= '<option value="' . $k . '"' . $sel . '>' . $v . '</option>';
        }
        $form .= '</select>
		 </div>
		 <div class="sidetitle">Status:</div>
		 <div class="sidecontent">
		<select name="status"  style="width: 90%">';
        $status_array[0] = _MD_AM_ALLSTATUS;
        foreach ( $status_array as $k => $v ) {
            $sel = '';
            if ( isset( $status ) && $k == $status ) {
                $sel = ' selected="selected"';
            }
            $form .= '<option value="' . $k . '"' . $sel . '>' . $v . '</option>';
        }
        $form .= '</select></div>
		<div class="sidetitle">Limit:</div>
		<div class="sidecontent"><select name="limit" style="width: 90%">';
        foreach ( $limit_array as $k ) {
            $sel = '';
            if ( isset( $limit ) && $k == $limit ) {
                $sel = ' selected="selected"';
            }
            $form .= '<option value="' . $k . '"' . $sel . '>' . $k . '</option>';
        }
        $form .= '</select>
			</div>
			<div class="sidecontent" align="right">
				<input type="hidden" name="fct" value="' . $fct . '" />
				<input type="hidden" name="op" value="' . $op . '" />
				<input type="submit" class="formbutton" value="' . _SUBMIT . '" name="selsubmit" />
			</div>
		   </form>';
        /**
         */
        zarilia_cp_header();
        zarilia_admin_menu( _MD_AD_FILTER_BOX, $form );
        $menu_handler->render( 1 );

        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'com_id', '5%', 'center', false );
        $tlist->AddHeader( 'com_icon', '10%', 'center', true );
        $tlist->AddHeader( 'com_title', '10%', 'center', true );
        $tlist->AddHeader( 'com_created', '', 'left', true );
        $tlist->AddHeader( 'com_poster', '', 'center', true );
        $tlist->AddHeader( 'com_ip', '', 'center', true );
        $tlist->AddHeader( 'com_modid', '', 'center', true );
        $tlist->AddHeader( 'com_status', '', 'center', true );
        $tlist->AddHeader( 'action', '', 'center', false );
        $tlist->setPrefix( '_CM_' );
        $tlist->setPath( 'op=' . $op );

        $button = array( 'edit', 'delete' );
        $comments = &$comment_handler->getCommentObj( $nav, $status, $com_modid );
        foreach ( $comments['list'] as $obj ) {
            $com_id = $obj->getVar( 'com_id' );
            $tlist->add(
                array( $com_id,
                    $icon = ( $obj->getVar( 'com_icon' ) != '' ) ? '<img src="' . ZAR_URL . '/images/subject/' . $obj->getVar( 'com_icon' ) . '" alt="" />' : '<img src="' . ZAR_URL . '/images/icons/no_posticon.gif" alt="" />', '<a href="index.php?fct=comments&amp;op=jump&amp;com_id=' . $com_id . '">' . $obj->getVar( 'com_title' ) . '</a>',
                    $obj->getVar( 'com_created' ),
                    $obj->getLinkedUserName(),
                    $obj->getVar( 'com_ip' ),
                    $addon_array[$obj->getVar( 'com_modid' )],
                    $status_array2[$obj->getVar( 'com_status' )],
                    zarilia_cp_icons( $button, 'com_id', $com_id )
                    ) );
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $comments['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op );
        break;

    case 'index':
    case 'default':
        zarilia_cp_header();
        zarilia_admin_menu(
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $menu_handler->render( 0 );
        break;
}
zarilia_cp_footer();

?>