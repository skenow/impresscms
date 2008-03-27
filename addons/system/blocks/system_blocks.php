<?php
// $Id: system_blocks.php,v 1.4 2007/05/05 11:11:07 catzwolf Exp $
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
function b_system_online_show()
{
    global $zariliaUser, $zariliaAddon;

    $online_handler = &zarilia_gethandler( 'online' );
    $onlines = &$online_handler->getAll();
    if ( false != $onlines )
    {
        $total = count( $onlines );
        $block = array();
        $guests = 0;
        $members = '';
        $_already_there_arr = array();
        foreach( $onlines as $online )
        {
            if ( $online->getVar( 'online_uid' ) > 0 && $online->getVar( 'online_hidden' ) != 1 )
            {
                $members .= ' <a href="' . ZAR_URL . '/index.php?page_type=userinfo&uid=' . $online->getVar( 'online_uid' ) . '">' . $online->getVar( 'online_uname' ) . '</a>,';
            }
            else
            {
                $guests++;
            }
        }
        $block['online_total'] = sprintf( _ONLINEPHRASE, $total );
        if ( is_object( $zariliaAddon ) )
        {
            $mytotal = $online_handler->getCount( new Criteria( 'online_addon', $zariliaAddon->getVar( 'mid' ) ) );
            $block['online_total'] .= ' (' . sprintf( _ONLINEPHRASEX, $mytotal, $zariliaAddon->getVar( 'name' ) ) . ')';
        }
        $block['lang_members'] = _MEMBERS;
        $block['lang_guests'] = _GUESTS;
        $block['online_names'] = $members;
        $block['online_members'] = $total - $guests;
        $block['online_guests'] = $guests;
        $block['lang_more'] = _MORE;
        return $block;
    }
    else
    {
        return false;
    }
}

function b_system_login_show()
{
    global $zariliaUser, $zariliaConfig, $config_handler;
    $block = array();
    if ( !is_object($zariliaUser) )
    {
		require_once  ZAR_ROOT_PATH. "/language/" . $zariliaConfig['language'].'/user.php';
        $zariliaConfigUser = &$config_handler->getConfigsByCat( ZAR_CONF_USER );
        if ( $zariliaConfigUser['showimagever'] )
        {
            $rand = '';
            $type = 2;
            $bgNum = 0;
            switch ( $type )
            {
                case 2:
                    $alphanum = "ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789";
                    $rand = substr( str_shuffle( $alphanum ), 0, 5 );
                    break;
                case 3:
                    $alphanum = "ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789";
                    $rand = substr( str_shuffle( $alphanum ), 0, 5 );
                    $bgNum = rand( 1, 4 );
                    break;
                case 1:
                default:
                    $rand = rand( 10000, 99999 );
                    break;
            } // switch
            if ( function_exists( 'gd_info' ) )
            {
                $veri_image = "<img src='" . ZAR_URL . "/include/verification/randomimage.php?rand=" . $rand . "&amp;bgNum=" . $bgNum . "' alt='' title='' />";
                $block['showimage'] = 1;
                $block['lang_verification'] = _MB_SYSTEM_LOGINCHECK;
                $block['verification_ver'] = $rand;
                $block['verification_image'] = $veri_image;
            }
            else
            {
                $block['verification_ver'] = 0;
                $block['showimage'] = 0;
            }
        }
        $block['lang_username'] = _USERNAME;
        $block['unamevalue'] = "";
        if ( isset( $_COOKIE[$zariliaConfig['usercookie']] ) )
        {
            $block['unamevalue'] = $_COOKIE[$zariliaConfig['usercookie']];
        }
        $block['lang_password'] = _PASSWORD;
        $block['lang_login'] = htmlspecialchars( _LOGIN );
        $block['lang_lostpass'] = _MB_SYSTEM_LPASS;

        $block['lang_registernow'] = '';
        $block['allow_register'] = (($zariliaConfigUser['allow_register'])==1)?1:0;
        if ( $zariliaConfigUser['allow_register'] == 1 )
        {
            $block['lang_registernow'] = _MB_SYSTEM_RNOW;
        }
        $block['lang_rememberme'] = _MB_SYSTEM_REMEMBERME;
        $block['lang_logonanon'] = _MB_SYSTEM_LOGINANON;
        if ( $zariliaConfig['use_ssl'] == 1 && $zariliaConfig['sslloginlink'] != '' )
        {
            $block['sslloginlink'] = "<a href=\"javascript:openWithSelfMain('" . $zariliaConfig['sslloginlink'] . "', 'ssllogin', 300, 200);\">" . _MB_SYSTEM_SECURE . "</a>";
        }
        return $block;
    }
    return false;
}

/**
 * b_system_main_show()
 *
 * @return
 */
function b_system_main_show()
{
    global $zariliaUser, $zariliaAddon;
    //unset( $_SESSION['user']['mainmenu'] );

	if (@$_REQUEST['debug'] == 'rebuild') {
		unset($_SESSION['user']['mainmenu'] );
	}

    if ( isset( $_SESSION['user']['mainmenu'] ) )
    {
        return $_SESSION['user']['mainmenu'];
    }

    $block = array();
    $menu_handler = &zarilia_gethandler( 'menus' );
    $menu_obj = &$menu_handler->getMenublock( 'mainmenu', false );

    $i = 0;
    if ( !empty( $menu_obj['list'] ) )
    {
        foreach( $menu_obj['list'] as $obj )
        {
            $linktype = $obj->getVar( 'menu_pid' );
            if ( $obj->getVar( 'menu_target' ) )
            {
                $target = 'target="' . $obj->getVar( 'menu_target' ) . '" ';
            }
            else
            {
                $target = ' ';
            }
            $url = str_replace( '{X_SITEURL}', ZAR_URL, $obj->getVar( 'menu_link', 'e' ) );
            if ( $zariliaUser )
            {
                $url = str_replace( '{X_UID}', $zariliaUser->getVar( 'uid' ), $url );
            }

            /**
             */
            if ( ( eregi( "mailto:", $url ) ) || ( eregi( "http://", $url ) ) || ( eregi( "https://", $url ) ) || ( eregi( "file://", $url ) ) || ( eregi( "ftp://", $url ) ) )
            {
                $link = $url;
            }
            else
            {
                $link = ZAR_URL . "/" . $url;
            }
            $block['mainmenu'][$i]['name'] = strip_tags( $obj->getVar( 'menu_title' ) );
            $block['mainmenu'][$i]['target'] = $target;
            $block['mainmenu'][$i]['image'] = $obj->getVar( 'menu_image' );
            $block['mainmenu'][$i]['directory'] = $link;
            $block['mainmenu'][$i]['link'] = $obj->getVar( 'menu_pid' );
            $style = ( $obj->getVar( 'menu_pid' ) ) ? 'menuSub' : 'menuMain';
            $block['mainmenu'][$i]['style'] = $obj->getVar( 'menu_class' ) ? $obj->getVar( 'menu_class' ) : $style;
            $i++;
        }
    }
    unset( $menu_obj );
    $_SESSION['user']['mainmenu'] = $block;
    return $block;
}

function b_system_search_show()
{
    $block = array();
    $block['lang_search'] = _MB_SYSTEM_SEARCH;
    $block['lang_advsearch'] = _MB_SYSTEM_ADVS;
    return $block;
}

function b_system_user_show()
{
    global $zariliaUser, $zariliaConfig;

	if (@$_REQUEST['debug'] == 'rebuild') {
		unset($_SESSION['user']['usermenu'] );
	}

    if ( is_object( $zariliaUser ) )
    {
        // unset( $_SESSION['user']['usermenu'] );
        if ( isset( $_SESSION['user']['usermenu'] ) )
        {
			return $_SESSION['user']['usermenu'];
        }

        $block = array();
        $menu_handler = &zarilia_gethandler( 'menus' );
        $user_menu_obj = &$menu_handler->getMenublock( 'usermenu', false );
        $i = 0;
        if ( !empty( $user_menu_obj['list'] ) )
        {
            foreach( $user_menu_obj['list'] as $obj )
            {
                $linktype = $obj->getVar( 'menu_pid' );
                if ( $obj->getVar( 'menu_target' ) )
                {
                    $target = 'target="' . $obj->getVar( 'menu_target' ) . '" ';
                }
                else
                {
                    $target = ' ';
                }
                $url = str_replace( '{X_SITEURL}', ZAR_URL, $obj->getVar( 'menu_link', 'e' ) );
                $url = str_replace( '{X_UID}', $zariliaUser->getVar( 'uid' ), $url );
                /**
                 */
                if ( ( eregi( "mailto:", $url ) ) || ( eregi( "http://", $url ) ) || ( eregi( "https://", $url ) ) || ( eregi( "file://", $url ) ) || ( eregi( "ftp://", $url ) ) )
                {
                    $link = $url;
                }
                else
                {
                    $link = ZAR_URL . "/" . $url;
                }
                $block['usermenu'][$i]['name'] = strip_tags( $obj->getVar( 'menu_title' ) );
                $block['usermenu'][$i]['target'] = $target;
                $block['usermenu'][$i]['image'] = $obj->getVar( 'menu_image' );
                $block['usermenu'][$i]['directory'] = $link;
                $block['usermenu'][$i]['link'] = $obj->getVar( 'menu_pid' );
                $style = ( $obj->getVar( 'menu_pid' ) ) ? 'menuSub' : 'menuMain';
                $block['usermenu'][$i]['style'] = $obj->getVar( 'menu_class' ) ? $obj->getVar( 'menu_class' ) : $style;
                $i++;
            }
        }
        unset( $user_menu_obj );
        $_SESSION['user']['usermenu'] = $block;
        return $block;
    }
    return false;
}

function b_system_rssshow( $options )
{
    $block = array();
    $rss_handler = &zarilia_gethandler( 'rss' );
    if ( $options[0] == 0 )
    {
        $_rss_obj = &$rss_handler->getObjects( new Criteria( 'rss_asblock', 1 ) );
        $count = count( $_rss_obj );
        for ( $i = 0; $i < $count; $i++ )
        {
            $renderer = $rss_handler->zariliarss_getrenderer( $_rss_obj[$i] );
            if ( !$renderer->renderBlock() )
            {
                $block['feeds'][] = sprintf( _HL_FAILGET, $_rss_obj[$i]->getVar( 'rss_name' ) );
                continue;
            }
            $block['feeds'][] = &$renderer->getBlock();
        }
    }
    else
    {
        $_rss_obj = &$rss_handler->get( $SESSION['category'] );
        if ( !is_object( $_rss_obj ) )
        {
            return false;
        }
        $renderer = $rss_handler->zariliarss_getrenderer( $_rss_obj );
        if ( !$renderer->renderBlock() )
        {
            $block['feeds'][] = sprintf( _HL_FAILGET, $_rss_obj->getVar( 'rss_name' ) );
            continue;
        }
        $block['feeds'] = $renderer->getBlock();
    }
    return $block;
}

function b_system_info_show( $options )
{
    global $zariliaConfig, $zariliaUser;

    $zariliaDB = &ZariliaDatabaseFactory::getdatabaseconnection();
    $block = array();
    if ( !empty( $options[3] ) )
    {
        $block['showgroups'] = true;
        $result = $zariliaDB->Execute( "SELECT u.uid, u.uname, u.email, u.user_viewemail, u.user_avatar, g.name AS groupname FROM " . $zariliaDB->prefix( "groups_users_link" ) . " l LEFT JOIN " . $zariliaDB->prefix( "users" ) . " u ON l.uid=u.uid LEFT JOIN " . $zariliaDB->prefix( "groups" ) . " g ON l.groupid=g.groupid WHERE g.group_type='Admin' ORDER BY l.groupid, u.uid" );
        if ( $zariliaDB->getRowsNum( $result ) > 0 )
        {
            $prev_caption = "";
            $i = 0;
            while ( $userinfo = $result->FetchArray() )
            {
                if ( $prev_caption != $userinfo['groupname'] )
                {
                    $prev_caption = $userinfo['groupname'];
                    $block['groups'][$i]['name'] = htmlSpecialChars( $userinfo['groupname'], ENT_QUOTES );
                }
                $avatar_exists = file_exists( ZAR_UPLOAD_PATH . '/' . $userinfo['user_avatar'] && $userinfo['user_avatar'] != 'blank.gif' ) ? true : false;
                $avatar = ( $avatar_exists == true ) ? ZAR_UPLOAD_URL . '/' . $userinfo['user_avatar'] : '';
                $contact_image = zarilia_img_show( 'pmmailtot', _CONTACT, 'middle' );
                if ( is_object( $zariliaUser ) )
                {
                    $block['groups'][$i]['users'][] =
                    array( 'id' => $userinfo['uid'],
                        'name' => htmlspecialchars( $userinfo['uname'], ENT_QUOTES ),
                        'msglink' => "<a href=\"javascript:openWithSelfMain('" . ZAR_URL . "/pmlite.php?send2=1&amp;to_userid=" . $userinfo['uid'] . "','pmlite',450,370);\">$contact_image</a>",
                        'avatar' => $avatar
                        );
                }
                else
                {
                    if ( $userinfo['user_viewemail'] )
                    {
                        $block['groups'][$i]['users'][] = array( 'id' => $userinfo['uid'],
                            'name' => htmlspecialchars( $userinfo['uname'], ENT_QUOTES ),
                            'msglink' => '<a href="mailto:' . $userinfo['email'] . '">' . $contact_image . '</a>',
                            'avatar' => $avatar
                            );
                    }
                    else
                    {
                        $block['groups'][$i]['users'][] =
                        array( 'id' => $userinfo['uid'],
                            'name' => htmlspecialchars( $userinfo['uname'], ENT_QUOTES ),
                            'msglink' => '&nbsp;',
                            'avatar' => $avatar
                            );
                    }
                }
                $i++;
            }
        }
    }
    else
    {
        $block['showgroups'] = false;
    }
    if ( isset( $options[2] ) )
    {
        $block['logourl'] = "<img src=\"" . ZAR_URL . "/images/" . $options[2] . "\" alt=\"\" border=\"0\" />";
    }
    else
    {
        $block['logourl'] = '';
    }
    if ( $options[2] )
    {
        // echo $options[2];
    }
    $block['recommendlink'] = "<a href=\"javascript:openWithSelfMain('" . ZAR_URL . "/misc.php?type=friend&amp;op=sendform&amp;t=" . time() . "','friend'," . $options[0] . "," . $options[1] . ")\">" . _MB_SYSTEM_RECO . "</a>";
    return $block;
}

function b_system_newmembers_show( $options )
{
    $block = array();
    $criteria = new CriteriaCompo( new Criteria( 'level', 0, '>' ) );
    $limit = ( !empty( $options[0] ) ) ? $options[0] : 10;
    $criteria->setOrder( 'DESC' );
    $criteria->setSort( 'user_regdate' );
    $criteria->setLimit( $limit );
    $member_handler = &zarilia_gethandler( 'member' );
    $newmembers = $member_handler->getUsers( $criteria );
    $count = count( $newmembers );
    for ( $i = 0; $i < $count; $i++ )
    {
        $avatar_exists = file_exists( ZAR_UPLOAD_PATH . '/' . $newmembers[$i]->getVar( 'user_avatar' ) && $newmembers[$i]->getVar( 'user_avatar' ) != 'blank.gif' ) ? true : false;
        $block['users'][$i]['avatar'] = ( $options[1] == 1 && $avatar_exists == true ) ? ZAR_UPLOAD_URL . '/' . $newmembers[$i]->getVar( 'user_avatar' ) : '';
        $block['users'][$i]['id'] = $newmembers[$i]->getVar( 'uid' );
        $block['users'][$i]['name'] = $newmembers[$i]->getVar( 'uname' );
        $block['users'][$i]['joindate'] = formatTimestamp( $newmembers[$i]->getVar( 'user_regdate' ), 's' );
    }
    return $block;
}

function b_system_topposters_show( $options )
{
    $block = array();
    $criteria = new CriteriaCompo( new Criteria( 'level', 0, '>' ) );
    $limit = ( !empty( $options[0] ) ) ? $options[0] : 10;
    $size = count( $options );
    for ( $i = 2; $i < $size; $i++ )
    {
        $criteria->add( new Criteria( 'rank', $options[$i], '<>' ) );
    }
    $criteria->setOrder( 'DESC' );
    $criteria->setSort( 'posts' );
    $criteria->setLimit( $limit );
    $member_handler = &zarilia_gethandler( 'member' );
    $topposters = $member_handler->getUsers( $criteria );
    $count = count( $topposters );
    for ( $i = 0; $i < $count; $i++ )
    {
        $block['users'][$i]['rank'] = $i + 1;
        $avatar_exists = file_exists( ZAR_UPLOAD_PATH . '/' . $topposters[$i]->getVar( 'user_avatar' ) && $topposters[$i]->getVar( 'user_avatar' ) != 'blank.gif' ) ? true : false;
        $block['users'][$i]['avatar'] = ( $options[1] == 1 && $avatar_exists == true ) ? ZAR_UPLOAD_URL . '/' . $topposters[$i]->getVar( 'user_avatar' ) : '';
        $block['users'][$i]['id'] = $topposters[$i]->getVar( 'uid' );
        $block['users'][$i]['name'] = $topposters[$i]->getVar( 'uname' );
        $block['users'][$i]['posts'] = $topposters[$i]->getVar( 'posts' );
    }
    return $block;
}

function b_system_comments_show( $options )
{
    $block = array();
    include_once ZAR_ROOT_PATH . '/include/comment_constants.php';
    $comment_handler = &zarilia_gethandler( 'comment' );
    $criteria = new CriteriaCompo( new Criteria( 'com_status', ZAR_COMMENT_ACTIVE ) );
    $criteria->setLimit( intval( $options[0] ) );
    $criteria->setSort( 'com_created' );
    $criteria->setOrder( 'DESC' );
    $comments = &$comment_handler->getObjects( $criteria, true );
    $member_handler = &zarilia_gethandler( 'member' );
    $addon_handler = &zarilia_gethandler( 'addon' );
    $addons = &$addon_handler->getObjects( new Criteria( 'hascomments', 1 ), true );
    $comment_config = array();
    foreach ( array_keys( $comments ) as $i )
    {
        $mid = $comments[$i]->getVar( 'com_modid' );
        $com['addon'] = '<a href="' . ZAR_URL . '/addons/' . $addons[$mid]->getVar( 'dirname' ) . '/">' . $addons[$mid]->getVar( 'name' ) . '</a>';
        if ( !isset( $comment_config[$mid] ) )
        {
            $comment_config[$mid] = $addons[$mid]->getInfo( 'comments' );
        }
        $com['id'] = $i;
        $com['title'] = '<a href="' . ZAR_URL . '/addons/' . $addons[$mid]->getVar( 'dirname' ) . '/' . $comment_config[$mid]['pageName'] . '?' . $comment_config[$mid]['itemName'] . '=' . $comments[$i]->getVar( 'com_itemid' ) . '&amp;com_id=' . $i . '&amp;com_rootid=' . $comments[$i]->getVar( 'com_rootid' ) . '&amp;' . $comments[$i]->getVar( 'com_exparams' ) . '#comment' . $i . '">' . $comments[$i]->getVar( 'com_title' ) . '</a>';
        $com['icon'] = $comments[$i]->getVar( 'com_icon' );
        $com['icon'] = ( $com['icon'] != '' ) ? $com['icon'] : 'icon1.gif';
        $com['time'] = formatTimestamp( $comments[$i]->getVar( 'com_created' ), 'm' );
        if ( $comments[$i]->getVar( 'com_uid' ) > 0 )
        {
            $poster = &$member_handler->getUser( $comments[$i]->getVar( 'com_uid' ) );
            if ( is_object( $poster ) )
            {
                $com['poster'] = '<a href="' . ZAR_URL . '/index.php?page_type=userinfo&uid=' . $comments[$i]->getVar( 'com_uid' ) . '">' . $poster->getVar( 'uname' ) . '</a>';
            }
            else
            {
                $com['poster'] = $GLOBALS['zariliaConfig']['anonymous'];
            }
        }
        else
        {
            $com['poster'] = $GLOBALS['zariliaConfig']['anonymous'];
        }
        $block['comments'][] = &$com;
        unset( $com );
    }
    return $block;
}
// RMV-NOTIFY
function b_system_notification_show()
{
    global $zariliaConfig, $zariliaUser, $zariliaAddon;
    include_once ZAR_ROOT_PATH . '/include/notification_functions.php';
    include_once ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/notifications.php';
    // Notification must be enabled, and user must be logged in
    if ( empty( $zariliaUser ) || !notificationEnabled( 'block' ) )
    {
        return false; // do not display block
    }

    $notification_handler = &zarilia_gethandler( 'notification' );
    // Now build the a nested associative array of info to pass
    // to the block template.
    $block = array();
    $categories = &notificationSubscribableCategoryInfo();
    if ( empty( $categories ) )
    {
        return false;
    }
    foreach ( $categories as $category )
    {
        $section['name'] = $category['name'];
        $section['title'] = $category['title'];
        $section['description'] = $category['description'];
        $section['itemid'] = $category['item_id'];
        $section['events'] = array();
        $subscribed_events = &$notification_handler->getSubscribedEvents ( $category['name'], $category['item_id'], $zariliaAddon->getVar( 'mid' ), $zariliaUser->getVar( 'uid' ) );
        foreach ( notificationEvents( $category['name'], true ) as $event )
        {
            if ( !empty( $event['admin_only'] ) && !$zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) )
            {
                continue;
            }
            $subscribed = in_array( $event['name'], $subscribed_events ) ? 1 : 0;
            $section['events'][$event['name']] = array ( 'name' => $event['name'], 'title' => $event['title'], 'caption' => $event['caption'], 'description' => $event['description'], 'subscribed' => $subscribed );
        }
        $block['categories'][$category['name']] = $section;
    }
    // Additional form data
    $block['target_page'] = "notification_update.php";
    // FIXME: better or more standardized way to do this?
    $script_url = explode( '/', $_SERVER['PHP_SELF'] );
    $script_name = $script_url[count( $script_url )-1];
    $block['redirect_script'] = $script_name;
    $block['submit_button'] = _NOT_UPDATENOW;
    return $block;
}

function b_system_comments_edit( $options )
{
    $inputtag = "<input type='text' name='options[]' value='" . intval( $options[0] ) . "' />";
    $form = sprintf( _MB_SYSTEM_DISPLAYC, $inputtag );
    return $form;
}

function b_system_topposters_edit( $options )
{
    include_once ZAR_ROOT_PATH . '/class/zarilialists.php';
    $inputtag = "<input type='text' name='options[]' value='" . intval( $options[0] ) . "' />";
    $form = sprintf( _MB_SYSTEM_DISPLAY, $inputtag );
    $form .= "<br />" . _MB_SYSTEM_DISPLAYA . "&nbsp;<input type='radio' id='options[]' name='options[]' value='1'";
    if ( $options[1] == 1 )
    {
        $form .= " checked='checked'";
    }
    $form .= " />&nbsp;" . _YES . "<input type='radio' id='options[]' name='options[]' value='0'";
    if ( $options[1] == 0 )
    {
        $form .= " checked='checked'";
    }
    $form .= " />&nbsp;" . _NO . "";
    $form .= "<br />" . _MB_SYSTEM_NODISPGR . "<br /><select id='options[]' name='options[]' multiple='multiple'>";
    $ranks = &ZariliaLists::getUserRankList();
    $size = count( $options );
    foreach ( $ranks as $k => $v )
    {
        $sel = "";
        for ( $i = 2; $i < $size; $i++ )
        {
            if ( $k == $options[$i] )
            {
                $sel = " selected='selected'";
            }
        }
        $form .= "<option value='$k'$sel>$v</option>";
    }
    $form .= "</select>";
    return $form;
}

function b_system_newmembers_edit( $options )
{
    $inputtag = "<input type='text' name='options[]' value='" . $options[0] . "' />";
    $form = sprintf( _MB_SYSTEM_DISPLAY, $inputtag );
    $form .= "<br />" . _MB_SYSTEM_DISPLAYA . "&nbsp;<input type='radio' id='options[]' name='options[]' value='1'";
    if ( $options[1] == 1 )
    {
        $form .= " checked='checked'";
    }
    $form .= " />&nbsp;" . _YES . "<input type='radio' id='options[]' name='options[]' value='0'";
    if ( $options[1] == 0 )
    {
        $form .= " checked='checked'";
    }
    $form .= " />&nbsp;" . _NO . "";
    return $form;
}

function b_system_info_edit( $options )
{
    $form = _MB_SYSTEM_PWWIDTH . "&nbsp;";
    $form .= "<input type='text' name='options[]' value='" . $options[0] . "' />";
    $form .= "<br />" . _MB_SYSTEM_PWHEIGHT . "&nbsp;";
    $form .= "<input type='text' name='options[]' value='" . $options[1] . "' />";
    $form .= "<br />" . sprintf( _MB_SYSTEM_LOGO, ZAR_URL . "/images/" ) . "&nbsp;";
    $form .= "<input type='text' name='options[]' value='" . $options[2] . "' />";
    $chk = "";
    $form .= "<br />" . _MB_SYSTEM_SADMIN . "&nbsp;";
    if ( $options[3] == 1 )
    {
        $chk = " checked='checked'";
    }
    $form .= "<input type='radio' name='options[3]' value='1'" . $chk . " />&nbsp;" . _YES . "";
    $chk = "";
    if ( $options[3] == 0 )
    {
        $chk = " checked=\"checked\"";
    }
    $form .= "&nbsp;<input type='radio' name='options[3]' value='0'" . $chk . " />" . _NO . "";
    return $form;
}

function b_system_themes_show( $options )
{
    global $zariliaConfig;
    $theme_options = '';
    foreach ( $zariliaConfig['theme_set_allowed'] as $theme )
    {
        $theme_options .= '<option value="' . $theme . '"';
        if ( $theme == $zariliaConfig['theme_set'] )
        {
            $theme_options .= ' selected="selected"';
        }
        $theme_options .= '>' . $theme . '</option>';
    }
    $block = array();
    if ( $options[0] == 1 )
    {
        $block['theme_select'] = "<img vspace=\"2\" id=\"zarilia_theme_img\" src=\"" . ZAR_THEME_URL . "/shot.gif\" alt=\"screenshot\" width=\"" . intval( $options[1] ) . "\" /><br /><select id=\"zarilia_theme_select\" name=\"zarilia_theme_select\" onchange=\"showImgSelected('zarilia_theme_img', 'zarilia_theme_select', 'themes', '/shot.gif', '" . ZAR_URL . "');\">" . $theme_options . "</select><input type=\"submit\" value=\"" . _GO . "\" />";
    }
    else
    {
        $block['theme_select'] = '<select name="zarilia_theme_select" onchange="submit();" size="3">' . $theme_options . '</select>';
    }
    $block['theme_select'] .= '<br />(' . sprintf( _MB_SYSTEM_NUMTHEME, '<b>' . count( $zariliaConfig['theme_set_allowed'] ) . '</b>' ) . ')<br />';
    return $block;
}

function b_system_themes_edit( $options )
{
    $chk = "";
    $form = _MB_SYSTEM_THSHOW . "&nbsp;";
    if ( $options[0] == 1 )
    {
        $chk = " checked='checked'";
    }
    $form .= "<input type='radio' name='options[0]' value='1'" . $chk . " />&nbsp;" . _YES;
    $chk = "";
    if ( $options[0] == 0 )
    {
        $chk = ' checked="checked"';
    }
    $form .= '&nbsp;<input type="radio" name="options[0]" value="0"' . $chk . ' />' . _NO;
    $form .= '<br />' . _MB_SYSTEM_THWIDTH . '&nbsp;';
    $form .= "<input type='text' name='options[1]' value='" . $options[1] . "' />";
    return $form;
}

function b_system_stream_show()
{
    $block = array();
    // $streaming_handler = &zarilia_gethandler( 'streaming' );
    // $streaming_handler->getStreamObj( $nav = null );
    $block['title'] = 'streaming Media';
    $block['path'] = ZAR_URL . '/class/streaming/';
    return $block;
}

function b_system_stream_edit()
{
}

?>