<?php
// $Id: block.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
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
 * A block
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 */
class ZariliaBlock extends ZariliaObject {
    /**
     * constructor
     *
     * @param mixed $id
     */
    function ZariliaBlock( $id = 0 ) {
        $this->ZariliaObject();
        $this->initVar( 'bid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'mid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'func_num', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'options', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'name', XOBJ_DTYPE_TXTBOX, null, true, 150 );
        $this->initVar( 'position', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'title', XOBJ_DTYPE_TXTBOX, null, false, 150 );
        $this->initVar( 'content', XOBJ_DTYPE_TXTAREA, null, false );
        $this->initVar( 'side', XOBJ_DTYPE_INT, 9, false );
        $this->initVar( 'weight', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'block_type', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'c_type', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'isactive', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'dirname', XOBJ_DTYPE_TXTBOX, null, false, 50 );
        $this->initVar( 'func_file', XOBJ_DTYPE_TXTBOX, null, false, 50 );
        $this->initVar( 'show_func', XOBJ_DTYPE_TXTBOX, null, false, 50 );
        $this->initVar( 'edit_func', XOBJ_DTYPE_TXTBOX, null, false, 50 );
        $this->initVar( 'template', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'bcachetime', XOBJ_DTYPE_LTIME, 0, false );
        $this->initVar( 'last_modified', XOBJ_DTYPE_LTIME, time(), false );
        $this->initVar( 'liveupdate', XOBJ_DTYPE_INT, 0, false );
    }

    /**
     * return the content of the block for output
     *
     * @param string $format
     * @param string $c_type type of content<br>
     * Legal value for the type of content<br>
     * <ul><li>H : custom HTML block
     * <li>P : custom PHP block
     * <li>S : use text sanitizater (smilies enabled)
     * <li>T : use text sanitizater (smilies disabled)</ul>
     * @return string content for output
     */
    function getContent( $format = 'S', $c_type = 'T' ) {
        switch ( $format ) {
            case 'S':
                $myts = &MyTextSanitizer::getInstance();
                if ( $c_type == 'H' ) {
                    return str_replace( '{X_SITEURL}', ZAR_URL . '/', $this->getVar( 'content', 'N' ) );
                } elseif ( $c_type == 'P' ) {
                    ob_start();
                    $content = ob_get_contents();
                    ob_end_clean();
                    return str_replace( '{X_SITEURL}', ZAR_URL . '/', $content );
                } elseif ( $c_type == 'S' ) {
                    return str_replace( '{X_SITEURL}', ZAR_URL . '/', $myts->displayTarea( $this->getVar( 'content', 'N' ), 0, 1 ) );
                } else {
                    return str_replace( '{X_SITEURL}', ZAR_URL . '/', $myts->displayTarea( $this->getVar( 'content', 'N' ), 0, 0 ) );
                }
                break;
            case 'E':
                return $this->getVar( 'content', 'E' );
                break;
            default:
                return $this->getVar( 'content', 'N' );
                break;
        }
    }

    /**
     * (HTML-) form for setting the options of the block
     *
     * @return string HTML for the form, FALSE if not defined for this block
     */
    function getOptions() {
        if ( $this->getVar( 'block_type' ) != 'C' ) {
            $edit_func = $this->getVar( 'edit_func' );
            if ( !$edit_func ) {
                return false;
            }
            if ( file_exists( ZAR_ROOT_PATH . '/addons/' . $this->getVar( 'dirname' ) . '/blocks/' . $this->getVar( 'func_file' ) ) ) {
                if ( file_exists( ZAR_ROOT_PATH . '/addons/' . $this->getVar( 'dirname' ) . '/language/' . $GLOBALS['zariliaConfig']['language'] . '/blocks.php' ) ) {
                    include_once ZAR_ROOT_PATH . '/addons/' . $this->getVar( 'dirname' ) . '/language/' . $GLOBALS['zariliaConfig']['language'] . '/blocks.php';
                } elseif ( file_exists( ZAR_ROOT_PATH . '/addons/' . $this->getVar( 'dirname' ) . '/language/english/blocks.php' ) ) {
                    include_once ZAR_ROOT_PATH . '/addons/' . $this->getVar( 'dirname' ) . '/language/english/blocks.php';
                }
                include_once ZAR_ROOT_PATH . '/addons/' . $this->getVar( 'dirname' ) . '/blocks/' . $this->getVar( 'func_file' );
                $options = explode( '|', $this->getVar( 'options' ) );
                $edit_form = $edit_func( $options );
                if ( !$edit_form ) {
                    return false;
                }
                return $edit_form;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getCacheTime() {
        $cachetimes = array( '0' => _NOCACHE, '30' => sprintf( _SECONDS, 30 ), '60' => _MINUTE, '300' => sprintf( _MINUTES, 5 ), '1800' => sprintf( _MINUTES, 30 ), '3600' => _HOUR, '18000' => sprintf( _HOURS, 5 ), '86400' => _DAY, '259200' => sprintf( _DAYS, 3 ), '604800' => _WEEK, '2592000' => _MONTH );
    }

    function getBlockSide() {
        $side_options = array(
            ZAR_SIDEBLOCK_LEFT => _AM_SBLEFT,
            ZAR_SIDEBLOCK_RIGHT => _AM_SBRIGHT,
            ZAR_CENTERBLOCK_LEFT => _AM_CBLEFT,
            ZAR_CENTERBLOCK_RIGHT => _AM_CBRIGHT,
            ZAR_CENTERBLOCK_CENTER => _AM_CBCENTER,
            ZAR_CENTERBLOCKDOWN_LEFT => _AM_CBLEFTDOWN,
            ZAR_CENTERBLOCKDOWN_RIGHT => _AM_CBRIGHTDOWN,
            ZAR_CENTERBLOCKDOWN_CENTER => _AM_CBCENTERDOWN,
            ZAR_BLOCK_INVISIBLE_EDIT => _AM_NOTVISIBLE );
    }
}

/**
 * ZARILIA block handler class. (Singelton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of ZARILIA block class objects.
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 * @subpackage block
 */
class ZariliaBlockHandler extends ZariliaPersistableObjectHandler {
    /*
	* Constructor
	*/
    function ZariliaBlockHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'newblocks', 'zariliablock', 'bid' );
    }

    /**
     * delete a block from the database
     *
     * @param object $ ZariliaBlock $obj reference to the block to delete
     * @return bool TRUE if succesful
     */
    function delete( &$obj ) {
        if ( strtolower( get_class( $obj ) ) != strtolower( $this->obj_class ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _ER_PAGE_NOT_OBJECT, __FILE__, __LINE__ );
            return false;
        }
        $id = $obj->getVar( $this->keyName );
        $sql = sprintf( "DELETE FROM %s WHERE bid = %u", $this->db->prefix( 'newblocks' ), $id );
        if ( !$result = $this->db->Execute( $sql ) ) {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
            return false;
        }
        $sql = sprintf( "DELETE FROM %s WHERE block_id = %u", $this->db->prefix( 'block_addon_link' ), $id );
        if ( !$this->db->Execute( $sql ) ) {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
            return false;
        }
        return true;
    }

    /**
     * retrieve array of {@link ZariliaBlock}s meeting certain conditions
     *
     * @param object $criteria {@link CriteriaElement} with conditions for the blocks
     * @param bool $id_as_key should the blocks' bid be the key for the returned array?
     * @return array {@link ZariliaBlock}s matching the conditions
     */
    function getObjects( $criteria = null, $id_as_key = false ) {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT DISTINCT(b.*) FROM ' . $this->db->prefix( 'newblocks' ) . ' b LEFT JOIN ' . $this->db->prefix( 'block_addon_link' ) . ' l ON b.bid=l.block_id';
        if ( isset( $criteria ) && is_subclass_of( $criteria, 'criteriaelement' ) ) {
            $sql .= ' ' . $criteria->renderWhere();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        if ( !$result = $this->db->SelectLimit( $sql, $limit, $start ) ) {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
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
     * get a list of blocks matchich certain conditions
     *
     * @param string $criteria conditions to match
     * @return array array of blocks matching the conditions
     */
    function getList( $criteria = null ) {
        $objs = &$this->getObjects( $criteria, true );
        $ret = array();
        foreach ( array_keys( $objs ) as $i ) {
            $name = ( $objs[$i]->getVar( 'block_type' ) != 'C' ) ? $objs[$i]->getVar( 'name' ) : $objs[$i]->getVar( 'title' );
            $ret[$i] = $name;
        }
        return $ret;
    }
}

?>