<?php
// $Id: notifications.php,v 1.1 2007/03/16 02:44:24 catzwolf Exp $

// RMV-NOTIFY

// Text for various templates...

define ('_NOT_NOTIFICATIONOPTIONS', 'Notification Options');
define ('_NOT_UPDATENOW', 'Update Now');
define ('_NOT_UPDATEOPTIONS', 'Update Notification Options');
define ('_NOT_CLEAR', 'Clear');
define ('_NOT_CHECKALL', 'Check All');
define ('_NOT_ADDON', 'Addons');
define ('_NOT_CATEGORY', 'Category');
define ('_NOT_ITEMID', 'ID');
define ('_NOT_ITEMNAME', 'Name');
define ('_NOT_EVENT', 'Event');
define ('_NOT_EVENTS', 'Events');
define ('_NOT_ACTIVENOTIFICATIONS', 'Active Notifications');
define ('_NOT_NAMENOTAVAILABLE', 'Name Not Available');
define ('_NOT_NONOTSFOUND', 'No notifications found');

// RMV-NEW : TODO: remove NAMENOTAVAILBLE above
define ('_NOT_ITEMNAMENOTAVAILABLE', 'Item Name Not Available');
define ('_NOT_ITEMTYPENOTAVAILABLE', 'Item Type Not Available');
define ('_NOT_ITEMURLNOTAVAILABLE', 'Item URL Not Available');
define ('_NOT_DELETINGNOTIFICATIONS', 'Deleting Notifications');
define ('_NOT_DELETESUCCESS', 'Notification(s) deleted successfully.');
define ('_NOT_UPDATEOK', 'Notification options updated');
define ('_NOT_NOTIFICATIONMETHODIS', 'Notification method is');
define ('_NOT_EMAIL', 'email');
define ('_NOT_PM', 'private message');
define ('_NOT_DISABLE', 'disabled');
define ('_NOT_CHANGE', 'Change');
define ('_NOT_NOACCESS', 'You do not have permission to access this page.');

// Text for addon config options
define ('_NOT_ENABLE', 'Enable');
define ('_NOT_NOTIFICATION', 'Notification');
define ('_NOT_CONFIG_ENABLED', 'Enable Notification');
define ('_NOT_CONFIG_ENABLEDDSC', 'This addon allows users to select to be notified when certain events occur.  Choose "yes" to enable this feature.');
define ('_NOT_CONFIG_EVENTS', 'Enable Specific Events');
define ('_NOT_CONFIG_EVENTSDSC', 'Select which notification events to which your users may subscribe.');
define ('_NOT_CONFIG_ENABLE', 'Enable Notification');
define ('_NOT_CONFIG_ENABLEDSC', 'This addon allows users to be notified when certain events occur.  Select if users should be presented with notification options in a Block (Block-style), within the addon (Inline-style), or both.  For block-style notification, the Notification Options block must be enabled for this addon.');
define ('_NOT_CONFIG_DISABLE', 'Disable Notification');
define ('_NOT_CONFIG_ENABLEBLOCK', 'Enable only Block-style');
define ('_NOT_CONFIG_ENABLEINLINE', 'Enable only Inline-style');
define ('_NOT_CONFIG_ENABLEBOTH', 'Enable Notification (both styles)');

// For notification about comment events
define ('_NOT_COMMENT_NOTIFY', 'Comment Added');
define ('_NOT_COMMENT_NOTIFYCAP', 'Notify me when a new comment is posted for this item.');
define ('_NOT_COMMENT_NOTIFYDSC', 'Receive notification whenever a new comment is posted (or approved) for this item.');
define ('_NOT_COMMENT_NOTIFYSBJ', '[{X_SITENAME}] {X_ADDON} auto-notify: Comment added to {X_ITEM_TYPE}');

define ('_NOT_COMMENTSUBMIT_NOTIFY', 'Comment Submitted');
define ('_NOT_COMMENTSUBMIT_NOTIFYCAP', 'Notify me when a new comment is submitted (awaiting approval) for this item.');
define ('_NOT_COMMENTSUBMIT_NOTIFYDSC', 'Receive notification whenever a new comment is submitted (awaiting approval) for this item.');
define ('_NOT_COMMENTSUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_ADDON} auto-notify: Comment submitted for {X_ITEM_TYPE}');

// For notification bookmark feature
// (Not really notification, but easy to do with this addon)
define ('_NOT_BOOKMARK_NOTIFY', 'Bookmark');
define ('_NOT_BOOKMARK_NOTIFYCAP', 'Bookmark this item (no notification).');
define ('_NOT_BOOKMARK_NOTIFYDSC', 'Keep track of this item without receiving any event notifications.');

// For user profile
// FIXME: These should be reworded a little...
define ('_NOT_NOTIFYMETHOD', 'Notification Method: When you monitor e.g. a forum, how would you like to receive notifications of updates?');
define ('_NOT_METHOD_EMAIL', 'Email (use address in my profile)');
define ('_NOT_METHOD_PM', 'Private Message');
define ('_NOT_METHOD_DISABLE', 'Temporarily Disable');

define ('_NOT_NOTIFYMODE', 'Default Notification Mode');
define ('_NOT_MODE_SENDALWAYS', 'Notify me of all selected updates');
define ('_NOT_MODE_SENDONCE', 'Notify me only once');
define ('_NOT_MODE_SENDONCEPERLOGIN', 'Notify me once then disable until I log in again');

/*
define('ZAR_NOTIFICATION_MODE_SENDALWAYS', 0);
define('ZAR_NOTIFICATION_MODE_SENDONCETHENDELETE', 1);
define('ZAR_NOTIFICATION_MODE_SENDONCETHENWAIT', 2);
define('ZAR_NOTIFICATION_MODE_WAITFORLOGIN', 3);

define('ZAR_NOTIFICATION_METHOD_DISABLE', 0);
define('ZAR_NOTIFICATION_METHOD_PM', 1);
define('ZAR_NOTIFICATION_METHOD_EMAIL', 2);

define('ZAR_NOTIFICATION_DISABLE', 0);
define('ZAR_NOTIFICATION_ENABLEBLOCK', 1);
define('ZAR_NOTIFICATION_ENABLEINLINE', 2);
define('ZAR_NOTIFICATION_ENABLEBOTH', 3);
*/
?>
