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
/**
 * AMS Import
 *
 * This script will import topics, articles, files, links, ratings, comments and notifications from AMS 2.41
 *
 * @package News
 * @author Instant Zero (http://www.instant-zero.com)
 * @copyright 2005, 2006 - Instant Zero
 * @version 1.0
 */

include_once '../../../include/cp_header.php';
zarilia_cp_header();
include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.newsstory.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.sfiles.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.newstopic.php';
include_once ZAR_ROOT_PATH.'/class/zariliatree.php';

if (is_object($zariliaUser) && $zariliaUser->isAdmin($zariliaAddon->getVar('mid'))) {
	if(!isset($_POST['go']) ) {
		echo '<h1>Welcome to the AMS 2.41 import script</h1>';
		echo '<br /><br />Select the import options you wan to use :';
		echo "<form method='post' action='amsimport.php'>";
		echo "<br /><input type='checkbox' name='useforum' value='1' /> Import forums links inside news (at the bottom of the news)";
		echo "<br /><input type='checkbox' name='useextlinks' value='1' /> Import external links inside news (at the bottom of the news)";
		echo "<br /><br /><input type='submit' name='go' value='Import' />";
		echo '</form>';
		echo "<br /><br />If you check the two last options then the forum's link and all the external links will be added at the end of the body text.";
	} else {
		// Launch the import
		if (file_exists(ZAR_ROOT_PATH.'/addons/AMS/language/'.$zariliaConfig['language'].'/main.php')) {
    		include_once ZAR_ROOT_PATH.'/addons/AMS/language/'.$zariliaConfig['language'].'/main.php';
		} else {
    		include_once ZAR_ROOT_PATH.'/addons/AMS/language/english/main.php';
		}
		if (file_exists(ZAR_ROOT_PATH.'/addons/AMS/language/'.$zariliaConfig['language'].'/admin.php')) {
    		include_once ZAR_ROOT_PATH.'/addons/AMS/language/'.$zariliaConfig['language'].'/admin.php';
		} else {
    		include_once ZAR_ROOT_PATH.'/addons/AMS/language/english/admin.php';
		}
		global $zariliaDB;
$db=&$zariliaDB;
		// User's choices
		$use_forum 		= (isset($_POST['useforum']) && $_POST['useforum']==1) ? 1 : 0;
		$use_extlinks 	= (isset($_POST['useextlinks']) && $_POST['useextlinks']==1) ? 1 : 0;
		// Retreive News module's ID
		$module_handler = &zarilia_gethandler('addon');
   		$newsModule = &$module_handler->getByDirname('news');
		$news_mid = $newsModule->getVar('mid');
		// Retreive AMS module's ID
   		$AmsModule = &$module_handler->getByDirname('AMS');
		$ams_mid = $AmsModule->getVar('mid');

		// Retreive AMS tables names
		$ams_topics		= $zariliaDB->prefix('ams_topics');
		$ams_articles	= $zariliaDB->prefix('ams_article');
		$ams_text		= $zariliaDB->prefix('ams_text');
		$ams_files		= $zariliaDB->prefix('ams_files');
		$ams_links		= $zariliaDB->prefix('ams_link');
		$ams_rating		= $zariliaDB->prefix('ams_rating');
		// Retreive News tables names
		$news_stories_votedata = $zariliaDB->prefix('stories_votedata');
		// Misc
		$comment_handler = &zarilia_gethandler('comment');
		$notification_handler = &zarilia_gethandler('notification');
		$ams_news_topics=array();	// Key => AMS Id,  Value => News ID

        // The import by itself
        // Read topics by their order
        $mytree = new ZariliaTree($ams_topics,'topic_id','topic_pid');
        $ams_topics = $mytree->getChildTreeArray(0,'weight');
		foreach($ams_topics as $one_amstopic) {
			// First we create the topic
			$topicpid=0;
			if($one_amstopic['topic_pid']!=0) { // Search for its the parent
				if(array_key_exists($one_amstopic['topic_pid'],$ams_news_topics)) {
					$topicpid=$ams_news_topics[$one_amstopic['topic_pid']];
				}
			}
			$news_topic = new NewsTopic();
			$news_topic->setTopicPid($topicpid);
			$news_topic->setTopicTitle($one_amstopic['topic_title']);
			$news_topic->setTopicImgurl($one_amstopic['topic_imgurl']);
			$news_topic->setMenu(0);
			$news_topic->setTopicFrontpage(1);
			$news_topic->Settopic_rssurl('');
			$news_topic->setTopicDescription('');
			$news_topic->setTopic_color('000000');
			$news_topic->store();
			echo '<br>- The following topic was imported : '.$news_topic->topic_title();
			$ams_topicid = $one_amstopic['topic_id'];
			$news_topicid = $news_topic->topic_id();
			$ams_news_topics[$ams_topicid] = $news_topicid;

			// Then we insert all its articles
			$result = $db->Execute('SELECT * FROM '.$ams_articles.' WHERE topicid='.$ams_topicid.' ORDER BY created');
			while ( $article = $result->FetchRow() ) {
				$ams_newsid = $article['storyid'];

				// We search for the last version
				$result2 = $db->Execute('SELECT * FROM '.$ams_text.' WHERE storyid='.$ams_newsid.' AND current=1');
				$text_lastversion = $result2->FetchRow();

				// We search for the number of votes
				$result3 = $db->Execute('SELECT count(*) as cpt FROM '.$ams_rating.' WHERE storyid='.$ams_newsid);
				$votes = $result3->FetchRow();

				// The links
				$links='';
				if($use_extlinks) {
					$result7 = $db->Execute('SELECT * FROM '.$ams_links.' WHERE storyid='.$ams_newsid.' ORDER BY linkid');
					while ( $link = $result7->FetchRow() ) {
						if(trim($links)=='') {
							$links="\n\n"._AMS_NW_RELATEDARTICLES."\n\n";
						}
						$links .= _AMS_NW_EXTERNALLINK.' [url='.$link['link_link'].']'.$link['link_title'].'[/url]'."\n";
					}
				}

				// The forum
				$forum='';
				if($use_forum && $one_amstopic['forum_id']!=0) {
					$forum = "\n\n".'[url='.ZAR_URL.'/addons/newbb/viewforum.php?forum='.$one_amstopic['forum_id'].']'._AMS_AM_LINKEDFORUM.'[/url]'."\n";
				}

				// We create the story
				$news = new NewsStory();
  				$news->setUid($text_lastversion['uid']);
  				$news->setTitle($article['title']);
  				$news->created=$article['created'];
  				$news->setPublished($article['published']);
  				$news->setExpired($article['expired']);
  				$news->setHostname($article['hostname']);
  				$news->setNohtml($article['nohtml']);
  				$news->setNosmiley($article['nosmiley']);
  				$news->setHometext($text_lastversion['hometext']);
  				$news->setBodytext($text_lastversion['bodytext'].$links.$forum);
  				$news->Setkeywords('');
  				$news->Setdescription('');
  				$news->counter=$article['counter'];
  				$news->setTopicId($news_topicid);
  				$news->setIhome($article['ihome']);
  				$news->setNotifyPub($article['notifypub']);
  				$news->story_type=$article['story_type'];
  				$news->setTopicdisplay($article['topicdisplay']);
  				$news->setTopicalign($article['topicalign']);
  				$news->setComments($article['comments']);
  				$news->rating=$article['rating'];
  				$news->votes=$votes['cpt'];
  				$approved = $article['published']>0 ? true : false;
  				$news->approved=$approved;
  				$news->store($approved);
  				echo '<br>&nbsp;&nbsp;This story was imported : '.$news->title();
  				$news_newsid=$news->storyid();	// ********************

  				// The files
				$result4 = $db->Execute('SELECT * FROM '.$ams_files.' WHERE storyid='.$ams_newsid);
				while ( $file = $result4->FetchRow() ) {
					$sfile = new sFiles();
  					$sfile->setFileRealName($file['filerealname']);
  					$sfile->setStoryid($news_newsid);
  					$sfile->date=$file['date'];
  					$sfile->setMimetype($file['mimetype']);
  					$sfile->setDownloadname($file['downloadname']);
  					$sfile->counter=$file['counter'];
  					$sfile->store();
  					echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;This file was imported : '.$sfile->getDownloadname();
  					$news_fileid=$sfile->fileid;
				}

				// The ratings
				$result5 = $db->Execute('SELECT * FROM '.$ams_rating.' WHERE storyid='.$ams_newsid);
				while ( $ratings = $result5->FetchRow() ) {
					$result6 = $db->ExecuteF('INSERT INTO '.$news_stories_votedata." (storyid, ratinguser, rating, ratinghostname, ratingtimestamp) VALUES (".$news_newsid.','.$ratings['ratinguser'].','.$ratings['rating'].','.$ratings['ratinghostname'].','.$ratings['ratingtimestamp'].')');
				}

				// The comments
				$comments = &$comment_handler->getByItemId($ams_mid, $ams_newsid, 'ASC');
				if(is_array($comments) && count($comments)>0) {
					foreach($comments as $onecomment) {
						$onecomment->setNew();
						$onecomment->setVar('com_modid',$news_mid);
						$onecomment->setVar('com_itemid',$news_newsid);
						$comment_handler->insert($onecomment);
					}
				}
				unset($comments);

				// The notifications of this news
				//$notifications = &$notification_handler->getByItemId($ams_mid, $ams_newsid, 'ASC');
	        	$criteria = new CriteriaCompo(new Criteria('not_modid', $ams_mid));
    	    	$criteria->add(new Criteria('not_itemid', $ams_newsid));
            	$criteria->setOrder('ASC');
        		$notifications = $notification_handler->getObjects($criteria);
				if(is_array($notifications) && count($notifications)>0) {
					foreach($notifications as $onenotification) {
						$onenotification->setNew();
						$onenotification->setVar('not_modid',$news_mid);
						$onenotification->setVar('not_itemid',$news_newsid);
						$notification_handler->insert($onenotification);
					}
				}
				unset($notifications);
			}
		}
		// Finally, import all the globals notifications
       	$criteria = new CriteriaCompo(new Criteria('not_modid', $ams_mid));
    	$criteria->add(new Criteria('not_category', 'global'));
       	$criteria->setOrder('ASC');
   		$notifications = $notification_handler->getObjects($criteria);
		if(is_array($notifications) && count($notifications)>0) {
			foreach($notifications as $onenotification) {
				$onenotification->setNew();
				$onenotification->setVar('not_modid',$news_mid);
				$onenotification->setVar('not_itemid',$news_newsid);
				$notification_handler->insert($onenotification);
			}
		}
		unset($notifications);
		echo "<p><a href='".ZAR_URL."/addons/news/admin/groupperms.php'>The import is finished, don't forget to verify and set the topics permissions !</a></p>";
	}
} else {
    redirect_header(ZAR_URL.'/addons/news/index.php', 3, _NOPERM);
    exit();
}
zarilia_cp_footer();
?>