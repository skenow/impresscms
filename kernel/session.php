<?php
// $Id: session.php,v 1.1 2007/03/16 02:39:12 catzwolf Exp $
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

/**
 * Handler for a session
 *
 * @package kernel
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaSessionHandler {
    /**
     * Database connection
     *
     * @var object
     * @access private
     */
    var $db;

    /**
     * Constructor
     *
     * @param object $ &$mf    reference to a ZariliaManagerFactory
     */
    function ZariliaSessionHandler( &$db ) {
        $this->db = &$db;
    }

    /**
     * Open a session
     *
     * @param string $save_path
     * @param string $session_name
     * @return bool
     */
    function open( $save_path, $session_name ) {
        return true;
    }

    /**
     * Close a session
     *
     * @return bool
     */
    function close() {
        return true;
    }

    /**
     * Read a session from the database
     *
     * @param string $ &sess_id    ID of the session
     * @return array Session data
     */
    function read( $sess_id ) {
		$old = $this->db->SetFetchMode(ADODB_FETCH_NUM);
        $sql = sprintf( 'SELECT sess_data FROM %s WHERE sess_id = %s', $this->db->prefix( 'session' ), $this->db->qstr( $sess_id ) );
        if ( false != $result = $this->db->Execute( $sql ) ) {
            if ( list( $sess_data ) = $result->FetchRow() ) {
				$this->db->SetFetchMode($old);
                return $sess_data;
            }
        }
		$this->db->SetFetchMode($old);
        return '';
    }

    /**
     * Write a session to the database
     *
     * @param string $sess_id
     * @param string $sess_data
     * @return bool
     */
    function getIp( $ip ) {
        $sess_id = $this->db->qstr( $sess_id );
        list( $count ) = $this->db->fetchRow( $this->db->Execute( "SELECT COUNT(*) FROM " . $this->db->prefix( 'session' ) . " WHERE sess_ip=" . $ip ) );
        $ret = ( $count > 0 ) ? true : false;
        return $ret;
    }

    /**
     * Write a session to the database
     *
     * @param string $sess_id
     * @param string $sess_data
     * @return bool
     */
    function write( $sess_id, $sess_data ) {
		$old = $this->db->SetFetchMode(ADODB_FETCH_NUM);
        $sess_id = $this->db->qstr( $sess_id );		
		$result = $this->db->Execute( "SELECT COUNT(*) FROM " . $this->db->prefix( 'session' ) . " WHERE sess_id=" . $sess_id );		
        list( $count ) = $result->FetchRow();
		$this->db->SetFetchMode($old);
		unset($old);
        if ( $count > 0 ) {
            $sql = sprintf( 'UPDATE %s SET sess_updated = %u, sess_data = %s WHERE sess_id = %s', $this->db->prefix( 'session' ), time(), $this->db->qstr( $sess_data ), $sess_id );
        } else {
            $sql = sprintf( 'INSERT INTO %s (sess_id, sess_updated, sess_ip, sess_data) VALUES (%s, %u, %s, %s)', $this->db->prefix( 'session' ), $sess_id, time(), $this->db->qstr( $_SERVER['REMOTE_ADDR'] ), $this->db->qstr( $sess_data ) );
        }
        if ( !$this->db->Execute( $sql ) ) {
            return false;
        }
        return true;
    }

    /**
     * Destroy a session
     *
     * @param string $sess_id
     * @return bool
     */
    function destroy( $sess_id ) {
        $sql = sprintf( 'DELETE FROM %s WHERE sess_id = %s', $this->db->prefix( 'session' ), $this->db->qstr( $sess_id ) );
        if ( !$result = $this->db->Execute( $sql ) ) {
            return false;
        }
        return true;
    }

    /**
     * Garbage Collector
     *
     * @param int $expire Time in seconds until a session expires
     * @return bool
     */
    function gc( $expire ) {
        $mintime = time() - intval( $expire );
        $sql = sprintf( 'DELETE FROM %s WHERE sess_updated < %u', $this->db->prefix( 'session' ), $mintime );
        return $this->db->Execute( $sql );
    }
}

?>