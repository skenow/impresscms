<?php
// $Id: notification_select.php,v 1.2 2007/03/30 22:06:42 catzwolf Exp $
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

global $zariliaTpl, $zariliaConfig, $zariliaLogger;

include_once ZAR_ROOT_PATH.'/include/notification_constants.php';
include_once ZAR_ROOT_PATH.'/include/notification_functions.php';
$zarilia_notification = array();
$zarilia_notification['show'] = isset($zariliaAddon) && is_object($zariliaUser) && notificationEnabled('inline') ? 1 : 0;
if ($zarilia_notification['show']) {
	include_once ZAR_ROOT_PATH.'/language/'.$zariliaConfig['language'].'/notifications.php';
	$categories =& notificationSubscribableCategoryInfo();
	$event_count = 0;
	if (!empty($categories)) {
		$notification_handler =& zarilia_gethandler('notification');
		foreach ($categories as $category) {
			$section['name'] = $category['name'];
			$section['title'] = $category['title'];
			$section['description'] = $category['description'];
			$section['itemid'] = $category['item_id'];
			$section['events'] = array();
			$subscribed_events =& $notification_handler->getSubscribedEvents($category['name'], $category['item_id'], $zariliaAddon->getVar('mid'), $zariliaUser->getVar('uid'));
			foreach (notificationEvents($category['name'], true) as $event) {
            	if (!empty($event['admin_only']) && !$zariliaUser->isAdmin($zariliaAddon->getVar('mid'))) {
                	continue;
            	}
				if (!empty($event['invisible'])) {
					continue;
				}
				$subscribed = in_array($event['name'], $subscribed_events) ? 1 : 0;
				$section['events'][$event['name']] = array ('name'=>$event['name'], 'title'=>$event['title'], 'caption'=>$event['caption'], 'description'=>$event['description'], 'subscribed'=>$subscribed);
				$event_count ++;
			}
			$zarilia_notification['categories'][$category['name']] = $section;
		}
		$zarilia_notification['target_page'] = "notification_update.php";
		$zarilia_notification['redirect_script'] = zarilia_getenv('PHP_SELF');
		$zariliaTpl->assign(array('lang_activenotifications' => _NOT_ACTIVENOTIFICATIONS, 'lang_notificationoptions' => _NOT_NOTIFICATIONOPTIONS, 'lang_updateoptions' => _NOT_UPDATEOPTIONS, 'lang_updatenow' => _NOT_UPDATENOW, 'lang_category' => _NOT_CATEGORY, 'lang_event' => _NOT_EVENT, 'lang_events' => _NOT_EVENTS, 'lang_checkall' => _NOT_CHECKALL, 'lang_notificationmethodis' => _NOT_NOTIFICATIONMETHODIS, 'lang_change' => _NOT_CHANGE, 'editprofile_url' => ZAR_URL . '/edituser.php?uid=' . $zariliaUser->getVar('uid')));
		switch ($zariliaUser->getVar('notify_method')) {
		case ZAR_NOTIFICATION_METHOD_DISABLE:
			$zariliaTpl->assign('user_method', _NOT_DISABLE);
			break;
		case ZAR_NOTIFICATION_METHOD_PM:
			$zariliaTpl->assign('user_method', _NOT_PM);
			break;
		case ZAR_NOTIFICATION_METHOD_EMAIL:
			$zariliaTpl->assign('user_method', _NOT_EMAIL);
			break;
		}
	} else {
		$zarilia_notification['show'] = 0;
	}
	if ($event_count == 0) {
		$zarilia_notification['show'] = 0;
	}
}
$zariliaTpl->assign('zarilia_notification', $zarilia_notification);
?>
