<?php
// $Id: checkadminlogin.php 1083 2007-10-16 16:42:51Z phppp $
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
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.xoops.org/ http://jp.xoops.org/  http://www.myweb.ne.jp/  //
// Project: The XOOPS Project (http://www.xoops.org/)                        //
// ------------------------------------------------------------------------- //
if(!defined('ICMS_ROOT_PATH')) {exit();}
include_once ICMS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/user.php';
$uname = !isset($_POST['uname']) ? '' : trim($_POST['uname']);
$pass = !isset($_POST['pass']) ? '' : trim($_POST['pass']);
/**
* Commented out for OpenID , we need to change it to make a better validation if OpenID is used
*/
$member_handler =& xoops_gethandler('member');
$myts =& MyTextsanitizer::getInstance();

include_once ICMS_ROOT_PATH.'/class/auth/authfactory.php';
include_once ICMS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/auth.php';

$xoopsAuth =& XoopsAuthFactory::getAuthConnection($myts->addSlashes($uname));

$uname4sql = addslashes($myts->stripSlashesGPC($uname));
$pass4sql = addslashes($myts->stripSlashesGPC($pass));
$user =& $xoopsAuth->authenticate($uname4sql, $pass4sql);

if(false != $user)
{
	$adsess_handler =& xoops_gethandler('adminsession');
	$_SESSION['xoopsAdminId'] = $user->getVar('uid');
	$session_expire = ini_get('session.cookie_lifetime');
	$Ynj = date('Y-n-j');
	setcookie('ICMSADSESSION', $Ynj.':'.hash('sha256', $user->getVar('pass').XOOPS_DB_SALT.$Ynj), $session_expire ? time() + $session_expire : 0, '/',  '', 0, 0);
	redirect_header(ICMS_URL.'/modules/system/admin.php', 1, sprintf(_US_LOGGINGUAD, $user->getVar('uname')), false);
}
else {redirect_header(ICMS_URL.'/admin.php', 5, $xoopsAuth->getHtmlErrors());}
exit();
?>
