<?php
// $Id: notification.inc.php,v 1.3 2004/05/09 12:56:07 mithyt2 Exp $
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
if (!defined('ZAR_ROOT_PATH')) {
	die("Zarilia root path not defined");
}

function news_notify_iteminfo($category, $item_id)
{
	if ($category=='global') {
		$item['name'] = '';
		$item['url'] = '';
		return $item;
	}

	global $zariliaDB;

	if ($category=='story') {
		// Assume we have a valid story id
		$sql = 'SELECT title FROM '.$zariliaDB->prefix('stories') . ' WHERE storyid = ' . intval($item_id);
		$result = $zariliaDB->Execute($sql);
		if($result) {
			$result_array = $result->FetchRow();
			$item['name'] = $result_array['title'];
			$item['url'] = ZAR_URL . '/addons/news/article.php?storyid=' . intval($item_id);
			return $item;
		} else {
			return null;
		}
	}
	
	// Added by Lankford on 2007/3/23
	if ($category=='category') {
		$sql = 'SELECT title FROM ' . $zariliaDB->prefix('topics') . ' WHERE topic_id = '.intval($item_id);
		$result = $zariliaDB->Execute($sql);
		if($result) {
			$result_array = $result->FetchRow();
			$item['name'] = $result_array['topic_id'];
			$item['url'] = ZAR_URL . '/addons/news/index.php?storytopic=' . $item_id;
			return $item;
		} else {
			return null;
		}
	}	
}
?>