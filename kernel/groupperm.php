<?php
// $Id: groupperm.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
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
 * A group permission
 *
 * These permissions are managed through a {@link ZariliaGroupPermHandler} object
 *
 * @package kernel
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaGroupPerm extends ZariliaObject {
    /**
     * Constructor
     */
    function ZariliaGroupPerm()
    {
        $this->ZariliaObject();
        $this->initVar( 'gperm_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'gperm_groupid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'gperm_itemid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'gperm_modid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'gperm_name', XOBJ_DTYPE_OTHER, null, false );
    }
}

/**
 * ZARILIA group permission handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of ZARILIA group permission class objects.
 * This class is an abstract class to be implemented by child group permission classes.
 *
 * @see ZariliaGroupPerm
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaGroupPermHandler extends ZariliaPersistableObjectHandler {
    function ZariliaGroupPermHandler( &$db )
    {
        $this->ZariliaPersistableObjectHandler( $db, 'group_permission', 'ZariliaGroupPerm', 'gperm_id' );
    }

    /**
     * Delete all addon specific permissions assigned for a group
     *
     * @param int $gperm_groupid ID of a group
     * @param int $gperm_modid ID of a addon
     * @return bool TRUE on success
     */
    function deleteByGroup( $gperm_groupid, $gperm_modid = null )
    {
        $criteria = new CriteriaCompo( new Criteria( 'gperm_groupid', intval( $gperm_groupid ) ) );
        if ( isset( $gperm_modid ) ) {
            $criteria->add( new Criteria( 'gperm_modid', intval( $gperm_modid ) ) );
        }
        return $this->deleteAll( $criteria );
    }

    /**
     * Delete all addon specific permissions
     *
     * @param int $gperm_modid ID of a addon
     * @param string $gperm_name Name of a addon permission
     * @param int $gperm_itemid ID of a addon item
     * @return bool TRUE on success
     */
    function deleteByAddon( $gperm_modid, $gperm_name = null, $gperm_itemid = null )
    {
        $criteria = new CriteriaCompo( new Criteria( 'gperm_modid', intval( $gperm_modid ) ) );

        if ( isset( $gperm_name ) ) {
            $criteria->add( new Criteria( 'gperm_name', $gperm_name ) );
            if ( isset( $gperm_itemid ) ) {
                $criteria->add( new Criteria( 'gperm_itemid', intval( $gperm_itemid ) ) );
            }
        }
        return $this->deleteAll( $criteria );
    }
    /**
     * *#@-
     */

    /**
     * Check permission
     *
     * @param string $gperm_name Name of permission
     * @param int $gperm_itemid ID of an item
     * @param int $ /array $gperm_groupid    A group ID or an array of group IDs
     * @param int $gperm_modid ID of a addon
     * @return bool TRUE if permission is enabled
     */
    function checkRight( $gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid = 1 )
    {
        global $zariliaUser;

		if ( $zariliaUser && $zariliaUser->getVar( 'uid' ) == 1 ) {
            return true;
        }

        $criteria = new CriteriaCompo( new Criteria( 'gperm_modid', $gperm_modid ) );
        $criteria->add( new Criteria( 'gperm_name', $gperm_name ) );

        if ( is_array( $gperm_groupid ) ) {
            /*
            if ( in_array( ZAR_GROUP_ADMIN, $gperm_groupid ) ) {
                return true;
            }
*/	
			if (count($gperm_groupid)>0) {
	            $criteria->add( new Criteria( 'gperm_groupid', "(" . implode( ',', $gperm_groupid ) . ")", "IN" ) );
			} 
        } else {
            /*
            if ( ZAR_GROUP_ADMIN == $gperm_groupid ) {
                return true;
            }
*/
            $criteria->add( new Criteria( 'gperm_groupid', $gperm_groupid ) );
        }
        $gperm_itemid = intval( $gperm_itemid );
        if ( $gperm_itemid > 0 ) {
            $criteria->add( new Criteria( 'gperm_itemid', $gperm_itemid ) );
        }
        if ( $this->getCount( $criteria ) > 0 ) {
            return true;
        }
        return false;
    }

    /**
     * Add a permission
     *
     * @param string $gperm_name Name of permission
     * @param int $gperm_itemid ID of an item
     * @param int $gperm_groupid ID of a group
     * @param int $gperm_modid ID of a addon
     * @return bool TRUE jf success
     */
    function addRight( $gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid = 1 )
    {
        $perm = &$this->create();
        $perm->setVar( 'gperm_name', $gperm_name );
        $perm->setVar( 'gperm_groupid', $gperm_groupid );
        $perm->setVar( 'gperm_itemid', $gperm_itemid );
        $perm->setVar( 'gperm_modid', $gperm_modid );
        return $this->insert( $perm );
    }

    /**
     * Remove a permission
     *
     * @param string $gperm_name Name of permission
     * @param int $gperm_itemid ID of an item
     * @param int $gperm_groupid ID of a group
     * @param int $gperm_modid ID of a addon
     * @return bool TRUE jf success
     */
    function deleteRight( $gperm_name, $gperm_itemid, $gperm_groupid, $gperm_modid = 1 )
    {
        $criteria = new CriteriaCompo( new Criteria( 'gperm_name', $gperm_name ) );
        $criteria->add( new Criteria( 'gperm_groupid', $gperm_groupid ) );
        $criteria->add( new Criteria( 'gperm_itemid', $gperm_itemid ) );
        $criteria->add( new Criteria( 'gperm_modid', $gperm_modid ) );
        $perm = $this->getObjects( $criteria );
        if ( isset( $perm[0] ) && is_object( $perm[0] ) ) {
            return $this->delete( $perm[0] );
        }
        return false;
    }

    /**
     * Get all item IDs that a group is assigned a specific permission
     *
     * @param string $gperm_name Name of permission
     * @param int $ /array $gperm_groupid    A group ID or an array of group IDs
     * @param int $gperm_modid ID of a addon
     * @return array array of item IDs
     */
    function getItemIds( $gperm_name, $gperm_groupid, $gperm_modid = 1 )
    {
        $ret = array();
        $criteria = new CriteriaCompo( new Criteria( 'gperm_modid', intval( $gperm_modid ) ) );
        $criteria->add( new Criteria( 'gperm_name', $gperm_name ) );
        if ( is_array( $gperm_groupid ) ) {
            $criteria->add( new Criteria( 'gperm_groupid', "(" . implode( ',', $gperm_groupid ) . ")", "IN" ) );
        } else {
            $criteria->add( new Criteria( 'gperm_groupid', intval( $gperm_groupid ) ) );
        }
        $perms = &$this->getObjects( $criteria, true );
        foreach ( array_keys( $perms ) as $i ) {
            $ret[] = $perms[$i]->getVar( 'gperm_itemid' );
        }
        return array_unique( $ret );
    }

    /**
     * Get all group IDs assigned a specific permission for a particular item
     *
     * @param string $gperm_name Name of permission
     * @param int $gperm_itemid ID of an item
     * @param int $gperm_modid ID of a addon
     * @return array array of group IDs
     */
    function getGroupIds( $gperm_name, $gperm_itemid, $gperm_modid = 1 )
    {
        $ret = array();
        $criteria = new CriteriaCompo( new Criteria( 'gperm_modid', intval( $gperm_modid ) ) );
        $criteria->add( new Criteria( 'gperm_name', $gperm_name ) );
        $criteria->add( new Criteria( 'gperm_itemid', intval( $gperm_itemid ) ) );
        $perms = &$this->getObjects( $criteria, true );
        foreach ( array_keys( $perms ) as $i ) {
            $ret[] = $perms[$i]->getVar( 'gperm_groupid' );
        }
        return $ret;
    }
}

?>