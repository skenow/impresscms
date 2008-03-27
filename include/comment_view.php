<?php
// $Id: comment_view.php,v 1.2 2007/04/21 09:44:16 catzwolf Exp $
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
if ( !defined( 'ZAR_ROOT_PATH' ) || !is_object( $zariliaAddon ) ) {
    exit();
}
include_once ZAR_ROOT_PATH . '/include/comment_constants.php';
include_once ZAR_ROOT_PATH . '/addons/system/constants.php';

if ( ZAR_COMMENT_APPROVENONE != $zariliaAddonConfig['com_rule'] ) {
    $gperm_handler = &zarilia_gethandler( 'groupperm' );
    $groups = ( $zariliaUser ) ? $zariliaUser->getGroups() : ZAR_GROUP_ANONYMOUS;
    $zariliaTpl->assign( 'zarilia_iscommentadmin', $gperm_handler->checkRight( 'system_admin', ZAR_SYSTEM_COMMENT, $groups ) );

    include_once ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/comment.php';
    $comment_config = $zariliaAddon->getInfo( 'comments' );
    $com_itemid = ( trim( $comment_config['itemName'] ) != '' && isset( $_GET[$comment_config['itemName']] ) ) ? floatval( $_GET[$comment_config['itemName']] ) : 0;

	
    if ( $com_itemid > 0 ) {
		
		global $zariliaUser, $zariliaConfig;
		if (is_object( $zariliaUser )) {
			$com_mode = zarilia_cleanRequestVars( $_REQUEST, 'com_mode', ($zariliaUser->getVar( 'umode' )=='')?$zariliaConfig['com_mode']:$zariliaUser->getVar( 'umode' ), XOBJ_DTYPE_TXTBOX );
			$com_order = zarilia_cleanRequestVars( $_REQUEST, 'com_order', ($zariliaUser->getVar( 'uorder' )=='')?$zariliaConfig['uorder']:$zariliaUser->getVar( 'uorder' ), XOBJ_DTYPE_INT );
		} else {
			$com_mode = zarilia_cleanRequestVars( $_REQUEST, 'com_mode', $zariliaConfig['com_mode'], XOBJ_DTYPE_TXTBOX );
			$com_order = zarilia_cleanRequestVars( $_REQUEST, 'com_order', @$zariliaConfig['uorder'], XOBJ_DTYPE_INT );
		}

//		trigger_error($com_mode, E_USER_ERROR);


        $zariliaTpl->assign( 'comment_mode', $com_mode );
        if ( $com_order != ZAR_COMMENT_OLD1ST ) {
            $zariliaTpl->assign( array( 'comment_order' => ZAR_COMMENT_NEW1ST, 'order_other' => ZAR_COMMENT_OLD1ST ) );
            $com_dborder = 'DESC';
        } else {
            $zariliaTpl->assign( array( 'comment_order' => ZAR_COMMENT_OLD1ST, 'order_other' => ZAR_COMMENT_NEW1ST ) );
            $com_dborder = 'ASC';
        }
        // admins can view all comments and IPs, others can only view approved(active) comments
        if ( is_object( $zariliaUser ) && $zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) ) {
            $admin_view = true;
        } else {
            $admin_view = false;
        }

        $com_id = isset( $_GET['com_id'] ) ? intval( $_GET['com_id'] ) : 0;
        $com_rootid = isset( $_GET['com_rootid'] ) ? intval( $_GET['com_rootid'] ) : 0;
        $comment_handler = &zarilia_gethandler( 'comment' );
        if ( $com_mode == 'flat' ) {
            $comments = &$comment_handler->getByItemId( $zariliaAddon->getVar( 'mid' ), $com_itemid, $com_dborder );
            include_once ZAR_ROOT_PATH . '/class/commentrenderer.php';
            $renderer = &ZariliaCommentRenderer::instance( $zariliaTpl );
            $renderer->setComments( $comments );
            $renderer->renderFlatView( $admin_view );
        } elseif ( $com_mode == 'thread' ) {
            // RMV-FIX... added extraParam stuff here
            $comment_url = $comment_config['pageName'] . '?';
            if ( isset( $comment_config['extraParams'] ) && is_array( $comment_config['extraParams'] ) ) {
                $extra_params = '';
                foreach ( $comment_config['extraParams'] as $extra_param ) {
                    // This page is included in the addon hosting page -- param could be from anywhere
                    if ( isset( ${$extra_param} ) ) {
                        $extra_params .= $extra_param . '=' . ${$extra_param} . '&amp;';
                    } elseif ( isset( $_POST[$extra_param] ) ) {
                        $extra_params .= $extra_param . '=' . $_POST[$extra_param] . '&amp;';
                    } elseif ( isset( $_GET[$extra_param] ) ) {
                        $extra_params .= $extra_param . '=' . $_GET[$extra_param] . '&amp;';
                    } else {
                        $extra_params .= $extra_param . '=&amp;';
                    }
                    // $extra_params .= isset(${$extra_param}) ? $extra_param .'='.${$extra_param}.'&amp;' : $extra_param .'=&amp;';
                }
                $comment_url .= $extra_params;
            }
            $zariliaTpl->assign( 'comment_url', $comment_url . $comment_config['itemName'] . '=' . $com_itemid . '&amp;com_mode=thread&amp;com_order=' . $com_order );
            if ( !empty( $com_id ) && !empty( $com_rootid ) && ( $com_id != $com_rootid ) ) {
                // Show specific thread tree
                $comments = &$comment_handler->getThread( $com_rootid, $com_id );
                if ( false != $comments ) {
                    include_once ZAR_ROOT_PATH . '/class/commentrenderer.php';
                    $renderer = &ZariliaCommentRenderer::instance( $zariliaTpl );
                    $renderer->setComments( $comments );
                    $renderer->renderThreadView( $com_id, $admin_view );
                }
            } else {
                // Show all threads
                $top_comments = &$comment_handler->getTopComments( $zariliaAddon->getVar( 'mid' ), $com_itemid, $com_dborder );
                $c_count = count( $top_comments );
                if ( $c_count > 0 ) {
                    for ( $i = 0; $i < $c_count; $i++ ) {
                        $comments = &$comment_handler->getThread( $top_comments[$i]->getVar( 'com_rootid' ), $top_comments[$i]->getVar( 'com_id' ) );
                        if ( false != $comments ) {
                            include_once ZAR_ROOT_PATH . '/class/commentrenderer.php';
                            $renderer = &ZariliaCommentRenderer::instance( $zariliaTpl );
                            $renderer->setComments( $comments );
                            $renderer->renderThreadView( $top_comments[$i]->getVar( 'com_id' ), $admin_view );
                        }
                        unset( $comments );
                    }
                }
            }
        } else {
            // Show all threads
            $top_comments = &$comment_handler->getTopComments( $zariliaAddon->getVar( 'mid' ), $com_itemid, $com_dborder );
            $c_count = count( $top_comments );
            if ( $c_count > 0 ) {
                for ( $i = 0; $i < $c_count; $i++ ) {
                    $comments = &$comment_handler->getThread( $top_comments[$i]->getVar( 'com_rootid' ), $top_comments[$i]->getVar( 'com_id' ) );
                    include_once ZAR_ROOT_PATH . '/class/commentrenderer.php';
                    $renderer = &ZariliaCommentRenderer::instance( $zariliaTpl );
                    $renderer->setComments( $comments );
                    $renderer->renderNestView( $top_comments[$i]->getVar( 'com_id' ), $admin_view );
                }
            }
        }
        // assign comment nav bar
        $navbar = '
<form method="get" action="' . $comment_config['pageName'] . '">
<table width="95%" class="outer" cellspacing="1">
  <tr>
    <td class="even" align="center"><select name="com_mode"><option value="flat"';
        if ( $com_mode == 'flat' ) {
            $navbar .= ' selected="selected"';
        }
        $navbar .= '>' . _FLAT . '</option><option value="thread"';
        if ( $com_mode == 'thread' || $com_mode == '' ) {
            $navbar .= ' selected="selected"';
        }
        $navbar .= '>' . _THREADED . '</option><option value="nest"';
        if ( $com_mode == 'nest' ) {
            $navbar .= ' selected="selected"';
        }
        $navbar .= '>' . _NESTED . '</option></select> <select name="com_order"><option value="' . ZAR_COMMENT_OLD1ST . '"';
        if ( $com_order == ZAR_COMMENT_OLD1ST ) {
            $navbar .= ' selected="selected"';
        }
        $navbar .= '>' . _OLDESTFIRST . '</option><option value="' . ZAR_COMMENT_NEW1ST . '"';
        if ( $com_order == ZAR_COMMENT_NEW1ST ) {
            $navbar .= ' selected="selected"';
        }
        unset( $postcomment_link );
        $navbar .= '>' . _NEWESTFIRST . '</option></select><input type="hidden" name="' . $comment_config['itemName'] . '" value="' . $com_itemid . '" /> <input type="submit" value="' . _CM_REFRESH . '" class="formButton" />';
        if ( !empty( $zariliaAddonConfig['com_anonpost'] ) || is_object( $zariliaUser ) ) {
            $postcomment_link = 'comment_new.php?com_itemid=' . $com_itemid . '&amp;com_order=' . $com_order . '&amp;com_mode=' . $com_mode;

            $zariliaTpl->assign( 'anon_canpost', true );
        }
        $link_extra = '';
        if ( isset( $comment_config['extraParams'] ) && is_array( $comment_config['extraParams'] ) ) {
            foreach ( $comment_config['extraParams'] as $extra_param ) {
                if ( isset( ${$extra_param} ) ) {
                    $link_extra .= '&amp;' . $extra_param . '=' . ${$extra_param};
                    $hidden_value = htmlspecialchars( ${$extra_param}, ENT_QUOTES );
                    $extra_param_val = ${$extra_param};
                } elseif ( isset( $_POST[$extra_param] ) ) {
                    $extra_param_val = $_POST[$extra_param];
                } elseif ( isset( $_GET[$extra_param] ) ) {
                    $extra_param_val = $_GET[$extra_param];
                }
                if ( isset( $extra_param_val ) ) {
                    $link_extra .= '&amp;' . $extra_param . '=' . $extra_param_val;
                    $hidden_value = htmlspecialchars( $extra_param_val, ENT_QUOTES );
                    $navbar .= '<input type="hidden" name="' . $extra_param . '" value="' . $hidden_value . '" />';
                }
            }
        }
        if ( isset( $postcomment_link ) ) {
            $navbar .= '&nbsp;<input type="button" onclick="self.location.href=\'' . $postcomment_link . '' . $link_extra . '\'" class="formButton" value="' . _CM_POSTCOMMENT . '" />';
        }
        $navbar .= '
    </td>
  </tr>
</table>
</form>';
        $zariliaTpl->assign( array( 'commentsnav' => $navbar, 'editcomment_link' => 'comment_edit.php?com_itemid=' . $com_itemid . '&amp;com_order=' . $com_order . '&amp;com_mode=' . $com_mode . '' . $link_extra, 'deletecomment_link' => 'comment_delete.php?com_itemid=' . $com_itemid . '&amp;com_order=' . $com_order . '&amp;com_mode=' . $com_mode . '' . $link_extra, 'replycomment_link' => 'comment_reply.php?com_itemid=' . $com_itemid . '&amp;com_order=' . $com_order . '&amp;com_mode=' . $com_mode . '' . $link_extra ) );
        // assign some lang variables
        $zariliaTpl->assign( array( 'lang_from' => _CM_FROM, 'lang_joined' => _CM_JOINED, 'lang_posts' => _CM_POSTS, 'lang_poster' => _CM_POSTER, 'lang_thread' => _CM_THREAD, 'lang_edit' => _EDIT, 'lang_delete' => _DELETE, 'lang_reply' => _REPLY, 'lang_subject' => _CM_REPLIES, 'lang_posted' => _CM_POSTED, 'lang_updated' => _CM_UPDATED, 'lang_notice' => _CM_NOTICE ) );
    }
}

?>