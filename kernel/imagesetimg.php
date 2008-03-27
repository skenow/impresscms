<?php
// $Id: imagesetimg.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
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
 * Zarilia Age Class
 * 
 * @package kernel
 * @author John Neill AKA Catzwolf 
 * @copyright (c) 2006 Zarilia
 */
class ZariliaImagesetimg extends ZariliaObject {
    function ZariliaImagesetimg() {
        $this->ZariliaObject();
        $this->initVar( 'imgsetimg_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'imgsetimg_file', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'imgsetimg_body', XOBJ_DTYPE_SOURCE, null, false );
        $this->initVar( 'imgsetimg_imgset', XOBJ_DTYPE_INT, null, false );
    } 
} 

/**
 * ZARILIA imageset image handler class.  
 * This class is responsible for providing data access mechanisms to the data source 
 * of ZARILIA imageset image class objects.
 * 
 * @author Kazumi Ono 
 */
class ZariliaImagesetimgHandler extends ZariliaPersistableObjectHandler {
    
    /**
     * ZariliaAgeHandler::ZariliaAgeHandler()
     * 
     * @param  $db 
     * @return 
     */
    function ZariliaImagesetimgHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'imgsetimg', 'zariliaimagesetimg', 'imgsetimg_id' );
    	$this -> db_table_link = $this->db->prefix('imgset_tplset_link');
	} 	

    function &getObjects( $criteria = null, $id_as_key = false ) {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT DISTINCT i.* FROM ' . $this -> db_table . ' i LEFT JOIN ' . $this -> db_table_link . ' l ON l.imgset_id=i.imgsetimg_imgset LEFT JOIN ' . $this->db->prefix( 'imgset' ) . ' s ON s.imgset_id=l.imgset_id';
        if ( isset( $criteria ) && is_subclass_of( $criteria, 'criteriaelement' ) ) {
            $sql .= ' ' . $criteria->renderWhere();
            $sql .= ' ORDER BY imgsetimg_id ' . $criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        } 
        if ( !$result = $this->db->Execute( $sql, $limit, $start )) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this -> db -> errno()." ".$this -> db -> error() , __FILE__, __LINE__ );
            return $ret;
        } while ( $myrow = $result->FetchRow() ) {
            $imgsetimg = new ZariliaImagesetimg();
            $imgsetimg->assignVars( $myrow );
            if ( !$id_as_key ) {
                $ret[] = &$imgsetimg;
            } else {
                $ret[$myrow['imgsetimg_id']] = &$imgsetimg;
            } 
            unset( $imgsetimg );
        } 
        return $ret;
    } 

    /**
     * ZariliaImagesetimgHandler::getCount()
     * 
     * @param unknown $criteria
     * @return 
     **/
    function getCount( $criteria = null ) {
        $sql = 'SELECT COUNT(i.imgsetimg_id) crx FROM ' . $this -> db_table . ' i LEFT JOIN ' . $this -> db_table_link . ' l ON l.imgset_id=i.imgsetimg_imgset';
        if ( isset( $criteria ) && is_subclass_of( $criteria, 'criteriaelement' ) ) {
            $sql .= ' ' . $criteria->renderWhere() . ' GROUP BY i.imgsetimg_id';
        } 
        if ( !$result = &$this->db->Execute( $sql ) ) {
            return 0;
        } 
        $count  = $result->FetchRow();
        return $count['crx'];
    } 

    /**
     * Function-Documentation
     * 
     * @param type $imgset_id documentation
     * @param type $id_as_key = false documentation
     * @return type documentation
     * @author Kazumi Ono 
     */
    function &getByImageset( $imgset_id, $id_as_key = false ) {
        return $this->getObjects( new Criteria( 'imgsetimg_imgset', intval( $imgset_id ) ), $id_as_key );
    } 

    /**
     * Function-Documentation
     * 
     * @param type $filename documentation
     * @param type $imgset_id documentation
     * @return type documentation
     * @author Kazumi Ono 
     */
    function imageExists( $filename, $imgset_id ) {
        $criteria = new CriteriaCompo( new Criteria( 'imgsetimg_file', $filename ) );
        $criteria->add( new Criteria( 'imgsetimg_imgset', intval( $imgset_id ) ) );
        if ( $this->getCount( $criteria ) > 0 ) {
            return true;
        } 
        return false;
    } 
} 

?>