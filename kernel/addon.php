<?php
// $Id: addon.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
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

/**
 * A Addons
 *
 * @package kernel
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaAddon extends ZariliaObject {
    /**
     *
     * @var array
     */
    var $addoninfo = null;
    /**
     *
     * @var string
     */
    // var $adminmenu;
    /**
     * Constructor
     */
    function ZariliaAddon() {
        $this->ZariliaObject();
        $this->initVar( 'mid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'name', XOBJ_DTYPE_TXTBOX, null, true, 150 );
        $this->initVar( 'version', XOBJ_DTYPE_INT, 100, false );
        $this->initVar( 'last_update', XOBJ_DTYPE_LTIME, null, false );
        $this->initVar( 'weight', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'isactive', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'dirname', XOBJ_DTYPE_OTHER, null, true );
        $this->initVar( 'hasmain', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'hasadmin', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'hassearch', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'hasconfig', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'hascomments', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'hasage', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'hasmimetype', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'hassubmit', XOBJ_DTYPE_INT, 0, false );
        // RMV-NOTIFY
        $this->initVar( 'hasnotification', XOBJ_DTYPE_INT, 0, false );
    }

    /**
     * Load addon info
     *
     * @param string $dirname Directory Name
     * @param boolean $verbose
     */
    function loadInfoAsVar( $dirname, $verbose = true ) {
        if ( !isset( $this->addoninfo ) ) {
            $this->loadInfo( $dirname, $verbose );
        }

        $this->setVar( 'name', $this->addoninfo['name'], true );
        $this->addoninfo['version'] = strval( $this->addoninfo['version'] );
        $cnt = ( ( $cnt = - strlen( $this->addoninfo['version'] ) + 3 ) < 0 )?0:$cnt;
        $this->setVar( 'version', sprintf( '%10s', $this->addoninfo['version'] . ( $cnt?'.':'' ) . str_repeat( '0', $cnt ) ) );
        unset( $cnt );
        $this->setVar( 'dirname', $this->addoninfo['dirname'], true );

        $hasmain = ( isset( $this->addoninfo['hasMain'] ) && $this->addoninfo['hasMain'] == 1 ) ? 1 : 0;
        $hasadmin = ( isset( $this->addoninfo['hasAdmin'] ) && $this->addoninfo['hasAdmin'] == 1 ) ? 1 : 0;
        $hassearch = ( isset( $this->addoninfo['hasSearch'] ) && $this->addoninfo['hasSearch'] == 1 ) ? 1 : 0;
        $hasconfig = ( ( isset( $this->addoninfo['config'] ) && is_array( $this->addoninfo['config'] ) ) || !empty( $this->addoninfo['hasComments'] ) ) ? 1 : 0;
        $hascomments = ( isset( $this->addoninfo['hasComments'] ) && $this->addoninfo['hasComments'] == 1 ) ? 1 : 0;
        $hasage = ( isset( $this->addoninfo['hasAge'] ) && $this->addoninfo['hasAge'] == 1 ) ? 1 : 0;
        $hasmimetype = ( isset( $this->addoninfo['hasMimetype'] ) && $this->addoninfo['hasMimetype'] == 1 ) ? 1 : 0;
        $hassubmit = ( isset( $this->addoninfo['hasSubmit'] ) && $this->addoninfo['hasSubmit'] == 1 ) ? 1 : 0;
        // RMV-NOTIFY
        $hasnotification = ( isset( $this->addoninfo['hasNotification'] ) && $this->addoninfo['hasNotification'] == 1 ) ? 1 : 0;

        $this->setVar( 'hasmain', $hasmain );
        $this->setVar( 'hasadmin', $hasadmin );
        $this->setVar( 'hassearch', $hassearch );
        $this->setVar( 'hasconfig', $hasconfig );
        $this->setVar( 'hascomments', $hascomments );
        $this->setVar( 'hasage', $hasage );
        $this->setVar( 'hasmimetype', $hasmimetype );
        $this->setVar( 'hassubmit', $hassubmit );
        // RMV-NOTIFY
        $this->setVar( 'hasnotification', $hasnotification );
    }

    /**
     * Get addon info
     *
     * @param string $name
     * @return array |string	Array of addon information.
     * 			If {@link $name} is set, returns a singel addon information item as string.
     */
    function &getInfo( $name = null ) {
        $load = false;
        if ( !isset( $this->addoninfo ) ) {
            $this->loadInfo( $this->getVar( 'dirname' ) );
        }
        if ( isset( $name ) && isset( $this->addoninfo[$name] ) ) {
            $load = $this->addoninfo[$name];
        }
        return $load;
    }

    /**
     * Load the addon info for this addon
     *
     * @param string $dirname Addons directory
     * @param bool $verbose Give an error on fail?
     */
    function loadInfo( $dirname, $verbose = true ) {
        $ret = false;
        global $zariliaConfig, $addonversion;

		if (substr($dirname,0,1)=='.') return;
		if (trim($dirname)=='') return;

        if ( file_exists( ZAR_ROOT_PATH . '/addons/' . $dirname . '/language/' . $zariliaConfig['language'] . '/addoninfo.php' ) ) {
            include_once ZAR_ROOT_PATH . '/addons/' . $dirname . '/language/' . $zariliaConfig['language'] . '/addoninfo.php';
        } else if ( file_exists( ZAR_ROOT_PATH . '/addons/' . $dirname . '/language/english/addoninfo.php' ) ) {
            include_once ZAR_ROOT_PATH . '/addons/' . $dirname . '/language/english/addoninfo.php';
        } else {
            trigger_error( 'Could not find addoninfo.php for ' . $zariliaConfig['language'] .' '. $dirname . ' language define' );
            return $ret;
        }

        if ( file_exists( ZAR_ROOT_PATH . '/addons/' . $dirname . '/zarilia_version.php' ) ) {
            include ZAR_ROOT_PATH . '/addons/' . $dirname . '/zarilia_version.php';
        } else {
            return $ret;
        }

        $this->addoninfo = array();
        if ( !isset( $addonversion['dirname'] ) ) {
            $addonversion['dirname'] = $dirname;
        }
        $this->addoninfo = &$addonversion;
    }

    /**
     * Search contents within a addon
     *
     * @param string $term
     * @param string $andor 'AND' or 'OR'
     * @param integer $limit
     * @param integer $offset
     * @param integer $userid
     * @return mixed Search result.
     */
    function &search( $term = '', $andor = 'AND', $limit = 0, $offset = 0, $userid = 0 ) {
        $search_term = false;
        if ( $this->getVar( 'hassearch' ) == 1 ) {
            $search_term = true;
        }

        if ( $search_term == true ) {
            $search = &$this->getInfo( 'search' );
            if ( $this->getVar( 'hassearch' ) != 1 || !isset( $search['file'] ) || !isset( $search['func'] ) || $search['func'] == '' || $search['file'] == '' ) {
                $search_term = false;
            }

            if ( $search_term == true ) {
                if ( file_exists( ZAR_ROOT_PATH . "/addons/" . $this->getVar( 'dirname' ) . '/' . $search['file'] ) ) {
                    include_once ZAR_ROOT_PATH . '/addons/' . $this->getVar( 'dirname' ) . '/' . $search['file'];
                } else {
                    $search_term = false;
                }
            }

            if ( $search_term == true && function_exists( $search['func'] ) ) {
                $func = $search['func'];
                $functn = $func( $term, $andor, $limit, $offset, $userid );
                return $functn;
            }
        }
        return $search_term;
    }

    /**
     * check user's access to the addon
     *
     * @return bool
     */
    function checkAccess() {
        $ret = false;
        global $zariliaUser, $zariliaOption;
        $groupperm_handler = &zarilia_gethandler( 'groupperm' );
        $groups = $zariliaUser ? $zariliaUser->getGroups() : ZAR_GROUP_ANONYMOUS;
        if ( file_exists( './zarilia_version.php' ) ) {
            $right = 'addon_read';
        } elseif ( file_exists( '../zarilia_version.php' ) ) {
            $zariliaOption['pagetype'] = "admin";
            $right = 'addon_admin';
        } else {
            return $ret;
        }
        return $groupperm_handler->checkRight( $right, $this->getVar( 'mid' ), $groups );
    }

    /**
     * load language strings in a addon
     *
     * @param string $type can be "main", "admin", "blocks" or any other filename located in the addon's language folder
     * @return void
     */
    function loadLanguage( $type = "main" ) {
        if ( $this->getVar( 'dirname' ) == 'system' ) {
            $ret = '';
            return $ret;
        }

        global $zariliaConfig;
        $_language = @file_exists( ZAR_ROOT_PATH . "/addons/" . $this->getVar( 'dirname' ) . "/language/" . $zariliaConfig['language'] . "/" . $type . ".php" ) ? $zariliaConfig['language'] : 'english';
        if ( file_exists( ZAR_ROOT_PATH . "/addons/" . $this->getVar( 'dirname' ) . "/language/" . $_language . "/" . $type . ".php" ) ) {
            include_once @ZAR_ROOT_PATH . "/addons/" . $this->getVar( 'dirname' ) . "/language/" . $_language . "/" . $type . ".php";
        }
    }

    function &getByDirName( $dirname ) {
        $modhandler = &zarilia_gethandler( 'addon' );
        return $modhandler->getByDirname( $dirname );
    }
    /**
     * *#@-
     */

    function getDisplay() {
        $id = $this->getVar( 'mid' );
        if ( $this->getVar( 'dirname' ) == 'system' ) {
            return '&nbsp;';
        } else {
            return $this->getYesNobox( 'mid', 'isactive' );
        }
    }

    function getTextboxes() {
        if ( $this->getVar( 'dirname' ) == 'system' ) {
            return '&nbsp;';
        } else {
            return $this->getTextbox( 'mid', 'weight', '5' );
        }
    }
}

/**
 * ZARILIA addon handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of ZARILIA addon class objects.
 *
 * @package kernel
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaAddonHandler extends ZariliaObjectHandler {
    /**
     * holds an array of cached addon references, indexed by addon id
     *
     * @var array
     * @access private
     */
    var $_cachedAddon_mid = array();

    /**
     * holds an array of cached addon references, indexed by addon dirname
     *
     * @var array
     * @access private
     */
    var $_cachedAddon_dirname = array();

    /**
     * Create a new {@link ZariliaAddon} object
     *
     * @param boolean $isNew Flag the new object as "new"
     * @return object
     */
    function &create( $isNew = true ) {
        $addon = new ZariliaAddon();
        if ( $isNew ) {
            $addon->setNew();
        }
        return $addon;
    }

    /**
     * Load a addon from the database
     *
     * @param int $id ID of the addon
     * @return object FALSE on fail
     */
    function &get( $id ) {
        static $_cachedAddon_dirname;
        static $_cachedAddon_mid;
        $id = intval( $id );
        if ( $id > 0 ) {
            if ( !empty( $_cachedAddon_mid[$id] ) ) {
                return $_cachedAddon_mid[$id];
            } else {
                $sql = 'SELECT * FROM ' . $this->db->prefix( 'addons' ) . ' WHERE mid = ' . $id;
                if ( !$result = $this->db->Execute( $sql ) ) {
                    return false;
                }
                $numrows = $result->RecordCount();
                if ( $numrows == 1 ) {
                    $addon = new ZariliaAddon();
                    $myrow = $result->FetchRow();
                    $addon->assignVars( $myrow );
                    $_cachedAddon_mid[$id] = &$addon;
                    $_cachedAddon_dirname[$addon->getVar( 'dirname' )] = &$addon;
                    return $addon;
                }
            }
        }
        return false;
    }

    /**
     * Load a addon by its dirname
     *
     * @param string $dirname
     * @return object FALSE on fail
     */
    function &getByDirname( $dirname ) {
        $ret = false;
        static $_cachedAddon_mid;
        static $_cachedAddon_dirname;
        if ( !empty( $_cachedAddon_dirname[$dirname] ) ) {
            return $_cachedAddon_dirname[$dirname];
        } else {
            $sql = "SELECT * FROM " . $this->db->prefix( 'addons' ) . " WHERE dirname = '" . trim( $dirname ) . "'";
            if ( !$result = $this->db->Execute( $sql ) ) {
                return $ret;
            }
            $numrows = $result->RecordCount();
            if ( $numrows == 1 ) {
                $addon = new ZariliaAddon();
                $myrow = $result->FetchRow();// $this->db->FetchArray( $result );
                $addon->assignVars( $myrow );
                $_cachedAddon_dirname[$dirname] = &$addon;
                $_cachedAddon_mid[$addon->getVar( 'mid' )] = &$addon;
                return $addon;
            }
            return $ret;
        }
    }

    /**
     * Write a addon to the database
     *
     * @param object $ &$addon reference to a {@link ZariliaAddon}
     * @return bool
     */
    function insert( &$addon ) {
		global $zariliaDB;
        if ( strtolower( get_class( $addon ) ) != 'zariliaaddon' ) {
            return false;
        }
        if ( !$addon->isDirty() ) {
            return true;
        }
        if ( !$addon->cleanVars() ) {
            return false;
        }
        foreach ( $addon->cleanVars as $k => $v ) {
            ${$k} = $v;
        }
        if ( is_null( $this->db ) ) {
            $this->db = &$zariliaDB;
        }
        if ( $addon->isNew() ) {
            // $mid = $this -> db -> genId( 'addons_mid_seq' );
            $sql = sprintf( "INSERT INTO %s (mid, name, version, last_update, weight, isactive, dirname, hasmain, hasadmin, hassearch, hasconfig, hascomments, hasnotification, hasage, hasmimetype) VALUES (%u, %s, %u, %u, %u, %u, %s, %u, %u, %u, %u, %u, %u, %u, %u )", $this->db->prefix( 'addons' ), '', $this->db->Qmagic( $name ), $version, time(), $weight, 1, $this->db->Qmagic( $dirname ), $hasmain, $hasadmin, $hassearch, $hasconfig, $hascomments, $hasnotification, $hasage, $hasmimetype );
        } else {
            $sql = sprintf( "UPDATE %s SET name = %s, dirname = %s, version = %u, last_update = %u, weight = %u, isactive = %u, hasmain = %u, hasadmin = %u, hassearch = %u, hasconfig = %u, hascomments = %u, hasnotification = %u, hasage = %u, hasmimetype = %u WHERE mid = %u", $this->db->prefix( 'addons' ), $this->db->Qmagic( $name ), $this->db->Qmagic( $dirname ), $version, time(), $weight, $isactive, $hasmain, $hasadmin, $hassearch, $hasconfig, $hascomments, $hasnotification, $hasage, $hasmimetype, $mid );
        }
        if ( !$result = $this->db->Execute( $sql ) ) {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
            return false;
        }
        if ( empty( $mid ) || $mid == 0 ) {
            $mid = $this->db->Insert_ID();
        }
        $addon->assignVar( 'mid', $mid );
        if ( !empty( $this->_cachedAddon_dirname[$dirname] ) ) {
            unset ( $this->_cachedAddon_dirname[$dirname] );
        }
        if ( !empty( $this->_cachedAddon_mid[$mid] ) ) {
            unset ( $this->_cachedAddon_mid[$mid] );
        }
        return true;
    }

    /**
     * Delete a addon from the database
     *
     * @param object $ &$addon
     * @return bool
     */
    function delete( &$addon ) {
        if ( strtolower( get_class( $addon ) ) != 'zariliaaddon' ) {
            return false;
        }
        $sql = sprintf( "DELETE FROM %s WHERE mid = %u", $this->db->prefix( 'addons' ), $addon->getVar( 'mid' ) );
        if ( !$result = $this->db->Execute( $sql ) ) {
            return false;
        }
        // delete admin permissions assigned for this addon
        $sql = sprintf( "DELETE FROM %s WHERE gperm_name = 'addon_admin' AND gperm_itemid = %u", $this->db->prefix( 'group_permission' ), $addon->getVar( 'mid' ) );
        $this->db->Execute( $sql );
        // delete read permissions assigned for this addon
        $sql = sprintf( "DELETE FROM %s WHERE gperm_name = 'addon_read' AND gperm_itemid = %u", $this->db->prefix( 'group_permission' ), $addon->getVar( 'mid' ) );
        $this->db->Execute( $sql );

        $sql = sprintf( "SELECT block_id FROM %s WHERE addon_id = %u", $this->db->prefix( 'block_addon_link' ), $addon->getVar( 'mid' ) );
        if ( $result = $this->db->Execute( $sql ) ) {
            $block_id_arr = array();
            while ( $myrow = $result->FetchRow() ) {
                array_push( $block_id_arr, $myrow['block_id'] );
            }
        }
        // loop through block_id_arr
        if ( isset( $block_id_arr ) ) {
            foreach ( $block_id_arr as $i ) {
                $sql = sprintf( "SELECT block_id FROM %s WHERE addon_id != %u AND block_id = %u", $this->db->prefix( 'block_addon_link' ), $addon->getVar( 'mid' ), $i );
                if ( $result2 = $this->db->Execute( $sql ) ) {
                    if ( 0 < $this->db->getRowsNum( $result2 ) ) {
                        // this block has other entries, so delete the entry for this addon
                        $sql = sprintf( "DELETE FROM %s WHERE (addon_id = %u) AND (block_id = %u)", $this->db->prefix( 'block_addon_link' ), $addon->getVar( 'mid' ), $i );
                        $this->db->Execute( $sql );
                    } else {
                        // this block doesnt have other entries, so disable the block and let it show on top page only. otherwise, this block will not display anymore on block admin page!
                        $sql = sprintf( "UPDATE %s SET visible = 0 WHERE bid = %u", $this->db->prefix( 'newblocks' ), $i );
                        $this->db->Execute( $sql );
                        $sql = sprintf( "UPDATE %s SET addon_id = -1 WHERE addon_id = %u", $this->db->prefix( 'block_addon_link' ), $addon->getVar( 'mid' ) );
                        $this->db->Execute( $sql );
                    }
                }
            }
        }

        if ( !empty( $this->_cachedAddon_dirname[$addon->getVar( 'dirname' )] ) ) {
            unset ( $this->_cachedAddon_dirname[$addon->getVar( 'dirname' )] );
        }
        if ( !empty( $this->_cachedAddon_mid[$addon->getVar( 'mid' )] ) ) {
            unset ( $this->_cachedAddon_mid[$addon->getVar( 'mid' )] );
        }
        return true;
    }

    /**
     * Load some addons
     *
     * @param object $criteria {@link CriteriaElement}
     * @param boolean $id_as_key Use the ID as key into the array
     * @return array
     */
    function &getObjects( $criteria = null, $id_as_key = false ) {
        $ret = array();
        $limit = $start = 0;
        $sql = 'SELECT * FROM ' . $this->db->prefix( 'addons' );
        if ( isset( $criteria ) && is_subclass_of( $criteria, 'criteriaelement' ) ) {
            $sql .= ' ' . $criteria->renderWhere();
            if ( $criteria->getSort() != '' ) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }

		if ($limit < 1) {
		    $result = $this->db->Execute( $sql );
		} else {
	        $result = $this->db->SelectLimit( $sql, $limit, $start );
		}
        if ( $result===false ) {
            return $ret;
        }
		while ( $myrow = $result->FetchRow() ) {
            $addon = new ZariliaAddon();
            $addon->assignVars( $myrow );
            if ( !$id_as_key ) {
                $ret[] = &$addon;
            } else {
                $ret[$myrow['mid']] = &$addon;
            }
            unset( $addon );
        }
        return $ret;
    }

    function getAddons( $nav = array(), $type = null ) {
        $criteria = new CriteriaCompo();
        if ( $type == 0 ) {
            $criteria->add ( new Criteria( 'isactive', 1, '=' ) );
        } elseif ( $type == 1 ) {
            $criteria->add ( new Criteria( 'isactive', 0, '=' ) );
        } elseif ( $type == 2 ) {
            $sql = 'SELECT dirname FROM ' . $this->db->prefix( 'addons' );
            $result = &$this->db->Execute( $sql );
            while ( $myrow = $result->FetchRow() ) {
                $obj['list'] = $myrow['dirname'];
            }
            $obj['count'] = count( $obj['list'] );
            return $obj;
        }
        if ( $type != 2 ) {
            $obj['count'] = $this->getCount( $criteria, false );
            $criteria->setSort( $nav['sort'] );
            $criteria->setOrder( $nav['order'] );
            $criteria->setStart( $nav['start'] );
            $criteria->setLimit( $nav['limit'] );
            $obj['list'] = $this->getObjects( $criteria, false );
        }
        return $obj;
    }

    /**
     * Count some addons
     *
     * @param object $criteria {@link CriteriaElement}
     * @return int
     */
    function getCount( $criteria = null ) {
		$old = $this->db->SetFetchMode(ADODB_FETCH_NUM);
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix( 'addons' );
        if ( isset( $criteria ) && is_subclass_of( $criteria, 'criteriaelement' ) ) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if ( !$result = &$this->db->Execute( $sql ) ) {
			$this->db->SetFetchMode($old);
            return 0;
        }
        list( $count ) = $result->FetchRow();
		$this->db->SetFetchMode($old);
        return $count;
    }

    /**
     * returns an array of addon names
     *
     * @param bool $criteria
     * @param boolean $dirname_as_key if true, array keys will be addon directory names
     *                      if false, array keys will be addon id
     * @return array
     */
    function &getList( $criteria = null, $dirname_as_key = false ) {
        $ret = array();
        $addons = &$this->getObjects( $criteria, true );
        foreach ( array_keys( $addons ) as $i ) {
            if ( !$dirname_as_key ) {
                $ret[$i] = &$addons[$i]->getVar( 'name' ) . "(";
            } else {
                $ret[$addons[$i]->getVar( 'dirname' )] = &$addons[$i]->getVar( 'name' );
            }
        }
        return $ret;
    }

    /**
     * ZariliaAddonHandler::getDirList()
     *
     * @param unknown $criteria
     * @param boolean $dirname_as_key
     * @return
     */
    function &getDirList( $criteria = null, $dirname_as_key = false ) {
        $ret = array();
        $addons = &$this->getObjects( $criteria, true );
        foreach ( array_keys( $addons ) as $i ) {
            if ( !$dirname_as_key ) {
                $ret[$i] = &$addons[$i]->getVar( 'dirname' ) . "(";
            } else {
                $ret[$addons[$i]->getVar( 'dirname' )] = &$addons[$i]->getVar( 'name' );
            }
        }
        return $ret;
    }

    /**
     * loads the {@link ZariliaAddon} object from requested url
     *
     * @return object
     */
    function loadAddon() {
        $url_arr = explode( '/', strstr( $_SERVER[ 'REQUEST_URI' ], '/addons/' ) );
        $_addon_name = ( isset( $url_arr[2] ) ) ? $url_arr[2] : 'system';
        unset( $url_arr );
        return $this->getByDirname( $_addon_name );
    }

    /**
     * Checks if there is true addon
     *
     * @param string $dirname
     * @result bool
     */
    function isAddon( $dirname ) {
        $path = ZAR_ROOT_PATH . "/addons/$dirname";
        if ( !file_exists( $path ) ) {
            return false;
        }
        if ( !file_exists( "$path/zarilia_version.php" ) ) {
            return false;
        }
        return true;
    } //*/
    /**
     * ZariliaPersistableObjectHandler::setSubmit()
     *
     * @param string $value
     * @param string $name
     * @param array $_array
     * @return
     */
    function setSubmit( $value = "", $name = "fct", $_array = array() ) {
        if ( empty( $link_array ) ) {
            $_array = array( 'updateall' => _UPDATE_SELECTED );
        }
        $ret = '<select size="1" name="op" id="op">';
        foreach( $_array as $k => $v ) {
            $ret .= '<option value="' . $k . '">' . htmlspecialchars( $v ) . '</option>';
        }
        $ret .= '</select>
			<input type="hidden" name="' . $name . '" value="' . $value . '" />
			<input type="reset" class="formbutton" value="' . _RESET . '" />
			<input type="submit" class="formbutton" value="' . _SUBMIT . '" />';
        return $ret;
    }
}

?>