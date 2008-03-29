<?php
define('ICMS_INCLUDE_OPENID', true);
$xoopsOption['pagetype'] = 'user';
include_once("mainfile.php");

$redirect_url = $_SESSION['frompage'];

/**
 * Registering a new user with his openid
 */
if (isset($_POST['openid_register'])) {
	$response=@$_SESSION['openid_response'];
	//icms_debug_vardump($_SESSION); exit;
	if (@$response->status != Auth_OpenID_SUCCESS) {
		redirect_header(XOOPS_URL . '/user.php', 3, _US_OPENID_NOPERM);
	} else {
	    $displayId = $response->getDisplayIdentifier();
	    $cid = $response->identity_url;
	}

	$err='';
	$msg='';

	$sreg=$_SESSION['openid_sreg'];
	include_once(XOOPS_ROOT_PATH.'/header.php');

	/**
	 * @todo generalize this
	 */
	$tzoffset=array('Tokyo'=>'+9','London'=>'0');

	$uname = quote_smart($_POST['uname']);

	/**
	 * @todo use the related UserConfigOption
	 */
	if (strlen($uname)<3 ){ // Username too short.
		/**
		 * @todo put this in a template and use language constant
		 */
		$msg  ='<h3 class="centerLblockTitle">スクリーン名のエラー</h3>';
		$msg .="<p>" . $uname . " は、短すぎます。<br />別のスクリーン名をお選びください。</p>\n";
		$msg .= '<form method="POST" action="register.php" />';
		$msg .= 'スクリーン名：<input type="text" name="uname" />';
		$msg .= '<input type="submit" value="登録"></form>';
		echo $msg;
		include_once(XOOPS_ROOT_PATH.'/footer.php');
	}

	$criteria = new CriteriaCompo(new Criteria('uname', $uname ));
	$user_handler =& xoops_gethandler('user');
	$users =& $user_handler->getObjects($criteria, false);

	if( empty( $users ) || count( $users ) != 1) {
		$username = quote_smart($uname);
		$email = quote_smart($sreg['email']);
		$name = quote_smart($sreg['fullname']);
		$tz = quote_smart($tzoffset[$sreg['timezone']]);
		$country = quote_smart($sreg['country']);
		$timenow = quote_smart(time());

		/*
		 * @todo use proper core class, manage activation_type and send notifications
		 */
		/*
		if ($xoopsConfigUser['activation_type'] == 1) {
			$newuser->setVar('level', 1, true);
		}
		if (!$user_handler->insert($newuser, true)) {
			$this->setErrors(106, 'The new user could not be created. ' . $newuser->getHtmlErrors());
			return false;
		}
		$newid = $newuser->getVar('uid');
		$mship_handler = new XoopsMembershipHandler($xoopsDB);
		$mship = & $mship_handler->create();
		$mship->setVar('groupid', XOOPS_GROUP_USERS);
		$mship->setVar('uid', $newid);
		if (!$mship_handler->insert($mship, true)) {
			$this->setErrors(107, 'The new user was created but could not be added to the Registered Users group');
			return false;
		}
		$this->setErrors(111, 'We have created an account for you on this site.');

		if ($xoopsConfigUser['activation_type'] == 1) {

			//Sending a confirmation email to the newly registered user

			$myts = & MyTextSanitizer :: getInstance();
			$xoopsMailer = & getMailer();
			$xoopsMailer->useMail();
			$xoopsMailer->setTemplate('welcome.tpl');
			$xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
			$xoopsMailer->assign('NAME', $sreg['fullname']);
			$xoopsMailer->assign('UNAME', $sreg['nickname']);
			$xoopsMailer->assign('X_UEMAIL', $sreg['email']);
			$xoopsMailer->assign('SITEURL', XOOPS_URL . "/");
			$xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
			$xoopsMailer->setToEmails($sreg['email']);
			$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
			$xoopsMailer->setFromName($xoopsConfig['sitename']);
			$xoopsMailer->setSubject(sprintf(_US_YOURINSCRIPTION, $myts->oopsStripSlashesGPC($xoopsConfig['sitename'])));
			$xoopsMailer->send();

			// Sending a notification email to a selected group when a new user registers

						if ($xoopsConfigUser['new_user_notify'] == 1 && !empty ($xoopsConfigUser['new_user_notify_group'])) {
				$xoopsMailer = & getMailer();
				$xoopsMailer->useMail();
				$xoopsMailer->setTemplate('newuser_notify.tpl');
				$xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
				$xoopsMailer->assign('NAME', $sreg['fullname']);
				$xoopsMailer->assign('UNAME', $sreg['nickname']);
				$xoopsMailer->assign('X_UEMAIL', $sreg['email']);
				$xoopsMailer->assign('SITEURL', XOOPS_URL . "/");
				$xoopsMailer->setToGroups($member_handler->getGroup($xoopsConfigUser['new_user_notify_group']));
				$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
				$xoopsMailer->setFromName($xoopsConfig['sitename']);
				$xoopsMailer->setSubject(sprintf(_US_NEWUSERREGAT, $xoopsConfig['sitename']));
				$xoopsMailer->send();
			}
		}
		if ($xoopsConfigUser['activation_type'] == 0) {
			$this->setErrors(112, 'A confirmation email was sent to your email adress. Please follow the instructions to activate your account.');
			$xoopsMailer = & getMailer();
			$xoopsMailer->useMail();
			$xoopsMailer->setTemplate('register.tpl');
			$xoopsMailer->assign('NAME', $sreg['fullname']);
			$xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
			$xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
			$xoopsMailer->assign('SITEURL', XOOPS_URL . "/");
			$xoopsMailer->setToUsers(new XoopsUser($newid));
			$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
			$xoopsMailer->setFromName($xoopsConfig['sitename']);
			$xoopsMailer->setSubject(sprintf(_US_USERKEYFOR, $sreg['nickname']));
			$xoopsMailer->send();
			if ($xoopsConfigUser['new_user_notify'] == 1 && !empty ($xoopsConfigUser['new_user_notify_group'])) {
				$xoopsMailer = & getMailer();
				$xoopsMailer->useMail();
				$xoopsMailer->setTemplate('newuser_notify.tpl');
				$xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
				$xoopsMailer->assign('NAME', $sreg['fullname']);
				$xoopsMailer->assign('UNAME', $sreg['nickname']);
				$xoopsMailer->assign('X_UEMAIL', $sreg['email']);
				$xoopsMailer->assign('SITEURL', XOOPS_URL . "/");
				$xoopsMailer->setToGroups($member_handler->getGroup($xoopsConfigUser['new_user_notify_group']));
				$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
				$xoopsMailer->setFromName($xoopsConfig['sitename']);
				$xoopsMailer->setSubject(sprintf(_US_NEWUSERREGAT, $xoopsConfig['sitename']));
				$xoopsMailer->send();
			}
		}
		elseif ($xoopsConfigUser['activation_type'] == 2) {
			$this->setErrors(113, 'Your account will need to be approved by an administrator. You will receive a notification when it\s done.');
			$xoopsMailer = & getMailer();
			$xoopsMailer->useMail();
			$xoopsMailer->setTemplate('adminactivate.tpl');
			$xoopsMailer->assign('NAME', $sreg['fullname']);
			$xoopsMailer->assign('USERNAME', $sreg['nickname']);
			$xoopsMailer->assign('USEREMAIL', $sreg['email']);
			$xoopsMailer->assign('USERACTLINK', XOOPS_URL . '/user.php?op=actv&id=' . $newid . '&actkey=' . $actkey);
			$xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
			$xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
			$xoopsMailer->assign('SITEURL', XOOPS_URL . "/");
			$member_handler = & xoops_gethandler('member');
			$xoopsMailer->setToGroups($member_handler->getGroup($xoopsConfigUser['activation_group']));
			$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
			$xoopsMailer->setFromName($xoopsConfig['sitename']);
			$xoopsMailer->setSubject(sprintf(_US_USERKEYFOR, $sreg['nickname']));
			$xoopsMailer->send();
		}
		if ($xoopsConfigUser['new_user_notify'] == 1 && !empty ($xoopsConfigUser['new_user_notify_group'])) {
			 // Sending a notification email to a selected group when a new user registers
			if ($xoopsConfigUser['new_user_notify'] == 1 && !empty ($xoopsConfigUser['new_user_notify_group'])) {
				$xoopsMailer = & getMailer();
				$xoopsMailer->useMail();
				$xoopsMailer->setTemplate('newuser_notify.tpl');
				$xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
				$xoopsMailer->assign('NAME', $sreg['fullname']);
				$xoopsMailer->assign('UNAME', $sreg['nickname']);
				$xoopsMailer->assign('X_UEMAIL', $sreg['email']);
				$xoopsMailer->assign('SITEURL', XOOPS_URL . "/");
				$xoopsMailer->setToGroups($member_handler->getGroup($xoopsConfigUser['new_user_notify_group']));
				$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
				$xoopsMailer->setFromName($xoopsConfig['sitename']);
				$xoopsMailer->setSubject(sprintf(_US_NEWUSERREGAT, $xoopsConfig['sitename']));
				$xoopsMailer->send();
			}
		}
		*/
		$t_user = $xoopsDB->prefix("users");
		$t_groups_users_link = $xoopsDB->prefix("groups_users_link");
		$query = "INSERT into $t_user
	                SET
	                uname=$username,
	                email=$email,
	                name=$name,
	                pass='*',
	                user_regdate=$timenow ,
	                timezone_offset=$tz,
	                user_from=$country,
					openid='$cid'
	                " ;
	    //echo $query; exit;
		$xoopsDB->queryF($query);
		$users =& $user_handler->getObjects($criteria, false);
		$user = $users[0] ;

		// Now, add the user to the group.
		$uid = $user->getVar('uid');
		$query2 = "INSERT INTO $t_groups_users_link
		(`linkid`,`groupid`,`uid`)
		VALUES (NULL, '2', '$uid');"   ;
		$xoopsDB->queryF($query2);

		unset( $users ) ;

		$msg = $msg . '<br />openid_localid 登録終了';

		// Login with this user.

		/**
		 * @todo use proper login process (include/checklogin.php)
		 */
		$xoopsUser = $user;

		if (false != $user && $user->getVar('level') > 0) {
			$member_handler =& xoops_gethandler('member');
			$user->setVar('last_login', time());
			if (!$member_handler->insertUser($user, false)) {
			}
			$_SESSION['xoopsUserId'] = $user->getVar('uid');
			$_SESSION['xoopsUserGroups'] = $user->getGroups();
			$user_theme = $user->getVar('theme');
			if (in_array($user_theme, $xoopsConfig['theme_set_allowed'])) {
				$_SESSION['xoopsUserTheme'] = $user_theme;
			}
			unset($_SESSION['openid_response']);
			unset($_SESSION['openid_sreg']);
			print_r($_SESSION['openid_response']);
	    header("Location: " . $redirect_url);
	    unset($_SESSION['frompage']);
		}
	} else { // Username Collided.
		/**
		 * @todo put this in a template and use language constant
		 */
		$msg="<br />" . $uname . " は、既に他の方が使われています。<br />別のスクリーン名をお選びください。<br />\n";
		$msg .= '<form method="POST" action="register.php" />';
		$msg .= 'スクリーン名：<input type="text" name="uname" />';
		$msg .= '<input type="submit" value="登録"></form>';
		echo $msg;
		include_once(XOOPS_ROOT_PATH.'/footer.php');
	}
} elseif(isset($_POST['openid_link'])) {
	/**
	 * Linking an existing user with this openid
	 */
	$response=$_SESSION['openid_response'];
	if (@$response->status != Auth_OpenID_SUCCESS) {
		redirect_header(XOOPS_URL . '/user.php', 3, _US_OPENID_NOPERM);
	} 
	
	include_once(XOOPS_ROOT_PATH.'/header.php');
	
	$myts = MyTextSanitizer::getInstance();
	
	$uname4sql = addslashes($myts->stripSlashesGPC($_POST['uname'])) ;
	$pass4sql = addslashes( $myts->stripSlashesGPC($_POST['pass']) ) ;
	
	$member_handler = xoops_gethandler('member');
	$thisUser = $member_handler->loginUser($uname4sql, $pass4sql);

	if (!$thisUser) {
		redirect_header($redirect_url, 3, _US_OPENID_LINKED_AUTH_FAILED);
	}
	
	if ($thisUser->getVar('level') == 0) {
		redirect_header($redirect_url, 3, _US_OPENID_LINKED_AUTH_NOT_ACTIVATED);
	}
	
	// This means the authentication succeeded.
    $displayId = $response->getDisplayIdentifier();
    $cid = $response->identity_url;

	$thisUser->setVar('last_login', time());
	$thisUser->setVar('openid', $response->identity_url);
	
	if (!$member_handler->insertUser($thisUser)) {
		redirect_header($redirect_url, 3, _US_OPENID_LINKED_AUTH_CANNOT_SAVE);
	}
	
	$_SESSION['xoopsUserId'] = $thisUser->getVar('uid');
	$_SESSION['xoopsUserGroups'] = $thisUser->getGroups();
	$user_theme = $thisUser->getVar('theme');
	
	if (in_array($user_theme, $xoopsConfig['theme_set_allowed'])) {
		$_SESSION['xoopsUserTheme'] = $user_theme;
	}

	unset($_SESSION['openid_response']);
	unset($_SESSION['openid_sreg']);
	unset($_SESSION['frompage']);
	
	redirect_header($redirect_url, 3, sprintf(_US_LOGGINGU, $thisUser->getVar('uname')));
} else {
	include_once("include/checklogin.php");
}
?>