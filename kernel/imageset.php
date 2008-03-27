<?php 
// $Id: imageset.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
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
 * Zarilia Imageset Class
 * 
 * @package kernel
 * @author John Neill AKA Catzwolf 
 * @copyright (c) 2006 Zarilia
 */
class ZariliaImageset extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaImageset() {
        $this->ZariliaObject();
        $this->initVar( 'imgset_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'imgset_name', XOBJ_DTYPE_TXTBOX, null, true, 50 );
        $this->initVar( 'imgset_refid', XOBJ_DTYPE_INT, 0, false );
    } 
} 

/**
 * ZariliaImagesetHandler
 * 
 * @package 
 * @author Catzwolf 
 * @copyright Copyright (c) 2006
 * @version $Id: imageset.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
 * @access public 
 */
class ZariliaImagesetHandler extends ZariliaPersistableObjectHandler {
    /**
     * constructor
     */
    function ZariliaImagesetHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'image', 'ZariliaImage', 'image_id' );
    } 

    /**
     */
    function delete( &$obj ) {
        if ( strtolower( get_class( $obj ) ) != strtolower( $this->obj_class ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _ER_PAGE_NOT_OBJECT, __FILE__, __LINE__ );
            return false;
        } 
        $sql = sprintf( "DELETE FROM %s WHERE imgset_id = %u", $this->db_table, $obj->getVar( $this->keyName ) );
        if ( !$result = $this->db->Execute( $sql ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this->db->errno() . " " . $this->db->error(), __FILE__, __LINE__ );
            return false;
        } 
        $sql = sprintf( "DELETE FROM %s WHERE imgset_id = %u", $this->db->prefix( 'imgset_tplset_link' ), $obj->getVar( $this->keyName ) );
        $this->db->Execute( $sql );
        return true;
    } 

    /**
     */
    function &getObjects( $criteria = null, $id_as_key = false ) {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT DISTINCT i.* FROM ' . $this->db_table . ' i LEFT JOIN ' . $this->db->prefix( 'imgset_tplset_link' ) . ' l ON l.imgset_id=i.imgset_id';
        if ( isset( $criteria ) && is_subclass_of( $criteria, 'criteriaelement' ) ) {
            $sql .= ' ' . $criteria->renderWhere();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        } 
        if ( !$result = $this->db->SelectLimit( $sql, $limit, $start ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this->db->errno() . " " . $this->db->error(), __FILE__, __LINE__ );
            return $ret;
        } while ( $myrow = $result->FetchRow() ) {
            $obj = &$this->create( false );
            $obj->assignVars( $myrow );
            if ( !$id_as_key ) {
                $ret[] = &$obj;
            } else {
                $ret[$myrow[$this->keyName]] = &$obj;
            } 
            unset( $obj );
        } 
        return $ret;
    } 

    /**
     * User defined
     */
    function linkThemeset( $imgset_id, $tplset_name ) {
        $imgset_id = intval( $imgset_id );
        $tplset_name = trim( $tplset_name );
        if ( $imgset_id <= 0 || $tplset_name == '' ) {
            return false;
        } 
        if ( !$this->unlinkThemeset( $imgset_id, $tplset_name ) ) {
            return false;
        } 
        $sql = sprintf( "INSERT INTO %s (imgset_id, tplset_name) VALUES (%u, %s)", $this->db->prefix( 'imgset_tplset_link' ), $imgset_id, $this->db->Qmagic( $tplset_name ) );
        if ( !$result = $this->db->Execute( $sql ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this->db->errno() . " " . $this->db->error(), __FILE__, __LINE__ );
            return false;
        } 
        return true;
    } 

    function unlinkThemeset( $imgset_id, $tplset_name ) {
        $imgset_id = intval( $imgset_id );
        $tplset_name = trim( $tplset_name );
        if ( $imgset_id <= 0 || $tplset_name == '' ) {
            return false;
        } 
        $sql = sprintf( "DELETE FROM %s WHERE imgset_id = %u AND tplset_name = %s", $this->db->prefix( 'imgset_tplset_link' ), $imgset_id, $this->db->Qmagic( $tplset_name ) );
        if ( !$result = $this->db->Execute( $sql ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this->db->errno() . " " . $this->db->error(), __FILE__, __LINE__ );
            return false;
        } 
        return true;
    } 

    function &getList( $refid = null, $tplset = null ) {
        $ret = array();

        $criteria = new CriteriaCompo();
        if ( isset( $refid ) ) {
            $criteria->add( new Criteria( 'imgset_refid', intval( $refid ) ) );
        } 
        if ( isset( $tplset ) ) {
            $criteria->add( new Criteria( 'tplset_name', $tplset ) );
        } 
        $imgsets = &$this->getObjects( $criteria, true );
        foreach ( array_keys( $imgsets ) as $i ) {
            $ret[$i] = $imgsets[$i]->getVar( 'imgset_name' );
        } 
        return $ret;
    } 
} 

?>