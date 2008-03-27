<?php
// $Id: comment_delete.php,v 1.1 2007/03/16 02:39:06 catzwolf Exp $
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

if (!defined('ZAR_ROOT_PATH') || !is_object($zariliaAddon)) {
	exit();
}
include_once ZAR_ROOT_PATH.'/include/comment_constants.php';
$op = 'delete';
if (!empty($_POST)) {
	extract($_POST);
	$com_mode = isset($com_mode) ? htmlspecialchars(trim($com_mode), ENT_QUOTES) : 'flat';
	$com_order = isset($com_order) ? intval($com_order) : ZAR_COMMENT_OLD1ST;
	$com_id = isset($com_id) ? intval($com_id) : 0;
} else {
	$com_mode = isset($_GET['com_mode']) ? htmlspecialchars(trim($_GET['com_mode']), ENT_QUOTES) : 'flat';
	$com_order = isset($_GET['com_order']) ? intval($_GET['com_order']) : ZAR_COMMENT_OLD1ST;
	$com_id = isset($_GET['com_id']) ? intval($_GET['com_id']) : 0;

}

if ('system' == $zariliaAddon->getVar('dirname')) {
	$comment_handler =& zarilia_gethandler('comment');
	$comment =& $comment_handler->get($com_id);
	$addon_handler =& zarilia_gethandler('addon');
	$addon =& $addon_handler->get($comment->getVar('com_modid'));
	$comment_config = $addon->getInfo('comments');
	$com_modid = $addon->getVar('mid');
	$redirect_page = ZAR_URL.'/addons/system/admin.php?fct=comments&amp;com_modid='.$com_modid.'&amp;com_itemid';
	$moddir = $addon->getVar('dirname');
	unset($comment);
} else {
	if (ZAR_COMMENT_APPROVENONE == $zariliaAddonConfig['com_rule']) {
		exit();
	}
	$comment_config = $zariliaAddon->getInfo('comments');
	$com_modid = $zariliaAddon->getVar('mid');
	$redirect_page = $comment_config['pageName'].'?';
	$comment_confirm_extra = array();
	if (isset($comment_config['extraParams']) && is_array($comment_config['extraParams'])) {
		foreach ($comment_config['extraParams'] as $extra_param) {
			if (isset(${$extra_param})) {
				$redirect_page .= $extra_param.'='.${$extra_param}.'&amp;';
				
				// for the confirmation page
				$comment_confirm_extra [$extra_param] = ${$extra_param};
			} elseif (isset($_GET[$extra_param])) {
				$redirect_page .= $extra_param.'='.$_GET[$extra_param].'&amp;';
				
				// for the confirmation page
				$comment_confirm_extra [$extra_param] = $_GET[$extra_param];
			}
		}
	}
	$redirect_page .= $comment_config['itemName'];
	$moddir = $zariliaAddon->getVar('dirname');
}

$accesserror = false;
if (!is_object($zariliaUser)) {
	$accesserror = true;
} else {
	if (!$zariliaUser->isAdmin($com_modid)) {
			$sysperm_handler =& zarilia_gethandler('groupperm');
			if (!$sysperm_handler->checkRight('system_admin', ZAR_SYSTEM_COMMENT, $zariliaUser->getGroups())) {
				$accesserror = true;
			}
	}
}

if (false != $accesserror) {
	$ref = zarilia_getenv('HTTP_REFERER');
	if ($ref != '') {
		redirect_header($ref, 2, _NOPERM);
	} else {
		redirect_header($redirect_page.'?'.$comment_config['itemName'].'='.intval($com_itemid), 2, _NOPERM);
	}
	exit();
}

include_once ZAR_ROOT_PATH.'/language/'.$zariliaConfig['language'].'/comment.php';

switch ($op) {
case 'delete_one':
	$comment_handler = zarilia_gethandler('comment');
	$comment =& $comment_handler->get($com_id);
	if (!$comment_handler->delete($comment)) {
		include ZAR_ROOT_PATH.'/header.php';
		zarilia_error(_CM_COMDELETENG.' (ID: '.$comment->getVar('com_id').')');
		include ZAR_ROOT_PATH.'/footer.php';
		exit();
	}

	$com_itemid = $comment->getVar('com_itemid');

	// execute updateStat callback function if set
	if (isset($comment_config['callback']['update']) && trim($comment_config['callback']['update']) != '') {
		$skip = false;
		if (!function_exists($comment_config['callback']['update'])) {
			if (isset($comment_config['callbackFile'])) {
				$callbackfile = trim($comment_config['callbackFile']);
				if ($callbackfile != '' && file_exists(ZAR_ROOT_PATH.'/addons/'.$moddir.'/'.$callbackfile)) {
					include_once ZAR_ROOT_PATH.'/addons/'.$moddir.'/'.$callbackfile;
				}
				if (!function_exists($comment_config['callback']['update'])) {
					$skip = true;
				}
			} else {
				$skip = true;
			}
		}
		if (!$skip) {
			$criteria = new CriteriaCompo(new Criteria('com_modid', $com_modid));
			$criteria->add(new Criteria('com_itemid', $com_itemid));
			$criteria->add(new Criteria('com_status', ZAR_COMMENT_ACTIVE));
			$comment_count = $comment_handler->getCount($criteria);
			$comment_config['callback']['update']($com_itemid, $comment_count);
		}
	}

	// update user posts if its not an anonymous post
	if ($comment->getVar('com_uid') != 0) {
		$member_handler =& zarilia_gethandler('member');
		$com_poster =& $member_handler->getUser($comment->getVar('com_uid'));
		if (is_object($com_poster)) {
			$member_handler->updateUserByField($com_poster, 'posts', $com_poster->getVar('posts') - 1);
		}
	}

	// get all comments posted later within the same thread
	$thread_comments =& $comment_handler->getThread($comment->getVar('com_rootid'), $com_id);
	
	include_once ZAR_ROOT_PATH.'/class/tree.php';
	$xot = new ZariliaObjectTree($thread_comments, 'com_id', 'com_pid', 'com_rootid');

	$child_comments =& $xot->getFirstChild($com_id);

	// now set new parent ID for direct child comments
	$new_pid = $comment->getVar('com_pid');
	$errs = array();
	foreach (array_keys($child_comments) as $i) {
		$child_comments[$i]->setVar('com_pid', $new_pid);
		// if the deleted comment is a root comment, need to change root id to own id
		if (false != $comment->isRoot()) {
			$new_rootid = $child_comments[$i]->getVar('com_id');
			$child_comments[$i]->setVar('com_rootid', $child_comments[$i]->getVar('com_id'));
			if (!$comment_handler->insert($child_comments[$i])) {
				$errs[] = 'Could not change comment parent ID from <b>'.$com_id.'</b> to <b>'.$new_pid.'</b>. (ID: '.$new_rootid.')';
			} else {
				// need to change root id for all its child comments as well
				$c_child_comments =& $xot->getAllChild($new_rootid);
				$cc_count = count($c_child_comments);
				foreach (array_keys($c_child_comments) as $j) {
					$c_child_comments[$j]->setVar('com_rootid', $new_rootid);
					if (!$comment_handler->insert($c_child_comments[$j])) {
						$errs[] = 'Could not change comment root ID from <b>'.$com_id.'</b> to <b>'.$new_rootid.'</b>.';
					}
				}
			}
		} else {
			if (!$comment_handler->insert($child_comments[$i])) {
				$errs[] = 'Could not change comment parent ID from <b>'.$com_id.'</b> to <b>'.$new_pid.'</b>.';
			}
		}
	}
	if (count($errs) > 0) {
		include ZAR_ROOT_PATH.'/header.php';
		zarilia_error($errs);
		include ZAR_ROOT_PATH.'/footer.php';
		exit();
	}
	redirect_header($redirect_page.'='.$com_itemid.'&amp;com_order='.$com_order.'&amp;com_mode='.$com_mode, 1, _CM_COMDELETED);
	break;

case 'delete_all':
	$comment_handler = zarilia_gethandler('comment');
	if (! ($comment =& $comment_handler->get($com_id))) {
		redirect_header($redirect_page.'='.$com_itemid.'&amp;com_order='.$com_order.'&amp;com_mode='.$com_mode, 1, _CM_COMDELETED);
		break;
	}
	$com_rootid = $comment->getVar('com_rootid');

	// get all comments posted later within the same thread
	$thread_comments =& $comment_handler->getThread($com_rootid, $com_id);

	// construct a comment tree
	include_once ZAR_ROOT_PATH.'/class/tree.php';
	$xot = new ZariliaObjectTree($thread_comments, 'com_id', 'com_pid', 'com_rootid');
	$child_comments =& $xot->getAllChild($com_id);
	// add itself here
	$child_comments[$com_id] =& $comment;
	$msgs = array();
	$deleted_num = array();
	$member_handler =& zarilia_gethandler('member');
	foreach (array_keys($child_comments) as $i) {
		if (!$comment_handler->delete($child_comments[$i])) {
			$msgs[] = _CM_COMDELETENG.' (ID: '.$child_comments[$i]->getVar('com_id').')';
		} else {
			$msgs[] = _CM_COMDELETED.' (ID: '.$child_comments[$i]->getVar('com_id').')';
			// store poster ID and deleted post number into array for later use
			$poster_id = $child_comments[$i]->getVar('com_uid');
			if ($poster_id > 0) {
				$deleted_num[$poster_id] = !isset($deleted_num[$poster_id]) ? 1 : ($deleted_num[$poster_id] + 1);
			}
		}
	}
	foreach ($deleted_num as $user_id => $post_num) {
		// update user posts
		$com_poster = $member_handler->getUser($user_id);
		if (is_object($com_poster)) {
			$member_handler->updateUserByField($com_poster, 'posts', $com_poster->getVar('posts') - $post_num);
		}
	}

	$com_itemid = $comment->getVar('com_itemid');

	// execute updateStat callback function if set
	if (isset($comment_config['callback']['update']) && trim($comment_config['callback']['update']) != '') {
		$skip = false;
		if (!function_exists($comment_config['callback']['update'])) {
			if (isset($comment_config['callbackFile'])) {
				$callbackfile = trim($comment_config['callbackFile']);
				if ($callbackfile != '' && file_exists(ZAR_ROOT_PATH.'/addons/'.$moddir.'/'.$callbackfile)) {
					include_once ZAR_ROOT_PATH.'/addons/'.$moddir.'/'.$callbackfile;
				}
				if (!function_exists($comment_config['callback']['update'])) {
					$skip = true;
				}
			} else {
				$skip = true;
			}
		}
		if (!$skip) {
			$criteria = new CriteriaCompo(new Criteria('com_modid', $com_modid));
			$criteria->add(new Criteria('com_itemid', $com_itemid));
			$criteria->add(new Criteria('com_status', ZAR_COMMENT_ACTIVE));
			$comment_count = $comment_handler->getCount($criteria);
			$comment_config['callback']['update']($com_itemid, $comment_count);
		}
	}
	
	redirect_header($redirect_page.'='.$com_itemid.'&amp;com_order='.$com_order.'&amp;com_mode='.$com_mode, 1, $msgsD);

/*	include ZAR_ROOT_PATH.'/header.php';
	zarilia_result($msgs);
	echo '<br /><a href="'.$redirect_page.'='.$com_itemid.'&amp;com_order='.$com_order.'&amp;com_mode='.$com_mode.'">'._BACK.'</a>';
	include ZAR_ROOT_PATH.'/footer.php';*/
	break;

case 'delete':
default:
	include ZAR_ROOT_PATH.'/header.php';
	$comment_confirm = array('com_id' => $com_id, 'com_mode' => $com_mode, 'com_order' => $com_order, 'op' => array(_CM_DELETEONE => 'delete_one', _CM_DELETEALL => 'delete_all'));
	if (!empty($comment_confirm_extra) && is_array($comment_confirm_extra)) {
		$comment_confirm = $comment_confirm + $comment_confirm_extra;
	}
	zarilia_confirm($comment_confirm, 'comment_delete.php', _CM_DELETESELECT);
	include ZAR_ROOT_PATH.'/footer.php';
	break;
}
?>