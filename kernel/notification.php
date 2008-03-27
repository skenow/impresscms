<?php
// $Id: notification.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
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

// RMV-NOTIFY
include_once ZAR_ROOT_PATH . '/include/notification_constants.php';
include_once ZAR_ROOT_PATH . '/include/notification_functions.php';

/**
 *
 * @package kernel
 * @subpackage notification
 * @author Michael van Dam <mvandam@caltech.edu>
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */

/**
 * A Notification
 *
 * @package kernel
 * @subpackage notification
 * @author Michael van Dam <mvandam@caltech.edu>
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaNotification extends ZariliaObject {
    /**
     * Constructor
     */
    function ZariliaNotification()
    {
        $this -> ZariliaObject();
        $this -> initVar( 'not_id', XOBJ_DTYPE_INT, null, false );
        $this -> initVar( 'not_modid', XOBJ_DTYPE_INT, null, false );
        $this -> initVar( 'not_category', XOBJ_DTYPE_TXTBOX, null, false, 30 );
        $this -> initVar( 'not_itemid', XOBJ_DTYPE_INT, 0, false );
        $this -> initVar( 'not_event', XOBJ_DTYPE_TXTBOX, null, false, 30 );
        $this -> initVar( 'not_uid', XOBJ_DTYPE_INT, 0, true );
        $this -> initVar( 'not_mode', XOBJ_DTYPE_INT, 0, false );
    }
    // FIXME:???
    // To send email to multiple users simultaneously, we would need to move
    // the notify functionality to the handler class.  BUT, some of the tags
    // are user-dependent, so every email msg will be unique.  (Unless maybe use
    // smarty for email templates in the future.)  Also we would have to keep
    // track if each user wanted email or PM.
    /**
     * Send a notification message to the user
     *
     * @param string $template_dir Template directory
     * @param string $template Template name
     * @param string $subject Subject line for notification message
     * @param array $tags Array of substitutions for template variables
     * @return bool true if success, false if error
     */
    function notifyUser( $template_dir, $template, $subject, $tags )
    {
        // Check the user's notification preference.
        $member_handler = &zarilia_gethandler( 'member' );
        $user = &$member_handler -> getUser( $this -> getVar( 'not_uid' ) );
        if ( !is_object( $user ) ) {
            return true;
        }
        $method = $user -> getVar( 'notify_method' );

        $zariliaMailer = &getMailer();
        include_once ZAR_ROOT_PATH . '/include/notification_constants.php';
        switch ( $method ) {
            case ZAR_NOTIFICATION_METHOD_PM:
                $zariliaMailer -> usePM();
                $config_handler = &zarilia_gethandler( 'config' );
                $zariliaMailerConfig = &$config_handler -> getConfigsByCat( ZAR_CONF_MAILER );
                $zariliaMailer -> setFromUser( $member_handler -> getUser( $zariliaMailerConfig['fromuid'] ) );
                foreach ( $tags as $k => $v ) {
                    $zariliaMailer -> assign( $k, $v );
                }
                break;
            case ZAR_NOTIFICATION_METHOD_EMAIL:
                $zariliaMailer -> useMail();
                foreach ( $tags as $k => $v ) {
                    $zariliaMailer -> assign( $k, preg_replace( "/&amp;/i", '&', $v ) );
                }
                break;
            default:
                return true; // report error in user's profile??
                break;
        }
        // Set up the mailer
        $zariliaMailer -> setTemplateDir( $template_dir );
        $zariliaMailer -> setTemplate( $template );
        $zariliaMailer -> setToUsers( $user );
        // global $zariliaConfig;
        // $zariliaMailer->setFromEmail($zariliaConfig['adminmail']);
        // $zariliaMailer->setFromName($zariliaConfig['sitename']);
        $zariliaMailer -> setSubject( $subject );
        $success = $zariliaMailer -> send();
        // If send-once-then-delete, delete notification
        // If send-once-then-wait, disable notification
        include_once ZAR_ROOT_PATH . '/include/notification_constants.php';
        $notification_handler = &zarilia_gethandler( 'notification' );

        if ( $this -> getVar( 'not_mode' ) == ZAR_NOTIFICATION_MODE_SENDONCETHENDELETE ) {
            $notification_handler -> delete( $this );
            return $success;
        }

        if ( $this -> getVar( 'not_mode' ) == ZAR_NOTIFICATION_MODE_SENDONCETHENWAIT ) {
            $this -> setVar( 'not_mode', ZAR_NOTIFICATION_MODE_WAITFORLOGIN );
            $notification_handler -> insert( $this );
        }
        return $success;
    }
}

/**
 * ZARILIA notification handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of ZARILIA notification class objects.
 *
 * @package kernel
 * @subpackage notification
 * @author Michael van Dam <mvandam@caltech.edu>
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaNotificationHandler extends ZariliaPersistableObjectHandler {
    /*
	*
	*/
	function ZariliaNotificationHandler( &$db )
    {
        $this -> ZariliaPersistableObjectHandler( $db, 'zarilianotifications', 'ZariliaNotification', 'not_id' );
    }

	// TODO: rename this...
    // Also, should we have get by addon, get by category, etc...??
    function &getNotification ( $addon_id, $category, $item_id, $event, $user_id )
    {
        $ret = false;
        $criteria = new CriteriaCompo();
        $criteria -> add( new Criteria( 'not_modid', intval( $addon_id ) ) );
        $criteria -> add( new Criteria( 'not_category', $category ) );
        $criteria -> add( new Criteria( 'not_itemid', intval( $item_id ) ) );
        $criteria -> add( new Criteria( 'not_event', $event ) );
        $criteria -> add( new Criteria( 'not_uid', intval( $user_id ) ) );
        $objects = $this -> getObjects( $criteria );
        if ( count( $objects ) == 1 ) {
            $ret = &$objects[0];
        }
        return $ret;
    }

    /**
     * Determine if a user is subscribed to a particular event in
     * a particular addon.
     *
     * @param string $category Category of notification event
     * @param int $item_id Item ID of notification event
     * @param string $event Event
     * @param int $addon_id ID of addon (default current addon)
     * @param int $user_id ID of user (default current user)
     * return int  0 if not subscribe; non-zero if subscribed
     */

    function isSubscribed ( $category, $item_id, $event, $addon_id, $user_id )
    {
        $criteria = new CriteriaCompo();
        $criteria -> add( new Criteria( 'not_modid', intval( $addon_id ) ) );
        $criteria -> add( new Criteria( 'not_category', $category ) );
        $criteria -> add( new Criteria( 'not_itemid', intval( $item_id ) ) );
        $criteria -> add( new Criteria( 'not_event', $event ) );
        $criteria -> add( new Criteria( 'not_uid', intval( $user_id ) ) );
        return $this -> getCount( $criteria );
    }

	// TODO: how about a function to subscribe a whole group of users???
    // e.g. if we want to add all moderators to be notified of subscription
    // of new threads...
    /**
     * Subscribe for notification for an event(s)
     *
     * @param string $category category of notification
     * @param int $item_id ID of the item
     * @param mixed $events event string or array of events
     * @param int $mode force a particular notification mode
     *                              (e.g. once_only) (default to current user preference)
     * @param int $addon_id ID of the addon (default to current addon)
     * @param int $user_id ID of the user (default to current user)
     */
    function subscribe ( $category, $item_id, $events, $mode = null, $addon_id = null, $user_id = null )
    {
        if ( !isset( $user_id ) ) {
            global $zariliaUser;
            if ( empty( $zariliaUser ) ) {
                return false; // anonymous cannot subscribe
            } else {
                $user_id = $zariliaUser -> getVar( 'uid' );
            }
        }

        if ( !isset( $addon_id ) ) {
            global $zariliaAddon;
            $addon_id = $zariliaAddon -> getVar( 'mid' );
        }

        if ( !isset( $mode ) ) {
            $user = new ZariliaUser( $user_id );
            $mode = $user -> getVar( 'notify_mode' );
        }

        if ( !is_array( $events ) ) $events = array( $events );
        foreach ( $events as $event ) {
            if ( $notification = &$this -> getNotification( $addon_id, $category, $item_id, $event, $user_id ) ) {
                if ( $notification -> getVar( 'not_mode' ) != $mode ) {
                    $this -> updateByField( $notification, 'not_mode', $mode );
                }
            } else {
                $notification = &$this -> create();
                $notification -> setVar( 'not_modid', $addon_id );
                $notification -> setVar( 'not_category', $category );
                $notification -> setVar( 'not_itemid', $item_id );
                $notification -> setVar( 'not_uid', $user_id );
                $notification -> setVar( 'not_event', $event );
                $notification -> setVar( 'not_mode', $mode );
                $this -> insert( $notification );
            }
        }
    }
    // TODO: this will be to provide a list of everything a particular
    // user has subscribed to... e.g. for on the 'Profile' page, similar
    // to how we see the various posts etc. that the user has made.
    // We may also want to have a function where we can specify addon id
    /**
     * Get a list of notifications by user ID
     *
     * @param int $user_id ID of the user
     * @return array Array of {@link ZariliaNotification} objects
     */
    function &getByUser ( $user_id )
    {
        $criteria = new Criteria ( 'not_uid', $user_id );
        $ret = &$this -> getObjects( $criteria, true );
        return $ret;
    }
    // TODO: rename this??
    /**
     * Get a list of notification events for the current item/mod/user
     */
    function &getSubscribedEvents ( $category, $item_id, $addon_id, $user_id )
    {
        $criteria = new CriteriaCompo();
        $criteria -> add ( new Criteria( 'not_modid', $addon_id ) );
        $criteria -> add ( new Criteria( 'not_category', $category ) );
        if ( $item_id ) {
            $criteria -> add ( new Criteria( 'not_itemid', $item_id ) );
        }
        $criteria -> add ( new Criteria( 'not_uid', $user_id ) );
        $results = $this -> getObjects( $criteria, true );
        $ret = array();
        foreach ( array_keys( $results ) as $i ) {
            $ret[] = $results[$i] -> getVar( 'not_event' );
        }
        return $ret;
    }
    // TODO: is this a useful function?? (Copied from comment_handler)
    /**
     * Retrieve items by their ID
     *
     * @param int $addon_id Addons ID
     * @param int $item_id Item ID
     * @param string $order Sort order
     * @return array Array of {@link ZariliaNotification} objects
     */
    function &getByItemId( $addon_id, $item_id, $order = null, $status = null )
    {
        $criteria = new CriteriaCompo( new Criteria( 'not_modid', intval( $addon_id ) ) );
        $criteria -> add( new Criteria( 'not_itemid', intval( $item_id ) ) );
        if ( isset( $order ) ) {
            $criteria -> setOrder( $order );
        }
        $ret = &$this -> getObjects( $criteria );
        return $ret;
    }

    /**
     * Send notifications to users
     *
     * @param string $category notification category
     * @param int $item_id ID of the item
     * @param string $event notification event
     * @param array $extra_tags array of substitutions for template to be
     *                              merged with the one from function..
     * @param array $user_list only notify the selected users
     * @param int $addon_id ID of the addon
     * @param int $omit_user_id ID of the user to omit from notifications. (default to current user).  set to 0 for all users to receive notification.
     */
    // TODO:(?) - pass in an event LIST.  This will help to avoid
    // problem of sending people multiple emails for similar events.
    // BUT, then we need an array of mail templates, etc...  Unless
    // mail templates can include logic in the future, then we can
    // tailor the mail so it makes sense for any of the possible
    // (or combination of) events.
    function triggerEvents ( $category, $item_id, $events, $extra_tags = array(), $user_list = array(), $addon_id = null, $omit_user_id = null )
    {
        if ( !is_array( $events ) ) {
            $events = array( $events );
        }
        foreach ( $events as $event ) {
            $this -> triggerEvent( $category, $item_id, $event, $extra_tags, $user_list, $addon_id, $omit_user_id );
        }
    }

    function triggerEvent ( $category, $item_id, $event, $extra_tags = array(), $user_list = array(), $addon_id = null, $omit_user_id = null )
    {
        if ( !isset( $addon_id ) ) {
            global $zariliaAddon;
            $addon = &$zariliaAddon;
            $addon_id = !empty( $zariliaAddon ) ? $zariliaAddon -> getVar( 'mid' ) : 0;
        } else {
            $addon_handler = &zarilia_gethandler( 'addon' );
            $addon = &$addon_handler -> get( $addon_id );
        }
        // Check if event is enabled
        $config_handler = &zarilia_gethandler( 'config' );
        $mod_config = &$config_handler -> getConfigsByCat( 0, $addon -> getVar( 'mid' ) );
        if ( empty( $mod_config['notification_enabled'] ) ) {
            return false;
        }
        $category_info = &notificationCategoryInfo ( $category, $addon_id );
        $event_info = &notificationEventInfo ( $category, $event, $addon_id );
        if ( !in_array( notificationGenerateConfig( $category_info, $event_info, 'option_name' ), $mod_config['notification_events'] ) && empty( $event_info['invisible'] ) ) {
            return false;
        }

        if ( !isset( $omit_user_id ) ) {
            global $zariliaUser;
            if ( !empty( $zariliaUser ) ) {
                $omit_user_id = $zariliaUser -> getVar( 'uid' );
            } else {
                $omit_user_id = 0;
            }
        }
        $criteria = new CriteriaCompo();
        $criteria -> add( new Criteria( 'not_modid', intval( $addon_id ) ) );
        $criteria -> add( new Criteria( 'not_category', $category ) );
        $criteria -> add( new Criteria( 'not_itemid', intval( $item_id ) ) );
        $criteria -> add( new Criteria( 'not_event', $event ) );
        $mode_criteria = new CriteriaCompo();
        $mode_criteria -> add ( new Criteria( 'not_mode', ZAR_NOTIFICATION_MODE_SENDALWAYS ), 'OR' );
        $mode_criteria -> add ( new Criteria( 'not_mode', ZAR_NOTIFICATION_MODE_SENDONCETHENDELETE ), 'OR' );
        $mode_criteria -> add ( new Criteria( 'not_mode', ZAR_NOTIFICATION_MODE_SENDONCETHENWAIT ), 'OR' );
        $criteria -> add( $mode_criteria );
        if ( !empty( $user_list ) ) {
            $user_criteria = new CriteriaCompo();
            foreach ( $user_list as $user ) {
                $user_criteria -> add ( new Criteria( 'not_uid', $user ), 'OR' );
            }
            $criteria -> add( $user_criteria );
        }
        $notifications = &$this -> getObjects( $criteria );
        if ( empty( $notifications ) ) {
            return;
        }
        // Add some tag substitutions here
        $not_config = $addon -> getInfo( 'notification' );
        $tags = array();
        if ( !empty( $not_config ) ) {
            if ( !empty( $not_config['tags_file'] ) ) {
                $tags_file = ZAR_ROOT_PATH . '/addons/' . $addon -> getVar( 'dirname' ) . '/' . $not_config['tags_file'];
                if ( file_exists( $tags_file ) ) {
                    include_once $tags_file;
                    if ( !empty( $not_config['tags_func'] ) ) {
                        $tags_func = $not_config['tags_func'];
                        if ( function_exists( $tags_func ) ) {
                            $tags = $tags_func( $category, intval( $item_id ), $event );
                        }
                    }
                }
            }
            // RMV-NEW
            if ( !empty( $not_config['lookup_file'] ) ) {
                $lookup_file = ZAR_ROOT_PATH . '/addons/' . $addon -> getVar( 'dirname' ) . '/' . $not_config['lookup_file'];
                if ( file_exists( $lookup_file ) ) {
                    include_once $lookup_file;
                    if ( !empty( $not_config['lookup_func'] ) ) {
                        $lookup_func = $not_config['lookup_func'];
                        if ( function_exists( $lookup_func ) ) {
                            $item_info = $lookup_func( $category, intval( $item_id ) );
                        }
                    }
                }
            }
        }
        $tags['X_ITEM_NAME'] = !empty( $item_info['name'] ) ? $item_info['name'] : '[' . _NOT_ITEMNAMENOTAVAILABLE . ']';
        $tags['X_ITEM_URL'] = !empty( $item_info['url'] ) ? $item_info['url'] : '[' . _NOT_ITEMURLNOTAVAILABLE . ']';
        $tags['X_ITEM_TYPE'] = !empty( $category_info['item_name'] ) ? $category_info['title'] : '[' . _NOT_ITEMTYPENOTAVAILABLE . ']';
        $tags['X_ADDON'] = $addon -> getVar( 'name' );
        $tags['X_ADDON_URL'] = ZAR_URL . '/addons/' . $addon -> getVar( 'dirname' ) . '/';
        $tags['X_NOTIFY_CATEGORY'] = $category;
        $tags['X_NOTIFY_EVENT'] = $event;

        $template_dir = $event_info['mail_template_dir'];
        $template = $event_info['mail_template'] . '.tpl';
        $subject = $event_info['mail_subject'];

        foreach ( $notifications as $notification ) {
            if ( empty( $omit_user_id ) || $notification -> getVar( 'not_uid' ) != $omit_user_id ) {
                // user-specific tags
                // $tags['X_UNSUBSCRIBE_URL'] = 'TODO';
                // TODO: don't show unsubscribe link if it is 'one-time' ??
                $tags['X_UNSUBSCRIBE_URL'] = ZAR_URL . '/notifications.php';
                $tags = array_merge ( $tags, $extra_tags );

                $notification -> notifyUser( $template_dir, $template, $subject, $tags );
            }
        }
    }

    /**
     * Delete all notifications for one user
     *
     * @param int $user_id ID of the user
     * @return bool
     */
    function unsubscribeByUser ( $user_id )
    {
        $criteria = new Criteria( 'not_uid', intval( $user_id ) );
        return $this -> deleteAll( $criteria );
    }
    // TODO: allow these to use current addon, etc...
    /**
     * Unsubscribe notifications for an event(s).
     *
     * @param string $category category of the events
     * @param int $item_id ID of the item
     * @param mixed $events event string or array of events
     * @param int $addon_id ID of the addon (default current addon)
     * @param int $user_id UID of the user (default current user)
     * @return bool
     */

    function unsubscribe ( $category, $item_id, $events, $addon_id = null, $user_id = null )
    {
        if ( !isset( $user_id ) ) {
            global $zariliaUser;
            if ( empty( $zariliaUser ) ) {
                return false; // anonymous cannot subscribe
            } else {
                $user_id = $zariliaUser -> getVar( 'uid' );
            }
        }

        if ( !isset( $addon_id ) ) {
            global $zariliaAddon;
            $addon_id = $zariliaAddon -> getVar( 'mid' );
        }

        $criteria = new CriteriaCompo();
        $criteria -> add ( new Criteria( 'not_modid', intval( $addon_id ) ) );
        $criteria -> add ( new Criteria( 'not_category', $category ) );
        $criteria -> add ( new Criteria( 'not_itemid', intval( $item_id ) ) );
        $criteria -> add ( new Criteria( 'not_uid', intval( $user_id ) ) );
        if ( !is_array( $events ) ) {
            $events = array( $events );
        }
        $event_criteria = new CriteriaCompo();
        foreach ( $events as $event ) {
            $event_criteria -> add ( new Criteria( 'not_event', $event ), 'OR' );
        }
        $criteria -> add( $event_criteria );
        return $this -> deleteAll( $criteria );
    }
    // TODO: When 'update' a addon, may need to switch around some
    // notification classes/IDs...  or delete the ones that no longer
    // exist.
    /**
     * Delete all notifications for a particular addon
     *
     * @param int $addon_id ID of the addon
     * @return bool
     */
    function unsubscribeByAddon ( $addon_id )
    {
        $criteria = new Criteria( 'not_modid', intval( $addon_id ) );
        return $this -> deleteAll( $criteria );
    }

    /**
     * Delete all subscriptions for a particular item.
     *
     * @param int $addon_id ID of the addon to which item belongs
     * @param string $category Notification category of the item
     * @param int $item_id ID of the item
     * @return bool
     */
    function unsubscribeByItem ( $addon_id, $category, $item_id )
    {
        $criteria = new CriteriaCompo();
        $criteria -> add ( new Criteria( 'not_modid', intval( $addon_id ) ) );
        $criteria -> add ( new Criteria( 'not_category', $category ) );
        $criteria -> add ( new Criteria( 'not_itemid', intval( $item_id ) ) );
        return $this -> deleteAll( $criteria );
    }

    /**
     * Perform notification maintenance activites at login time.
     * In particular, any notifications for the newly logged-in
     * user with mode ZAR_NOTIFICATION_MODE_WAITFORLOGIN are
     * switched to mode ZAR_NOTIFICATION_MODE_SENDONCETHENWAIT.
     *
     * @param int $user_id ID of the user being logged in
     */
    function doLoginMaintenance ( $user_id )
    {
        $criteria = new CriteriaCompo();
        $criteria -> add ( new Criteria( 'not_uid', intval( $user_id ) ) );
        $criteria -> add ( new Criteria( 'not_mode', ZAR_NOTIFICATION_MODE_WAITFORLOGIN ) );

        $notifications = $this -> getObjects( $criteria, true );
        foreach ( $notifications as $n ) {
            $n -> setVar( 'not_mode', ZAR_NOTIFICATION_MODE_SENDONCETHENWAIT );
            $this -> insert( $n );
        }
    }

    /**
     * Update
     *
     * @param object $ &$notification  {@link ZariliaNotification} object
     * @param string $field_name Name of the field
     * @param mixed $field_value Value to write
     * @return bool
     */
    function updateByField( &$notification, $field_name, $field_value )
    {
        $notification -> unsetNew();
        $notification -> setVar( $field_name, $field_value );
        return $this -> insert( $notification );
    }
}

?>