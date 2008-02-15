<?php
function openid_login_show()
{
	global $xoopsUser, $xoopsConfig, $xoopsTpl;
	if (!$xoopsUser) {
		$xoopsTpl->assign('frompage',$_SERVER['REQUEST_URI']);
		$block = array();
		$block['content'] = "OpenID Login";
		return $block;
	}
	return false;
}

?>
