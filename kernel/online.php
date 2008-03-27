<?php
// $Id: online.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
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
 * ZariliaOnline
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: online.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
 * @access public
 */
class ZariliaOnline extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaOnline() {
        $this->zariliaObject();
        $this->initVar( 'online_sessionid', XOBJ_DTYPE_TXTBOX, null, false, 35 );
        $this->initVar( 'online_uid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'online_uname', XOBJ_DTYPE_TXTBOX, null, false, 25 );
        $this->initVar( 'online_updated', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'online_addon', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'online_component', XOBJ_DTYPE_TXTBOX, null, false, 25 );
        $this->initVar( 'online_ip', XOBJ_DTYPE_TXTBOX, null, false, 15 );
        $this->initVar( 'online_hidden', XOBJ_DTYPE_INT, 0, false );
    }
}

/**
 * ZariliaOnlineHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: online.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
 * @access public
 */
class ZariliaOnlineHandler extends ZariliaPersistableObjectHandler {
    var $expire;
    /**
     * ZariliaOnlineHandler::ZariliaOnlineHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaOnlineHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'online', 'ZariliaOnline', 'online_sessionid', 'online_uid' );
    }

    function setExpire( $value ) {
        $this->expire = ( $value ) ? $value : 300;
    }

    function write() {
        global $zariliaUser, $zariliaAddon, $zariliaConfig;

        mt_srand( ( double )microtime() * 1000000 );
        // set gc probabillity to 10% for now..
        if ( mt_rand( 1, 100 ) < 11 ) {
            $this->gc( 300 );
        }

		if ( is_object( $zariliaUser ) ) {
            $uid = $zariliaUser->getVar( 'uid' ) ;
            $uname = $zariliaUser->getVar( 'uname' );
            $hidden = $zariliaUser->getVar( 'user_anon' );
        } else {
            $uid = 0;
            $uname = $zariliaConfig['anonymous'];
            $hidden = 0;
        }

        if ( $online_obj = $this->get( session_id() ) ) {
        } else {
            $online_obj = $this->create();
            $online_obj->setVar( 'online_updated', time() );
        }
        $online_obj->setVar( 'online_sessionid', session_id() );

        if ( is_object( $zariliaAddon ) ) {
            $online_obj->setVar( 'online_addon', $zariliaAddon->getVar( 'mid' ) );
        }
		$online_obj->setVar( 'online_component', '' );
        $online_obj->setVar( 'online_ip', $_SERVER['REMOTE_ADDR'] );
        $online_obj->setVar( 'online_uid', $uid );
        $online_obj->setVar( 'online_uname', $uname );
        $online_obj->setVar( 'online_hidden', $hidden );
        $this->insert( $online_obj, true );
    }

    /*
    function destroy( $uid ) {
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'online_uid', $uid, '=' ) );
        return $this->delete( $criteria );
    }
*/

    /*
    function gc( $expire ) {
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'online_updated', time() - intval( $expire ), '<' ) );
        return $this->delete( $criteria );
    }
*/

    function gc( $expire ) {
        $sql = sprintf( "DELETE FROM %s WHERE online_updated < %u", $this->db->prefix( 'online' ), time() - intval( $expire ) );
        $this->db->Execute( $sql );
    }

    /**
     * Delete online information for a user
     *
     * @param int $uid UID
     * @return bool TRUE on success
     */
    function destroy( $uid ) {
        $sql = sprintf( "DELETE FROM %s WHERE online_uid = %u", $this->db->prefix( 'online' ), $uid );
        if ( !$result = $this->db->Execute( $sql ) ) {
            return false;
        }
        return true;
    }

    // /**
    // * Garbage Collection
    // *
    // * Delete all online information that has not been updated for a certain time
    // *
    // * @param int $expire Expiration time in seconds
    // */
    // function gc( $expire ) {
    // $sql = sprintf( "DELETE FROM %s WHERE online_updated < %u", $this->db->prefix( 'online' ), time() - intval( $expire ) );
    // $this->db->Execute( $sql );
    // }
    function getAll( $criteria = null ) {
        return $this->getObjects( $criteria, true, true );
    }
}

?>