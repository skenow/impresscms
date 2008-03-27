<?php
// $Id: tplfile.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
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
 * ZariliaTplfile
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: tplfile.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 */
class ZariliaTplfile extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaTplfile() {
        $this->ZariliaObject();
        $this->initVar( 'tpl_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'tpl_refid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'tpl_tplset', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'tpl_file', XOBJ_DTYPE_TXTBOX, null, true, 100 );
        $this->initVar( 'tpl_desc', XOBJ_DTYPE_TXTBOX, null, false, 100 );
        $this->initVar( 'tpl_lastmodified', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'tpl_lastimported', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'tpl_addon', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'tpl_type', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'tpl_source', XOBJ_DTYPE_SOURCE, null, false );
    }

    /**
     * ZariliaTplfile::formEdit()
     *
     * @return
     */
    function formEdit() {
        require_once ZAR_ROOT_PATH . '/kernel/kernel_forms/tplfile.php';
        return $form;
    }

    /**
     * ZariliaTplfile::getSource()
     *
     * @return
     */
    function &getSource() {
        return $this->getVar( 'tpl_source' );
    }

    /**
     * ZariliaTplfile::getLastModified()
     *
     * @return
     */
    function getLastModified() {
        return $this->getVar( 'tpl_lastmodified' );
    }
}

/**
 * ZARILIA template file handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of ZARILIA template file class objects.
 *
 * @author Kazumi Ono
 */

class ZariliaTplfileHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaBannersHandler::ZariliaBannersHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaTplfileHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'tplfile', 'zariliatplfile', 'tpl_id' );
    }

    /**
     * ZariliaTplfileHandler::get()
     *
     * @param mixed $id
     * @param mixed $getsource
     * @return
     **/
    function &get( $id, $getsource = false ) {
        $id = intval( $id );
        if ( $id > 0 ) {
            if ( !$getsource ) {
                $sql = 'SELECT * FROM ' . $this->db_table . ' WHERE tpl_id=' . $id;
            } else {
                $sql = 'SELECT f.*, s.tpl_source FROM ' . $this->db_table . ' f LEFT JOIN ' . $this->db->prefix( 'tplsource' ) . ' s  ON s.tpl_id=f.tpl_id WHERE f.tpl_id=' . $id;
            }
            if ( !$result = $this->db->Execute( $sql ) ) {
                $this->setErrors( 101 );
                return false;
            }
            $numrows = $result->RecordCount();
            if ( $numrows == 1 ) {
                $obj = new $this->obj_class();
                $obj->assignVars( $result->FetchRow() );
                return $obj;
            }
        }
        $this->setErrors( 101 );
        return false;
    }

    function loadSource( &$obj ) {
        if ( strtolower( get_class( $obj ) ) != strtolower( $this->obj_class ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _ER_PAGE_NOT_OBJECT, __FILE__, __LINE__ );
            return false;
        }
        if ( !$obj->getVar( 'tpl_source' ) ) {
            $sql = 'SELECT tpl_source FROM ' . $this->db->prefix( 'tplsource' ) . ' WHERE tpl_id=' . $obj->getVar( $this->keyName );
            if ( !$result = $this->db->Execute( $sql ) ) {
                $this->setErrors( 101 );
                return false;
            }
            $myrow = $this->db->fetchArray( $result );
            $obj->assignVar( 'tpl_source', $myrow['tpl_source'] );
        }
        return true;
    }

    function insert( &$obj, $checkObject = true ) {
        if ( $checkObject === true ) {
            if ( !is_object( $obj ) ) {
                $__LINE__ = __LINE__;
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _ER_PAGE_NOT_OBJECT, __FILE__, $__LINE__ );
                return false;
            }

            if ( !is_a( $obj, $this->obj_class ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _ER_PAGE_NOT_OBJECT, __FILE__, $__LINE__ );
                return false;
            }

            if ( !$obj->isDirty() ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _ER_PAGE_NOT_DIRTY, __FILE__, $__LINE__ );
                return true;
            }
        }

        if ( !$obj->cleanVars() ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _ER_PAGE_NOT_CLEAN, __FILE__, $__LINE__ );
            return false;
        }

        foreach ( $obj->cleanVars as $k => $v ) {
            ${$k} = $v;
        }

        if ( $obj->isNew() ) {
            $tpl_id = $this->db->genId( 'tpltpl_file_id_seq' );
            $sql = sprintf( "INSERT INTO %s (tpl_id, tpl_addon, tpl_refid, tpl_tplset, tpl_file, tpl_desc, tpl_lastmodified, tpl_lastimported, tpl_type) VALUES (%u, %s, %u, %s, %s, %s, %u, %u, %s)", $this->db_table, $tpl_id, $this->db->qstr( $tpl_addon ), $tpl_refid, $this->db->qstr( $tpl_tplset ), $this->db->qstr( $tpl_file ), $this->db->qstr( $tpl_desc ), $tpl_lastmodified, $tpl_lastimported, $this->db->qstr( $tpl_type ) );
            if ( !$result = $this->db->Execute( $sql ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this->db->errno() . " " . $this->db->error(), __FILE__, __LINE__ );
                return false;
            }

            if ( empty( $tpl_id ) ) {
                $tpl_id = $this->db->getInsertId();
            }
            if ( isset( $tpl_source ) && $tpl_source != '' ) {
                $sql = sprintf( "INSERT INTO %s (tpl_id, tpl_source) VALUES (%u, %s)", $this->db->prefix( 'tplsource' ), $tpl_id, $this->db->qstr( $tpl_source ) );
                if ( !$result = $this->db->Execute( $sql ) ) {
                    $this->db->Execute( sprintf( "DELETE FROM %s WHERE tpl_id = %u", $this->db_table, $tpl_id ) );
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this->db->errno() . " " . $this->db->error(), __FILE__, __LINE__ );
                    return false;
                }
            }

            $obj->assignVar( $this->keyName, $tpl_id );
        } else {
            $sql = sprintf( "UPDATE %s SET tpl_tplset = %s, tpl_file = %s, tpl_desc = %s, tpl_lastimported = %u, tpl_lastmodified = %u WHERE tpl_id = %u", $this->db_table, $this->db->Qmagic( $tpl_tplset ), $this->db->Qmagic( $tpl_file ), $this->db->Qmagic( $tpl_desc ), $tpl_lastimported, $tpl_lastmodified, $tpl_id );
            if ( !$result = $this->db->Execute( $sql ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this->db->errno() . " " . $this->db->error(), __FILE__, __LINE__ );
                return false;
            }
            if ( isset( $tpl_source ) && $tpl_source != '' ) {
                $sql = sprintf( "UPDATE %s SET tpl_source = %s WHERE tpl_id = %u", $this->db->prefix( 'tplsource' ), $this->db->Qmagic( $tpl_source ), $tpl_id );
                if ( !$result = $this->db->Execute( $sql ) ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this->db->errno() . " " . $this->db->error(), __FILE__, __LINE__ );
                    return false;
                }
            }
        }
        return true;
    }

    function forceUpdate( &$obj ) {
        if ( $checkObject === true ) {
            if ( !is_object( $obj ) ) {
                $__LINE__ = __LINE__;
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _ER_PAGE_NOT_OBJECT, __FILE__, $__LINE__ );
                return false;
            }

            if ( !is_a( $obj, $this->obj_class ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _ER_PAGE_NOT_OBJECT, __FILE__, $__LINE__ );
                return false;
            }

            if ( !$obj->isDirty() ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _ER_PAGE_NOT_DIRTY, __FILE__, $__LINE__ );
                return true;
            }
        }

        if ( !$obj->cleanVars() ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _ER_PAGE_NOT_CLEAN, __FILE__, $__LINE__ );
            return false;
        }

        foreach ( $obj->cleanVars as $k => $v ) {
            ${$k} = $v;
        }
        if ( !$obj->isNew() ) {
            $sql = sprintf( "UPDATE %s SET tpl_tplset = %s, tpl_file = %s, tpl_desc = %s, tpl_lastimported = %u, tpl_lastmodified = %u WHERE tpl_id = %u", $this->db_table, $this->db->Qmagic( $tpl_tplset ), $this->db->Qmagic( $tpl_file ), $this->db->Qmagic( $tpl_desc ), $tpl_lastimported, $tpl_lastmodified, $tpl_id );
            if ( !$result = $this->db->Execute( $sql ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this->db->errno() . " " . $this->db->error(), __FILE__, __LINE__ );
                return false;
            }
            if ( isset( $tpl_source ) && $tpl_source != '' ) {
                $sql = sprintf( "UPDATE %s SET tpl_source = %s WHERE tpl_id = %u", $this->db->prefix( 'tplsource' ), $this->db->Qmagic( $tpl_source ), $tpl_id );
                if ( !$result = $this->db->Execute( $sql ) ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this->db->errno() . " " . $this->db->error(), __FILE__, __LINE__ );
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    function delete( &$obj ) {
        if ( strtolower( get_class( $obj ) ) != strtolower( $this->obj_class ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _ER_PAGE_NOT_OBJECT, __FILE__, __LINE__ );
            return false;
        }
        $id = $obj->getVar( $this->keyName );
        $sql = sprintf( "DELETE FROM %s WHERE tpl_id = %u", $this->db_table, $id );
        if ( !$result = $this->db->Execute( $sql ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this->db->errno() . " " . $this->db->error(), __FILE__, __LINE__ );
            return false;
        }
        $sql = sprintf( "DELETE FROM %s WHERE tpl_id = %u", $this->db->prefix( 'tplsource' ), $id );
        $this->db->Execute( $sql );
        return true;
    }

    function &getObjects( $criteria = null, $getsource = false, $id_as_key = false ) {
		global $ADODB_FETCH_MODE;
		$ADODB_FETCH_MODE = 'ADODB_FETCH_ASSOC';
        $ret = array();
        $limit = $start = 0;
        if ( $getsource ) {
            $sql = 'SELECT f.*, s.tpl_source FROM ' . $this->db_table . ' f LEFT JOIN ' . $this->db->prefix( 'tplsource' ) . ' s ON s.tpl_id=f.tpl_id';
        } else {
            $sql = 'SELECT * FROM ' . $this->db_table;
        }
        if ( isset( $criteria ) && is_subclass_of( $criteria, 'criteriaelement' ) ) {
            $sql .= ' ' . $criteria->renderWhere() . ' ORDER BY tpl_refid';
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->Execute( $sql, $limit, $start );
        if ( !$result ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this->db->errno() . " " . $this->db->error() , __FILE__, __LINE__ );
            return $ret;
        } while ( $myrow = $result->FetchRow() ) {
            $obj = new $this->obj_class();
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

    function getCount( $criteria = null ) {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db_table;
        if ( isset( $criteria ) && is_subclass_of( $criteria, 'criteriaelement' ) ) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if ( !$result = &$this->db->Execute( $sql ) ) {
            return 0;
        }
        list( $count ) = $this->db->fetchRow( $result );
        return $count;
    }

    function getAddonTplCount( $tplset ) {
        $ret = array();
        $sql = "SELECT tpl_addon, COUNT(tpl_id) AS count FROM " . $this->db_table . " WHERE tpl_tplset='" . $tplset . "' GROUP BY tpl_addon";
        $result = $this->db->Execute( $sql );
        if ( !$result ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this->db->errno() . " " . $this->db->error() , __FILE__, __LINE__ );
            return $ret;
        } while ( $myrow = $this->db->fetchArray( $result ) ) {
            if ( $myrow['tpl_addon'] != '' ) {
                $ret[$myrow['tpl_addon']] = $myrow['count'];
            }
        }
        return $ret;
    }

    function &find( $tplset = null, $type = null, $refid = null, $addon = null, $file = null, $getsource = false ) {
        $criteria = new CriteriaCompo();
        if ( isset( $tplset ) ) {
            $criteria->add( new Criteria( 'tpl_tplset', $tplset ) );
        }
        if ( isset( $addon ) ) {
            $criteria->add( new Criteria( 'tpl_addon', $addon ) );
        }
        if ( isset( $refid ) ) {
            $criteria->add( new Criteria( 'tpl_refid', $refid ) );
        }
        if ( isset( $file ) ) {
            $criteria->add( new Criteria( 'tpl_file', $file ) );
        }
        if ( isset( $type ) ) {
            if ( is_array( $type ) ) {
                $criteria2 = new CriteriaCompo();
                foreach ( $type as $t ) {
                    $criteria2->add( new Criteria( 'tpl_type', $t ), 'OR' );
                }
                $criteria->add( $criteria2 );
            } else {
                $criteria->add( new Criteria( 'tpl_type', $type ) );
            }
        }
        return $this->getObjects( $criteria, $getsource, false );
    }

    function templateExists( $tplname, $tplset_name ) {
        $criteria = new CriteriaCompo( new Criteria( 'tpl_file', trim( $tplname ) ) );
        $criteria->add( new Criteria( 'tpl_tplset', trim( $tplset_name ) ) );
        if ( $this->getCount( $criteria ) > 0 ) {
            return true;
        }
        return false;
    }
}

?>