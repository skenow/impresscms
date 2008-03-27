<?php
// $Id: comment_reply.php,v 1.1 2007/03/16 02:39:06 catzwolf Exp $
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
$r_name = ZariliaUser::getUnameFromId($comment->getVar('com_uid'));
$r_text = _CM_POSTER.': <b>'.$r_name.'</b>&nbsp;&nbsp;'._CM_POSTED.': <b>'.$comment->getVar('com_created').'</b><br /><br />'.$comment->getVar('com_text');$com_title = $comment->getVar('com_title', 'E');
if (!preg_match("/^(Re|"._CM_RE."):/i", $com_title)) {
	$com_title = _CM_RE.": ".zarilia_substr($com_title, 0, 56);
}
$com_pid = $com_id;
$com_text = '';
$com_id = 0;
$dosmiley = 1;
$dohtml = 0;
$doxcode = 1;
$dobr = 1;
$doimage = 1;
$com_icon = '';
$com_rootid = $comment->getVar('com_rootid');
$com_itemid = $comment->getVar('com_itemid');
include ZAR_ROOT_PATH.'/header.php';
themecenterposts($comment->getVar('com_title'), $r_text);
include ZAR_ROOT_PATH.'/include/comment_form.php';
include ZAR_ROOT_PATH.'/footer.php';
?>