<?php
/**
* The check login include file
 * This file is included from several others during their login validation process
 *  - user.php, site-closed.php, finish_auth.php. checklogin.php does not return
 *  to any of those files calling it. The outcome is a redirect with a result
*
* @copyright	http://www.xoops.org/ The XOOPS Project
* @copyright	XOOPS_copyrights.txt
* @copyright	http://www.impresscms.org/ The ImpressCMS Project
* @license	LICENSE.txt
* @package	core
* @since	XOOPS
* @author	http://www.xoops.org The XOOPS Project
* @author	modified by UnderDog <underdog@impresscms.org>
* @version	$Id: checklogin.php 11462 2011-12-11 01:07:52Z skenow $
*/

defined('ICMS_ROOT_PATH') || exit();

icms_loadLanguageFile('core', 'user');
$uname = !isset($_POST['uname']) ? '' : trim($_POST['uname']);
$pass = !isset($_POST['pass']) ? '' : trim($_POST['pass']);

/* make sure redirect stays within domain and isn't open to exploit */
if (!isset($redirect)) {

	$redirect = isset($_GET['xoops_redirect'])
		? $_GET['xoops_redirect']
		: isset($_POST['xoops_redirect'])
			? $_POST['xoops_redirect']
			: ICMS_URL;
	
		$redirect = htmlspecialchars(trim($redirect));
		if ($redirect !== htmlspecialchars($_SERVER['REQUEST_URI'])) $redirect = ICMS_URL;
}

/* if redirect goes to the register page, divert to main page - users don't go to register */
if ($redirect && strpos($redirect, 'register') !== FALSE) {
	$redirect = ICMS_URL;
}

/* prevent breaking out of the domain */
$pos = strpos($redirect, '://');
if ($pos !== FALSE) {
	$icmsLocation = substr(ICMS_URL, strpos(ICMS_URL, '://') + 3);
	if (substr($redirect, $pos + 3, strlen($icmsLocation)) != $icmsLocation) {
		$redirect = ICMS_URL;
	} elseif (substr($redirect, $pos + 3, strlen($icmsLocation) + 1) == $icmsLocation . '.') {
		$redirect = ICMS_URL;
	}
}

/* Commented out for OpenID , we need to change it to make a better validation if OpenID is used
 if ($uname == '' || $pass == '') {
	redirect_header(ICMS_URL.'/user.php', 1, _US_INCORRECTLOGIN);
	exit();
}*/
$member_handler =& xoops_gethandler('member');
$myts =& MyTextsanitizer::getInstance();

include_once ICMS_ROOT_PATH.'/class/auth/authfactory.php';
icms_loadLanguageFile('core', 'auth');
$xoopsAuth =& XoopsAuthFactory::getAuthConnection($myts->addSlashes($uname));
//$user = $xoopsAuth->authenticate($myts->addSlashes($uname), $myts->addSlashes($pass));
// uname&email hack GIJ
$uname4sql = addslashes( $myts->stripSlashesGPC($uname) ) ;
$pass4sql =  $myts->stripSlashesGPC($pass);
/*if( strstr( $uname , '@' ) ) {
	// check by email if uname includes '@'
	$criteria = new CriteriaCompo(new Criteria('email', $uname4sql ));
	$criteria->add(new Criteria('pass', $pass4sql));
	$user_handler =& xoops_gethandler('user');
	$users =& $user_handler->getObjects($criteria, false);
	if( empty( $users ) || count( $users ) != 1 ) $user = false ;
	else $user = $users[0] ;
	unset( $users ) ;
} */
if(empty($user) || !is_object($user)) {$user =& $xoopsAuth->authenticate($uname4sql, $pass4sql);}
// end of uname&email hack GIJ

/* User exists: check to see if the user has been activated.
 * If not, redirect with 'no permission' message
 */
if (false != $user) {
	if (0 == $user->getVar('level')) {
		redirect_header(ICMS_URL.'/index.php', 5, _US_NOACTTPADM);
		exit();
	}
	
	/* Check to see if logins from multiple locations is permitted.
	 * If it is not, check for existing login and redirect if detected
	 */
	if ($icmsConfigPersona['multi_login']){
		if( is_object( $user ) ) {
			$online_handler =& xoops_gethandler('online');
			$online_handler->gc(300);
			$onlines =& $online_handler->getAll();
			foreach( $onlines as $online ) {
				if( $online['online_uid'] == $user->uid() ) {
					$user = false;
					redirect_header(ICMS_URL.'/index.php',3,_US_MULTLOGIN);
				}
			}
			if( is_object( $user ) ) {
				$online_handler->write($user->uid(), $user->uname(),
				time(),0,$_SERVER['REMOTE_ADDR']);
			}
		}
	}
	if ($icmsConfig['closesite'] == 1) {
		$allowed = false;
		foreach ($user->getGroups() as $group) {
			if (in_array($group, $icmsConfig['closesite_okgrp']) || ICMS_GROUP_ADMIN == $group) {
				$allowed = true;
				break;
			}
		}
		if (!$allowed) {
			redirect_header(ICMS_URL.'/', 1, _NOPERM);
			exit();
		}
	}
	$user->setVar('last_login', time());
	if (!$member_handler->insertUser($user)) {
	}
	// Regenerate a new session id and destroy old session
	session_regenerate_id(true);
	$_SESSION = array();
	$_SESSION['xoopsUserId'] = $user->getVar('uid');
	$_SESSION['xoopsUserGroups'] = $user->getGroups();
	if ($icmsConfig['use_mysession'] && $icmsConfig['session_name'] != '') {
		setcookie($icmsConfig['session_name'], session_id(), time()+(60 * $icmsConfig['session_expire']), '/',  '', 0);
	}
	$_SESSION['xoopsUserLastLogin'] = $user->getVar('last_login');
	if (!$member_handler->updateUserByField($user, 'last_login', time())) {
	}
	$user_theme = $user->getVar('theme');
	if (in_array($user_theme, $icmsConfig['theme_set_allowed'])) {
		$_SESSION['xoopsUserTheme'] = $user_theme;
	}
	if (!empty($_POST['xoops_redirect']) && !strpos($_POST['xoops_redirect'], 'register')) {
		$_POST['xoops_redirect'] = trim( $_POST['xoops_redirect'] );
		$parsed = parse_url(ICMS_URL);
		$url = isset($parsed['scheme']) ? $parsed['scheme'].'://' : 'http://';
		if ( isset( $parsed['host'] ) ) {
			$url .= $parsed['host'];
			if ( isset( $parsed['port'] ) ) {
				$url .= ':' . $parsed['port'];
			}
		} else {
			$url .= $_SERVER['HTTP_HOST'];
		}
		if ( @$parsed['path'] ) {
			if ( strncmp( $parsed['path'], $_POST['xoops_redirect'], strlen( $parsed['path'] ) ) ) {
				$url .= $parsed['path'];
			}
		}
		$url .= $_POST['xoops_redirect'];
	} else {
		$url = ICMS_URL.'/index.php';
	}
	if ($pos = strpos( $url, '://' )) {
		$xoopsLocation = substr( ICMS_URL, strpos( ICMS_URL, '://' ) + 3 );
		if ( substr($url, $pos + 3, strlen($xoopsLocation)) != $xoopsLocation)  {
			$url = ICMS_URL;
		 }elseif(substr($url, $pos + 3, strlen($xoopsLocation)+1) == $xoopsLocation.'.') {
			$url = ICMS_URL;
		 }
		 if( substr($url, 0, strlen(ICMS_URL)*2) ==  ICMS_URL.ICMS_URL){
		 	$url = substr($url, strlen(ICMS_URL));

		 }
	}

	// autologin hack V3.1 GIJ (set cookie)
	$secure = substr(ICMS_URL, 0, 5) == 'https' ? 1 : 0; // we need to secure cookie when using SSL
	$xoops_cookie_path = defined('XOOPS_COOKIE_PATH') ? XOOPS_COOKIE_PATH : preg_replace( '?http://[^/]+(/.*)$?' , "$1" , ICMS_URL ) ;
	if( $xoops_cookie_path == ICMS_URL ) $xoops_cookie_path = '/' ;
	if (!empty($_POST['rememberme'])) {
		$expire = time() + ( defined('XOOPS_AUTOLOGIN_LIFETIME') ? XOOPS_AUTOLOGIN_LIFETIME : 604800 ) ; // 1 week default
		setcookie('autologin_uname', $user->getVar('login_name'), $expire, $xoops_cookie_path, '', 0);
		$Ynj = date( 'Y-n-j' ) ;
		setcookie('autologin_pass', $Ynj . ':' . md5( $user->getVar('pass') . XOOPS_DB_PASS . XOOPS_DB_PREFIX . $Ynj ) , $expire, $xoops_cookie_path, '', 0);
	}
	// end of autologin hack V3.1 GIJ

	// RMV-NOTIFY
	// Perform some maintenance of notification records
	$notification_handler =& xoops_gethandler('notification');
	$notification_handler->doLoginMaintenance($user->getVar('uid'));

	redirect_header($url, 1, sprintf(_US_LOGGINGU, $user->getVar('uname')), false);
} elseif(!isset($redirect)) {
	redirect_header(ICMS_URL.'/user.php', 5, $xoopsAuth->getHtmlErrors());
}else{
	redirect_header(ICMS_URL.'/user.php?xoops_redirect='. urlencode($redirect), 5, $xoopsAuth->getHtmlErrors(), false);
}
exit();

?>