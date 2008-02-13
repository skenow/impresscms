<?php
// $Id$
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

$xoopsOption['pagetype'] = 'user';

include 'mainfile.php';
$myts =& MyTextSanitizer::getInstance();

$config_handler =& xoops_gethandler('config');
$xoopsConfigUser =& $config_handler->getConfigsByCat(XOOPS_CONF_USER);
if (empty($xoopsConfigUser['allow_register'])) {
	redirect_header('index.php', 6, _US_NOREGISTER);
	exit();
}

function userCheck($uname, $email, $pass, $vpass)
{
	global $xoopsConfigUser;
	$xoopsDB =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
	$stop = '';
	if (!checkEmail($email)) {
		$stop .= _US_INVALIDMAIL.'<br />';
	}
	foreach ($xoopsConfigUser['bad_emails'] as $be) {
		if (!empty($be) && preg_match("/".$be."/i", $email)) {
			$stop .= _US_INVALIDMAIL.'<br />';
			break;
		}
	}
	if (strrpos($email,' ') > 0) {
		$stop .= _US_EMAILNOSPACES.'<br />';
	}
	$uname = xoops_trim($uname);
	switch ($xoopsConfigUser['uname_test_level']) {
	case 0:
		// strict
		$restriction = '/[^a-zA-Z0-9\_\-]/';
		break;
	case 1:
		// medium
		$restriction = '/[^a-zA-Z0-9\_\-\<\>\,\.\$\%\#\@\!\\\'\"]/';
		break;
	case 2:
		// loose
		$restriction = '/[\000-\040]/';
		break;
	}
	if (empty($uname) || preg_match($restriction, $uname)) {
		$stop .= _US_INVALIDNICKNAME."<br />";
	}
	if (strlen($uname) > $xoopsConfigUser['maxuname']) {
		$stop .= sprintf(_US_NICKNAMETOOLONG, $xoopsConfigUser['maxuname'])."<br />";
	}
	if (strlen($uname) < $xoopsConfigUser['minuname']) {
		$stop .= sprintf(_US_NICKNAMETOOSHORT, $xoopsConfigUser['minuname'])."<br />";
	}
	foreach ($xoopsConfigUser['bad_unames'] as $bu) {
		if (!empty($bu) && preg_match("/".$bu."/i", $uname)) {
			$stop .= _US_NAMERESERVED."<br />";
			break;
		}
	}
	if (strrpos($uname, ' ') > 0) {
		$stop .= _US_NICKNAMENOSPACES."<br />";
	}
	$sql = sprintf('SELECT COUNT(*) FROM %s WHERE uname = %s', $xoopsDB->prefix('users'), $xoopsDB->quoteString(addslashes($uname)));
	$result = $xoopsDB->query($sql);
	list($count) = $xoopsDB->fetchRow($result);
	if ($count > 0) {
		$stop .= _US_NICKNAMETAKEN."<br />";
	}
	$count = 0;
	if ( $email ) {
		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE email = %s', $xoopsDB->prefix('users'), $xoopsDB->quoteString(addslashes($email)));
		$result = $xoopsDB->query($sql);
		list($count) = $xoopsDB->fetchRow($result);
		if ( $count > 0 ) {
			$stop .= _US_EMAILTAKEN."<br />";
		}
	}
	if ( !isset($pass) || $pass == '' || !isset($vpass) || $vpass == '' ) {
		$stop .= _US_ENTERPWD.'<br />';
	}
	if ( (isset($pass)) && ($pass != $vpass) ) {
		$stop .= _US_PASSNOTSAME.'<br />';
	} elseif ( ($pass != '') && (strlen($pass) < $xoopsConfigUser['minpass']) ) {
		$stop .= sprintf(_US_PWDTOOSHORT,$xoopsConfigUser['minpass'])."<br />";
	}
	return $stop;
}
$op = !isset($_POST['op']) ? 'register' : $_POST['op'];
$uname = isset($_POST['uname']) ? $myts->stripSlashesGPC($_POST['uname']) : '';

/* Hack by marcan (InBox Solutions) for Ampersand Design
 * Adding the real name as a mandatory field on regisration form
 */
$name = isset($_POST['name']) ? $myts->stripSlashesGPC($_POST['name']) : '';
// Adding the real name as a mandatory field on regisration form

$email = isset($_POST['email']) ? trim($myts->stripSlashesGPC($_POST['email'])) : '';
$url = isset($_POST['url']) ? trim($myts->stripSlashesGPC($_POST['url'])) : '';
$pass = isset($_POST['pass']) ? $myts->stripSlashesGPC($_POST['pass']) : '';
$vpass = isset($_POST['vpass']) ? $myts->stripSlashesGPC($_POST['vpass']) : '';
$timezone_offset = isset($_POST['timezone_offset']) ? intval($_POST['timezone_offset']) : $xoopsConfig['default_TZ'];
$user_viewemail = (isset($_POST['user_viewemail']) && intval($_POST['user_viewemail'])) ? 1 : 0;
// Hack by marcan for Ampersand Design
//$user_mailok = (isset($_POST['user_mailok']) && intval($_POST['user_mailok'])) ? 1 : 0;
$user_mailok = (isset($_POST['user_mailok']) && intval($_POST['user_mailok'])) ? 1 : 1;
// End of Hack by marcan for AmpersandDesign
$agree_disc = (isset($_POST['agree_disc']) && intval($_POST['agree_disc'])) ? 1 : 0;
switch ( $op ) {
case 'newuser':
	if (!$GLOBALS['xoopsSecurity']->check()) {
	    /* Hack by marcan <InBox Solutions>
	     * When $GLOBALS['xoopsSecurity']->check() returned false, the script was ending.
	     */
	    redirect_header(XOOPS_URL . "/register.php", 4, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
		//echo implode('<br />', $GLOBALS['xoopsSecurity']->getErrors());
		// When $GLOBALS['xoopsSecurity']->check() returned false, the script was ending.
		exit();
	}
	include 'header.php';
	$stop = '';
	if ($xoopsConfigUser['reg_dispdsclmr'] != 0 && $xoopsConfigUser['reg_disclaimer'] != '') {
		if (empty($agree_disc)) {
			$stop .= _US_UNEEDAGREE.'<br />';
		}
	}
	$stop .= userCheck($uname, $email, $pass, $vpass);
	if (empty($stop)) {
		echo _US_USERNAME.": ".$myts->htmlSpecialChars($uname)."<br />";
		/* Hack by marcan (InBox Solutions) for Ampersand Design
		 * Adding the real name as a mandatory field on regisration form
		 */	
		echo _US_REALNAME.": ".$myts->htmlSpecialChars($name)."<br />";
	    // Adding the real name as a mandatory field on regisration form
		echo _US_EMAIL.": ".$myts->htmlSpecialChars($email)."<br />";
		if ($url != '') {
			$url = formatURL($url);
			echo _US_WEBSITE.': '.$myts->htmlSpecialChars($url).'<br />';
		}
		$f_timezone = ($timezone_offset < 0) ? 'GMT '.$timezone_offset : 'GMT +'.$timezone_offset;
		echo _US_TIMEZONE.": $f_timezone<br />";
		
		/* Hack by marcan (InBox Solutions) for Ampersand Design
		 * Adding the real name as a mandatory field on regisration form
		 */			
		echo "<form action='register.php' method='post'>
		<input type='hidden' name='uname' value='".$myts->htmlSpecialChars($uname)."' />
		<input type='hidden' name='email' value='".$myts->htmlSpecialChars($email)."' />
		<input type='hidden' name='name' value='".$myts->htmlSpecialChars($name)."' />";		
		// Adding the real name as a mandatory field on regisration form
		echo "<input type='hidden' name='user_viewemail' value='".$user_viewemail."' />
		<input type='hidden' name='timezone_offset' value='".(float)$timezone_offset."' />
		<input type='hidden' name='url' value='".$myts->htmlSpecialChars($url)."' />
		<input type='hidden' name='pass' value='".$myts->htmlSpecialChars($pass)."' />
		<input type='hidden' name='vpass' value='".$myts->htmlSpecialChars($vpass)."' />
		<input type='hidden' name='user_mailok' value='".$user_mailok."' />
		<br /><br /><input type='hidden' name='op' value='finish' />".$GLOBALS['xoopsSecurity']->getTokenHTML()."<input type='submit' value='". _US_FINISH ."' /></form>";
	} else {
		echo "<span style='color:#ff0000;'>$stop</span>";
		include 'include/registerform.php';
		$reg_form->display();
	}
	include 'footer.php';
	break;
case 'finish':
	if (!$GLOBALS['xoopsSecurity']->check()) {
	    echo implode('<br />', $GLOBALS['xoopsSecurity']->getErrors());
		exit();
	}
	include 'header.php';
	$stop = userCheck($uname, $email, $pass, $vpass);
	if ( empty($stop) ) {
		$member_handler =& xoops_gethandler('member');
		$newuser =& $member_handler->createUser();
		$newuser->setVar('user_viewemail',$user_viewemail, true);
		$newuser->setVar('uname', $uname, true);
		/* Hack by marcan (InBox Solutions) for Ampersand Design
		 * Adding the real name as a mandatory field on regisration form
		 */	
		$newuser->setVar('name', $name, true);
		// Adding the real name as a mandatory field on regisration form	
		$newuser->setVar('email', $email, true);
		if ($url != '') {
			$newuser->setVar('url', formatURL($url), true);
		}
		$newuser->setVar('user_avatar','blank.gif', true);
		$actkey = substr(md5(uniqid(mt_rand(), 1)), 0, 8);
		$newuser->setVar('actkey', $actkey, true);
		$newuser->setVar('pass', md5($pass), true);
		$newuser->setVar('timezone_offset', $timezone_offset, true);
		$newuser->setVar('user_regdate', time(), true);
		$newuser->setVar('uorder',$xoopsConfig['com_order'], true);
		$newuser->setVar('umode',$xoopsConfig['com_mode'], true);
		$newuser->setVar('user_mailok',$user_mailok, true);
		if ($xoopsConfigUser['activation_type'] == 1) {
			$newuser->setVar('level', 1, true);
		}
		if (!$member_handler->insertUser($newuser)) {
			echo _US_REGISTERNG;
			include 'footer.php';
			exit();
		}
		$newid = $newuser->getVar('uid');
		if (!$member_handler->addUserToGroup(XOOPS_GROUP_USERS, $newid)) {
			echo _US_REGISTERNG;
			include 'footer.php';
			exit();
		}
		if ($xoopsConfigUser['activation_type'] == 1) {
      
			/* Hack by marcan <InBox Solutions> for Ampersand Design
			 * Sending a confirmation email to the newly registered user
			 */
			$myts =& MyTextSanitizer::getInstance();
			$xoopsMailer =& getMailer();
			$xoopsMailer->useMail();
			$xoopsMailer->setTemplate('welcome.tpl');
			$xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
			$xoopsMailer->assign('NAME', $name);
			$xoopsMailer->assign('UNAME', $uname);
  			$xoopsMailer->assign('PASSWORD', $vpass);
			$xoopsMailer->assign('X_UEMAIL', $email);			
			$xoopsMailer->assign('SITEURL', XOOPS_URL."/");
			$xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);			
      		$xoopsMailer->setToEmails($email);
			$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
			$xoopsMailer->setFromName($xoopsConfig['sitename']);
			$xoopsMailer->setSubject(sprintf(_US_YOURINSCRIPTION,$myts->oopsStripSlashesGPC($xoopsConfig['sitename'])));
			$xoopsMailer->send();
			/* End of Hack by marcan <InBox Solutions> for Ampersand Design
			 * Sending a confirmation email to the newly registered user
			 */			
			
			/* Hack by marcan <InBox Solutions> for Ampersand Design
			 * Sending a notification email to a selected group when a new user registers
			 */	
			if ($xoopsConfigUser['new_user_notify'] == 1 && !empty($xoopsConfigUser['new_user_notify_group'])) {
				$xoopsMailer =& getMailer();
				$xoopsMailer->useMail();
				$xoopsMailer->setTemplate('newuser_notify.tpl');
				$xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
				$xoopsMailer->assign('NAME', $name);
				$xoopsMailer->assign('UNAME', $uname);
				$xoopsMailer->assign('X_UEMAIL', $email);			
				$xoopsMailer->assign('SITEURL', XOOPS_URL."/");
	      		$xoopsMailer->setToGroups($member_handler->getGroup($xoopsConfigUser['new_user_notify_group']));
				$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
				$xoopsMailer->setFromName($xoopsConfig['sitename']);
				$xoopsMailer->setSubject(sprintf(_US_NEWUSERREGAT,$xoopsConfig['sitename']));
				$xoopsMailer->send();
			}	
			/* End of Hack by marcan <InBox Solutions> for Ampersand Design
			 * Sending a notification email to a selected group when a new user registers
			 */						
			
			redirect_header('user.php', 4, _US_ACTLOGIN);
			exit();
		}
		if ($xoopsConfigUser['activation_type'] == 0) {
			$xoopsMailer =& getMailer();
			$xoopsMailer->useMail();
			$xoopsMailer->setTemplate('register.tpl');
			/* Hack by marcan <InBox Solutions>
			 * Including the real name in the mail notification
			 */	
			$xoopsMailer->assign('NAME', $name);
			/* End of Hack by marcan <InBox Solutions>
			 * Including the real name in the mail notification
			 */					
			$xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
			$xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
			$xoopsMailer->assign('SITEURL', XOOPS_URL."/");
			$xoopsMailer->setToUsers(new XoopsUser($newid));
			$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
			$xoopsMailer->setFromName($xoopsConfig['sitename']);
			$xoopsMailer->setSubject(sprintf(_US_USERKEYFOR, $uname));
			if ( !$xoopsMailer->send() ) {
				echo _US_YOURREGMAILNG;
			} else {
				echo _US_YOURREGISTERED;
			}
			
			/* Hack by marcan <InBox Solutions> for Ampersand Design
			 * Sending a notification email to a selected group when a new user registers
			 */	
			if ($xoopsConfigUser['new_user_notify'] == 1 && !empty($xoopsConfigUser['new_user_notify_group'])) {
				$xoopsMailer =& getMailer();
				$xoopsMailer->useMail();
				$xoopsMailer->setTemplate('newuser_notify.tpl');
				$xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
				$xoopsMailer->assign('NAME', $name);
				$xoopsMailer->assign('UNAME', $uname);
				$xoopsMailer->assign('X_UEMAIL', $email);			
				$xoopsMailer->assign('SITEURL', XOOPS_URL."/");
	      		$xoopsMailer->setToGroups($member_handler->getGroup($xoopsConfigUser['new_user_notify_group']));
				$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
				$xoopsMailer->setFromName($xoopsConfig['sitename']);
				$xoopsMailer->setSubject(sprintf(_US_NEWUSERREGAT,$xoopsConfig['sitename']));
				$xoopsMailer->send();
			}	
			/* End of Hack by marcan <InBox Solutions> for Ampersand Design
			 * Sending a notification email to a selected group when a new user registers
			 */				
		} elseif ($xoopsConfigUser['activation_type'] == 2) {
			$xoopsMailer =& getMailer();
			$xoopsMailer->useMail();
			$xoopsMailer->setTemplate('adminactivate.tpl');
			/* Hack by marcan <InBox Solutions>
			 * Including the real name in the mail notification
			 */	
			$xoopsMailer->assign('NAME', $name);
			/* End of Hack by marcan <InBox Solutions>
			 * Including the real name in the mail notification
			 */				
			$xoopsMailer->assign('USERNAME', $uname);
			$xoopsMailer->assign('USEREMAIL', $email);
			$xoopsMailer->assign('USERACTLINK', XOOPS_URL.'/user.php?op=actv&id='.$newid.'&actkey='.$actkey);
			$xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
			$xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
			$xoopsMailer->assign('SITEURL', XOOPS_URL."/");
			$member_handler =& xoops_gethandler('member');
			$xoopsMailer->setToGroups($member_handler->getGroup($xoopsConfigUser['activation_group']));
			$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
			$xoopsMailer->setFromName($xoopsConfig['sitename']);
			$xoopsMailer->setSubject(sprintf(_US_USERKEYFOR, $uname));
			if ( !$xoopsMailer->send() ) {
				echo _US_YOURREGMAILNG;
			} else {
				echo _US_YOURREGISTERED2;
			}
		}
		if ($xoopsConfigUser['new_user_notify'] == 1 && !empty($xoopsConfigUser['new_user_notify_group'])) {
			/* Hack by marcan <InBox Solutions> for Ampersand Design
			 * Sending a notification email to a selected group when a new user registers
			 */	
			if ($xoopsConfigUser['new_user_notify'] == 1 && !empty($xoopsConfigUser['new_user_notify_group'])) {
				$xoopsMailer =& getMailer();
				$xoopsMailer->useMail();
				$xoopsMailer->setTemplate('newuser_notify.tpl');
				$xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
				$xoopsMailer->assign('NAME', $name);
				$xoopsMailer->assign('UNAME', $uname);
				$xoopsMailer->assign('X_UEMAIL', $email);			
				$xoopsMailer->assign('SITEURL', XOOPS_URL."/");
	      		$xoopsMailer->setToGroups($member_handler->getGroup($xoopsConfigUser['new_user_notify_group']));
				$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
				$xoopsMailer->setFromName($xoopsConfig['sitename']);
				$xoopsMailer->setSubject(sprintf(_US_NEWUSERREGAT,$xoopsConfig['sitename']));
				$xoopsMailer->send();
			}	

			/*$xoopsMailer =& getMailer();
			$xoopsMailer->useMail();
			$member_handler =& xoops_gethandler('member');
			$xoopsMailer->setToGroups($member_handler->getGroup($xoopsConfigUser['new_user_notify_group']));
			$xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
			$xoopsMailer->setFromName($xoopsConfig['sitename']);
			$xoopsMailer->setSubject(sprintf(_US_NEWUSERREGAT,$xoopsConfig['sitename']));
			$xoopsMailer->setBody(sprintf(_US_HASJUSTREG, $uname));
			$xoopsMailer->send();*/
			
			/* End of Hack by marcan <InBox Solutions> for Ampersand Design
			 * Sending a notification email to a selected group when a new user registers
			 */					
						
		}
	} else {
		echo "<span style='color:#ff0000; font-weight:bold;'>$stop</span>";
		include 'include/registerform.php';
		$reg_form->display();
	}
	include 'footer.php';
	break;
case 'register':
default:
	include 'header.php';
	include 'include/registerform.php';
	$reg_form->display();
	
	/* Hack by marcan <InBox Solutions>
	 * Adding a little box related to connexion problems
	 */
	echo '<br /><fieldset style="padding: 10px;">
  		<legend style="font-weight: bold;">' . _US_CONNECTPROBLEM . '</legend>
		' . _US_CONNECTPROBLEM_TEXT . '
	</fieldset> <br/>';
	/* End of Hack by marcan <InBox Solutions>
	 * Adding a little box related to connexion problems
	 */	
	include 'footer.php';
	break;
}
?>
