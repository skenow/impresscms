<?php
// $Id: tplset.php,v 1.1 2007/03/16 02:39:12 catzwolf Exp $
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
 * ZariliaTplset
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: tplset.php,v 1.1 2007/03/16 02:39:12 catzwolf Exp $
 * @access public
 **/
class ZariliaTplset extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaTplset() {
        $this->ZariliaObject();
        $this->initVar( 'tplset_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'tplset_name', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'tplset_desc', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'tplset_credits', XOBJ_DTYPE_TXTAREA, null, false );
        $this->initVar( 'tplset_created', XOBJ_DTYPE_INT, 0, false );
    }
}

/**
 * ZARILIA tplset handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of ZARILIA tplset class objects.
 *
 * @author Kazumi Ono
 */
class ZariliaTplsetHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaAgeHandler::ZariliaAgeHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaTplsetHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'tplset', 'zariliatplset', 'tplset_id', 'tplset_name' );
    }

    function &getByName( $tplset_name ) {
        $tplset_name = trim( $tplset_name );
        if ( $tplset_name != '' ) {
            $sql = 'SELECT * FROM ' . $this->db->prefix( 'tplset' ) . ' WHERE tplset_name=' . $this->db->qstr( $tplset_name );
            if ( !$result = $this->db->Execute( $sql ) ) {
                return false;
            }
            $numrows = $this->db->getRowsNum( $result );
            if ( $numrows == 1 ) {
                $tplset = new ZariliaTplset();
                $tplset->assignVars( $result->FetchRow() );
                return $tplset;
            }
        }
        return false;
    }

    function delete( &$tplset ) {
        if ( strtolower( get_class( $tplset ) ) != 'zariliatplset' ) {
            return false;
        }
        $sql = sprintf( "DELETE FROM %s WHERE tplset_id = %u", $this->db->prefix( 'tplset' ), $tplset->getVar( 'tplset_id' ) );
        if ( !$result = $this->db->Execute( $sql ) ) {
            return false;
        }
        $sql = sprintf( "DELETE FROM %s WHERE tplset_name = %s", $this->db->prefix( 'imgset_tplset_link' ), $this->db->qstr( $tplset->getVar( 'tplset_name' ) ) );
        $this->db->Execute( $sql );
        return true;
    }

    function gettplsetsObj( $nav = array(), $avatar_type = null, $avatar_display = null ) {
        $criteria = new CriteriaCompo();
        $obj['count'] = $this->getCount( $criteria, false );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $obj['list'] = $this->getObjects( $criteria, false );
        return $obj;
    }

    function &getList( $criteria = null ) {
        $ret = array();
        $tplsets = &$this->getObjects( $criteria, true );
        foreach ( array_keys( $tplsets ) as $i ) {
            $ret[$tplsets[$i]->getVar( 'tplset_name' )] = $tplsets[$i]->getVar( 'tplset_name' );
        }
        return $ret;
    }
}

?>