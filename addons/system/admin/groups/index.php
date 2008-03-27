<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:25 catzwolf Exp $
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
require_once ZAR_ROOT_PATH . '/class/zariliablock.php';

$_callback = &zarilia_gethandler( 'group' );
$do_callback = ZariliaCallback::getSingleton();
$do_callback->setCallback( $_callback );

$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'list' );
$g_id = zarilia_cleanRequestVars( $_REQUEST, 'g_id', 0 );
if ( !empty( $_REQUEST['memberslist_id'] ) && is_array( $_REQUEST['memberslist_id'] ) ) {
    $op = 'addUser';
    $_REQUEST['uids'] = $_REQUEST['memberslist_id'];
}
switch ( $op ) {
    case 'help':
    case 'about':
        $do_callback->setmenu( 2 );
        call_user_func( array( $do_callback, $op ) );
        break;

    case 'edit':
    case 'create':
    case 'clone':
        $clonegroup = zarilia_cleanRequestVars( $_REQUEST, 'clonegroup', 0 );
        $op_value = ( $g_id ) ? 'update' : 'add';
        $form_title = ( $g_id ) ? _MA_AD_EDITADG : _MA_AD_CREATENEWADG;
        if ( $op == 'clone' ) {
            $submit_value = _MA_AD_CLONE;
            $form_title = _MA_AD_CLONEADG;
            $clonegroup = 1;
        }

        $_function = ( $g_id > 0 ) ? 'get': 'create';
        $_obj = call_user_func( array( $_callback, $_function ), $g_id );
        $name_value = ( $clonegroup == 0 ) ? $_obj->getVar( 'name', 'e' ) : '';
        $desc_value = ( $clonegroup == 0 ) ? $_obj->getVar( 'description', 'e' ) : '';

        $addonperm_handler = &zarilia_gethandler( 'groupperm' );
        $a_mod_value = call_user_func( array( $addonperm_handler, 'getItemIds' ), 'addon_admin', $_obj->getVar( 'groupid' ) );
        $r_mod_value = call_user_func( array( $addonperm_handler, 'getItemIds' ), 'addon_read', $_obj->getVar( 'groupid' ) );

        /*
		* Get all blocks actually seen by this group membership
		*/
        $r_block_value = &ZariliaBlock::getAllBlocksByGroup( $g_id, false );
        $g_id_value = $_obj->getVar( 'groupid' );
        $type_value = $_obj->getVar( 'group_type', 'E' );
        if ( ZAR_GROUP_ADMIN == $g_id ) {
            $s_cat_disable = true;
        }

        $sysperm_handler = &zarilia_gethandler( 'groupperm' );
        $s_cat_value = call_user_func( array( $addonperm_handler, 'getItemIds' ), 'system_admin', $g_id );

        zarilia_cp_header();
        $menu_handler->render( 2 );
        include ZAR_ROOT_PATH . '/addons/system/admin/groups/groupform.php';
        break;

    case 'user';
        $member_handler = &zarilia_gethandler( 'member' );
        $userstart = $memstart = 0;
        $userstart = zarilia_cleanRequestVars( $_REQUEST, 'userstart', 0 );
        $memstart = zarilia_cleanRequestVars( $_REQUEST, 'memstart', 0 );

        $_obj = &call_user_func( array( $member_handler, 'getGroup' ), $g_id );
        $usercount = call_user_func( array( $member_handler, 'getUserCount' ), new Criteria( 'level', 0, '>' ) );
        $membercount = call_user_func( array( $member_handler, 'getUserCountByGroup' ), $g_id );

        if ( $usercount < 200 && $membercount < 200 ) {
            // do the old way only when counts are small
            $mlist = array();
            $members = &$member_handler->getUsersByGroup($g_id); 
            if ( count( $members ) > 0 ) {
                $member_criteria = new Criteria( 'uid', '(' . implode( ',', $members ) . ')', 'IN' );
                $member_criteria->setSort( 'uname' );
                $mlist = &call_user_func( array( $member_handler, 'getUserList' ), $member_criteria );
            }
            $criteria = new Criteria( 'level', 0, '>' );
            $criteria->setSort( 'uname' );
            $userslist = &call_user_func( array( $member_handler, 'getUserList' ), $criteria );
            $users = &array_diff( $userslist, $mlist );

            zarilia_cp_header();
            $menu_handler->render( 0 );
            echo '
			<table width="80%" align="center" valign="top" class="outer">
			<tr>
			 <th width="45%" align="center">' . _MA_AD_NONMEMBERS . '<br /></th>
			 <th></th>
			 <th width="45%" align="center">' . _MA_AD_MEMBERS . '<br /></th>
			</tr>
		    <tr>
			 <td class="even" valign="top">
			  <!---form start here-->
			  <form action="index.php" method="post">
			  <select name="uids[]" size="20" multiple="multiple">
              <option value="0">---------------------------------------------------</option>';
            foreach ( $users as $u_id => $u_name ) {
                echo '<option value="' . $u_id . '">' . $u_name . '</option>' . "\n";
            }
            echo '</select></td>';
            /*
			* middle col
			*/
            echo "
			  <td align='center' valign='top' class='odd'>
				<input type='hidden' name='op' value='addUser' />
				<input type='hidden' name='fct' value='groups' />
				<input type='hidden' name='groupid' value='" . $_obj->getVar( "groupid" ) . "' />
			    <input type='hidden' name='g_id' value='" . $g_id . "' />
				<input type='submit' name='submit' value='" . _MA_AD_ADDBUTTON . "' />
				</form>
				<!---form end here-->
				<!---form start here-->
				<form action='index.php' method='post' />
				<input type='hidden' name='op' value='delUser' />
				<input type='hidden' name='fct' value='groups' />
				<input type='hidden' name='groupid' value='" . $_obj->getVar( "groupid" ) . "' />
			    <input type='hidden' name='g_id' value='" . $g_id . "' />
				<input type='submit' name='submit' value='" . _MA_AD_DELBUTTON . "' />
			   </td>";
            /*
			* third col
			*/
            echo '
			<td class="even" valign="top">
			 <select name="uids[]" size="20" multiple="multiple">
              <option value="0">---------------------------------------------------</option>';
            foreach ( $mlist as $m_id => $m_name ) {
                echo '<option value="' . $m_id . '">' . $m_name . '</option>';
            }
            echo '</select>
            	 </td></tr>
			    </form>
				<!---form end here-->
			   </table>';
        } else {
            $members = &call_user_func( array( $member_handler, 'getUsersByGroup' ), $g_id, false, 200, $memstart );
            $mlist = array();
            if ( count( $members ) > 0 ) {
                $member_criteria = new Criteria( 'uid', "(" . implode( ',', $members ) . ")", "IN" );
                $member_criteria->setSort( 'uname' );
                $mlist = call_user_func( array( $member_handler, 'getUserList' ), $member_criteria );
            }
            echo '<a href="' . ZAR_URL . '/addons/system/index.php?fct=findusers&amp;group=' . $g_id . '">' . _MA_AD_FINDU4GROUP . '</a><br />
            	 <form action="index.php" method="post">
				<table class="outer">
			   <tr>
			   <th align="center">' . _MA_AD_MEMBERS . '<br />';

            zarilia_pagnav( $membercount, 200, $memstart, 'memstart', 1, "fct=groups&amp;op=modify&amp;g_id=" . $g_id );
            echo $nav->renderNav( 4 );
            echo "</th>
			     </tr>
				<tr><td class='even' align='center'>
				<input type='hidden' name='op' value='delUser' />
				<input type='hidden' name='fct' value='groups' />
				<input type='hidden' name='groupid' value='" . $_obj->getVar( 'groupid' ) . "' />
				<input type='hidden' name='g_id' value='" . $g_id . "' />
				<input type='hidden' name='memstart' value='" . $memstart . "' />
				<select name='uids[]' size='10' multiple='multiple'>";
            foreach ( $mlist as $m_id => $m_name ) {
                echo '<option value="' . $m_id . '">' . $m_name . '</option>' . "\n";
            }
            echo "</select><br />
				 <input type='submit' name='submit' value='" . _DELETE . "' />
				</td>
			   </tr>
			  </table>
			 </form>";
        }
        break;

    case 'update':
        $system_catids = zarilia_cleanRequestVars( $_REQUEST, 'system_catids', array(), XOBJ_DTYPE_ARRAY );
        $admin_mids = zarilia_cleanRequestVars( $_REQUEST, 'admin_mids', array(), XOBJ_DTYPE_ARRAY );
        $read_mids = zarilia_cleanRequestVars( $_REQUEST, 'read_mids', array(), XOBJ_DTYPE_ARRAY );
        $read_bids = zarilia_cleanRequestVars( $_REQUEST, 'read_bids', array(), XOBJ_DTYPE_ARRAY );

        $group = $member_handler->getGroup( $g_id );
        $group->setVar( 'name', $_REQUEST['name'] );
        $group->setVar( 'description', $_REQUEST['desc'] );
        if ( !in_array( $group->getVar( 'groupid' ), $zariliaOption['non_delete_groups'] ) ) {
            if ( count( $system_catids ) > 0 ) {
                $group->setVar( 'group_type', 'Admin' );
            } else {
                $group->setVar( 'group_type', '' );
            }
        }

        if ( !call_user_func( array( $member_handler, 'insertGroup' ), $group ) ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender( E_USER_ERROR, $group->getHtmlErrors() );
        } else {
            $groupid = $group->getVar( 'groupid' );
            $gperm_handler = &zarilia_gethandler( 'groupperm' );
            $criteria = new CriteriaCompo( new Criteria( 'gperm_groupid', $groupid ) );
            $criteria->add( new Criteria( 'gperm_modid', 1 ) );
            $criteria2 = new CriteriaCompo( new Criteria( 'gperm_name', 'system_admin' ) );
            $criteria2->add( new Criteria( 'gperm_name', 'addon_admin' ), 'OR' );
            $criteria2->add( new Criteria( 'gperm_name', 'addon_read' ), 'OR' );
            $criteria2->add( new Criteria( 'gperm_name', 'block_read' ), 'OR' );
            $criteria->add( $criteria2 );
            call_user_func( array( &$gperm_handler, 'deleteAll' ), $criteria );
            if ( count( $system_catids ) > 0 ) {
                array_push( $admin_mids, 1 );
                foreach ( $system_catids as $s_cid ) {
                    $sysperm = &call_user_func( array( &$gperm_handler, 'create' ) );
                    $sysperm->setVar( 'gperm_groupid', $groupid );
                    $sysperm->setVar( 'gperm_itemid', $s_cid );
                    $sysperm->setVar( 'gperm_name', 'system_admin' );
                    $sysperm->setVar( 'gperm_modid', 1 );
                    $gperm_handler->insert( $sysperm );
                }
            }
            foreach ( $admin_mids as $a_mid ) {
                $modperm = &call_user_func( array( &$gperm_handler, 'create' ) );
                $modperm->setVar( 'gperm_groupid', $groupid );
                $modperm->setVar( 'gperm_itemid', $a_mid );
                $modperm->setVar( 'gperm_name', 'addon_admin' );
                $modperm->setVar( 'gperm_modid', 1 );
                $gperm_handler->insert( $modperm );
            }
            array_push( $read_mids, 1 );
            foreach ( $read_mids as $r_mid ) {
                $modperm = &call_user_func( array( &$gperm_handler, 'create' ) );
                $modperm->setVar( 'gperm_groupid', $groupid );
                $modperm->setVar( 'gperm_itemid', $r_mid );
                $modperm->setVar( 'gperm_name', 'addon_read' );
                $modperm->setVar( 'gperm_modid', 1 );
                $gperm_handler->insert( $modperm );
            }
            foreach ( $read_bids as $r_bid ) {
                $blockperm = &call_user_func( array( &$gperm_handler, 'create' ) );
                $blockperm->setVar( 'gperm_groupid', $groupid );
                $blockperm->setVar( 'gperm_itemid', $r_bid );
                $blockperm->setVar( 'gperm_name', 'block_read' );
                $blockperm->setVar( 'gperm_modid', 1 );
                $gperm_handler->insert( $blockperm );
            }
            redirect_header( $_SERVER['HTTP_REFERER'], 0, _DBOPT );
        }
        break;

    case 'add':
        $system_catids = zarilia_cleanRequestVars( $_REQUEST, 'system_catids', array(), XOBJ_DTYPE_ARRAY );
        $admin_mids = zarilia_cleanRequestVars( $_REQUEST, 'admin_mids', array(), XOBJ_DTYPE_ARRAY );
        $read_mids = zarilia_cleanRequestVars( $_REQUEST, 'read_mids', array(), XOBJ_DTYPE_ARRAY );
        $read_bids = zarilia_cleanRequestVars( $_REQUEST, 'read_bids', array(), XOBJ_DTYPE_ARRAY );

        $group = &$member_handler->createGroup();
        $group->setVar( 'name', $_REQUEST['name'] );
        $group->setVar( 'description', $_REQUEST['desc'] );

        if ( count( $system_catids ) > 0 ) {
            $group->setVar( 'group_type', 'Admin' );
        }
        if ( !call_user_func( array( &$member_handler, 'insertGroup' ), $group ) ) {
            zarilia_cp_header();
            $menu_handler->render( 0 );
            echo $group->getHtmlErrors();
        } else {
            $groupid = $group->getVar( 'groupid' );
            $gperm_handler = &zarilia_gethandler( 'groupperm' );
            if ( count( $system_catids ) > 0 ) {
                array_push( $admin_mids, 1 );
                foreach ( $system_catids as $s_cid ) {
                    $sysperm = &call_user_func( array( &$gperm_handler, 'create' ) );
                    $sysperm->setVar( 'gperm_groupid', $groupid );
                    $sysperm->setVar( 'gperm_itemid', $s_cid );
                    $sysperm->setVar( 'gperm_name', 'system_admin' );
                    $sysperm->setVar( 'gperm_modid', 1 );
                    call_user_func( array( &$gperm_handler, 'insert' ), $sysperm );
                }
            }
            foreach ( $admin_mids as $a_mid ) {
                $modperm = &call_user_func( array( &$gperm_handler, 'create' ) );
                $modperm->setVar( 'gperm_groupid', $groupid );
                $modperm->setVar( 'gperm_itemid', $a_mid );
                $modperm->setVar( 'gperm_name', 'addon_admin' );
                $modperm->setVar( 'gperm_modid', 1 );
                call_user_func( array( &$gperm_handler, 'insert' ), $modperm );
            }
            array_push( $read_mids, 1 );
            foreach ( $read_mids as $r_mid ) {
                $modperm = &call_user_func( array( &$gperm_handler, 'create' ) );
                $modperm->setVar( 'gperm_groupid', $groupid );
                $modperm->setVar( 'gperm_itemid', $r_mid );
                $modperm->setVar( 'gperm_name', 'addon_read' );
                $modperm->setVar( 'gperm_modid', 1 );
                call_user_func( array( &$gperm_handler, 'insert' ), $modperm );
            }
            foreach ( $read_bids as $r_bid ) {
                $blockperm = &call_user_func( array( &$gperm_handler, 'create' ) );
                $blockperm->setVar( 'gperm_groupid', $groupid );
                $blockperm->setVar( 'gperm_itemid', $r_bid );
                $blockperm->setVar( 'gperm_name', 'block_read' );
                $blockperm->setVar( 'gperm_modid', 1 );
                call_user_func( array( &$gperm_handler, 'insert' ), $blockperm );
            }
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 0, _DBUPDATED );
        }
        break;

    case 'delete':
        $g_id = zarilia_cleanRequestVars( $_REQUEST, 'g_id', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        if ( intval( $g_id ) > 0 && in_array( $g_id, $zariliaOption['non_delete_groups'] ) ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender( E_USER_ERROR, _MA_AD_UNEED2ENTER );
            zarilia_cp_footer();
            exit();
        }

        if ( $ok == 1 ) {
            if ( intval( $g_id ) > 0 && !in_array( $g_id, $zariliaOption['non_delete_groups'] ) ) {
                $group = &$member_handler->getGroup( $g_id );
                $member_handler->deleteGroup( $group );
                $gperm_handler = &zarilia_gethandler( 'groupperm' );
                $gperm_handler->deleteByGroup( $g_id );
            }
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 0, _DBUPDATED );
            break;
        } else {
            zarilia_cp_header();
            $menu_handler->render( 0 );
            zarilia_confirm( array( 'fct' => $fct, 'op' => 'delete', 'g_id' => $g_id, 'ok' => 1 ), $addonversion['adminpath'], _MA_AD_AREUSUREDEL );
        }
        break;

    case "addUser":
        $groupid = zarilia_cleanRequestVars( $_REQUEST, 'groupid', 0 );
        $uids = zarilia_cleanRequestVars( $_REQUEST, 'uids', array(), XOBJ_DTYPE_ARRAY );
        for ( $i = 0; $i < sizeof( $uids ); $i++ ) {
            if ( !$member_handler->addUserToGroup( $groupid, $uids[$i] ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, zarilia_getLinkedUnameFromId( $uids[$i] ) ) );
            }
        }
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&op=user&g_id=' . $g_id, 0, _DBUPDATED );
        }
        break;

    case 'delUser':
        $groupid = zarilia_cleanRequestVars( $_REQUEST, 'groupid', 0 );
        $uids = zarilia_cleanRequestVars( $_REQUEST, 'uids', array(), XOBJ_DTYPE_ARRAY );
        if ( intval( $groupid ) > 0 ) {
            $memstart = zarilia_cleanRequestVars( $_REQUEST, 'memstart', 0 );
            if ( $groupid == ZAR_GROUP_ADMIN ) {
                if ( $member_handler->getUserCountByGroup( $groupid ) > count( $uids ) ) {
                    if ( !$member_handler->removeUsersFromGroup( $groupid, $uids ) ) {
                        $err = '';
                    }
                }
            } else {
                if ( !$member_handler->removeUsersFromGroup( $groupid, $uids ) ) {
                    $err = '';
                }
            }
        }
        if ( @$err ) {
            zarilia_cp_header();
            $menu_handler->render( 0 );
            zarilia_error( $err );
        }
        redirect_header( $addonversion['adminpath'] . '&amp;op=user&amp;g_id=' . $groupid . '&amp;memstart=' . $memstart, 0, _DBUPDATED );
        break;

    case 'list':
    default:
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        $buttons = array( 'edit', 'delete', 'clone', 'user' );

        zarilia_cp_header();
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . '&amp;op=edit' => _MA_AD_NEWGROUPS ) );
        $menu_handler->render( 1 );
        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'groupid', '5%', 'center', false );
        $tlist->AddHeader( 'groups', '20%', 'left', true );
        $tlist->AddHeader( 'description', '', 'left', true );
        $tlist->AddHeader( 'group_type', '', 'left', true );
        $tlist->AddHeader( 'action', '', 'center', false );
        /**
         */
        $group = &$member_handler->getGroups();
        foreach ( $group as $obj ) {
            $groupid = $obj->getVar( 'groupid' );
            $button[0] = 'edit';
            $button[1] = 'delete';
            $button[2] = 'clone';
            $button[3] = 'user';
            if ( in_array( $groupid , $zariliaOption['non_delete_groups'] ) ) {
                unset( $button[1] );
            }
            $tlist->add(
                array( $groupid,
                    $obj->getVar( 'name' ),
                    $obj->getVar( 'description' ),
                    $obj->getVar( 'group_type' ),
                    zarilia_cp_icons( $button, 'g_id', $groupid )
                    )
                );
        }
        $tlist->render();
        zarilia_cp_legend( $buttons );
        break;

    case 'index':
    case 'default':
        zarilia_cp_header();
        $menu_handler->render( 0 );
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . '&amp;op=edit' => _MA_AD_NEWGROUPS ) );
        break;
}
zarilia_cp_footer();

?>