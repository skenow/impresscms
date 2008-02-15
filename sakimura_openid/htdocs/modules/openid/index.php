<?php
/* -----------------------------------------------------
// OpenID RP Module for Xoops
//  by Nat Sakimura
//  (c) 2008 by Nat Sakimura (=nat)
//  License: GPL
-------------------------------------------------------- */
require_once('header.php');

$xoopsOption['template_main'] = 'openid_consumer.html';

include_once(XOOPS_ROOT_PATH.'/header.php');

$xoopsTpl->assign('rptitle', _OD_TITLE);

include(XOOPS_ROOT_PATH.'/footer.php');

?>
