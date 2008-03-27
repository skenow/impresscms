<?php
// $Id: notification_functions.php,v 1.2 2007/03/30 22:06:42 catzwolf Exp $
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
// RMV-NOTIFY
// FIXME: Do some caching, so we don't retrieve the same category / event info many times.
/**
 * Determine if notification is enabled for the selected addon.
 *
 * @param string $style Subscription style: 'block' or 'inline'
 * @param int $addon_id ID of the addon  (default current addon)
 * @return bool
 */
function notificationEnabled ( $style, $addon_id = null ) {
    if ( isset( $GLOBALS['zariliaAddonConfig']['notification_enabled'] ) ) {
        $status = $GLOBALS['zariliaAddonConfig']['notification_enabled'];
    } else {
        if ( !isset( $addon_id ) ) {
            return false;
        }
        $addon_handler = &zarilia_gethandler( 'addon' );
        $addon = &$addon_handler->get( $addon_id );
        if ( !empty( $addon ) && $addon->getVar( 'hasnotification' ) == 1 ) {
            $config_handler = &zarilia_gethandler( 'config' );
            $config = $config_handler->getConfigsByCat( 0, $addon_id );
            $status = $config['notification_enabled'];
        } else {
            return false;
        }
    }
    include_once ZAR_ROOT_PATH . '/include/notification_constants.php';
    if ( ( $style == 'block' ) && ( $status == ZAR_NOTIFICATION_ENABLEBLOCK || $status == ZAR_NOTIFICATION_ENABLEBOTH ) ) {
        return true;
    }
    if ( ( $style == 'inline' ) && ( $status == ZAR_NOTIFICATION_ENABLEINLINE || $status == ZAR_NOTIFICATION_ENABLEBOTH ) ) {
        return true;
    }
    // if ($status != ZAR_NOTIFICATION_DISABLE) {
    // return true;
    // }
    return false;
}

/**
 * Get an associative array of info for a particular notification
 * category in the selected addon.  If no category is selected,
 * return an array of info for all categories.
 *
 * @param string $name Category name (default all categories)
 * @param int $addon_id ID of the addon (default current addon)
 * @return mixed
 */
function &notificationCategoryInfo ( $category_name = '', $addon_id = null ) {
    if ( !isset( $addon_id ) ) {
        global $zariliaAddon;
        $addon_id = !empty( $zariliaAddon ) ? $zariliaAddon->getVar( 'mid' ) : 0;
        $addon = &$zariliaAddon;
    } else {
        $addon_handler = &zarilia_gethandler( 'addon' );
        $addon = &$addon_handler->get( $addon_id );
    }
    $not_config = &$addon->getInfo( 'notification' );
    if ( empty( $category_name ) ) {
        return $not_config['category'];
    }
    foreach ( $not_config['category'] as $category ) {
        if ( $category['name'] == $category_name ) {
            return $category;
        }
    }
    return false;
}

/**
 * Get associative array of info for the category to which comment events
 * belong.
 *
 * @todo This could be more efficient... maybe specify in
 *         $addonversion['comments'] the notification category.
 *        This would also serve as a way to enable notification
 *         of comments, and also remove the restriction that
 *         all notification categories must have unique item_name. (TODO)
 * @param int $addon_id ID of the addon (default current addon)
 * @return mixed Associative array of category info
 */
function &notificationCommentCategoryInfo( $addon_id = null ) {
    $all_categories = &notificationCategoryInfo ( '', $addon_id );
    if ( empty( $all_categories ) ) {
        return false;
    }
    foreach ( $all_categories as $category ) {
        $all_events = &notificationEvents ( $category['name'], false, $addon_id );
        if ( empty( $all_events ) ) {
            continue;
        }
        foreach ( $all_events as $event ) {
            if ( $event['name'] == 'comment' ) {
                return $category;
            }
        }
    }
    return false;
}
// TODO: some way to include or exclude admin-only events...
/**
 * Get an array of info for all events (each event has associative array)
 * in the selected category of the selected addon.
 *
 * @param string $category_name Category name
 * @param bool $enabled_only If true, return only enabled events
 * @param int $addon_id ID of the addon (default current addon)
 * @return mixed
 */
function &notificationEvents ( $category_name, $enabled_only, $addon_id = null ) {
    if ( !isset( $addon_id ) ) {
        global $zariliaAddon;
        $addon_id = !empty( $zariliaAddon ) ? $zariliaAddon->getVar( 'mid' ) : 0;
        $addon = &$zariliaAddon;
    } else {
        $addon_handler = &zarilia_gethandler( 'addon' );
        $addon = &$addon_handler->get( $addon_id );
    }
    $not_config = &$addon->getInfo( 'notification' );
    $config_handler = &zarilia_gethandler( 'config' );
    $mod_config = $config_handler->getConfigsByCat( 0, $addon_id );

    $category = &notificationCategoryInfo( $category_name, $addon_id );

    global $zariliaConfig;
    $event_array = array();

    $override_comment = false;
    $override_commentsubmit = false;
    $override_bookmark = false;

    foreach ( $not_config['event'] as $event ) {
        if ( $event['category'] == $category_name ) {
            $event['mail_template_dir'] = ZAR_ROOT_PATH . '/addons/' . $addon->getVar( 'dirname' ) . '/language/' . $zariliaConfig['language'] . '/mail_template/';
            if ( !$enabled_only || notificationEventEnabled ( $category, $event, $addon ) ) {
                $event_array[] = $event;
            }
            if ( $event['name'] == 'comment' ) {
                $override_comment = true;
            }
            if ( $event['name'] == 'comment_submit' ) {
                $override_commentsubmit = true;
            }
            if ( $event['name'] == 'bookmark' ) {
                $override_bookmark = true;
            }
        }
    }

    include_once ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/notifications.php';
    // Insert comment info if applicable
    if ( $addon->getVar( 'hascomments' ) ) {
        $com_config = $addon->getInfo( 'comments' );
        if ( !empty( $category['item_name'] ) && $category['item_name'] == $com_config['itemName'] ) {
            $mail_template_dir = ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/mail_template/';
            include_once ZAR_ROOT_PATH . '/include/comment_constants.php';
            $config_handler = &zarilia_gethandler( 'config' );
            $com_config = $config_handler->getConfigsByCat( 0, $addon_id );
            if ( !$enabled_only ) {
                $insert_comment = true;
                $insert_submit = true;
            } else {
                $insert_comment = false;
                $insert_submit = false;
                switch ( $com_config['com_rule'] ) {
                    case ZAR_COMMENT_APPROVENONE:
                        // comments disabled, no comment events
                        break;
                    case ZAR_COMMENT_APPROVEALL:
                        // all comments are automatically approved, no 'submit'
                        if ( !$override_comment ) {
                            $insert_comment = true;
                        }
                        break;
                    case ZAR_COMMENT_APPROVEUSER:
                    case ZAR_COMMENT_APPROVEADMIN:
                        // comments first submitted, require later approval
                        if ( !$override_comment ) {
                            $insert_comment = true;
                        }
                        if ( !$override_commentsubmit ) {
                            $insert_submit = true;
                        }
                        break;
                }
            }
            if ( $insert_comment ) {
                $event = array ( 'name' => 'comment', 'category' => $category['name'], 'title' => _NOT_COMMENT_NOTIFY, 'caption' => _NOT_COMMENT_NOTIFYCAP, 'description' => _NOT_COMMENT_NOTIFYDSC, 'mail_template_dir' => $mail_template_dir, 'mail_template' => 'comment_notify', 'mail_subject' => _NOT_COMMENT_NOTIFYSBJ );
                if ( !$enabled_only || notificationEventEnabled( $category, $event, $addon ) ) {
                    $event_array[] = $event;
                }
            }
            if ( $insert_submit ) {
                $event = array ( 'name' => 'comment_submit', 'category' => $category['name'], 'title' => _NOT_COMMENTSUBMIT_NOTIFY, 'caption' => _NOT_COMMENTSUBMIT_NOTIFYCAP, 'description' => _NOT_COMMENTSUBMIT_NOTIFYDSC, 'mail_template_dir' => $mail_template_dir, 'mail_template' => 'commentsubmit_notify', 'mail_subject' => _NOT_COMMENTSUBMIT_NOTIFYSBJ, 'admin_only' => 1 );
                if ( !$enabled_only || notificationEventEnabled( $category, $event, $addon ) ) {
                    $event_array[] = $event;
                }
            }
        }
    }
    // Insert bookmark info if appropriate
    if ( !empty( $category['allow_bookmark'] ) ) {
        if ( !$override_bookmark ) {
            $event = array ( 'name' => 'bookmark', 'category' => $category['name'], 'title' => _NOT_BOOKMARK_NOTIFY, 'caption' => _NOT_BOOKMARK_NOTIFYCAP, 'description' => _NOT_BOOKMARK_NOTIFYDSC );
            if ( !$enabled_only || notificationEventEnabled( $category, $event, $addon ) ) {
                $event_array[] = $event;
            }
        }
    }
    return $event_array;
}

/**
 * Determine whether a particular notification event is enabled.
 * Depends on addon config options.
 *
 * @todo Check that this works correctly for comment and other
 *    events which depend on additional config options...
 * @param array $category Category info array
 * @param array $event Event info array
 * @param object $addon Addons
 * @return bool
 */
function notificationEventEnabled ( &$category, &$event, &$addon ) {
    $config_handler = &zarilia_gethandler( 'config' );
    $mod_config = $config_handler->getConfigsByCat( 0, $addon->getVar( 'mid' ) );

    $option_name = notificationGenerateConfig ( $category, $event, 'option_name' );
    if ( in_array( $option_name, $mod_config['notification_events'] ) ) {
        return true;
    }
    $notification_handler = &zarilia_gethandler( 'notification' );
    return false;
}

/**
 * Get associative array of info for the selected event in the selected
 * category (for the selected addon).
 *
 * @param string $category_name Notification category
 * @param string $event_name Notification event
 * @param int $addon_id ID of the addon (default current addon)
 * @return mixed
 */
function &notificationEventInfo ( $category_name, $event_name, $addon_id = null ) {
    $all_events = &notificationEvents ( $category_name, false, $addon_id );
    foreach ( $all_events as $event ) {
        if ( $event['name'] == $event_name ) {
            return $event;
        }
    }
    return false;
}

/**
 * Get an array of associative info arrays for subscribable categories
 * for the selected addon.
 *
 * @param int $addon_id ID of the addon
 * @return mixed
 */

function &notificationSubscribableCategoryInfo ( $addon_id = null ) {
    $all_categories = &notificationCategoryInfo ( '', $addon_id );
    // FIXME: better or more standardized way to do this?
    $script_url = explode( '/', $_SERVER['PHP_SELF'] );
    $script_name = $script_url[count( $script_url )-1];

    $sub_categories = array();

    foreach ( $all_categories as $category ) {
        // Check the script name
        $subscribe_from = $category['subscribe_from'];
        if ( !is_array( $subscribe_from ) ) {
            if ( $subscribe_from == '*' ) {
                $subscribe_from = array( $script_name );
                // FIXME: this is just a hack: force a match
            } else {
                $subscribe_from = array( $subscribe_from );
            }
        }
        if ( !in_array( $script_name, $subscribe_from ) ) {
            continue;
        }
        // If 'item_name' is missing, automatic match.  Otherwise
        // check if that argument exists...
        if ( empty( $category['item_name'] ) ) {
            $category['item_name'] = '';
            $category['item_id'] = 0;
            $sub_categories[] = $category;
        } else {
            $item_name = $category['item_name'];
            $id = ( $item_name != '' && isset( $_GET[$item_name] ) ) ? intval( $_GET[$item_name] ) : 0;
            if ( $id > 0 ) {
                $category['item_id'] = $id;
                $sub_categories[] = $category;
            }
        }
    }
    return $sub_categories;
}

/**
 * Generate addon config info for a particular category, event pair.
 * The selectable config options are given names depending on the
 * category and event names, and the text depends on the category
 * and event titles.  These are pieced together in this function in
 * case we wish to alter the syntax.
 *
 * @param array $category Array of category info
 * @param array $event Array of event info
 * @param string $type The particular name to generate
 * return string
 */
function notificationGenerateConfig ( &$category, &$event, $type ) {
    switch ( $type ) {
        case 'option_value':
        case 'name':
            return 'notify:' . $category['name'] . '-' . $event['name'];
            break;
        case 'option_name':
            return $category['name'] . '-' . $event['name'];
            break;
        default:
            return false;
            break;
    }
}

?>