<?php
// $Id: member.php,v 1.2 2007/04/21 09:44:19 catzwolf Exp $
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
 *
 * @author John Neill AKA Catzwolf <catzwolf@zarilia.com>
 * @copyright copyright (c) 2006 Zarilia
 */

require_once ZAR_ROOT_PATH . '/kernel/user.php';
require_once ZAR_ROOT_PATH . '/kernel/group.php';

/**
 * ZARILIA member handler class.
 * This class provides simple interface (a facade class) for handling groups/users/
 * membership data.
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 */
class ZariliaMemberHandler {
    /**
     * *#@+
     * holds reference to group handler(DAO) class
     *
     * @access private
     */
    var $_gHandler;

    /**
     * holds reference to user handler(DAO) class
     */
    var $_uHandler;

    /**
     * holds reference to membership handler(DAO) class
     */
    var $_mHandler;

    /**
     * holds temporary user objects
     */
    var $_members = array();
    /**
     * *#@-
     */

    /**
     * constructor
     */
    function ZariliaMemberHandler( &$db ) {
        $this->_gHandler = new ZariliaGroupHandler( $db );
        $this->_uHandler = new ZariliaUserHandler( $db );
        $this->_mHandler = new ZariliaMembershipHandler( $db );
    }

    /**
     * create a new group
     *
     * @return object ZariliaGroup reference to the new group
     */
    function &createGroup() {
        return $this->_gHandler->create();
    }

    /**
     * create a new user
     *
     * @return object ZariliaUser reference to the new user
     */
    function &createUser() {
        $obj = $this->_uHandler->create();;
        return $obj;
    }

    /**
     * retrieve a group
     *
     * @param int $id ID for the group
     * @return object ZariliaGroup reference to the group
     */
    function getGroup( $id ) {
        return $this->_gHandler->get( $id );
    }

    /**
     * retrieve a group
     *
     * @param int $id ID for the group
     * @return object ZariliaGroup reference to the group
     */
    function getCreate() {
        return $this->_gHandler->create();
    }

    /**
     * retrieve a user
     *
     * @param int $id ID for the user
     * @return object ZariliaUser reference to the user
     */
    function &getUser( $id ) {
        if ( !isset( $this->_members[$id] ) ) {
            $this->_members[$id] = &$this->_uHandler->get( $id );
            if ( count( $this->_members[$id] ) > 0 ) {
                return $this->_members[$id];
            } else {
                return false;
            }
        }
        return $this->_members[$id];
    }

    /**
     * delete a group
     *
     * @param object $group reference to the group to delete
     * @return bool FALSE if failed
     */
    function deleteGroup( &$group ) {
        $this->_gHandler->delete( $group );
        $this->_mHandler->deleteAll( new Criteria( 'groupid', $group->getVar( 'groupid' ) ) );
        return true;
    }

    /**
     * delete a user
     *
     * @param object $user reference to the user to delete
     * @return bool FALSE if failed
     */
    function deleteUser( &$user ) {
        $this->_uHandler->delete( $user );
        $this->_mHandler->deleteAll( new Criteria( 'uid', $user->getVar( 'uid' ) ) );
        return true;
    }

    /**
     * insert a group into the database
     *
     * @param object $group reference to the group to insert
     * @return bool TRUE if already in database and unchanged
     * FALSE on failure
     */
    function insertGroup( &$group ) {
        return $this->_gHandler->insert( $group );
    }

    /**
     * insert a user into the database
     *
     * @param object $user reference to the user to insert
     * @return bool TRUE if already in database and unchanged
     * FALSE on failure
     */
    function insertUser( &$user, $force = false ) {
        return $this->_uHandler->insert( $user, $force );
    }

    /**
     * retrieve groups from the database
     *
     * @param object $criteria {@link CriteriaElement}
     * @param bool $id_as_key use the group's ID as key for the array?
     * @return array array of {@link ZariliaGroup} objects
     */
    function getGroups( $criteria = null, $id_as_key = false ) {
        return $this->_gHandler->getObjects( $criteria, $id_as_key );
    }

    /**
     * retrieve groups from the database
     *
     * @param object $criteria {@link CriteriaElement}
     * @param bool $id_as_key use the group's ID as key for the array?
     * @return array array of {@link ZariliaGroup} objects
     */
    function getGroupsArray( $criteria = null, $id_as_key = false ) {
        $group = $this->_gHandler->getObjects( new Criteria( 'groupid', ZAR_GROUP_ANONYMOUS, '!=' ), $id_as_key );
        for ( $i = 0; $i < count( $group ); $i++ ) {
            $id = $group[$i]->getVar( 'groupid' );
            $groups[$id] = $group[$i]->getVar( 'name' );
        }
        return $groups;
    }

    /**
     * retrieve users from the database
     *
     * @param object $criteria {@link CriteriaElement}
     * @param bool $id_as_key use the group's ID as key for the array?
     * @return array array of {@link ZariliaUser} objects
     */
    function getUsers( $criteria = null, $id_as_key = false ) {
        return $this->_uHandler->getObjects( $criteria, $id_as_key );
    }

    /**
     * retrieve users from the database
     *
     * @param object $criteria {@link CriteriaElement}
     * @param bool $id_as_key use the group's ID as key for the array?
     * @return array array of {@link ZariliaUser} objects
     */
    function getUserslist( $limit = 10, $order, $sort, $start, $level = 0, $id_as_key = false ) {
        $criteria = new CriteriaCompo();
        if ( is_numeric( $level ) ) {
            $criteria->add( new Criteria( 'level', 0 ) );
        } else {
            $criteria->add( new Criteria( 'uname', $list . '%', 'LIKE' ) );
        }
        $criteria->add( new Criteria( 'level', 0 ) );
        $criteria->setOrder( $nav['order'] );
        $criteria->setSort( $nav['sort'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( 20 );
        $users_list = $this->_uHandler->getObjects( $criteria, $id_as_key );
        return $users_list;
    }

    function getUsersByGroups( $groupid = null, $limit = 0, $start = 0, $sort = '', $order = '' ) {
        global $zariliaDB;
        $ret = array();
        $sql = "SELECT DISTINCT u.uid, u.uname FROM " . $zariliaDB->prefix( 'users' ) . " u";
        if ( $groupid != 0 ) {
            $sql .= " LEFT JOIN " . $zariliaDB->prefix( 'groups_users_link' ) . " g ON u.uid = g.uid WHERE g.groupid = " . $groupid;
        }
        if ( $sort != '' ) {
            $sql .= ' ORDER BY u.' . $sort . ' ' . $order;
        }
        if ( !$result = $zariliaDB->SelectLimit( $sql, $limit, $start ) ) {
            return $ret;
        } while ( $myrow = $result->FetchRow() ) {
            $ret[$myrow['uid']] = $myrow['uname'];
        }
        return $ret;
    }

    /**
     * retrieve users belonging to a group and matching conditions
     *
     * @param int $groupid ID of the group
     * @param bool $asobject return users as {@link ZariliaUser} objects?
     * FALSE will return arrays
     * @param int $limit number of entries to return
     * @param int $start offset of first entry to return
     * @return array array of users belonging to the group
     */
    function getUsersByGroupList( $groupid = null, $list_type = null, $list_letters = null, $limit = 0, $start = 0, $sort = '', $order ) {
        global $zariliaDB;
        $ret = array();
        $sql = "SELECT DISTINCT u.uid FROM " . $zariliaDB->prefix( 'users' ) . " u";
        if ( $groupid != 0 ) {
            $sql .= " LEFT JOIN " . $zariliaDB->prefix( 'groups_users_link' ) . " g ON u.uid = g.uid WHERE g.groupid = " . $groupid;
        }
        if ( $list_type >= 0 ) {
            $AND = ( $groupid > 0 ) ? 'AND' : 'WHERE';
            if ( $list_type == 1 ) {
                $sql .= ' ' . $AND . '( u.level=5 OR u.level =' . intval( $list_type ) . ') ';
            } else {
                $sql .= ' ' . $AND . ' u.level=' . intval( $list_type );
            }
        }
        if ( $list_letters != 0 ) {
            $list_letters = "'" . addslashes( stripslashes( chr( $list_letters ) ) ) . "%'";
            $AND = ( $groupid > 0 || $list_type >= 0 ) ? "AND" : "WHERE";
            $sql .= ' ' . $AND . ' u.uname LIKE ' . $list_letters;
        }

        if ( $sort != '' ) {
            $sql .= ' ORDER BY u.' . $sort . ' ' . $order;
        }
        if ( !$result = $zariliaDB->SelectLimit( $sql, $limit, $start ) ) {
            return $ret;
        } while ( $myrow = $result->FetchRow() ) {
            $user = &$this->getUser( $myrow['uid'] );
            if ( is_object( $user ) ) {
                $ret[] = &$user;
            }
            unset( $user );
        }
        return $ret;
    }

    function getUsersByGroupListCount( $groupid = null, $list_type = null, $list_letters = null ) {
        global $zariliaDB;
        $ret = array();
        $sql = "SELECT DISTINCT u.uid FROM " . $zariliaDB->prefix( 'users' ) . " u";
        if ( $groupid != 0 ) {
            $sql .= " LEFT JOIN " . $zariliaDB->prefix( 'groups_users_link' ) . " g ON u.uid = g.uid WHERE g.groupid = " . $groupid;
        }
        if ( $list_type >= 0 ) {
            $AND = ( $groupid > 0 ) ? 'AND' : 'WHERE';
            if ( $list_type == 1 ) {
                $sql .= ' ' . $AND . '( u.level=5 OR u.level =' . intval( $list_type ) . ') ';
            } else {
                $sql .= ' ' . $AND . ' u.level=' . intval( $list_type );
            }
        }
        if ( $list_letters != 0 ) {
            $list_letters = "'" . addslashes( stripslashes( chr( $list_letters ) ) ) . "%'";
            $AND = ( $groupid > 0 || $list_type >= 0 ) ? "AND" : "WHERE";
            $sql .= ' ' . $AND . ' u.uname LIKE ' . $list_letters;
        }
        $result = $zariliaDB->Execute( $sql );
        $count = $result->RecordCount();
        return $count;
    }

    /**
     * get a list of groupnames and their IDs
     *
     * @param object $criteria {@link CriteriaElement} object
     * @return array associative array of group-IDs and names
     */
    function &getGroupList( $criteria = null ) {
        $groups = &$this->_gHandler->getObjects( $criteria, true );
        $ret = array();
        foreach ( array_keys( $groups ) as $i ) {
            $ret[$i] = $groups[$i]->getVar( 'name' );
        }
        unset( $groups );
        return $ret;
    }

    /**
     * get a list of usernames and their IDs
     *
     * @param object $criteria {@link CriteriaElement} object
     * @return array associative array of user-IDs and names
     */
    function getUserList( $criteria = null ) {
        $users = &$this->_uHandler->getObjects( $criteria, true );
        $ret = array();
        foreach ( array_keys( $users ) as $i ) {
            $ret[$i] = $users[$i]->getVar( 'uname' );
        }
        unset( $users );
        return $ret;
    }

    /**
     * add a user to a group
     *
     * @param int $group_id ID of the group
     * @param int $user_id ID of the user
     * @return object ZariliaMembership
     */
    function addUserToGroup( $group_id, $user_id ) {
        $mship = &$this->_mHandler->create();
        $mship->setVar( 'groupid', $group_id );
        $mship->setVar( 'uid', $user_id );
        return $this->_mHandler->insert( $mship );
    }

    /**
     * remove a list of users from a group
     *
     * @param int $group_id ID of the group
     * @param array $user_ids array of user-IDs
     * @return bool success?
     */
    function removeUsersFromGroup( $group_id, $user_ids = array() ) {
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'groupid', $group_id ) );
        $criteria2 = new CriteriaCompo();
        foreach ( $user_ids as $uid ) {
            $criteria2->add( new Criteria( 'uid', $uid ), 'OR' );
        }
        $criteria->add( $criteria2 );
        return $this->_mHandler->deleteAll( $criteria );
    }

    /**
     * get a list of users belonging to a group
     *
     * @param int $group_id ID of the group
     * @param bool $asobject return the users as objects?
     * @param int $limit number of users to return
     * @param int $start index of the first user to return
     * @return array Array of {@link ZariliaUser} objects (if $asobject is TRUE)
     * or of associative arrays matching the record structure in the database.
     */

    function getUsersByGroup( $group_id, $asobject = false, $limit = 0, $start = 0 ) {
        $user_ids = &$this->_mHandler->getUsersByGroup( $group_id, $limit, $start );
        if ( !$asobject ) {
            return $user_ids;
        } else {
            $ret = array();
            foreach ( $user_ids as $u_id ) {
                $user = &$this->getUser( $u_id );
                if ( is_object( $user ) ) {
                    $ret[] = &$user;
                }
                unset( $user );
            }
            return $ret;
        }
    }

    /**
     * get a list of users by name
     *
     * @param bool $asobject return the users as objects?
     * @param int $limit number of users to return
     */
    function getUserByName( $name = '' , $id_as_key = false ) {
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'uname', $name ) );
        $user = &$this->_uHandler->getObjects( $criteria, $id_as_key );
        if ( !$user || count( $user ) != 1 ) {
            return false;
        } else {
            return $user[0];
        }
    }

    /**
     * get a list of users belonging to a group
     *
     * @param int $group_id ID of the group
     * @param bool $asobject return the users as objects?
     * @param int $limit number of users to return
     * @param int $start index of the first user to return
     * @return array Array of {@link ZariliaUser} objects (if $asobject is TRUE)
     * or of associative arrays matching the record structure in the database.
     */
    function getUserBySearch( $name = '' , $value = '', $nav = array(), $id_as_key = false ) {
        $users_list = array();
        $match = ( $name != '' ) ? intval( $name ) : ZAR_MATCH_START;
        $criteria = new CriteriaCompo();
        $value = ( get_magic_quotes_gpc() ) ? addSlashes( trim( $value ) ) : trim( $value );
        if ( $value != '' ) {
            switch ( $match ) {
                case 0:
                    $criteria->add( new Criteria( 'uname', $value . '%', 'LIKE' ) );
                    break;
                case 1:
                    $criteria->add( new Criteria( 'uname', '%' . $value, 'LIKE' ) );
                    break;
                case 2:
                    $criteria->add( new Criteria( 'uname', $value ) );
                    break;
                case 3:
                    $criteria->add( new Criteria( 'uname', '%' . $value . '%', 'LIKE' ) );
                    break;
                case 4:
                    $criteria->add( new Criteria( 'email', $value . '%', 'LIKE' ) );
                    break;
                case 5:
                    $criteria->add( new Criteria( 'email', '%' . $value, 'LIKE' ) );
                    break;
                case 6:
                    $criteria->add( new Criteria( 'email', $value ) );
                    break;
                case 7:
                    $criteria->add( new Criteria( 'email', '%' . $value . '%', 'LIKE' ) );
                    break;
                case 8:
                    $f_user_lastlog_more = intval( trim( $value ) );
                    $time = time() - ( 60 * 60 * 24 * $f_user_lastlog_more );
                    if ( $time > 0 ) {
                        $criteria->add( new Criteria( 'last_login', $time, '<' ) );
                    }
                    break;
                case 9:
                    $f_user_lastlog_less = intval( trim( $value ) );
                    $time = time() - ( 60 * 60 * 24 * $f_user_lastlog_less );
                    if ( $time > 0 ) {
                        $criteria->add( new Criteria( 'last_login', $time, '>' ) );
                    }
                    break;
                case 9:
                    $f_user_reg_more = intval( trim( $value ) );
                    $time = time() - ( 60 * 60 * 24 * $f_user_reg_more );
                    if ( $time > 0 ) {
                        $criteria->add( new Criteria( 'user_regdate', $time, '<' ) );
                    }
                    break;
                case 10:
                    $f_user_reg_less = intval( trim( $value ) );
                    $time = time() - ( 60 * 60 * 24 * $f_user_reg_less );
                    if ( $time > 0 ) {
                        $criteria->add( new Criteria( 'user_regdate', $time, '>' ) );
                    }
                    break;
            }
        }
        $users_list['count'] = $this->_uHandler->getObjects( $criteria, $id_as_key );
        $criteria->setOrder( $nav['order'] );
        $criteria->setSort( $nav['sort'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $users_list['list'] = $this->_uHandler->getObjects( $criteria, $id_as_key );
        return $users_list;
    }

    /**
     * get a list of groups that a user is member of
     *
     * @param int $user_id ID of the user
     * @param bool $asobject return groups as {@link ZariliaGroup} objects or arrays?
     * @return array array of objects or arrays
     */
    function getGroupsByUser( $user_id, $asobject = false ) {
        $group_ids = &$this->_mHandler->getGroupsByUser( $user_id );
        if ( !$asobject ) {
            return $group_ids;
        } else {
            foreach ( $group_ids as $g_id ) {
                $ret[] = &$this->getGroup( intval($g_id) );
            }
            return $ret;
        }
    }

    /**
     * count users matching certain conditions
     *
     * @param object $criteria {@link CriteriaElement} object
     * @return int
     */
    function getUserCount( $criteria = null ) {
        return $this->_uHandler->getCount( $criteria );
    }

    /**
     * count users belonging to a group
     *
     * @param int $group_id ID of the group
     * @return int
     */
    function getUserCountByGroup( $group_id ) {
        return $this->_mHandler->getCount( new Criteria( 'groupid', $group_id ) );
    }

    /**
     * updates a single field in a users record
     *
     * @param object $user reference to the {@link ZariliaUser} object
     * @param string $fieldName name of the field to update
     * @param string $fieldValue updated value for the field
     * @return bool TRUE if success or unchanged, FALSE on failure
     */
    function updateUserByField( &$user, $fieldName, $fieldValue ) {
        $user->setVar( $fieldName, $fieldValue );
        return $this->insertUser( $user );
    }

    /**
     * updates a single field in a users record
     *
     * @param string $fieldName name of the field to update
     * @param string $fieldValue updated value for the field
     * @param object $criteria {@link CriteriaElement} object
     * @return bool TRUE if success or unchanged, FALSE on failure
     */
    function updateUsersByField( $fieldName, $fieldValue, $criteria = null ) {
        return $this->_uHandler->updateAll( $fieldName, $fieldValue, $criteria );
    }

    /**
     * activate a user
     *
     * @param object $user reference to the {@link ZariliaUser} object
     * @return bool successful?
     */
    function activateUser( &$user ) {
        if ( $user->getVar( 'level' ) != 0 ) {
            return true;
        }
        $user->setVar( 'level', 1 );
        return $this->_uHandler->insert( $user, true );
    }

    /**
     * ZariliaMemberHandler::isActivateUser()
     *
     * @param mixed $user
     * @return
     */
    function isActivateUser( &$user ) {
        if ( !is_object( $user ) ) {
            $user = &$this->getUser( $user );
        }
        $isactive = ( !is_object( $user ) || $user->getVar( 'level' ) == 0 || $user->getVar( 'level' ) == 1 ) ? false : true;
        return $isactive;
    }

    /**
     * ZariliaMemberHandler::suspendUser()
     *
     * @param  $user
     * @return
     */
    function suspendUser( &$user ) {
        if ( $user->getVar( 'level' ) != 0 ) {
            return true;
        }
        $user->setVar( 'level', 1 );
        return $this->_uHandler->insert( $user, true );
    }

    /**
     * ZariliaMemberHandler::setSubmit()
     *
     * @param string $value
     * @param string $name
     * @param array $_array
     * @return
     */
    function setSubmit( $value = "", $name = "fct", $_array = array() ) {
        if ( empty( $link_array ) ) {
            $_array = array( 'delete_many' => 'Delete Selected' );
        }
        $ret = '<select size="1" name="op" id="op">';
        foreach( $_array as $k => $v ) {
            $ret .= '<option value="' . $k . '">' . htmlspecialchars( $v ) . '</option>';
        }
        $ret .= '</select>
			<input type="hidden" name="' . $name . '" value="' . $value . '" />
			<input type="submit" class="formbutton" value="' . _SUBMIT . '" />';
        return $ret;
    }


	function form() {
        if ( is_readable( ZAR_ROOT_PATH . '/kernel/kernel_forms/finduser.php' ) ) {
            require_once ZAR_ROOT_PATH . '/kernel/kernel_forms/finduser.php';
        }
    }
}

?>