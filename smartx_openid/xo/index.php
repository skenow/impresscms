<?php

include('header.php');

$xoTheme->addStylesheet(XO_URL . 'xo.css');

$xoopsTpl->display(XO_ROOT_PATH . 'templates/xo_index.html');

include_once(XOOPS_ROOT_PATH . '/footer.php');
?>