<?php
// $Id: index.php,v 1.4 2007/05/05 11:10:44 catzwolf Exp $
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
require_once ZAR_ROOT_PATH . "/class/zarilialists.php";
require_once ZAR_ROOT_PATH . "/class/zariliaformloader.php";
require_once ZAR_ROOT_PATH . '/class/class.menubar.php';

$member_handler = &zarilia_gethandler( 'member' );
$uid = zarilia_cleanRequestVars( $_REQUEST, 'uid', 0 );
switch ( strtolower( $op ) ) {
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

    case "create":
    case "edituser":
        zarilia_cp_header();
        $menu_handler->render( 2 );
        if ( $uid ) {
            $user = $member_handler->getUser( $uid );
            $form_title = _AM_UPDATEUSER . ": " . $user->getVar( "uname" );
            $form_isedit = true;
            $groups = array_values( $user->getGroups() );
        } else {
            $user = $member_handler->createUser();
            $form_title = _AM_ADDUSER;
            $form_isedit = false;
            $groups = array( ZAR_GROUP_USERS );
        }
        if ( $uid > 0 && is_object( $user ) && !$user->isActive() ) {
            zarilia_confirm( array( 'fct' => $fct, 'op' => 'reactivate', 'uid' => $user->getVar( 'uid' ) ), $addonversion['adminpath'], _AM_NOTACTIVE );
        }
        $opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0 );
        $tabbar = new ZariliaTabMenu( $opt );
        $url = $addonversion['adminpath'] . "&amp;uid=" . $user->getVar( 'uid' ) . "&amp;op=edituser";
        $this_array = array( _AM_PERSONAL => $url, _AM_NOTIFICATIONS => $url );
        if ( $uid > 0 ) {
            $profilecat_handler = &zarilia_gethandler( 'profilecategory' );
            $_array = $profilecat_handler->getList();
            foreach ( $_array as $k => $v ) {
                $this_array[$v] = $url . "&amp;profile_id=" . intval( $k );
            }
        }

        $tabbar->addTabArray( $this_array );
        $tabbar->renderStart( 0, 1 );

        $form = new ZariliaThemeForm( $form_title, "userinfo", "index.php" );
        $opt = ( $opt > 2 ) ? 2 : $opt;
        switch ( intval( $opt ) ) {
            case 0:
            default:
                require_once ZAR_ROOT_PATH . '/addons/system/admin/users/userpersonalform.php';
                $create_tray = new ZariliaFormElementTray( '', '' );
                $create_tray->addElement( new ZariliaFormHidden( 'op', 'adduser' ) );
                break;
            case 1:
                require_once ZAR_ROOT_PATH . '/addons/system/admin/users/usernotifiform.php';
                $create_tray = new ZariliaFormElementTray( '', '' );
                $create_tray->addElement( new ZariliaFormHidden( 'op', 'adduser' ) );
                break;
            case 2:
                $profile_id = zarilia_cleanRequestVars( $_REQUEST, 'profile_id', 0 );
                $userprofile_handler = &zarilia_gethandler( 'userprofile' );
                $form = $userprofile_handler->displayProfile( $form, $profile_id, $uid );
                $create_tray = new ZariliaFormElementTray( '', '' );
                $create_tray->addElement( new ZariliaFormHidden( 'op', 'addprofile' ) );
                break;
        } // switch
        $create_tray->addElement( new ZariliaFormHidden( 'fct', $fct ) );
        if ( $opt > 0 ) {
            $create_tray->addElement( new ZariliaFormHidden( 'login', $user->getVar( 'login', 'e' ) ) );
            $create_tray->addElement( new ZariliaFormHidden( 'groups', $groups ) );
        }
        $create_tray->addElement( new ZariliaFormHidden( 'uid', $uid ) );
        $create_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
        $create_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ) );
        $create_tray->addElement( new ZariliaFormButton( '', 'submit', _SUBMIT, 'submit' ) );
        $form->addElement( $create_tray );
        $form->display();
        break;

    case "delete_many":
        $memberslist_id = zarilia_cleanRequestVars( $_REQUEST, 'memberslist_id', array() );
        if ( count( $memberslist_id ) == 0 ) {
            zarilia_cp_header();
            $menu_handler->render( 0 );
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_NOUSERS );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        /*
		*
		*/
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        if ( $ok ) {
            $count = count( $memberslist_id );
            $output = array();
            for ( $i = 0; $i < $count; $i++ ) {
                $deluser = $member_handler->getUser( $memberslist_id[$i] );
                $groups = $deluser->getGroups();
                if ( in_array( ZAR_GROUP_ADMIN, $groups ) ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, sprintf( _AM_US_ERROR, $_del_username, $_del_username ) );
                } else {
                    if ( !$member_handler->deleteUser( $deluser ) ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, sprintf( _AM_US_NOTDELUSER ) );
                    }
                    zarilia_notification_deletebyuser( $deluser->getVar( 'uid' ) );
                }
            }
            if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                zarilia_cp_header();
                $menu_handler->render( 0 );
                $GLOBALS['zariliaLogger']->sysRender();
            } else {
                redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
                break;
            }
        } else {
            zarilia_cp_header();
            $menu_handler->render( 0 );
            zarilia_confirm( array( 'memberslist_id' => $memberslist_id, 'op' => 'delete_many', 'ok' => 1, 'fct' => $fct ), 'index.php', _AM_DELALLUSERS );
        }
        break;

    case 'deluser':
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $user = $member_handler->getUser( $uid );
        $_is_user = ( is_object( $user ) ) ? true : false;

        if ( $ok == 1 ) {
            if ( $_is_user == true ) {
                $groups = $user->getGroups();
                $user_name = $user->getVar( "uname" );
                if ( in_array( ZAR_GROUP_ADMIN, $groups ) ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, sprintf( _AM_US_ERROR, $user_name, $user_name ) );
                } elseif ( !$member_handler->deleteUser( $user ) ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, sprintf( _AM_US_NOTDELUSER, $user_name ) );
                } else {
                    $online_handler = &zarilia_gethandler( 'online' );
                    $online_handler->destroy( $uid );
                    // RMV-NOTIFY
                    zarilia_notification_deletebyuser( $uid );
                }
            } else {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_US_NOTDELIDNOTFOUND );
            }

            if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                zarilia_cp_header();
                $menu_handler->render( 1 );
                $GLOBALS['zariliaLogger']->sysRender();
            } else {
                redirect_header( $addonversion['adminpath'] . "&amp;op=list", 1, _DBUPDATED );
            }
            break;
        } else {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            zarilia_confirm( array( 'fct' => $fct, 'op' => 'deluser', 'uid' => $uid, 'ok' => 1 ), $addonversion['adminpath'], sprintf( _AM_AYSYWTDU, $user->getVar( 'uname' ) ) );
        }
        break;

    case "addprofile":
        if ( is_array( $_REQUEST['userprofile'] ) && count( $_REQUEST['userprofile'] ) > 0 ) {
            $userprofile_handler = &zarilia_gethandler( 'userprofile' );
            $count = count( $_REQUEST['userprofile'] );
            if ( $count > 0 ) {
                $userprofile = is_array( $_REQUEST['userprofile'] ) ? $_REQUEST['userprofile'] : array();
                $uid = zarilia_cleanRequestVars( $_REQUEST, 'uid', 0 );
                $criteria = new CriteriaCompo();
                $criteria->add( new Criteria( 'userprofile_uid', $uid ) );
                $userprofile_handler->deleteAll( $criteria );
                foreach ( $_REQUEST['userprofile'] as $k => $v ) {
                    $profile_obj = $userprofile_handler->create();
                    $profile_obj->setVar( 'userprofile_uid', $uid );
                    $profile_obj->setVar( 'userprofile_cid', 1 );
                    $profile_obj->setVar( 'userprofile_value', $v );
                    $profile_obj->setVar( 'userprofile_pid', $k );
                    if ( !$userprofile_handler->insert( $profile_obj ) ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_US_NOTDELIDNOTFOUND );
                    }
                }
                if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                    $GLOBALS['zariliaLogger']->sysRender();
                } else {
                    redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
                }
            } else {
            }
            foreach ( $_REQUEST['userprofile'] as $k => $v ) {
                $k = ( $_REQUEST['profile_valuetype'][$k] == XOBJ_DTYPE_TXTBOX ) ? $k : intval( $k );
                $profile_obj = $userprofile_handler->create();
                $profile_obj->setVar( 'userprofile_uid', $uid );
                $profile_obj->setVar( 'userprofile_cid', 1 );
                $profile_obj->setVar( 'userprofile_value', $v );
                $profile_obj->setVar( 'userprofile_pid', $k );
                if ( $userprofile_handler->insert( $profile_obj ) ) {
                    redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
                } else {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_US_NOTDELIDNOTFOUND );
                    $GLOBALS['zariliaLogger']->sysRender();
                }
            }
        } else {
        }
        break;

    case "adduser":
        $login = zarilia_cleanRequestVars( $_REQUEST, 'login', '', XOBJ_DTYPE_TXTBOX );
        $uname = zarilia_cleanRequestVars( $_REQUEST, 'uname', '', XOBJ_DTYPE_TXTBOX );
        $email = zarilia_cleanRequestVars( $_REQUEST, 'email', '', XOBJ_DTYPE_EMAIL );
        $pass = zarilia_cleanRequestVars( $_REQUEST, 'pass', '', XOBJ_DTYPE_TXTBOX );
        $pass2 = zarilia_cleanRequestVars( $_REQUEST, 'pass2', '', XOBJ_DTYPE_TXTBOX );
        unset( $_REQUEST['pass'], $_REQUEST['pass2'] );

        $edituser = ( $uid == 0 ) ? $member_handler->createUser() : $member_handler->getUser( $uid );
        if ( $uid == 0 ) {
            if ( !$login || !$uname || !$email || !$pass ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_YMCACF );
                zarilia_cp_header();
                $menu_handler->render( 1 );
                $GLOBALS['zariliaLogger']->sysRender();
                zarilia_cp_footer();
                exit();
            }
            if ( trim( $login ) == trim( $uname ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_US_CANNOTBESAME );
            }

            /**
             */
            if ( $member_handler->getUserCount( new Criteria( 'login', addslashes( $login ) ) ) > 0 ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_US_LOGINEXISTS );
            }
            /**
             */
            if ( $member_handler->getUserCount( new Criteria( 'uname', addslashes( $uname ) ) ) > 0 ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_US_UNAMEEXISTS );
            }
        } else {
            /**
             */
            if ( $edituser->getVar( 'login', 'n' ) != $login && $member_handler->getUserCount( new Criteria( 'login', addslashes( $login ) ) ) > 0 ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_US_LOGINEXISTS );
            }
        }

        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            $edituser->setVars( $_REQUEST );
            if ( isset( $_REQUEST['user_coppa_dob'] ) ) {
                $edituser->setVar( 'user_coppa_dob', strtotime( $_REQUEST['user_coppa_dob'] ) );
                $edituser->setVar( 'user_coppa_agree', isset( $_REQUEST['user_coppa_agree'] ) ? 1 : 0 );
            }
            if ( !empty( $pass ) && !empty( $pass2 ) ) {
                if ( $pass != $pass2 ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_STNPDNM );
                } else {
                    $edituser->setVar( "pass", $GLOBALS['zariliaSecurity']->execEncryptionFunc('encrypt', $pass ) );
                }
            } else {
                if ( ( !empty( $pass ) && empty( $pass2 ) ) || ( empty( $pass ) && !empty( $pass2 ) ) ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_YMEBPWS );
                }
            }
        }

        $groups = ( isset( $_REQUEST['groups'] ) ) ? $_REQUEST['groups'] : '';
        if ( isset( $_REQUEST['groups'] ) && in_array( 4, $_REQUEST['groups'] ) ) {
            $edituser->setVar( "rank", 6 );
        } elseif ( isset( $_REQUEST['groups'] ) && in_array( 1, $_REQUEST['groups'] ) ) {
            $edituser->setVar( "rank", 7 );
        } else {
            $edituser->setVar( "rank", 0 );
        }

        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            if ( !$member_handler->insertUser( $edituser ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $edituser->getErrors() );
            } else {
                if ( $uid ) {
                    if ( is_array( $groups ) && !empty( $groups ) ) {
                        // global $zariliaUser;
                        $oldgroups = $edituser->getGroups();
                        // If the edited user is the current user and the current user WAS in the webmaster's group and is NOT in the new groups array
                        if ( $edituser->getVar( 'uid' ) == $zariliaUser->getVar( 'uid' ) && ( in_array( ZAR_GROUP_ADMIN, $oldgroups ) ) && !( in_array( ZAR_GROUP_ADMIN, $groups ) ) ) {
                            // Add the webmaster's group to the groups array to prevent accidentally removing oneself from the webmaster's group
                            array_push( $groups, ZAR_GROUP_ADMIN );
                        }
                        foreach ( $oldgroups as $groupid ) {
                            $member_handler->removeUsersFromGroup( $groupid, array( $edituser->getVar( 'uid' ) ) );
                        }
                        foreach ( $groups as $groupid ) {
                            $member_handler->addUserToGroup( $groupid, $edituser->getVar( 'uid' ) );
                        }
                    }
                } else {
                    if ( !$member_handler->addUserToGroup( ZAR_GROUP_USERS, $edituser->getVar( 'uid' ) ) ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _AM_CNRNU2 );
                    }
                }
            }
        }

        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 0 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
        }
        break;

    case "synchronize":
        synchronize( $id, $type );
        break;

    case "activate":
    case "reactivate":
        $edituser = $member_handler->getUser( $uid );
        $edituser->setVar( "level", 1 );
        /**
         */
        $oldgroups = $edituser->getGroups();
        if ( !array( $oldgroups ) ) {
            $member_handler->addUserToGroup( 2, $edituser->getVar( 'uid' ) );
        }
        if ( is_array( $oldgroups ) && in_array( 1, $oldgroups ) ) {
            $edituser->setVar( "rank", 7 );
        }
        if ( !$member_handler->insertUser( $edituser ) ) {
            zarilia_cp_header();
            echo $edituser->getHtmlErrors();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
        }
        break;

    case "deactivate":
        $edituser = $member_handler->getUser( $uid );
        $edituser->setVar( 'level', 0 );
        $edituser->setVar( 'rank', 0 );
        if ( !$member_handler->insertUser( $edituser ) ) {
            zarilia_cp_header();
            echo $edituser->getHtmlErrors();
        } else {
            $online_handler = &zarilia_gethandler( 'online' );
            $online_handler->destroy( $edituser->getVar( 'uid' ) );
            /**
             */
            $oldgroups = $edituser->getGroups();
            foreach ( $oldgroups as $groupid ) {
                $member_handler->removeUsersFromGroup( $groupid, array( $edituser->getVar( 'uid' ) ) );
            }
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
        }
        break;

    case "suspend":
        $edituser = $member_handler->getUser( $uid );
        $edituser->setVar( 'level', 6 );
        $edituser->setVar( 'rank', 0 );
        if ( !$member_handler->insertUser( $edituser ) ) {
            zarilia_cp_header();
            echo $edituser->getHtmlErrors();
        } else {
            $online_handler = &zarilia_gethandler( 'online' );
            $online_handler->destroy( $edituser->getVar( 'uid' ) );
            // $oldgroups = $edituser->getGroups();
            // foreach ( $oldgroups as $groupid ) {
            // $member_handler->removeUsersFromGroup( $groupid, array( $edituser->getVar( 'uid' ) ) );
            // }
            redirect_header( $addonversion['adminpath'] . '&amp;op=list', 1, _DBUPDATED );
        }
        break;

    case "list":
    default:
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'uid', XOBJ_DTYPE_TXTBOX );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );

        $list_types = zarilia_cleanRequestVars( $_REQUEST, 'list_types', -1 );
        $list_groups = zarilia_cleanRequestVars( $_REQUEST, 'list_groups', 0 );
        $list_letters = zarilia_cleanRequestVars( $_REQUEST, 'list_letters', 0 );
        $user_uname = zarilia_cleanRequestVars( $_REQUEST, 'user_uname', null );
        $user_uname_match = zarilia_cleanRequestVars( $_REQUEST, 'user_uname_match', '' );

        if ( $user_uname == null ) {
            $foundusers = $member_handler->getUsersByGroupList( $list_groups, $list_types, $list_letters, $nav['limit'], $nav['start'], $nav['sort'], $nav['order'] );
            $foundusers_count = $member_handler->getUsersByGroupListCount( $list_groups, $list_types, $list_letters );
        } else {
            $users_array = $member_handler->getUserBySearch( $user_uname, $user_uname_match, $nav, $id_as_key = false );
            $foundusers = $users_array['list'];
            $foundusers_count = $users_array['count'];
        }			
		
        $types_array = array( -1 => _AM_MENUALLUSERS, 1 => _AM_MENUACTIVEUSERS, 2 => _AM_MENUSUSUSERS, 0 => _AM_MENUNEWUSERS );
        $group_array = $member_handler->getGroupsArray();

        $trans_array = array();
        $trans_array[0] = _AM_ALL;
        for ( $i = 48; $i < 48 + 9; $i++ ) {
            $trans_array[$i] = chr( $i );
        }
        for ( $i = 65; $i < 65 + 26; $i++ ) {
            $trans_array[$i] = chr( $i );
        }
        $match_option = array( 99 => _AM_SELECTMATCHTYPE, 0 => _STARTSWITH, 1 => _ENDSWITH, 2 => _MATCHES, 3 => _CONTAINS, 4 => "Email Starts With", 5 => "Email Ends With", 6 => "Email Matches", 7 => "Email Contains" );

        zarilia_cp_header();
        $menu_handler->render( 1 );

        require_once ZAR_CONTROLS_PATH . '/statictabs/control.class.php';
        $st = new ZariliaControl_StaticTabs(
            array( _AM_USER_SEARCHDEFAULT => '
				<div class="outer" style="width: 90%; padding: 3px;">
				<div class="sidetitle">' . _AM_USER_SEARCHTYPE . '</div>
				<div class="sidecontent">' . zarilia_getSelection( $types_array, $list_types, 'list_types', 1, 0 , false, false, "style=\"width: 90%\" onchange='location.href=\"" . $addonversion['adminpath'] . "&amp;op=list" . "&amp;list_groups=" . $list_groups . "&amp;list_letters=" . $list_letters . "&amp;list_types=\"+this.options[this.selectedIndex].value'", 0 , false ) . '</div>
				<div class="sidetitle">' . _AM_USER_SEARCHGROUP . '</div>
				<div class="sidecontent">' . zarilia_getSelection( $group_array, $list_groups, 'list_groups', 1, 1 , false, 'All Groups', "style=\"width: 90%\" onchange='location.href=\"" . $addonversion['adminpath'] . "&amp;op=list" . "&amp;amp;list_letters=" . $list_letters . "&amp;list_types=" . $list_types . "&amp;list_groups=\"+this.options[this.selectedIndex].value'", 0 , false ) . '</div>
				<div class="sidetitle">' . _AM_USER_SEARCHLETTER . '</div>
				<div class="sidecontent">' . zarilia_getSelection( $trans_array, $list_letters, 'list_letters', 1, 0 , false, false, "style=\"width: 90%\" onchange='location.href=\"" . $addonversion['adminpath'] . "&amp;op=list" . "&amp;list_types=" . $list_types . "&amp;list_groups=" . $list_groups . "&amp;list_letters=\"+this.options[this.selectedIndex].value'", 0, false ) . '</div>
			    </div>',

                _AM_USER_SEARCHCUSTOM => '
					<form name="form1" action="' . $addonversion['adminpath'] . '" method="get">
						<input type="hidden" name="fct" value="users" />
						<input type="hidden" name="op" value="list" />
							<table class="outer" border="0" width="100%">
								<tr><td>' . zarilia_getSelection( $match_option, $user_uname, 'user_uname', 1, 0 , false, '', 'style=\"width: 90%\"', 0 , false ) . '</td>
								</tr>
								<tr>
									<td><input type="text" name="user_uname_match" id="user_uname_match" size="25" maxlength="25" value="' . $user_uname_match . '" style=\"width: 100%\" />
									</td>
								</tr>
								<tr>
									<td><input type="submit" class="formbutton" value="' . _AM_USER_SEARCH . '" />
								</td>
							</tr>
						</table>
					</form>'
                ),
            'filter' );

        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . '&amp;op=edituser' => _AM_ADDUSER ), _MD_AD_FILTER_BOX , $st->render() );

        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'uid', '5%', 'center', true );
        $tlist->AddHeader( 'uname', '150px', 'left', true );
        $tlist->AddHeader( 'rank', '', 'center', true );
        $tlist->AddHeader( 'status', '', 'center', false );
        $tlist->AddHeader( 'user_regdate', '', 'center', true );
        $tlist->AddHeader( 'last_login', '', 'center', true );
        $tlist->AddHeader( 'ipaddress', '', 'center', true );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '10%', 'left' );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'], 'usersform' );
        $tlist->addFooter( $member_handler->setSubmit( $fct, 'fct' ) );
        $tlist->setPath( 'op=' . $op );

        $button = array( 'edit', 'delete', 'contact', 'suspend', 'deactivate', 'activate' );
        $i = 0;
        foreach ( $foundusers as $obj ) {		
            $status = $obj->getVar( "level" );
            $uid = $obj->getVar( "uid" );
            switch ( $status ) {
                case 0:
                    $user_status = _MA_AD_UNOTACTIVE;
                    $class = 'notactive';
                    break;
                case 6:
                    $user_status = _MA_AD_SUSPENDED;
                    $class = 'suspended';
                    break;
                case 6:
                default:
                    $user_status = _MA_AD_ACTIVE;
                    if ( $i % 2 ) {
                        $class = 'odd';
                    } else {
                        $class = 'even';
                    }
                    break;
            } // switch
            $icons['edit'] = '<a href="index.php?fct=users&amp;uid=' . $uid . '&amp;op=edituser">' . zarilia_img_show( 'edit', _EDIT ) . '</a>';
            $icons['edit'] .= '<a href="index.php?fct=users&amp;uid=' . $uid . '&amp;op=deluser">' . zarilia_img_show( 'delete', _DELETE ) . '</a>';
            $icons['edit'] .= '<a href="index.php?fct=mailusers&amp;uid=' . $uid . '&amp;type=0">' . zarilia_img_show( 'contact', _CONTACT ) . '</a>';
            if ( $status >= 1 && $status <= 5 ) {
                $icons['edit'] .= '<a href="index.php?fct=users&amp;uid=' . $uid . '&amp;op=suspend">' . zarilia_img_show( 'suspend', _SUSPEND ) . '</a>';
                $icons['edit'] .= '<a href="index.php?fct=users&amp;uid=' . $uid . '&amp;op=deactivate">' . zarilia_img_show( 'deactivate', _DEACTIVATE ) . '</a>';
            } else if ( $status == 0 || $status == 6 ) {
                $icons['edit'] .= '<a href="index.php?fct=users&amp;uid=' . $uid . '&amp;op=activate">' . zarilia_img_show( 'activate', _ACTIVATE ) . '</a>';
            }
            $tlist->add(
                array( $obj->getVar( 'uid' ),
                    $obj->getUnameFromId( 0, 0, 1 ),
                    $obj->rank( true ),
                    $user_status,
                    $obj->getTimeStamp( null, 'user_regdate' ),
                    $obj->getTimeStamp( null, 'last_login' ),
                    $obj->getVar( "ipaddress" ),
                    $obj->getCheckbox( $i, 'memberslist_id' ),
                    $icons['edit']
                    ), $class );
            $i++;
        }
        $tlist->render();
        zarilia_pagnav( $foundusers_count, $nav['limit'], $nav['start'], 'start', 1, '?fct=' . $fct . '&amp;list_groups=' . $list_groups . '&amp;list_letters=' . $list_letters . '&amp;list_types=' . $list_types . '&amp;op=list&amp;limit=' . $nav['limit'] );
        zarilia_cp_legend( $button );
        break;

    case 'index':
    case 'default':
        zarilia_cp_header();
        $menu_handler->render( 0 );
        break;
}
zarilia_cp_footer();

?>
