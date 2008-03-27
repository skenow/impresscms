<?php
// $Id: index.php,v 1.21 2004/09/01 17:48:07 hthouzard Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
// ------------------------------------------------------------------------- //
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
 * Module's index
 *
 * This page displays a list of the published articles and can also display the
 * stories of a particular topic.
 *
 * @package News
 * @author Xoops Modules Dev Team
 * @copyright (c) The Xoops Project - www.xoops.org
 *
 * Parameters received by this page :
 * @page_param 	int		storytopic 					Topic's ID
 * @page_param	int		topic_id					Topic's ID
 * @page_param	int		storynum					Number of news per page
 * @page_param	int		start						First news to display
 *
 * @page_title			Topic's title - Story's title - Module's name
 *
 * @template_name		news_index.html or news_by_topic.html
 *
 * Template's variables :
 * For each article
 * @template_var 	int		id			story's ID
 * @template_var 	string	poster		Complete link to the author's profile
 * @template_var 	string	author_name	Author's name according to the module's option called displayname
 * @template_var 	int		author_uid	Author's ID
 * @template_var 	float	rating		New's rating
 * @template_var 	int		votes		number of votes
 * @template_var 	int		posttimestamp Timestamp representing the published date
 * @template_var 	string	posttime		Formated published date
 * @template_var 	string	text		The introduction's text
 * @template_var 	string	morelink	The link to read the full article (points to article.php)
 * @template_var 	string	adminlink	Link reserved to the admin to edit and delete the news
 * @template_var 	string	mail_link	Link used to send the story's url by email
 * @template_var 	string	title		Story's title presented on the form of a link
 * @template_var	string	news_title	Just the news title
 * @template_var	string	topic_title	Just the topic's title
 * @template_var	int		hits		Number of times the article was read
 * @template_var 	int		files_attached	Number of files attached to this news
 * @template_var 	string	attached_link	An URL pointing to the attached files
 * @template_var 	string	topic_color	The topic's color
 * @template_var 	int		columnwidth	column's width
 * @template_var 	int		displaynav	To know if we must display the navigation's box
 * @template_var 	string	lang_go		fixed text : Go!
 * @template_var 	string	lang_morereleases	fixed text : More releases in
 * @template_var 	string	lang_on		fixed text : on
 * @template_var 	string	lang_postedby	fixed text : Posted by
 * @template_var 	string	lang_printerpage	fixed text : Printer Friendly Page
 * @template_var 	string	lang_ratethisnews	fixed text : Rate this News
 * @template_var 	string	lang_ratingc	fixed text : Rating:
 * @template_var 	string	lang_reads		fixed text : reads
 * @template_var 	string	lang_sendstory	fixed text : Send this Story to a Friend
 * @template_var 	string	 topic_select	contains the topics selector
*/
include_once '../../mainfile.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.newsstory.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.sfiles.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.newstopic.php';
include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';
include_once ZAR_ROOT_PATH.'/class/tree.php';

$storytopic=0;
if(isset($_GET['storytopic'])) {
	$storytopic=intval($_GET['storytopic']);
} else {
	if(isset($_GET['topic_id'])) {
		$storytopic=intval($_GET['topic_id']);
	}
}

if ($storytopic) {
    $groups = is_object($zariliaUser) ? $zariliaUser->getGroups() : ZAR_GROUP_ANONYMOUS;
    $gperm_handler = &zarilia_gethandler('groupperm');
    if (!$gperm_handler->checkRight('news_view', $storytopic, $groups, $zariliaAddon->getVar('mid'))) {
        redirect_header(ZAR_URL.'/addons/news/index.php', 3, _NOPERM);
        exit();
    }
	$zariliaOption['storytopic'] = $storytopic;
} else {
	$zariliaOption['storytopic'] = 0;
}
if (isset($_GET['storynum'])) {
	$zariliaOption['storynum'] = intval($_GET['storynum']);
	if ($zariliaOption['storynum'] > 30) {
		$zariliaOption['storynum'] = $zariliaAddonConfig['storyhome'];
	}
} else {
	$zariliaOption['storynum'] = $zariliaAddonConfig['storyhome'];
}

if (isset($_GET['start']) ) {
	$start = intval($_GET['start']);
} else {
	$start = 0;
}

if (empty($zariliaAddonConfig['newsdisplay']) || $zariliaAddonConfig['newsdisplay'] == 'Classic' || $zariliaOption['storytopic'] > 0) {
    $showclassic = 1;
} else {
    $showclassic = 0;
}
$firsttitle='';
$topictitle='';
$myts = &MyTextSanitizer::getInstance();
$sfiles = new sFiles();

$column_count = $zariliaAddonConfig['columnmode'];

if ($showclassic) {
    $zariliaOption['template_main'] = 'news_index.html';
	include_once ZAR_ROOT_PATH.'/header.php';
	$xt = new NewsTopic();

    $zariliaTpl->assign('columnwidth', intval(1/$column_count*100));
	if ($zariliaAddonConfig['ratenews']) {
		$zariliaTpl->assign('rates', true);
		$zariliaTpl->assign('lang_ratingc', _NW_RATINGC);
		$zariliaTpl->assign('lang_ratethisnews', _NW_RATETHISNEWS);
	} else {
		$zariliaTpl->assign('rates', false);
	}

	if($zariliaOption['storytopic']) {
		$xt->getTopic($zariliaOption['storytopic']);
		$zariliaTpl->assign('topic_description', $xt->topic_description('S'));
		$zariliaTpl->assign('topic_color', '#'.$xt->topic_color('S'));
		$topictitle=$xt->topic_title();
	}

	if ($zariliaAddonConfig['displaynav'] == 1 ) {
        $zariliaTpl->assign('displaynav', true);

		$allTopics = $xt->getAllTopics($zariliaAddonConfig['restrictindex']);
		$topic_tree = new ZariliaObjectTree($allTopics, 'topic_id', 'topic_pid');
		$topic_select = $topic_tree->makeSelBox('storytopic', 'topic_title', '-- ', $zariliaOption['storytopic'], true);

        $zariliaTpl->assign('topic_select', $topic_select);
        $storynum_options = '';
        for ( $i = 5; $i <= 30; $i = $i + 5 ) {
            $sel = '';
            if ($i == $zariliaOption['storynum']) {
                $sel = ' selected="selected"';
            }
            $storynum_options .= '<option value="'.$i.'"'.$sel.'>'.$i.'</option>';
        }
        $zariliaTpl->assign('storynum_options', $storynum_options);
    } else {
        $zariliaTpl->assign('displaynav', false);
    }
	if($zariliaOption['storytopic']==0) {
		$topic_frontpage=true;
	} else {
		$topic_frontpage=false;
	}
	$sarray = NewsStory::getAllPublished($zariliaOption['storynum'], $start, $zariliaAddonConfig['restrictindex'], $zariliaOption['storytopic'], 0, true, 'published', $topic_frontpage);

    $scount = count($sarray);
    $zariliaTpl->assign('story_count', $scount);
    $k = 0;
    $columns = array();
    if($scount>0)
    {
    	$storieslist=array();
    	foreach ($sarray as $storyid => $thisstory) {
    		$storieslist[]=$thisstory->storyid();
    	}
		$filesperstory = $sfiles->getCountbyStories($storieslist);

	    foreach ($sarray as $storyid => $thisstory) {
	    	$filescount = array_key_exists($thisstory->storyid(),$filesperstory) ? $filesperstory[$thisstory->storyid()] : 0;
        	$story = $thisstory->prepare2show($filescount);
        	// The line below can be used to display a Permanent Link image
        	// $story['title'] .= "&nbsp;&nbsp;<a href='".ZAR_URL."/addons/news/article.php?storyid=".$sarray[$i]->storyid()."'><img src='".ZAR_URL."/addons/news/images/x.gif' alt='Permanent Link' /></a>";
        	$story['news_title'] = $story['title'];
        	$story['title'] = $thisstory->textlink().'&nbsp;:&nbsp;'.$story['title'];
        	$story['topic_title'] = $thisstory->textlink();
        	$story['topic_color'] = '#'.$myts->displayTarea($thisstory->topic_color);
	       	if($firsttitle=='') {
       			$firsttitle=$myts->htmlSpecialChars($thisstory->topic_title()) . ' - ' .  $myts->htmlSpecialChars($thisstory->title());
       		}
        	$columns[$k][] = $story;
        	$k++;
        	if ($k == $column_count) {
	            $k = 0;
        	}
		}
	}
	$zariliaTpl->assign('columns', $columns);
	unset($story);

	$totalcount = NewsStory::countPublishedByTopic($zariliaOption['storytopic'], $zariliaAddonConfig['restrictindex']);
    if ( $totalcount > $scount ) {
        include_once ZAR_ROOT_PATH.'/class/pagenav.php';
		$pagenav = new ZariliaPageNav($totalcount, $zariliaOption['storynum'], $start, 'start', 'storytopic='.$zariliaOption['storytopic']);
		if(news_isbot()) { 		// A bot is reading the news, we are going to show it all the links so that he can read everything
        	$zariliaTpl->assign('pagenav', $pagenav->renderNav($totalcount));
        } else {
            $zariliaTpl->assign('pagenav', $pagenav->renderNav());
    	}
    } else {
        $zariliaTpl->assign('pagenav', '');
    }
} else {
    $zariliaOption['template_main'] = 'news_by_topic.html';
    include_once ZAR_ROOT_PATH.'/header.php';
    $zariliaTpl->assign('columnwidth', intval(1/$column_count*100));
	if ($zariliaAddonConfig['ratenews']) {
		$zariliaTpl->assign('rates', true);
		$zariliaTpl->assign('lang_ratingc', _NW_RATINGC);
		$zariliaTpl->assign('lang_ratethisnews', _NW_RATETHISNEWS);
	} else {
		$zariliaTpl->assign('rates', false);
	}

	$xt = new NewsTopic();
    $alltopics = &$xt->getTopicsList(true,$zariliaAddonConfig['restrictindex']);
    $smarty_topics = array();
    $topicstories = array();

    foreach ($alltopics as $topicid => $topic) {
		$allstories = NewsStory::getAllPublished($zariliaAddonConfig['storyhome'], 0, $zariliaAddonConfig['restrictindex'], $topicid);
    	$storieslist=array();
    	foreach ($allstories as $thisstory) {
    		$storieslist[]=$thisstory->storyid();
    	}
		$filesperstory = $sfiles->getCountbyStories($storieslist);
		foreach ($allstories as $thisstory) {
			$filescount = array_key_exists($thisstory->storyid(),$filesperstory) ? $filesperstory[$thisstory->storyid()] : 0;
			$topicstories[$topicid][] = $thisstory->prepare2show($filescount);
		}
		if(isset($topicstories[$topicid])) {
			$smarty_topics[$topicstories[$topicid][0]['posttimestamp']] = array('title' => $topic['title'], 'stories' => $topicstories[$topicid], 'id' => $topicid, 'topic_color'=>$topic['color']);
		}
    }

    krsort($smarty_topics);
    $columns = array();
    $i = 0;
    foreach ($smarty_topics as $thistopictimestamp => $thistopic) {
        $columns[$i][] = $thistopic;
        $i++;
        if ($i == $column_count) {
            $i = 0;
        }
    }
    //$zariliaTpl->assign('topics', $smarty_topics);
    $zariliaTpl->assign('columns', $columns);
}

$zariliaTpl->assign('advertisement', news_getmoduleoption('advertisement'));

/**
 * Create the Meta Datas
 */
news_CreateMetaDatas();


/**
 * Create a clickable path from the root to the current topic (if we are viewing a topic)
 * Actually this is not used in the default templates but you can use it as you want
 * You can comment the code to optimize the requests count
 */
if($zariliaOption['storytopic']) {
	include_once ZAR_ROOT_PATH.'/class/zariliatree.php';
	$mytree = new ZariliaTree($zariliaDB->prefix('topics'),'topic_id','topic_pid');
	$topicpath = $mytree->getNicePathFromId($zariliaOption['storytopic'], 'topic_title', 'index.php?op=1');
	$zariliaTpl->assign('topic_path', $topicpath);
	unset($mytree);
}

/**
 * Create a link for the RSS feed (if the module's option is activated)
 */
if($zariliaAddonConfig['topicsrss'] && $zariliaOption['storytopic']) {
	$link=sprintf("<a href='%s' title='%s'><img src='%s' border=0 alt='%s'></a>",ZAR_URL.'/addons/news/backendt.php?topicid='.$zariliaOption['storytopic'], _NW_RSSFEED, ZAR_URL.'/addons/news/images/rss.gif',_NW_RSSFEED);
	$zariliaTpl->assign('topic_rssfeed_link',$link);
}

/**
 * Assign page's title
 */
if($firsttitle!='') {
	$zariliaTpl->addTitle( $myts->htmlSpecialChars($firsttitle) . ' - ' . $myts->htmlSpecialChars($zariliaAddon->getVar('name')));
} else {
	if($topictitle!='') {
		$zariliaTpl->addTitle( $myts->htmlSpecialChars($topictitle));
	} else {
		$zariliaTpl->addTitle( $myts->htmlSpecialChars($zariliaAddon->getVar('name')));
	}
}

$zariliaTpl->assign('lang_go', _GO);
$zariliaTpl->assign('lang_on', _ON);
$zariliaTpl->assign('lang_printerpage', _NW_PRINTERFRIENDLY);
$zariliaTpl->assign('lang_sendstory', _NW_SENDSTORY);
$zariliaTpl->assign('lang_postedby', _POSTEDBY);
$zariliaTpl->assign('lang_reads', _READS);
$zariliaTpl->assign('lang_morereleases', _NW_MORERELEASES);
include_once ZAR_ROOT_PATH.'/footer.php';
?>