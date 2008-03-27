<?php
// $Id: comment_new.php,v 1.1 2007/03/16 02:39:06 catzwolf Exp $
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
$com_itemid = isset($_GET['com_itemid']) ? intval($_GET['com_itemid']) : 0;

if ($com_itemid > 0) {
	include ZAR_ROOT_PATH.'/header.php';
	if (isset($com_replytitle)) {
		if (isset($com_replytext)) {
			themecenterposts($com_replytitle, $com_replytext);
		}
		$myts =& MyTextSanitizer::getInstance();
		$com_title = $myts->htmlSpecialChars($com_replytitle);
		if (!preg_match("/^(Re|"._CM_RE."):/i", $com_title)) {
			$com_title = _CM_RE.": ".zarilia_substr($com_title, 0, 56);
		}
	} else {
		$com_title = '';
	}
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
	$com_id = 0;
	$noname = 0;
	$dosmiley = 1;
	$dohtml = 0;
	$dobr = 1;
	$doxcode = 1;
	$com_icon = '';
	$com_pid = 0;
	$com_rootid = 0;
	$com_text = '';

	include ZAR_ROOT_PATH.'/include/comment_form.php';
	include ZAR_ROOT_PATH.'/footer.php';
}
?>