<?php
// $Id: user.php,v 1.4 2007/05/05 11:12:12 catzwolf Exp $
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
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * ZariliaUser
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: user.php,v 1.4 2007/05/05 11:12:12 catzwolf Exp $
 * @access public
 */
class ZariliaUser extends ZariliaObject {
    var $_groups = array();
    var $_isAdmin = null;
    var $_rank = null;
    var $_isOnline = null;

    /**
     * constructor
     */
    function ZariliaUser()
    {
        $this->zariliaObject();
        $this->initVar( 'uid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'login', XOBJ_DTYPE_TXTBOX, null, false, 25 );
        $this->initVar( 'name', XOBJ_DTYPE_TXTBOX, null, false, 60 );
        $this->initVar( 'uname', XOBJ_DTYPE_TXTBOX, null, true, 25 );
        $this->initVar( 'email', XOBJ_DTYPE_TXTBOX, null, true, 60 );
        $this->initVar( 'user_regdate', XOBJ_DTYPE_LTIME, null, false );
        $this->initVar( 'user_viewemail', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'actkey', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'pass', XOBJ_DTYPE_TXTBOX, null, false, 32 );
        $this->initVar( 'posts', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'rank', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'level', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'theme', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'timezone_offset', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'last_login', XOBJ_DTYPE_LTIME, time(), false );
        $this->initVar( 'umode', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'uorder', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'ipaddress', XOBJ_DTYPE_TXTBOX, 0, true, 20 );
        $this->initVar( 'url', XOBJ_DTYPE_TXTBOX, 0, false, 100 );
        // RMV-NOTIFY
        $this->initVar( 'notify_method', XOBJ_DTYPE_OTHER, 1, false );
        $this->initVar( 'notify_mode', XOBJ_DTYPE_OTHER, 0, false );
        $this->initVar( 'user_mailok', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'user_coppa_dob', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'user_coppa_agree', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'user_language', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'user_avatar', XOBJ_DTYPE_TXTBOX, 0, false, 100 );
        $this->initVar( 'editor', XOBJ_DTYPE_TXTBOX, 0, false, 100 );
        $this->initVar( 'user_usrlevel', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'user_usrmedpref', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'user_cookie', XOBJ_DTYPE_TXTBOX, null, false, 32 );
        $this->initVar( 'user_anon', XOBJ_DTYPE_INT, 0, false );
    }

    /**
     * check if the user is a guest user
     *
     * @return bool returns false
     */
    function isGuest()
    {
        return false;
    }

    /**
     * Updated by Catzwolf 11 Jan 2004
     * find the username for a given ID
     *
     * @param int $userid ID of the user to find
     * @param int $usereal switch for usename or realname
     * @return string name of the user. name for "anonymous" if not found.
     */
    function getUnameFromId( $userid = 0, $usereal = 0, $is_linked = 1 )
    {		
		if (isset($this)) {
			$zariliaUser = &$this;
		} else {
			global $zariliaUser;
		}		
        $name = '';
        $userid = intval( $userid ) > 0 ? intval( $userid ) : $zariliaUser->getVar( 'uid' );
        $usereal = intval( $usereal );
        if ( $userid > 0 ) {
            $member_handler = &zarilia_gethandler( 'member' );
            $user = &$member_handler->getUser( $userid );
            if ( is_object( $user ) ) {
                if ( $usereal ) {
                    $name = htmlSpecialChars( $user->getVar( 'name' ), ENT_QUOTES );
                } else {
                    $name = htmlSpecialChars( $user->getVar( 'uname' ), ENT_QUOTES );
                }
            }
            if ( $is_linked ) {
                $name = '<a href="' . ZAR_URL . '/index.php?page_type=userinfo&uid=' . $userid . '">' . $name . '</a>';
            }
        } else {
            $name = $GLOBALS['zariliaConfig']['anonymous'];
        }
        return $name;
    }

    /**
     * increase the number of posts for the user
     *
     * @deprecated
     */
    function incrementPost()
    {
        $member_handler = &zarilia_gethandler( 'member' );
        return $member_handler->updateUserByField( $this, 'posts', $this->getVar( 'posts' ) + 1 );
    }
    /**
     * set the groups for the user
     *
     * @param array $groupsArr Array of groups that user belongs to
     */
    function setGroups( $groupsArr )
    {
        if ( is_array( $groupsArr ) ) {
            $this->_groups = &$groupsArr;
        }
    }
    /**
     * get the groups that the user belongs to
     *
     * @return array array of groups
     */
    function getGroups()
    {
//		$this->_groups = array();
        if ( empty( $this->_groups ) ) {
            $member_handler = &zarilia_gethandler( 'member' );
            if ( $this->getVar( 'uid' ) ) {
                $this->_groups = $member_handler->getGroupsByUser( $this->getVar( 'uid' ) );				
            } else {
                $this->_groups = array( 0 => ZAR_GROUP_ANONYMOUS );
            }
        }		
        return $this->_groups;
    }
    /**
     * alias for {@link getGroups()}
     *
     * @see getGroups
     * @return array array of groups
     * @deprecated
     */
    function groups()
    {
        return $this->getGroups();
    }

    /**
     * Is the user admin ?
     *
     * This method will return true if this user has admin rights for the specified addon.<br />
     * - If you don't specify any addon ID, the current addon will be checked.<br />
     * - If you set the addon_id to -1, it will return true if the user has admin rights for at least one addon
     *
     * @param int $addon_id check if user is admin of this addon
     * @return bool is the user admin of that addon?
     */
    function isAdmin( $addon_id = null )
    {
        if ( is_null( $addon_id ) ) {
            $addon_id = isset( $GLOBALS['zariliaAddon'] ) ? $GLOBALS['zariliaAddon']->getVar( 'mid', 'n' ) : 1;
        } elseif ( intval( $addon_id ) < 1 ) {
            $addon_id = 0;
        }
        $addonperm_handler = &zarilia_gethandler( 'groupperm' );
        return $addonperm_handler->checkRight( 'addon_admin', $addon_id, $this->getGroups() );
    }

    /**
     * is the user activated?
     *
     * @return bool
     */
    function isActive()
    {
        if ( $this->getVar( 'level' ) == 0 ) {
            return false;
        }
        return true;
    }

   function getTimeStamp( $time = null, $var = 'user_regdate' )
    {
        $time = $this->getVar( $var );
        return ( strlen( strval( $time ) ) == 10 ) ? formatTimestamp( $time ) : 'Empty';
    }

    /**
     * is the user activated?
     *
     * @return bool
     */
    function isBanned()
    {
        if ( $this->getVar( 'level' ) == 6 ) {
            return true;
        }
        return false;
    }

    /**
     * is the user currently logged in?
     *
     * @return bool
     */
    function isOnline()
    {
        if ( !isset( $this->_isOnline ) ) {
            $onlinehandler = &zarilia_gethandler( 'online' );
            $this->_isOnline = ( $onlinehandler->getCount( new Criteria( 'online_uid', $this->getVar( 'uid' ) ) ) > 0 ) ? true : false;
        }
        return $this->_isOnline;
    }

    /**
     * get the user's rank
     *
     * @return array array of rank ID and title
     */
    function rank( $image = false )
    {
        $rankhandler = &zarilia_gethandler( 'rank' );
        $this->_rank = $rankhandler->getRank( $this->getVar( 'rank' ), $this->getVar( 'posts' ) );
        if ( $image == true ) {
            $ret = "<img src='" . ZAR_UPLOAD_URL . "/" . $this->_rank->getVar( 'rank_image' ) . "' alt='" . $this->_rank->getVar( 'rank_title' ) . "' />";
            return $ret;
        }
        return $this->_rank;
    }

    /**
     * ZariliaUser::avatar()
     *
     * @return
     */
    function avatar()
    {
        $ret = '';
        if ( $this->getVar( 'user_avatar' ) != '' ) {
            if ( file_exists( ZAR_UPLOAD_URL . '/' . $this->getVar( 'user_avatar' ) ) ) {
                $ret = ZAR_UPLOAD_URL . '/' . $this->getVar( 'user_avatar' );
            }
        }

        return $ret;
    }

    /**
     * get the user's email
     *
     * @param string $format format for the output, see {@link ZariliaObject::getVar()}
     * @return string
     */
    function email( $format = "S", $spamguard = false )
    {
        $email = $this->getVar( "email", $format );
        if ( $spamguard == true ) {
            $email = str_replace( "@", " at ", $email );
            $email = str_replace( ".", " dot ", $email );
        }
        return $email;
    }

    /**
     * ZariliaUser::timezone()
     *
     * @return
     */
    function timezone()
    {
        return $this->getVar( "timezone_offset" );
    }

    /**
     * ZariliaUser::getCheckbox()
     *
     * @param mixed $i
     * @param mixed $old_value
     * @return
     */
    function getCheckbox( $i = null, $value = null )
    {
        // <input type='checkbox' name='memberslist_id[]' id='memberslist_id[]' value='".$foundusers[$j]->getVar("uid")."' />
        // <input type='hidden' name='memberslist_uname[".$foundusers[$j]->getVar("uid")."]' id='memberslist_uname[]' value='".$foundusers[$j]->getVar("uname")."' />
        $ret = '<input type="checkbox" name="' . $value . '[]" id="' . $value . '[]" value="' . $this->getVar( 'uid' ) . '"/>';
        $ret .= '<input type="hidden" name="memberslist_uname[' . $this->getVar( 'uid' ) . ']" id="memberslist_uname[]" value="' . $this->getVar( 'uname' ) . '" />';
        return $ret;
    }

    function userLogout()
    {
    }
}

/**
 * Class that represents a guest user
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 */
class ZariliaGuestUser extends ZariliaUser {
    /**
     * check if the user is a guest user
     *
     * @return bool returns true
     */
    function isGuest()
    {
        return true;
    }
}

/**
 * ZARILIA user handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of ZARILIA user class objects.
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 */
class ZariliaUserHandler extends ZariliaPersistableObjectHandler {
    /**
     * categoryHandler::categoryHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaUserHandler( &$db )
    {
        $this->ZariliaPersistableObjectHandler( $db, 'users', 'zariliauser', 'uid', 'uname' );
    }

    /**
     * ZariliaUserHandler::getUsers()
     *
     * @param integer $limit
     * @param string $mod_id
     * @param integer $start
     * @param string $sort
     * @param string $order
     * @return
     */
    function getUsers( $limit = 0, $mod_id = 'a', $start = 0, $sort = 'uid', $order = 'DESC' )
    {
        $criteria = new CriteriaCompo();
        $criteria->setSort( $sort );
        $criteria->setOrder( $order );
        $criteria->setStart( $start );
        $criteria->setLimit( $limit );
        if ( $mod_id != '' ) {
            $criteria->add( new Criteria( 'uname', $mod_id . '%', 'LIKE' ) );
        }
        return $this->getObjects( $criteria, false );
    }
}

?>