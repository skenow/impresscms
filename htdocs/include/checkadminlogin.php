<?php
/**
* Checks & starts the ICMS Admin Session.
*
* @copyright	http://www.impresscms.org/ The ImpressCMS Project
* @license	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @package	core
* @since	ImpressCMS 1.2
* @author	Vaughan Montgomery <vaughan@impresscms.org>
* @author	The ImpressCMS Project
* @version	$Id$
*/
if(!defined('ICMS_ROOT_PATH')) {exit();}
include_once ICMS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/user.php';
$uname = !isset($_POST['uname']) ? '' : trim($_POST['uname']);
$pass = !isset($_POST['pass']) ? '' : trim($_POST['pass']);
/**
* Commented out for OpenID , we need to change it to make a better validation if OpenID is used
*/
$member_handler = xoops_gethandler('member');
$myts = MyTextsanitizer::getInstance();

include_once ICMS_ROOT_PATH.'/class/auth/authfactory.php';
include_once ICMS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/auth.php';

$xoopsAuth = XoopsAuthFactory::getAuthConnection($myts->addSlashes($uname));

$uname4sql = addslashes($myts->stripSlashesGPC($uname));
$pass4sql = addslashes($myts->stripSlashesGPC($pass));
$user = $xoopsAuth->authenticate($uname4sql, $pass4sql);

if(false != $user)
{
	$cookie_secure = 0;
	$adsess_handler = xoops_gethandler('adminsession');
	$_SESSION['icmsAdminId'] = $user->getVar('uid');
	if($xoopsConfig['admin_use_mysession'] && $xoopsConfig['admin_session_name'] != '' && $xoopsConfig['admin_session_expire'] > 0)
	{
		$admin_sess_name = $xoopsConfig['admin_session_name'];
		$admin_sess_expire = 60*$xoopsConfig['admin_session_expire'];
	}
	else
	{
		$admin_sess_name = 'ICMSADSESSION';
		$admin_sess_expire = ini_get('session.cookie_lifetime');
	}
	$adsess_handler->icms_sessionOpen($user->getVar('pass'));
	$admin_sess_fprint = $_SESSION['icms_admin_fprint'];
	setcookie($admin_sess_name, $admin_sess_fprint, $admin_sess_expire ? time()+$admin_sess_expire : 0, '/',  '', $cookie_secure, 0);
	redirect_header(ICMS_URL.'/modules/system/admin.php', 1, sprintf(_US_LOGGINGUAD, $user->getVar('uname')), false);
}
else {redirect_header(ICMS_URL.'/admin.php', 5, $xoopsAuth->getHtmlErrors());}
exit();
?>