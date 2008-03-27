<?php
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                  Copyright (c) 2005-2006 Instant Zero                     //
//                     <http://xoops.instant-zero.com/>                      //
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

/*
 * Created on 28 oct. 2006
 *
 * This page will display a list of the authors of the site
 *
 * @package News
 * @author Instant Zero
 * @copyright (c) Instant Zero - http://www.instant-zero.com
 */
include_once '../../mainfile.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.newsstory.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.newstopic.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.sfiles.php';
include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';

if(!news_getmoduleoption('newsbythisauthor')) {
    redirect_header('index.php',2,_ERRORS);
    exit();
}

$zariliaOption['template_main'] = 'news_whos_who.html';
include_once ZAR_ROOT_PATH.'/header.php';

$option = news_getmoduleoption('displayname');
$article = new NewsStory();
$uid_ids = array();
$uid_ids = $article->getWhosWho(news_getmoduleoption('restrictindex'));
if(count($uid_ids) > 0) {
	$lst_uid = implode(',', $uid_ids);
	$member_handler = &zarilia_gethandler('member');
	$critere = new Criteria('uid', '('.$lst_uid.')', 'IN');
	$tbl_users = $member_handler->getUsers($critere);
	foreach($tbl_users as $one_user) {
		$uname = '';
		switch($option) {
			case 1:		// Username
				$uname = $one_user->getVar('uname');
				break;

			case 2:		// Display full name (if it is not empty)
				if(zarilia_trim($one_user->getVar('name')) != '') {
					$uname = $one_user->getVar('name');
				} else {
					$uname = $one_user->getVar('uname');
				}
				break;
		}
		$zariliaTpl->append('whoswho', array('uid' => $one_user->getVar('uid'), 'name' => $uname, 'user_avatarurl' => ZAR_URL.'/uploads/'.$one_user->getVar('user_avatar')));
	}
}

$zariliaTpl->assign('advertisement', news_getmoduleoption('advertisement'));

/**
 * Manage all the meta datas
 */
news_CreateMetaDatas($article);

$zariliaTpl->addTitle( _AM_NEWS_WHOS_WHO);
$myts = &MyTextSanitizer::getInstance();
$meta_description = _AM_NEWS_WHOS_WHO . ' - '.$myts->htmlSpecialChars($zariliaAddon->getVar('name'));
if(isset($xoTheme) && is_object($xoTheme)) {
	$xoTheme->addMeta( 'meta', 'description', $meta_description);
} else {	// Compatibility for old Xoops versions
	$zariliaTpl->addMeta('description',  $meta_description);
}

include_once ZAR_ROOT_PATH.'/footer.php';
?>