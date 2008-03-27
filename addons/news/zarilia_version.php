<?php
// $Id: zarilia_version.php,v 1.34 2004/09/01 17:48:07 hthouzard Exp $
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
	die('Zarilia root path not defined');
}

$addonversion['name'] = _MI_NEWS_NAME;
$addonversion['version'] = 1.56;
$addonversion['description'] = _MI_NEWS_DESC;
$addonversion['credits'] = "The XOOPS Project, Christian, Pilou, Marco, ALL the members of the Newbb Team, GIJOE, Zoullou, Mithrandir, Setec Astronomy, Marcan, 5vision, Anne";
$addonversion['author'] = "The XOOPS Project Module Dev Team & Instant Zero";
$addonversion['help'] = "";
$addonversion['license'] = "GPL see LICENSE";
$addonversion['official'] = 1;
$addonversion['image'] = "images/news_slogo.png";
$addonversion['dirname'] = "news";

$addonversion['sqlfile']['mysql'] = "sql/mysql.sql";

// Tables created by sql file (without prefix!)
$addonversion['tables'][0] = "stories";
$addonversion['tables'][1] = "topics";
$addonversion['tables'][2] = "stories_files";
$addonversion['tables'][3] = "stories_votedata";

// Admin things
$addonversion['hasAdmin'] = 1;
$addonversion['adminindex'] = "admin/index.php";
$addonversion['adminmenu'] = "admin/menu.php";

// Templates
$addonversion['templates'][1]['file'] = 'news_item.html';
$addonversion['templates'][1]['description'] = '';
$addonversion['templates'][2]['file'] = 'news_archive.html';
$addonversion['templates'][2]['description'] = '';
$addonversion['templates'][3]['file'] = 'news_article.html';
$addonversion['templates'][3]['description'] = '';
$addonversion['templates'][4]['file'] = 'news_index.html';
$addonversion['templates'][4]['description'] = '';
$addonversion['templates'][5]['file'] = 'news_by_topic.html';
$addonversion['templates'][5]['description'] = '';
$addonversion['templates'][6]['file'] = 'news_by_this_author.html';
$addonversion['templates'][6]['description'] = 'Shows a page resuming all the articles of the same author (according to the perms)';
$addonversion['templates'][7]['file'] = 'news_ratenews.html';
$addonversion['templates'][7]['description'] = 'Template used to rate a news';
$addonversion['templates'][8]['file'] = 'news_rss.html';
$addonversion['templates'][8]['description'] = 'Used for RSS per topics';
$addonversion['templates'][9]['file'] = 'news_whos_who.html';
$addonversion['templates'][9]['description'] = "Who's who";
$addonversion['templates'][10]['file'] = 'news_topics_directory.html';
$addonversion['templates'][10]['description'] = "Topics Directory";


// Blocks
$addonversion['blocks'][1]['file'] = "news_topics.php";
$addonversion['blocks'][1]['name'] = _MI_NEWS_BNAME1;
$addonversion['blocks'][1]['description'] = "Shows news topics";
$addonversion['blocks'][1]['show_func'] = "b_news_topics_show";
$addonversion['blocks'][1]['template'] = 'news_block_topics.html';

$addonversion['blocks'][2]['file'] = "news_bigstory.php";
$addonversion['blocks'][2]['name'] = _MI_NEWS_BNAME3;
$addonversion['blocks'][2]['description'] = "Shows most read story of the day";
$addonversion['blocks'][2]['show_func'] = "b_news_bigstory_show";
$addonversion['blocks'][2]['template'] = 'news_block_bigstory.html';

$addonversion['blocks'][3]['file'] = "news_top.php";
$addonversion['blocks'][3]['name'] = _MI_NEWS_BNAME4;
$addonversion['blocks'][3]['description'] = "Shows top read news articles";
$addonversion['blocks'][3]['show_func'] = "b_news_top_show";
$addonversion['blocks'][3]['edit_func'] = "b_news_top_edit";
$addonversion['blocks'][3]['options'] = "counter|10|25|0|0|0|0||1||||||";
$addonversion['blocks'][3]['template'] = 'news_block_top.html';

$addonversion['blocks'][4]['file'] = "news_top.php";
$addonversion['blocks'][4]['name'] = _MI_NEWS_BNAME5;
$addonversion['blocks'][4]['description'] = "Shows recent articles";
$addonversion['blocks'][4]['show_func'] = "b_news_top_show";
$addonversion['blocks'][4]['edit_func'] = "b_news_top_edit";
$addonversion['blocks'][4]['options'] = "published|10|25|0|0|0|0||1||||||";
$addonversion['blocks'][4]['template'] = 'news_block_top.html';

$addonversion['blocks'][5]['file'] = "news_moderate.php";
$addonversion['blocks'][5]['name'] = _MI_NEWS_BNAME6;
$addonversion['blocks'][5]['description'] = "Shows a block to moderate articles";
$addonversion['blocks'][5]['show_func'] = "b_news_topics_moderate";
$addonversion['blocks'][5]['template'] = 'news_block_moderate.html';

$addonversion['blocks'][6]['file'] = "news_topicsnav.php";
$addonversion['blocks'][6]['name'] = _MI_NEWS_BNAME7;
$addonversion['blocks'][6]['description'] = "Shows a block to navigate topics";
$addonversion['blocks'][6]['show_func'] = "b_news_topicsnav_show";
$addonversion['blocks'][6]['template'] = 'news_block_topicnav.html';
$addonversion['blocks'][6]['options'] = "0";
$addonversion['blocks'][6]['edit_func'] = "b_news_topicsnav_edit";

$addonversion['blocks'][7]['file'] = "news_randomnews.php";
$addonversion['blocks'][7]['name'] = _MI_NEWS_BNAME8;
$addonversion['blocks'][7]['description'] = "Shows a block where news appears randomly";
$addonversion['blocks'][7]['show_func'] = "b_news_randomnews_show";
$addonversion['blocks'][7]['template'] = 'news_block_randomnews.html';
$addonversion['blocks'][7]['options'] = "published|10|25|0|0";
$addonversion['blocks'][7]['edit_func'] = "b_news_randomnews_edit";

$addonversion['blocks'][8]['file'] = "news_archives.php";
$addonversion['blocks'][8]['name'] = _MI_NEWS_BNAME9;
$addonversion['blocks'][8]['description'] = "Shows a block where you can see archives";
$addonversion['blocks'][8]['show_func'] = "b_news_archives_show";
$addonversion['blocks'][8]['template'] = 'news_block_archives.html';
$addonversion['blocks'][8]['options'] = "0|0|0|0|1|1";	// Starting date (year, month), ending date (year, month), until today, sort order
$addonversion['blocks'][8]['edit_func'] = "b_news_archives_edit";

// Menu
$addonversion['hasMain'] = 1;

$cansubmit = 0;

/**
 * This part inserts the selected topics as sub items in the Xoops main menu
 */
$module_handler = &zarilia_gethandler('addon');
$module = &$module_handler->getByDirname($addonversion['dirname']);
if ($module) {
    global $zariliaUser;
    if (is_object($zariliaUser)) {
        $groups = $zariliaUser->getGroups();
    } else {
        $groups = ZAR_GROUP_ANONYMOUS;
    }
    $gperm_handler = &zarilia_gethandler('groupperm');
    if ($gperm_handler->checkRight("news_submit", 0, $groups, $module->getVar('mid'))) {
          $cansubmit = 1;
    }
}

// ************
$i = 1;
global $zariliaDB, $zariliaUser, $zariliaConfig, $zariliaAddon, $zariliaAddonConfig;
// We try to "win" some time
// 1)  Check to see it the module is the current module
if (is_object($zariliaAddon) && $zariliaAddon->getVar('dirname') == $addonversion['dirname'] && $zariliaAddon->getVar('isactive')) {
	// 2) If there's no topics to display as sub menus we can go on
	if(!isset($_SESSION['items_count']) || $_SESSION['items_count']== -1) {
		$sql = "SELECT COUNT(*) cpt FROM ".$zariliaDB->prefix("topics")." WHERE menu=1";
		$result = $zariliaDB->Execute($sql);
		$count = $result->FetchRow();
		$_SESSION['items_count'] = floatval($count['cpt']);
	} else {
		$count = $_SESSION['items_count'];
	}
	if($count>0) {
		include_once ZAR_ROOT_PATH.'/class/tree.php';
		include_once ZAR_ROOT_PATH.'/addons/news/class/class.newstopic.php';
		include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';
		$xt = new NewsTopic();
		$allTopics = $xt->getAllTopics(news_getmoduleoption('restrictindex'));
		$topic_tree = new ZariliaObjectTree($allTopics, 'topic_id', 'topic_pid');
		$topics_arr = $topic_tree->getAllChild(0);
		if ($module) {
			foreach ($topics_arr as $onetopic) {
				if ($gperm_handler->checkRight('news_view', $onetopic->topic_id(), $groups, $zariliaAddon->getVar('mid')) && $onetopic->menu()) {
	            	$addonversion['sub'][$i]['name'] = $onetopic->topic_title();
  					$addonversion['sub'][$i]['url'] = "index.php?storytopic=" . $onetopic->topic_id();
   				}
       			$i++;
   			}
		}
		unset($xt);
	}
}

$addonversion['sub'][$i]['name'] = _MI_NEWS_SMNAME2;
$addonversion['sub'][$i]['url'] = "archive.php";
if ($cansubmit) {
	$i++;
    $addonversion['sub'][$i]['name'] = _MI_NEWS_SMNAME1;
    $addonversion['sub'][$i]['url'] = "submit.php";
}
unset($cansubmit);

include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';
if(news_getmoduleoption('newsbythisauthor')) {
	$i++;
	$addonversion['sub'][$i]['name'] = _MI_NEWS_WHOS_WHO;
	$addonversion['sub'][$i]['url'] = "whoswho.php";
}

$i++;
$addonversion['sub'][$i]['name'] = _MI_NEWS_TOPICS_DIRECTORY;
$addonversion['sub'][$i]['url'] = "topics_directory.php";


// Search
$addonversion['hasSearch'] = 1;
$addonversion['search']['file'] = "include/search.inc.php";
$addonversion['search']['func'] = "news_search";

// Comments
$addonversion['hasComments'] = 1;
$addonversion['comments']['pageName'] = 'article.php';
$addonversion['comments']['itemName'] = 'storyid';
// Comment callback functions
$addonversion['comments']['callbackFile'] = 'include/comment_functions.php';
$addonversion['comments']['callback']['approve'] = 'news_com_approve';
$addonversion['comments']['callback']['update'] = 'news_com_update';

/**
 * Select the number of news items to display on top page
 */
$addonversion['config'][1]['name'] = 'storyhome';
$addonversion['config'][1]['title'] = '_MI_STORYHOME';
$addonversion['config'][1]['description'] = '_MI_STORYHOMEDSC';
$addonversion['config'][1]['formtype'] = 'select';
$addonversion['config'][1]['valuetype'] = 'int';
$addonversion['config'][1]['default'] = 5;
$addonversion['config'][1]['options'] = array('5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30);

/**
 * Format of the date to use in the module, if you don't specify anything then the default date's format will be used
 */
$addonversion['config'][2]['name'] = 'dateformat';
$addonversion['config'][2]['title'] = '_MI_NEWS_DATEFORMAT';
$addonversion['config'][2]['description'] = '_MI_NEWS_DATEFORMAT_DESC';
$addonversion['config'][2]['formtype'] = 'textbox';
$addonversion['config'][2]['valuetype'] = 'text';
$addonversion['config'][2]['default'] = "";

/**
 * Display a navigation's box on the pages ?
 * This navigation's box enable you to jump from one topic to another
 */
$addonversion['config'][3]['name'] = 'displaynav';
$addonversion['config'][3]['title'] = '_MI_DISPLAYNAV';
$addonversion['config'][3]['description'] = '_MI_DISPLAYNAVDSC';
$addonversion['config'][3]['formtype'] = 'yesno';
$addonversion['config'][3]['valuetype'] = 'int';
$addonversion['config'][3]['default'] = 1;

/*
$addonversion['config'][4]['name'] = 'anonpost';
$addonversion['config'][4]['title'] = '_MI_ANONPOST';
$addonversion['config'][4]['description'] = '';
$addonversion['config'][4]['formtype'] = 'yesno';
$addonversion['config'][4]['valuetype'] = 'int';
$addonversion['config'][4]['default'] = 0;
*/

/**
 * Auto approuve submited stories
 */
$addonversion['config'][5]['name'] = 'autoapprove';
$addonversion['config'][5]['title'] = '_MI_AUTOAPPROVE';
$addonversion['config'][5]['description'] = '_MI_AUTOAPPROVEDSC';
$addonversion['config'][5]['formtype'] = 'yesno';
$addonversion['config'][5]['valuetype'] = 'int';
$addonversion['config'][5]['default'] = 0;

/**
 * Dispay layout, classic or by topics
 */
$addonversion['config'][6]['name'] = 'newsdisplay';
$addonversion['config'][6]['title'] = '_MI_NEWSDISPLAY';
$addonversion['config'][6]['description'] = '_MI_NEWSDISPLAYDESC';
$addonversion['config'][6]['formtype'] = 'select';
$addonversion['config'][6]['valuetype'] = 'text';
$addonversion['config'][6]['default'] = "Classic";
$addonversion['config'][6]['options'] = array('_MI_NEWSCLASSIC' => 'Classic','_MI_NEWSBYTOPIC' => 'Bytopic');

/**
 * How to display Author's name, username, full name or nothing ?
 */
$addonversion['config'][7]['name'] = 'displayname';
$addonversion['config'][7]['title'] = '_MI_NAMEDISPLAY';
$addonversion['config'][7]['description'] = '_MI_ADISPLAYNAMEDSC';
$addonversion['config'][7]['formtype'] = 'select';
$addonversion['config'][7]['valuetype'] = 'int';
$addonversion['config'][7]['default'] = 1;
$addonversion['config'][7]['options']	= array('_MI_DISPLAYNAME1' => 1, '_MI_DISPLAYNAME2' => 2, '_MI_DISPLAYNAME3' => 3);

/**
 * Number of columns to use to display news
 */
$addonversion['config'][8]['name'] = 'columnmode';
$addonversion['config'][8]['title'] = '_MI_COLUMNMODE';
$addonversion['config'][8]['description'] = '_MI_COLUMNMODE_DESC';
$addonversion['config'][8]['formtype'] = 'select';
$addonversion['config'][8]['valuetype'] = 'int';
$addonversion['config'][8]['default'] = 1;
$addonversion['config'][8]['options'] = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5);

/**
 * Number of news and topics to display in the module's admin part
 */
$addonversion['config'][9]['name'] = 'storycountadmin';
$addonversion['config'][9]['title'] = '_MI_STORYCOUNTADMIN';
$addonversion['config'][9]['description'] = '_MI_STORYCOUNTADMIN_DESC';
$addonversion['config'][9]['formtype'] = 'select';
$addonversion['config'][9]['valuetype'] = 'int';
$addonversion['config'][9]['default'] = 10;
$addonversion['config'][9]['options'] = array('5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30, '35' => 35, '40' => 40);

/**
 * Authorized groups to upload
 */
$addonversion['config'][10]['name'] = 'uploadgroups';
$addonversion['config'][10]['title'] = '_MI_UPLOADGROUPS';
$addonversion['config'][10]['description'] = '_MI_UPLOADGROUPS_DESC';
$addonversion['config'][10]['formtype'] = 'select';
$addonversion['config'][10]['valuetype'] = 'int';
$addonversion['config'][10]['default'] = 2;
$addonversion['config'][10]['options'] = array('_MI_UPLOAD_GROUP1' => 1, '_MI_UPLOAD_GROUP2' => 2, '_MI_UPLOAD_GROUP3' => 3);

/**
 * MAX Filesize Upload in kilo bytes
 */
$addonversion['config'][11]['name'] = 'maxuploadsize';
$addonversion['config'][11]['title'] = '_MI_UPLOADFILESIZE';
$addonversion['config'][11]['description'] = '_MI_UPLOADFILESIZE_DESC';
$addonversion['config'][11]['formtype'] = 'textbox';
$addonversion['config'][11]['valuetype'] = 'int';
$addonversion['config'][11]['default'] = 1048576;

/**
 * Restrict Topics on Index Page
 *
 * This is one of the mot important option in the module.
 * If you set it to No, then the users can see the introduction's text of each
 * story even if they don't have the right to see the topic attached to the news.
 * If you set it to Yes then you can only see what you have the right to see.
 * Many of the permissions are based on this option.
 */
$addonversion['config'][12]['name'] = 'restrictindex';
$addonversion['config'][12]['title'] = '_MI_RESTRICTINDEX';
$addonversion['config'][12]['description'] = '_MI_RESTRICTINDEXDSC';
$addonversion['config'][12]['formtype'] = 'yesno';
$addonversion['config'][12]['valuetype'] = 'int';
$addonversion['config'][12]['default'] = 0;

/**
 * Do you want to enable your visitors to see all the other articles
 * created by the author they are currently reading ?
 */
$addonversion['config'][13]['name'] = 'newsbythisauthor';
$addonversion['config'][13]['title'] = '_MI_NEWSBYTHISAUTHOR';
$addonversion['config'][13]['description'] = '_MI_NEWSBYTHISAUTHORDSC';
$addonversion['config'][13]['formtype'] = 'yesno';
$addonversion['config'][13]['valuetype'] = 'int';
$addonversion['config'][13]['default'] = 0;

/**
 * If you set this option to yes then you will see two links at the bottom
 * of each article. The first link will enable you to go to the previous
 * article and the other link will bring you to the next article
 */
$addonversion['config'][14]['name'] = 'showprevnextlink';
$addonversion['config'][14]['title'] = '_MI_NEWS_PREVNEX_LINK';
$addonversion['config'][14]['description'] = '_MI_NEWS_PREVNEX_LINK_DESC';
$addonversion['config'][14]['formtype'] = 'yesno';
$addonversion['config'][14]['valuetype'] = 'int';
$addonversion['config'][14]['default'] = 0;

/**
 * Do you want to see a summary table at the bottom of each article ?
 */
$addonversion['config'][15]['name'] = 'showsummarytable';
$addonversion['config'][15]['title'] = '_MI_NEWS_SUMMARY_SHOW';
$addonversion['config'][15]['description'] = '_MI_NEWS_SUMMARY_SHOW_DESC';
$addonversion['config'][15]['formtype'] = 'yesno';
$addonversion['config'][15]['valuetype'] = 'int';
$addonversion['config'][15]['default'] = 0;

/**
 * Do you enable author's to edit their posts ?
 */
$addonversion['config'][16]['name'] = 'authoredit';
$addonversion['config'][16]['title'] = '_MI_NEWS_AUTHOR_EDIT';
$addonversion['config'][16]['description'] = '_MI_NEWS_AUTHOR_EDIT_DESC';
$addonversion['config'][16]['formtype'] = 'yesno';
$addonversion['config'][16]['valuetype'] = 'int';
$addonversion['config'][16]['default'] = 1;

/**
 * Do you want to enable your visitors to rate news ?
 */
$addonversion['config'][17]['name'] = 'ratenews';
$addonversion['config'][17]['title'] = "_MI_NEWS_RATE_NEWS";
$addonversion['config'][17]['description'] = "";
$addonversion['config'][17]['formtype'] = 'yesno';
$addonversion['config'][17]['valuetype'] = 'int';
$addonversion['config'][17]['default'] = 0;

/**
 * You can set RSS feeds per topic
 */
$addonversion['config'][18]['name'] = 'topicsrss';
$addonversion['config'][18]['title'] = "_MI_NEWS_TOPICS_RSS";
$addonversion['config'][18]['description'] = "_MI_NEWS_TOPICS_RSS_DESC";
$addonversion['config'][18]['formtype'] = 'yesno';
$addonversion['config'][18]['valuetype'] = 'int';
$addonversion['config'][18]['default'] = 0;

/**
 * If you set this option to yes then the approvers can type the keyword
 * and description's meta datas
 */
$addonversion['config'][19]['name'] = 'metadata';
$addonversion['config'][19]['title'] = "_MI_NEWS_META_DATA";
$addonversion['config'][19]['description'] = "_MI_NEWS_META_DATA_DESC";
$addonversion['config'][19]['formtype'] = 'yesno';
$addonversion['config'][19]['valuetype'] = 'int';
$addonversion['config'][19]['default'] = 0;

/**
 * Editor to use
 */
$addonversion['config'][20]['name'] = 'form_options';
$addonversion['config'][20]['title'] = "_MI_NEWS_FORM_OPTIONS";
$addonversion['config'][20]['description'] = '_MI_NEWS_FORM_OPTIONS_DESC';
$addonversion['config'][20]['formtype'] = 'select';
$addonversion['config'][20]['valuetype'] = 'text';
$addonversion['config'][20]['options'] = array(
											_MI_NEWS_FORM_DHTML=>'dhtml',
											_MI_NEWS_FORM_COMPACT=>'textarea',
											_MI_NEWS_FORM_SPAW=>'spaw',
											_MI_NEWS_FORM_HTMLAREA=>'htmlarea',
											_MI_NEWS_FORM_KOIVI=>'koivi',
											_MI_NEWS_FORM_FCK=>'fck',
											_MI_NEWS_FORM_TINYEDITOR=>'tinyeditor'
											);
$addonversion['config'][20]['default'] = 'dhtml';

/**
 * If you set this option to Yes then the keywords entered in the
 * search will be highlighted in the articles.
 */
$addonversion['config'][21]['name'] = 'keywordshighlight';
$addonversion['config'][21]['title'] = "_MI_NEWS_KEYWORDS_HIGH";
$addonversion['config'][21]['description'] = "_MI_NEWS_KEYWORDS_HIGH_DESC";
$addonversion['config'][21]['formtype'] = 'yesno';
$addonversion['config'][21]['valuetype'] = 'int';
$addonversion['config'][21]['default'] = 0;

/**
 * If you have enabled the previous option then with this one
 * you can select the color to use to highlight words
 */
$addonversion['config'][22]['name'] = 'highlightcolor';
$addonversion['config'][22]['title'] = '_MI_NEWS_HIGH_COLOR';
$addonversion['config'][22]['description'] = '_MI_NEWS_HIGH_COLOR_DES';
$addonversion['config'][22]['formtype'] = 'textbox';
$addonversion['config'][22]['valuetype'] = 'text';
$addonversion['config'][22]['default'] = "#FFFF80";

/**
 * Tooltips, or infotips are some small textes you can see when you
 * move your mouse over an article's title. This text contains the
 * first (x) characters of the story
 */
$addonversion['config'][23]['name'] = 'infotips';
$addonversion['config'][23]['title'] = '_MI_NEWS_INFOTIPS';
$addonversion['config'][23]['description'] = '_MI_NEWS_INFOTIPS_DES';
$addonversion['config'][23]['formtype'] = 'textbox';
$addonversion['config'][23]['valuetype'] = 'int';
$addonversion['config'][23]['default'] = "0";

/**
 * This option is specific to Mozilla/Firefox and Opera
 * Both of them can display a toolbar wich contains buttons to
 * go from article to article. It can show other information too
 */
$addonversion['config'][24]['name'] = 'sitenavbar';
$addonversion['config'][24]['title'] = "_MI_NEWS_SITE_NAVBAR";
$addonversion['config'][24]['description'] = "_MI_NEWS_SITE_NAVBAR_DESC";
$addonversion['config'][24]['formtype'] = 'yesno';
$addonversion['config'][24]['valuetype'] = 'int';
$addonversion['config'][24]['default'] = 0;

/**
 * With this option you can select the skin (apparence) to use for the blocks containing tabs
 */
$addonversion['config'][25]['name'] = 'tabskin';
$addonversion['config'][25]['title'] = "_MI_NEWS_TABS_SKIN";
$addonversion['config'][25]['description'] = "_MI_NEWS_TABS_SKIN_DESC";
$addonversion['config'][25]['formtype'] = 'select';
$addonversion['config'][25]['valuetype'] = 'int';
$addonversion['config'][25]['options'] = array(
											_MI_NEWS_SKIN_1=>1,
											_MI_NEWS_SKIN_2=>2,
											_MI_NEWS_SKIN_3=>3,
											_MI_NEWS_SKIN_4=>4,
											_MI_NEWS_SKIN_5=>5,
											_MI_NEWS_SKIN_6=>6,
											_MI_NEWS_SKIN_7=>7,
											_MI_NEWS_SKIN_8=>8
											);
$addonversion['config'][25]['default'] = 6;


/**
 * Display a navigation's box on the pages ?
 * This navigation's box enable you to jump from one topic to another
 */
$addonversion['config'][26]['name'] = 'footNoteLinks';
$addonversion['config'][26]['title'] = '_MI_NEWS_FOOTNOTES';
$addonversion['config'][26]['description'] = '';
$addonversion['config'][26]['formtype'] = 'yesno';
$addonversion['config'][26]['valuetype'] = 'int';
$addonversion['config'][26]['default'] = 1;


/**
 * Activate Dublin Core Metadata ?
 */
$addonversion['config'][27]['name'] = 'dublincore';
$addonversion['config'][27]['title'] = '_MI_NEWS_DUBLINCORE';
$addonversion['config'][27]['description'] = '_MI_NEWS_DUBLINCORE_DSC';
$addonversion['config'][27]['formtype'] = 'yesno';
$addonversion['config'][27]['valuetype'] = 'int';
$addonversion['config'][27]['default'] = 0;


/**
 * Display a "Bookmark this article at these sites" block ?
 */
$addonversion['config'][28]['name'] = 'bookmarkme';
$addonversion['config'][28]['title'] = '_MI_NEWS_BOOKMARK_ME';
$addonversion['config'][28]['description'] = '_MI_NEWS_BOOKMARK_ME_DSC';
$addonversion['config'][28]['formtype'] = 'yesno';
$addonversion['config'][28]['valuetype'] = 'int';
$addonversion['config'][28]['default'] = 0;

/**
 * Activate Firefox 2 microformats ?
 */
$addonversion['config'][29]['name'] = 'firefox_microsummaries';
$addonversion['config'][29]['title'] = '_MI_NEWS_FF_MICROFORMAT';
$addonversion['config'][29]['description'] = '_MI_NEWS_FF_MICROFORMAT_DSC';
$addonversion['config'][29]['formtype'] = 'yesno';
$addonversion['config'][29]['valuetype'] = 'int';
$addonversion['config'][29]['default'] = 0;

/**
 * Advertisement
 */
$addonversion['config'][30]['name'] = 'advertisement';
$addonversion['config'][30]['title'] = '_MI_NEWS_ADVERTISEMENT';
$addonversion['config'][30]['description'] = '_MI_NEWS_ADV_DESCR';
$addonversion['config'][30]['formtype'] = 'textarea';
$addonversion['config'][30]['valuetype'] = 'text';
$addonversion['config'][30]['default'] = '';

/**
 * Mime Types
 *
 * Default values : Web pictures (png, gif, jpeg), zip, pdf, gtar, tar, pdf
 */
$addonversion['config'][31]['name'] = 'mimetypes';
$addonversion['config'][31]['title'] = '_MI_NEWS_MIME_TYPES';
$addonversion['config'][31]['description'] = '';
$addonversion['config'][31]['formtype'] = 'textarea';
$addonversion['config'][31]['valuetype'] = 'text';
$addonversion['config'][31]['default'] = "image/gif\nimage/jpeg\nimage/pjpeg\nimage/x-png\nimage/png\napplication/x-zip-compressed\napplication/zip\napplication/pdf\napplication/x-gtar\napplication/x-tar";

/**
 * Use enhanced page separator ?
 */
$addonversion['config'][32]['name'] = 'enhanced_pagenav';
$addonversion['config'][32]['title'] = '_MI_NEWS_ENHANCED_PAGENAV';
$addonversion['config'][32]['description'] = '_MI_NEWS_ENHANCED_PAGENAV_DSC';
$addonversion['config'][32]['formtype'] = 'yesno';
$addonversion['config'][32]['valuetype'] = 'int';
$addonversion['config'][32]['default'] = 0;


// Notification
$addonversion['hasNotification'] = 1;
$addonversion['notification']['lookup_file'] = 'include/notification.inc.php';
$addonversion['notification']['lookup_func'] = 'news_notify_iteminfo';

$addonversion['notification']['category'][1]['name'] = 'global';
$addonversion['notification']['category'][1]['title'] = _MI_NEWS_GLOBAL_NOTIFY;
$addonversion['notification']['category'][1]['description'] = _MI_NEWS_GLOBAL_NOTIFYDSC;
$addonversion['notification']['category'][1]['subscribe_from'] = array('index.php', 'article.php');

$addonversion['notification']['category'][2]['name'] = 'story';
$addonversion['notification']['category'][2]['title'] = _MI_NEWS_STORY_NOTIFY;
$addonversion['notification']['category'][2]['description'] = _MI_NEWS_STORY_NOTIFYDSC;
$addonversion['notification']['category'][2]['subscribe_from'] = array('article.php');
$addonversion['notification']['category'][2]['item_name'] = 'storyid';
$addonversion['notification']['category'][2]['allow_bookmark'] = 1;

// Added by Lankford on 2007/3/23
$addonversion['notification']['category'][3]['name'] = 'category';
$addonversion['notification']['category'][3]['title'] = _MI_NEWS_CATEGORY_NOTIFY;
$addonversion['notification']['category'][3]['description'] = _MI_NEWS_CATEGORY_NOTIFYDSC;
$addonversion['notification']['category'][3]['subscribe_from'] = array('index.php', 'article.php');
$addonversion['notification']['category'][3]['item_name'] = 'storytopic';
$addonversion['notification']['category'][3]['allow_bookmark'] = 1;

$addonversion['notification']['event'][1]['name'] = 'new_category';
$addonversion['notification']['event'][1]['category'] = 'global';
$addonversion['notification']['event'][1]['title'] = _MI_NEWS_GLOBAL_NEWCATEGORY_NOTIFY;
$addonversion['notification']['event'][1]['caption'] = _MI_NEWS_GLOBAL_NEWCATEGORY_NOTIFYCAP;
$addonversion['notification']['event'][1]['description'] = _MI_NEWS_GLOBAL_NEWCATEGORY_NOTIFYDSC;
$addonversion['notification']['event'][1]['mail_template'] = 'global_newcategory_notify';
$addonversion['notification']['event'][1]['mail_subject'] = _MI_NEWS_GLOBAL_NEWCATEGORY_NOTIFYSBJ;

$addonversion['notification']['event'][2]['name'] = 'story_submit';
$addonversion['notification']['event'][2]['category'] = 'global';
$addonversion['notification']['event'][2]['admin_only'] = 1;
$addonversion['notification']['event'][2]['title'] = _MI_NEWS_GLOBAL_STORYSUBMIT_NOTIFY;
$addonversion['notification']['event'][2]['caption'] = _MI_NEWS_GLOBAL_STORYSUBMIT_NOTIFYCAP;
$addonversion['notification']['event'][2]['description'] = _MI_NEWS_GLOBAL_STORYSUBMIT_NOTIFYDSC;
$addonversion['notification']['event'][2]['mail_template'] = 'global_storysubmit_notify';
$addonversion['notification']['event'][2]['mail_subject'] = _MI_NEWS_GLOBAL_STORYSUBMIT_NOTIFYSBJ;

$addonversion['notification']['event'][3]['name'] = 'new_story';
$addonversion['notification']['event'][3]['category'] = 'global';
$addonversion['notification']['event'][3]['title'] = _MI_NEWS_GLOBAL_NEWSTORY_NOTIFY;
$addonversion['notification']['event'][3]['caption'] = _MI_NEWS_GLOBAL_NEWSTORY_NOTIFYCAP;
$addonversion['notification']['event'][3]['description'] = _MI_NEWS_GLOBAL_NEWSTORY_NOTIFYDSC;
$addonversion['notification']['event'][3]['mail_template'] = 'global_newstory_notify';
$addonversion['notification']['event'][3]['mail_subject'] = _MI_NEWS_GLOBAL_NEWSTORY_NOTIFYSBJ;

$addonversion['notification']['event'][4]['name'] = 'approve';
$addonversion['notification']['event'][4]['category'] = 'story';
$addonversion['notification']['event'][4]['invisible'] = 1;
$addonversion['notification']['event'][4]['title'] = _MI_NEWS_STORY_APPROVE_NOTIFY;
$addonversion['notification']['event'][4]['caption'] = _MI_NEWS_STORY_APPROVE_NOTIFYCAP;
$addonversion['notification']['event'][4]['description'] = _MI_NEWS_STORY_APPROVE_NOTIFYDSC;
$addonversion['notification']['event'][4]['mail_template'] = 'story_approve_notify';
$addonversion['notification']['event'][4]['mail_subject'] = _MI_NEWS_STORY_APPROVE_NOTIFYSBJ;

// Added by Lankford on 2007/3/23
$addonversion['notification']['event'][5]['name'] = 'new_story';
$addonversion['notification']['event'][5]['category'] = 'category';
$addonversion['notification']['event'][5]['title'] = _MI_NEWS_CATEGORY_STORYPOSTED_NOTIFY;
$addonversion['notification']['event'][5]['caption'] = _MI_NEWS_CATEGORY_STORYPOSTED_NOTIFYCAP;
$addonversion['notification']['event'][5]['description'] = _MI_NEWS_CATEGORY_STORYPOSTED_NOTIFYDSC;
$addonversion['notification']['event'][5]['mail_template'] = 'category_newstory_notify';
$addonversion['notification']['event'][5]['mail_subject'] = _MI_NEWS_CATEGORY_STORYPOSTED_NOTIFYSBJ;
?>