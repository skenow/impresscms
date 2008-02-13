<?php

/**
* $Id$
* Module: NotFound
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

include_once ('header.php');

$xoopsTpl->assign('xoops_pagetitle', _NOTFOUND_404_PAGE_TITLE);

$xoopsTpl->assign('not_found_text', sprintf(_NOTFOUND_404_TEXT, $siteName));

$xoopsTpl->display(XOOPS_ROOT_PATH . "/notfound/templates/404.html");

require_once XOOPS_ROOT_PATH.'/footer.php';

?>