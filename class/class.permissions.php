<?php
// $Id: class.permissions.php,v 1.3 2007/04/22 07:21:32 catzwolf Exp $
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

include_once ZAR_ROOT_PATH . '/class/zariliaform/grouppermform.php';
/**
 * cpPermissionHandler
 *
 * @package
 * @author Catzwolf
 * @copyright Copyright (c) 2005
 * @version $Id: class.permissions.php,v 1.3 2007/04/22 07:21:32 catzwolf Exp $
 * @access public
 */
class cpPermission {
    var $db;
    var $db_table;
    var $mod_id = 0;
    var $perm_name;
    var $perm_title;

    /**
     * cpPermission::cpPermission()
     *
     * @param string $_table
     * @param string $_perm_name
     * @param string $_perm_title
     * @return
     */
    function cpPermission( $_table = '', $_perm_name = '', $_perm_title = '', $_addon_id ) {
        global $zariliaAddon;

        if ( !empty( $_table ) ) {
            $this->db = &ZariliaDatabaseFactory::getDatabaseConnection();
            $this->db_table = $this->db->prefix( $_table );
        }
        $this->mod_id = intval( $_addon_id );
        $this->perm_name = strval( $_perm_name );
        $this->perm_title = strval( $_perm_title );
    }

    /**
     * cpPermission::cpPermission_render()
     *
     * @param array $_arr
     * @return
     */
    function cpPermission_render( $_arr = array() ) {
        $ret = '';
        $_form_info = new ZariliaGroupPermForm( '',
            $this->mod_id,
            $this->perm_name,
            "<h4 style='margin: 0px;'>" . $this->perm_title . "</h4>"
            );

        $sql = "SELECT {$_arr['cid']}, {$_arr['pid']}, {$_arr['title']} FROM " . $this->db_table;
        if ( !$result = $this->db->Execute( $sql ) ) {
            $_error = $this->db->error() . " : " . $this->db->errno();
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $_error, __FILE__, __LINE__ );
        }

        if ( $this->db->getRowsNum( $result ) ) {
            while ( $_row_arr = $result->FetchArray() ) {
                $_form_info->addItem( $_row_arr[$_arr['cid']], $_row_arr[$_arr['title']], $_row_arr[$_arr['pid']] );
            }
            $ret = $_form_info->render();
        }
        unset( $_form_info );
        return $ret;
    }

    /**
     * cpPermission::cpPermission_get()
     *
     * @param  $_item_id
     * @return
     */
    function cpAdminPermission_get( $_item_id ) {
        $_item_id = intval( $_item_id );
        $gperm_handler = &zarilia_gethandler( 'groupperm' );
        $groups = $gperm_handler->getGroupIds( $this->perm_name, $_item_id, $this->mod_id );
        if ( !count( $groups ) ) {
            $groups = array( 0 => 1, 1 => 2 );
        }
        return $groups;
    }

    /**
     * cpPermission::cpPermission_save()
     *
     * @param string $_groups
     * @param integer $_item_id
     * @return
     */
    function cpPermission_save( $_groups = array(), $_item_id = 0 ) {
        $_item_id = strval( intval( $_item_id ) );
        if ( !is_array( $_groups ) || !count( $_groups ) || $_item_id == 0 ) {
            return false;
        }

        /**
         * Save the new permissions
         */
        $gperm_handler = &zarilia_gethandler( 'groupperm' );
        if ( is_object( $gperm_handler ) && !empty( $gperm_handler ) ) {
            /**
             * First, if the permissions are already there, delete them
             */
            $gperm_handler->deleteByAddon( $this->mod_id, $this->perm_name, $_item_id );
            foreach ( $_groups as $_group_id ) {
                if ( !$gperm_handler->addRight( $this->perm_name, $_item_id, $_group_id, $this->mod_id ) ) {
                    return false;
                }
            }
        } else {
            return false;
        }
        return true;
    }

    /**
     * cpPermission::cpPermission_get()
     *
     * @param  $_item_id
     * @return
     */
    function cpPermission_get( $_item_id ) {
        global $zariliaUser;

        $_item_id = strval( intval( $_item_id ) );
        $_groups = ( is_object( $zariliaUser ) ) ? $zariliaUser->getGroups() : ZAR_GROUP_ANONYMOUS;
        $gperm_handler = &zarilia_gethandler( 'groupperm' );
        if ( $_groups && is_object( $gperm_handler ) ) {
            return $gperm_handler->checkRight( $this->perm_name, $_item_id , $_groups, $this->mod_id );
        }
        return false;
    }

    /**
     */
    function cpPermission_delete( $_item_id ) {
        global $zariliaUser;
        $_item_id = strval( intval( $_item_id ) );
        $_groups = ( is_object( $zariliaUser ) ) ? $zariliaUser->getGroups() : ZAR_GROUP_ANONYMOUS;
        $gperm_handler = &zarilia_gethandler( 'groupperm' );
        if ( $_groups && is_object( $gperm_handler ) ) {
            $gperm_handler->deleteByAddon( $this->mod_id, $this->perm_name, $_item_id );
        }
        return false;
    }
}

?>