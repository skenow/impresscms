<?php
// $Id: comment_edit.php,v 1.1 2007/03/16 02:39:06 catzwolf Exp $
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
if ('system' != $zariliaAddon->getVar('dirname') && ZAR_COMMENT_APPROVENONE == $zariliaAddonConfig['com_rule']) {
	exit();
}

include_once ZAR_ROOT_PATH.'/language/'.$zariliaConfig['language'].'/comment.php';
$com_id = isset($_GET['com_id']) ? intval($_GET['com_id']) : 0;
$com_mode = isset($_GET['com_mode']) ? htmlspecialchars(trim($_GET['com_mode']), ENT_QUOTES) : '';
if ($com_mode == '') {
	if (is_object($zariliaUser)) {
		$com_mode = $zariliaUser->getVar('umode');
	} else {
		$com_mode = $zariliaConfig['com_mode'];
	}
}
if (!isset($_GET['com_order'])) {
	if (is_object($zariliaUser)) {
		$com_order = $zariliaUser->getVar('uorder');
	} else {
		$com_order = $zariliaConfig['com_order'];
	}
} else {
	$com_order = intval($_GET['com_order']);
}
$comment_handler =& zarilia_gethandler('comment');
$comment =& $comment_handler->get($com_id);
$dohtml = $comment->getVar('dohtml');
$dosmiley = $comment->getVar('dosmiley');
$dobr = $comment->getVar('dobr');
$doxcode = $comment->getVar('doxcode');
$com_icon = $comment->getVar('com_icon');
$com_itemid = $comment->getVar('com_itemid');
$com_title = $comment->getVar('com_title', 'E');
$com_text = $comment->getVar('com_text', 'E');
$com_pid = $comment->getVar('com_pid');
$com_status = $comment->getVar('com_status');
$com_rootid = $comment->getVar('com_rootid');
if ($zariliaAddon->getVar('dirname') != 'system') {
	include ZAR_ROOT_PATH.'/header.php';
	include ZAR_ROOT_PATH.'/include/comment_form.php';
	include ZAR_ROOT_PATH.'/footer.php';
} else {
	include ZAR_ROOT_PATH.'/include/comment_form.php';
}
?>