<?php
// $Id$
// auth_xoops.php - XOOPS authentification class
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
/**
 * @package     kernel
 * @subpackage  auth
 * @description	Authentification class for Native XOOPS
 * @author	    Pierre-Eric MENUET	<pemphp@free.fr>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class XoopsAuthXoops extends XoopsAuth {
	/**
	 * Authentication Service constructor
	 */
	function XoopsAuthXoops(& $dao) {
		$this->_dao = $dao;
		$this->auth_method = 'xoops';
	}
	/**
	 *  Authenticate user
	 *
	 * @param string $uname
	 * @param string $pwd
	 *
	 * @return bool
	 */
	function authenticate($uname, $pwd = null) {
		$member_handler = & xoops_gethandler('member');
		$user = & $member_handler->loginUser($uname, $pwd);
		if ($user == false) {
			$this->setErrors(1, _US_INCORRECTLOGIN);
		}
		return ($user);
	}
	/**
	 *  Authenticate user with an openid
	 *
	 * @return bool
	 */
	function authenticateWithOpenid() {
		require_once XOOPS_ROOT_PATH . '/xo/common.php';
		$consumer = getConsumer();
		// Complete the authentication process using the server's
		// response.
		$response = $consumer->complete();
		// Check the response status.
		if ($response->status == Auth_OpenID_CANCEL) {
			// This means the authentication was cancelled.
			$this->setErrors(101, 'Verification cancelled.');
			return false;
		} else
			if ($response->status == Auth_OpenID_FAILURE) {
				// Authentication failed; display the error message.
				$this->setErrors(103, 'OpenID authentication failed: ' . $response->message);
				return false;
			} else
				if ($response->status == Auth_OpenID_SUCCESS) {
					// This means the authentication succeeded; extract the
					// identity URL and Simple Registration data (if it was
					// returned).
					$openid = $response->identity_url;
					$esc_identity = htmlspecialchars($openid, ENT_QUOTES);
					$sreg_resp = Auth_OpenID_SRegResponse :: fromSuccessResponse($response);
					$sreg = $sreg_resp->contents();
					// cleaning the trailing / if any
					$last_char = substr($esc_identity, strlen($esc_identity) - 1, 1);
					if ($last_char == '/') {
						$esc_identity = substr($esc_identity, 0, strlen($esc_identity) - 1);
					}
					// let's see if we have a user with this openid
					$member_handler = & xoops_gethandler('member');
					$criteria = new CriteriaCompo();
					$criteria->add(new Criteria('openid_url', $esc_identity));
					$users = & $member_handler->getUsers($criteria);
					if ($users && count($users) > 0) {
						if (count($users) > 1) {
							/**
							 * more then 1 users found. This needs to be adressed in the futur providing the
							 * user with a select box containing the users found and asking him what user to use
							 */
							$this->setErrors(104, 'More then one user found.');
							return false;
						}
						$user = $users[0];
						return $user;
					} else {
						global $xoopsConfig, $xoopsDB, $xoopsConfigUser;
						$config_handler = & xoops_gethandler('config');
						$xoopsConfigUser = & $config_handler->getConfigsByCat(XOOPS_CONF_USER);
						$this->setErrors(105, 'No related openid found.');
						if (!isset ($sreg['nickname']) || $sreg['nickname'] == '') {
							$this->setErrors(108, 'No username specified. An account could not be created.');
							return false;
						}
						if (!isset ($sreg['email']) || $sreg['email'] == '') {
							$this->setErrors(109, 'No email specified. An account could not be created.');
							return false;
						}
						$sreg['fullname'] = utf8_decode($sreg['fullname']);
						// let's create the account
						$user_handler = & xoops_gethandler('user');
						$newuser = & $user_handler->create();
						$newuser->setVar('user_viewemail', false, true);
						$newuser->setVar('uname', $sreg['nickname'], true);
						/* Hack by marcan (InBox Solutions) for Ampersand Design
						 * Adding the real name as a mandatory field on regisration form
						 */
						$newuser->setVar('name', $sreg['fullname'], true);
						// Adding the real name as a mandatory field on regisration form
						$newuser->setVar('email', $sreg['email'], true);
						$newuser->setVar('openid_url', $esc_identity, true);
						$newuser->setVar('user_avatar', 'blank.gif', true);
						$actkey = substr(md5(uniqid(mt_rand(), 1)), 0, 8);
						$newuser->setVar('actkey', $actkey, true);
						$newuser->setVar('pass', md5($esc_identity), true);
						$newuser->setVar('user_regdate', time(), true);
						$newuser->setVar('uorder', $xoopsConfig['com_order'], true);
						$newuser->setVar('umode', $xoopsConfig['com_mode'], true);
						$newuser->setVar('user_mailok', false, true);
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
							/* Hack by marcan <InBox Solutions> for Ampersand Design
							 * Sending a confirmation email to the newly registered user
							 */
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
							/* End of Hack by marcan <InBox Solutions> for Ampersand Design
							 * Sending a confirmation email to the newly registered user
							 */
							/* Hack by marcan <InBox Solutions> for Ampersand Design
							 * Sending a notification email to a selected group when a new user registers
							 */
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
							/* End of Hack by marcan <InBox Solutions> for Ampersand Design
							 * Sending a notification email to a selected group when a new user registers
							 */
						}
						if ($xoopsConfigUser['activation_type'] == 0) {
							$this->setErrors(112, 'A confirmation email was sent to your email adress. Please follow the instructions to activate your account.');
							$xoopsMailer = & getMailer();
							$xoopsMailer->useMail();
							$xoopsMailer->setTemplate('register.tpl');
							/* Hack by marcan <InBox Solutions>
							 * Including the real name in the mail notification
							 */
							$xoopsMailer->assign('NAME', $sreg['fullname']);
							/* End of Hack by marcan <InBox Solutions>
							 * Including the real name in the mail notification
							 */
							$xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
							$xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
							$xoopsMailer->assign('SITEURL', XOOPS_URL . "/");
							$xoopsMailer->setToUsers(new XoopsUser($newid));
							$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
							$xoopsMailer->setFromName($xoopsConfig['sitename']);
							$xoopsMailer->setSubject(sprintf(_US_USERKEYFOR, $sreg['nickname']));
							$xoopsMailer->send();
							/* Hack by marcan <InBox Solutions> for Ampersand Design
							 * Sending a notification email to a selected group when a new user registers
							 */
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
							/* End of Hack by marcan <InBox Solutions> for Ampersand Design
							 * Sending a notification email to a selected group when a new user registers
							 */
						}
						elseif ($xoopsConfigUser['activation_type'] == 2) {
							$this->setErrors(113, 'Your account will need to be approved by an administrator. You will receive a notification when it\s done.');
							$xoopsMailer = & getMailer();
							$xoopsMailer->useMail();
							$xoopsMailer->setTemplate('adminactivate.tpl');
							/* Hack by marcan <InBox Solutions>
							 * Including the real name in the mail notification
							 */
							$xoopsMailer->assign('NAME', $sreg['fullname']);
							/* End of Hack by marcan <InBox Solutions>
							 * Including the real name in the mail notification
							 */
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
							/* Hack by marcan <InBox Solutions> for Ampersand Design
							 * Sending a notification email to a selected group when a new user registers
							 */
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
						return $newuser;
					}
				}
	}
}
?>