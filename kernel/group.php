<?php
// $Id: group.php,v 1.2 2007/04/21 09:44:19 catzwolf Exp $
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

defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );
/**
 *
 * @author John Neill AKA Catzwolf <catzwolf@zarilia.com>
 * @copyright copyright (c) 2006 Zarilia
 */

/**
 * a group of users
 *
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @author Kazumi Ono
 * @package kernel
 */
class ZariliaGroup extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaGroup() {
        $this->ZariliaObject();
        $this->initVar( 'groupid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'name', XOBJ_DTYPE_TXTBOX, null, true, 100 );
        $this->initVar( 'description', XOBJ_DTYPE_TXTAREA, '', false );
        $this->initVar( 'group_type', XOBJ_DTYPE_OTHER, '', false );
    }
}

/**
 * ZARILIA group handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of ZARILIA group class objects.
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 * @subpackage member
 */
class ZariliaGroupHandler extends ZariliaPersistableObjectHandler {
    function ZariliaGroupHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'groups', 'ZariliaGroup', 'groupid', 'name' );
    }
}

/**
 * membership of a user in a group
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 */
class ZariliaMembership extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaMembership() {
        $this->ZariliaObject();
        $this->initVar( 'linkid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'groupid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'uid', XOBJ_DTYPE_INT, null, false );
    }
}

/**
 * ZARILIA membership handler class. (Singleton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of ZARILIA group membership class objects.
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 */
class ZariliaMembershipHandler extends ZariliaPersistableObjectHandler {
    function ZariliaMembershipHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'groups_users_link', 'ZariliaMembership', 'linkid' );
    }

    /**
     * retrieve groups for a user
     *
     * @param int $uid ID of the user
     * @param bool $asobject should the groups be returned as {@link ZariliaGroup}
     * objects? FALSE returns associative array.
     * @return array array of groups the user belongs to
     */
    function getGroupsByUser( $uid ) {
        $ret = array();
        $sql = 'SELECT groupid FROM ' . $this->db->prefix( 'groups_users_link' ) . ' WHERE uid=' . intval( $uid );
        if ( !$result = $this->db->Execute( $sql ) ) {
            return $ret;
        } while ( $myrow = $result->FetchRow() ) {
            $ret[] = intval($myrow['groupid']);
        }
        return $ret;
    }

    /**
     * retrieve users belonging to a group
     *
     * @param int $groupid ID of the group
     * @param bool $asobject return users as {@link ZariliaUser} objects?
     * FALSE will return arrays
     * @param int $limit number of entries to return
     * @param int $start offset of first entry to return
     * @return array array of users belonging to the group
     */
    function getUsersByGroup( $groupid, $limit = 0, $start = 0 ) {
        $ret = array();
        $sql = 'SELECT uid FROM ' . $this->db->prefix( 'groups_users_link' ) . ' WHERE groupid=' . intval( $groupid );
		if (($start == 0) && ($limit == 0))  {
			$result = $this->db->SelectLimit( $sql);
		} elseif ($start == 0) {
			$result = $this->db->SelectLimit( $sql, $limit );
		} elseif ($limit == 0) {
			$result = $this->db->SelectLimit( $sql, null, $start );
		} else {
			$result = $this->db->SelectLimit( $sql, $limit, $start );
		}
		while ( $myrow = $result->FetchRow() ) {
            $ret[] = $myrow['uid'];
        }
        return $ret;
    }
}

?>