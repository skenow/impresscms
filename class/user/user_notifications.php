<?php
// $Id: user_notifications.php,v 1.5 2007/05/05 11:11:34 catzwolf Exp $
// auth_zarilia.php - ZARILIA authentification class
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
 * ZariliaUserRegister
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: user_notifications.php,v 1.5 2007/05/05 11:11:34 catzwolf Exp $
 * @access public
 */
class ZariliaUserNotifications extends ZariliaAuth
{
    /**
     * Authentication Service constructor
     */
    function ZariliaUserNotifications()
    {
    }

    function cancel()
    {
        header( 'Location: ' . ZAR_URL );
    }

    function check()
    {
        global $zariliaUser;
        static $del_not;

        if ( !is_object( $zariliaUser ) )
        {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_ERROR_NOTLOGIN );
            return false;
        }
        $del_not = zarilia_cleanRequestVars( $_REQUEST, 'del_not', '', XOBJ_DTYPE_TXTBOX );
        if ( !is_array( $del_not ) && !count( $del_not ) )
        {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _NOT_NOTHINGTODELETE );
            return false;
        }
        $ret['uid'] = $zariliaUser->getVar( 'uid' );
        $ret['del_not'] = $del_not;
        return $ret;
    }

    function delete()
    {
        $ret = $this->check();
        $notification_handler = &zarilia_gethandler( 'notification' );
        foreach ( $ret['del_not'] as $n_array )
        {
            foreach ( $n_array as $n )
            {
                $notification = &$notification_handler->get( $n );
                if ( $notification->getVar( 'not_uid' ) == $ret['uid'] )
                {
                    $notification_handler->delete( $notification );
                }
            }
        }
        redirect_header( 'index.php?page_type=notifications', 2, _NOT_DELETESUCCESS );
    }

    function deleteok()
    {
        $ret = $this->check();
        $ret = '<h4>' . _NOT_DELETINGNOTIFICATIONS . '</h4>';
        $ret .= zarilia_confirm( array( 'uid' => $ret['uid'], 'page_type' => 'notifications', 'act' => 'delete', 'del_not' => $ret['del_not'] ), 'index.php', _NOT_RUSUREDEL );
        return $ret;
    }

    function isdefault()
    {
        $ret = $this->check();

        global $zariliaUser;

        $criteria = new Criteria ( 'not_uid', $ret['uid'] );
        $criteria->setSort ( 'not_modid,not_category,not_itemid' );

        $notification_handler = &zarilia_gethandler( 'notification' );
        $notifications = &$notification_handler->getObjects( $criteria );

        $addon_handler = &zarilia_gethandler( 'addon' );
        include_once ZAR_ROOT_PATH . '/include/notification_functions.php';

        $addons = array();
        $prev_modid = -1;
        $prev_category = -1;
        $prev_item = -1;
        foreach ( $notifications as $n )
        {
            $modid = $n->getVar( 'not_modid' );
            if ( $modid != $prev_modid )
            {
                $prev_modid = $modid;
                $prev_category = -1;
                $prev_item = -1;
                $addon = &$addon_handler->get( $modid );
                $addons[$modid] = array ( 'id' => $modid, 'name' => $addon->getVar( 'name' ), 'categories' => array() );
                // *//
                $not_config = $addon->getInfo( 'notification' );
                $lookup_func = '';
                if ( !empty( $not_config['lookup_file'] ) )
                {
                    $lookup_file = ZAR_ROOT_PATH . '/addons/' . $addon->getVar( 'dirname' ) . '/' . $not_config['lookup_file'];
                    if ( file_exists( $lookup_file ) )
                    {
                        include_once $lookup_file;
                        if ( !empty( $not_config['lookup_func'] ) && function_exists( $not_config['lookup_func'] ) )
                        {
                            $lookup_func = $not_config['lookup_func'];
                        }
                    }
                }
            }
            $category = $n->getVar( 'not_category' );
            if ( $category != $prev_category )
            {
                $prev_category = $category;
                $prev_item = -1;
                $category_info = &notificationCategoryInfo( $category, $modid );
                $addons[$modid]['categories'][$category] = array ( 'name' => $category, 'title' => $category_info['title'], 'items' => array() );
            }
            $item = $n->getVar( 'not_itemid' );
            if ( $item != $prev_item )
            {
                $prev_item = $item;
                if ( !empty( $lookup_func ) )
                {
                    $item_info = $lookup_func( $category, $item );
                }
                else
                {
                    $item_info = array ( 'name' => '[' . _NOT_NAMENOTAVAILABLE . ']', 'url' => '' );
                }
                $addons[$modid]['categories'][$category]['items'][$item] = array ( 'id' => $item, 'name' => $item_info['name'], 'url' => $item_info['url'], 'notifications' => array() );
            }
            $event_info = &notificationEventInfo( $category, $n->getVar( 'not_event' ), $n->getVar( 'not_modid' ) );
            $addons[$modid]['categories'][$category]['items'][$item]['notifications'][] = array ( 'id' => $n->getVar( 'not_id' ), 'addon_id' => $n->getVar( 'not_modid' ), 'category' => $n->getVar( 'not_category' ), 'category_title' => $category_info['title'], 'item_id' => $n->getVar( 'not_itemid' ), 'event' => $n->getVar( 'not_event' ), 'event_title' => $event_info['title'], 'user_id' => $n->getVar( 'not_uid' ) );
        }
        $ret['template_main'] = 'system_notification_list.html';
        $ret['addons'] = $addons;
        $ret['user'] = array ( 'uid' => $zariliaUser->getVar( 'uid' ) );
        // $zariliaTpl->assign ( 'lang_event', _NOT_EVENT );
        return $ret;
    }
}

?>