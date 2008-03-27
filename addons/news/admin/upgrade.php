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

include_once '../../../include/cp_header.php';
zarilia_cp_header();
include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';


if (is_object($zariliaUser) && $zariliaUser->isAdmin($zariliaAddon->getVar('mid'))) {
	$errors=0;
	// 1) Create, if it does not exists, the stories_files table
	if(!news_TableExists($zariliaDB->prefix('stories_files')))
	{
		$sql = 'CREATE TABLE '.$zariliaDB->prefix('stories_files')." (
  			fileid int(8) unsigned NOT NULL auto_increment,
  			filerealname varchar(255) NOT NULL default '',
  			storyid int(8) unsigned NOT NULL default '0',
  			date int(10) NOT NULL default '0',
  			mimetype varchar(64) NOT NULL default '',
  			downloadname varchar(255) NOT NULL default '',
  			counter int(8) unsigned NOT NULL default '0',
  			PRIMARY KEY  (fileid),
  			KEY storyid (storyid)
			) TYPE=MyISAM;";
		if (!$zariliaDB->Execute($sql)) {
	    	echo '<br />' . _AM_NEWS_UPGRADEFAILED.' '._AM_NEWS_UPGRADEFAILED1;
	    	$errors++;
		}
	}

	// 2) Change the topic title's length, in the topics table
	$sql=sprintf('ALTER TABLE ' . $zariliaDB->prefix('topics') . ' CHANGE topic_title topic_title VARCHAR( 255 ) NOT NULL;');
	$result=$zariliaDB->Execute($sql);
	if (!$result) {
    	echo '<br />' .  _AM_NEWS_UPGRADEFAILED.' '._AM_NEWS_UPGRADEFAILED2;
    	$errors++;
	}

	// 2.1) Add the new fields to the topic table
	if (!news_FieldExists('menu',$zariliaDB->prefix('topics'))) {
		news_AddField("menu TINYINT( 1 ) DEFAULT '0' NOT NULL",$zariliaDB->prefix('topics'));
	}
	if (!news_FieldExists('topic_frontpage',$zariliaDB->prefix('topics'))) {
		news_AddField("topic_frontpage TINYINT( 1 ) DEFAULT '1' NOT NULL",$zariliaDB->prefix('topics'));
	}
	if (!news_FieldExists('topic_rssurl',$zariliaDB->prefix('topics'))) {
		news_AddField("topic_rssurl VARCHAR( 255 ) NOT NULL",$zariliaDB->prefix('topics'));
	}
	if (!news_FieldExists('topic_description',$zariliaDB->prefix('topics'))) {
		news_AddField("topic_description TEXT NOT NULL",$zariliaDB->prefix('topics'));
	}
	if (!news_FieldExists('topic_color',$zariliaDB->prefix('topics'))) {
		news_AddField("topic_color varchar(6) NOT NULL default '000000'",$zariliaDB->prefix('topics'));
	}

	// 3) If it does not exists, create the table stories_votedata
	if(!news_TableExists($zariliaDB->prefix('stories_votedata')))
	{
		$sql = 'CREATE TABLE '.$zariliaDB->prefix('stories_votedata')." (
  			ratingid int(11) unsigned NOT NULL auto_increment,
  			storyid int(8) unsigned NOT NULL default '0',
  			ratinguser int(11) NOT NULL default '0',
  			rating tinyint(3) unsigned NOT NULL default '0',
  			ratinghostname varchar(60) NOT NULL default '',
  			ratingtimestamp int(10) NOT NULL default '0',
  			PRIMARY KEY  (ratingid),
  			KEY ratinguser (ratinguser),
  			KEY ratinghostname (ratinghostname),
  			KEY storyid (storyid)
			) TYPE=MyISAM;";
		if (!$zariliaDB->Execute($sql)) {
	    	echo '<br />' .  _AM_NEWS_UPGRADEFAILED.' '._AM_NEWS_UPGRADEFAILED3;
	    	$errors++;
		}
	}

	// 4) Create the four new fields for the votes in the story table
	if (!news_FieldExists('rating',$zariliaDB->prefix('stories'))) {
		news_AddField("rating DOUBLE( 6, 4 ) DEFAULT '0.0000' NOT NULL",$zariliaDB->prefix('stories'));
	}
	if (!news_FieldExists('votes',$zariliaDB->prefix('stories'))) {
		news_AddField("votes INT( 11 ) UNSIGNED DEFAULT '0' NOT NULL",$zariliaDB->prefix('stories'));
	}
	if (!news_FieldExists('keywords',$zariliaDB->prefix('stories'))) {
		news_AddField("keywords VARCHAR(255) NOT NULL",$zariliaDB->prefix('stories'));
	}
	if (!news_FieldExists('description',$zariliaDB->prefix('stories'))) {
		news_AddField("description VARCHAR(255) NOT NULL",$zariliaDB->prefix('stories'));
	}

	// 5) Add some indexes to the topics table
	$sql=sprintf('ALTER TABLE ' . $zariliaDB->prefix('topics') . " ADD INDEX ( `topic_title` );");
	$result=$zariliaDB->Execute($sql);
	$sql=sprintf('ALTER TABLE ' . $zariliaDB->prefix('topics') . " ADD INDEX ( `menu` );");
	$result=$zariliaDB->Execute($sql);


    // At the end, if there was errors, show them or redirect user to the module's upgrade page
	if($errors) {
		echo '<H1>' . _AM_NEWS_UPGRADEFAILED . '</H1>';
		echo '<br />' . _AM_NEWS_UPGRADEFAILED0;
	} else {
		echo _AM_NEWS_UPGRADECOMPLETE." - <a href='".ZAR_URL."/addons/system/admin.php?fct=modulesadmin&op=update&module=news'>"._AM_NEWS_UPDATEMODULE."</a>";
	}
} else {
	printf("<h2>%s</h2>\n",_AM_NEWS_UPGR_ACCESS_ERROR);
}
zarilia_cp_footer();
?>