<?php
// $Id: index.php,v 1.25 2004/09/02 17:04:07 hthouzard Exp $
// ------------------------------------------------------------------------ //
// XOOPS - PHP Content Management System  				                    //
// Copyright (c) 2000 XOOPS.org                         					//
// <http://www.xoops.org/>                             						//
// ------------------------------------------------------------------------ //
// This program is free software; you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License, or        //
// (at your option) any later version.                                      //
// 																			//
// You may not change or alter any portion of this comment or credits       //
// of supporting developers from this source code or any supporting         //
// source code which is considered copyrighted (c) material of the          //
// original comment or credit authors.                                      //
// 																			//
// This program is distributed in the hope that it will be useful,          //
// but WITHOUT ANY WARRANTY; without even the implied warranty of           //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
// GNU General Public License for more details.                             //
// 																			//
// You should have received a copy of the GNU General Public License        //
// along with this program; if not, write to the Free Software              //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------ //
include_once '../../../include/cp_header.php';
include_once ZAR_ROOT_PATH.'/class/zariliatopic.php';
include_once ZAR_ROOT_PATH.'/class/zarilialists.php';
include_once ZAR_ROOT_PATH.'/addons/news/config.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.newsstory.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.newstopic.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.sfiles.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/blacklist.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/registryfile.php';
include_once ZAR_ROOT_PATH.'/class/uploader.php';
include_once ZAR_ROOT_PATH.'/class/pagenav.php';
include_once ZAR_ROOT_PATH.'/addons/news/admin/functions.php';
include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';
include_once ZAR_ROOT_PATH.'/class/tree.php';
$dateformat=news_getmoduleoption('dateformat');
$myts = &MyTextSanitizer::getInstance();
$topicscount=0;

/**
 * Show new submissions
 *
 * This list can be view in the module's admin when you click on the tab named "Post/Edit News"
 * Submissions are news that was submit by users but who are not approved, so you need to edit
 * them to approve them.
 * Actually you can see the the story's title, the topic, the posted date, the author and a
 * link to delete the story. If you click on the story's title, you will be able to edit the news.
 * The table contains the last x new submissions.
 * The system's block called "Waiting Contents" is listing the number of those news.
 */
function newSubmissions()
{
    global $dateformat;
    $start = isset($_GET['startnew']) ? intval($_GET['startnew']) : 0;
    $newsubcount = NewsStory :: getAllStoriesCount(3,false);
    $storyarray = NewsStory :: getAllSubmitted(news_getmoduleoption('storycountadmin'),true,news_getmoduleoption('restrictindex'),$start);
    if ( count($storyarray)> 0) {
    	$pagenav = new ZariliaPageNav( $newsubcount, news_getmoduleoption('storycountadmin'), $start, 'startnew', 'op=newarticle');
		news_collapsableBar('newsub', 'topnewsubicon');
		echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='topnewsubicon' name='topnewsubicon' src='" . ZAR_URL . "/addons/news/images/close12.gif' alt='' /></a>&nbsp;"._AM_NEWSUB."</h4>";
		echo "<div id='newsub'>";
		echo '<br />';
        echo "<div style='text-align: center;'><table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'><tr class='bg3'><td align='center'>" . _AM_TITLE . "</td><td align='center'>" . _AM_TOPIC . "</td><td align='center'>" . _AM_POSTED . "</td><td align='center'>" . _AM_POSTER . "</td><td align='center'>" . _AM_ACTION . "</td></tr>\n";
        $class='';
        foreach( $storyarray as $newstory ) {
            $class = ($class == 'even') ? 'odd' : 'even';
            echo "<tr class='".$class."'><td align='left'>\n";
            $title = $newstory->title();
            if (!isset($title) || ($title == '' )) {
                echo "<a href='".ZAR_URL."/addons/news/admin/index.php?op=edit&amp;returnside=1&amp;storyid=" . $newstory -> storyid() . "'>" . _AD_NOSUBJECT . "</a>\n";
            } else {
                echo "&nbsp;<a href='".ZAR_URL."/addons/news/submit.php?returnside=1&amp;op=edit&amp;storyid=" . $newstory -> storyid() . "'>" . $title . "</a>\n";
            }
            echo "</td><td>" . $newstory->topic_title() . "</td><td align='center' class='nw'>" . formatTimestamp($newstory->created(),$dateformat) . "</td><td align='center'><a href='" . ZAR_URL . "/index.php?page_type=userinfo&uid=" . $newstory->uid() . "'>" . $newstory->uname() . "</a></td><td align='right'><a href='".ZAR_URL."/addons/news/admin/index.php?op=delete&amp;storyid=" . $newstory->storyid() . "'>" . _AM_DELETE . "</a></td></tr>\n";
        }
        echo '</table></div>';
        echo "<div align='right'>".$pagenav->renderNav().'</div><br />';
        echo '<br /></div><br />';
    }
}

/**
 * Shows all automated stories
 *
 * Automated stories are stories that have a publication's date greater than "now"
 * This list can be view in the module's admin when you click on the tab named "Post/Edit News"
 * Actually you can see the story's ID, its title, the topic, the author, the
 * programmed date and time, the expiration's date  and two links. The first link is
 * used to edit the story while the second is used to remove the story.
 * The list only contains the last (x) automated news
 */
function autoStories()
{
    global $dateformat;
    $start = isset($_GET['startauto']) ? intval($_GET['startauto']) : 0;
    $storiescount = NewsStory :: getAllStoriesCount(2,false);
    $storyarray = NewsStory :: getAllAutoStory(news_getmoduleoption('storycountadmin'),true,$start);
    $class='';
    if(count($storyarray) > 0) {
    	$pagenav = new ZariliaPageNav($storiescount, news_getmoduleoption('storycountadmin'), $start, 'startauto', 'op=newarticle');
		news_collapsableBar('autostories', 'topautostories');
		echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='topautostories' name='topautostories' src='" . ZAR_URL . "/addons/news/images/close12.gif' alt='' /></a>&nbsp;"._AM_AUTOARTICLES."</h4>";
		echo "<div id='autostories'>";
		echo '<br />';
        echo "<div style='text-align: center;'>\n";
        echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'><tr class='bg3'><td align='center'>" . _AM_STORYID . "</td><td align='center'>" . _AM_TITLE . "</td><td align='center'>" . _AM_TOPIC . "</td><td align='center'>" . _AM_POSTER . "</td><td align='center' class='nw'>" . _AM_PROGRAMMED . "</td><td align='center' class='nw'>" . _AM_EXPIRED . "</td><td align='center'>" . _AM_ACTION . "</td></tr>";
        foreach($storyarray as $autostory) {
            $topic = $autostory -> topic();
            $expire = ( $autostory->expired() > 0 ) ? formatTimestamp($autostory->expired(),$dateformat) : '';
            $class = ($class == 'even') ? 'odd' : 'even';
            echo "<tr class='".$class."'>";
        	echo "<td align='center'><b>" . $autostory -> storyid() . "</b>
        		</td><td align='left'><a href='" . ZAR_URL . "/addons/news/article.php?storyid=" . $autostory->storyid() . "'>" . $autostory->title() . "</a>
        		</td><td align='center'>" . $topic->topic_title() . "
        		</td><td align='center'><a href='" . ZAR_URL . "/index.php?page_type=userinfo&uid=" . $autostory->uid() . "'>" . $autostory->uname() . "</a></td><td align='center' class='nw'>" . formatTimestamp($autostory->published(),$dateformat) . "</td><td align='center'>" . $expire . "</td><td align='center'><a href='".ZAR_URL."/addons/news/submit.php?returnside=1&amp;op=edit&amp;storyid=" . $autostory->storyid() . "'>" . _AM_EDIT . "</a>-<a href='".ZAR_URL."/addons/news/admin/index.php?op=delete&amp;storyid=" . $autostory->storyid() . "'>" . _AM_DELETE . "</a>";
            echo "</td></tr>\n";
        }
        echo '</table></div>';
        echo "<div align='right'>".$pagenav->renderNav().'</div><br />';
        echo '</div><br />';
    }
}

/**
 * Shows last x published stories
 *
 * This list can be view in the module's admin when you click on the tab named "Post/Edit News"
 * Actually you can see the the story's ID, its title, the topic, the author, the number of hits
 * and two links. The first link is used to edit the story while the second is used to remove the story.
 * The table only contains the last X published stories.
 * You can modify the number of visible stories with the module's option named
 * "Number of new articles to display in admin area".
 * As the number of displayed stories is limited, below this list you can find a text box
 * that you can use to enter a story's Id, then with the scrolling list you can select
 * if you want to edit or delete the story.
 */
function lastStories()
{
    global $dateformat;
	news_collapsableBar('laststories', 'toplaststories');
	echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='toplaststories' name='toplaststories' src='" . ZAR_URL . "/addons/news/images/close12.gif' alt='' /></a>&nbsp;".sprintf(_AM_LAST10ARTS,news_getmoduleoption('storycountadmin'))."</h4>";
	echo "<div id='laststories'>";
	echo '<br />';
    echo "<div style='text-align: center;'>";
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    $storyarray = NewsStory :: getAllPublished(news_getmoduleoption('storycountadmin'), $start, false, 0, 1 );
    $storiescount = NewsStory :: getAllStoriesCount(4,false);
    $pagenav = new ZariliaPageNav( $storiescount, news_getmoduleoption('storycountadmin'), $start, 'start', 'op=newarticle');
    $class='';
    echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'><tr class='bg3'><td align='center'>" . _AM_STORYID . "</td><td align='center'>" . _AM_TITLE . "</td><td align='center'>" . _AM_TOPIC . "</td><td align='center'>" . _AM_POSTER . "</td><td align='center' class='nw'>" . _AM_PUBLISHED . "</td><td align='center' class='nw'>" . _AM_HITS . "</td><td align='center'>" . _AM_ACTION . "</td></tr>";
    foreach( $storyarray as $eachstory ) {
        $published = formatTimestamp($eachstory->published(),$dateformat );
        // $expired = ( $eachstory -> expired() > 0 ) ? formatTimestamp($eachstory->expired(),$dateformat) : '---';
        $topic = $eachstory -> topic();
        $class = ($class == 'even') ? 'odd' : 'even';
        echo "<tr class='".$class."'>";
        echo "<td align='center'><b>" . $eachstory -> storyid() . "</b>
        	</td><td align='left'><a href='" . ZAR_URL . "/addons/news/article.php?storyid=" . $eachstory -> storyid() . "'>" . $eachstory -> title() . "</a>
        	</td><td align='center'>" . $topic -> topic_title() . "
        	</td><td align='center'><a href='" . ZAR_URL . "/index.php?page_type=userinfo&uid=" . $eachstory -> uid() . "'>" . $eachstory -> uname() . "</a></td><td align='center' class='nw'>" . $published . "</td><td align='center'>" . $eachstory -> counter() . "</td><td align='center'><a href='".ZAR_URL."/addons/news/submit.php?returnside=1&amp;op=edit&amp;storyid=" . $eachstory -> storyid() . "'>" . _AM_EDIT . "</a>-<a href='".ZAR_URL."/addons/news/admin/index.php?op=delete&amp;storyid=" . $eachstory -> storyid() . "'>" . _AM_DELETE . "</a>";
        echo "</td></tr>\n";
    }
    echo '</table><br />';
	echo "<div align='right'>".$pagenav->renderNav().'</div><br />';

    echo "<form action='index.php' method='get'>" . _AM_STORYID . " <input type='text' name='storyid' size='10' />
    	<select name='op'>
    		<option value='edit' selected='selected'>" . _AM_EDIT . "</option>
    		<option value='delete'>" . _AM_DELETE . "</option>
    	</select>
		<input type='hidden' name='returnside' value='1'>
    	<input type='submit' value='" . _AM_GO . "' />
    	</form>
	</div>";
    echo '</div><br />';
}


/**
 * Display a list of the expired stories
 *
 * This list can be view in the module's admin when you click on the tab named "Post/Edit News"
 * Actually you can see the story's ID, the title, the topic, the author,
 * the creation and expiration's date and you have two links, one to delete
 * the story and the other to edit the story.
 * The table only contains the last X expired stories.
 * You can modify the number of visible stories with the module's option named
 * "Number of new articles to display in admin area".
 * As the number of displayed stories is limited, below this list you can find a text box
 * that you can use to enter a story's Id, then with the scrolling list you can select
 * if you want to edit or delete the story.
 */
function expStories()
{
    global $dateformat;
    $start = isset($_GET['startexp']) ? intval($_GET['startexp']) : 0;
	$expiredcount = NewsStory :: getAllStoriesCount(1,false);
	$storyarray = NewsStory :: getAllExpired(news_getmoduleoption('storycountadmin'), $start, 0, 1 );
	$pagenav = new ZariliaPageNav( $expiredcount, news_getmoduleoption('storycountadmin'), $start, 'startexp', 'op=newarticle');

    if(count($storyarray) > 0) {
    	$class='';
		news_collapsableBar('expstories', 'topexpstories');
		echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='topexpstories' name='topexpstories' src='" . ZAR_URL . "/addons/news/images/close12.gif' alt='' /></a>&nbsp;"._AM_EXPARTS."</h4>";
		echo "<div id='expstories'>";
		echo '<br />';
    	echo "<div style='text-align: center;'>";
    	echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'><tr class='bg3'><td align='center'>" . _AM_STORYID . "</td><td align='center'>" . _AM_TITLE . "</td><td align='center'>" . _AM_TOPIC . "</td><td align='center'>" . _AM_POSTER . "</td><td align='center' class='nw'>" . _AM_CREATED . "</td><td align='center' class='nw'>" . _AM_EXPIRED . "</td><td align='center'>" . _AM_ACTION . "</td></tr>";
    	foreach( $storyarray as $eachstory ) {
	        $created = formatTimestamp($eachstory->created(),$dateformat);
        	$expired = formatTimestamp($eachstory->expired(),$dateformat);
        	$topic = $eachstory -> topic();
        	// added exired value field to table
        	$class = ($class == 'even') ? 'odd' : 'even';
        	echo "<tr class='".$class."'>";
        	echo "<td align='center'><b>" . $eachstory -> storyid() . "</b>
	        	</td><td align='left'><a href='" . ZAR_URL . "/addons/news/article.php?returnside=1&amp;storyid=" . $eachstory -> storyid() . "'>" . $eachstory -> title() . "</a>
        		</td><td align='center'>" . $topic -> topic_title() . "
        		</td><td align='center'><a href='" . ZAR_URL . "/index.php?page_type=userinfo&uid=" . $eachstory -> uid() . "'>" . $eachstory -> uname() . "</a></td><td align='center' class='nw'>" . $created . "</td><td align='center' class='nw'>" . $expired . "</td><td align='center'><a href='".ZAR_URL."/addons/news/submit.php?returnside=1&amp;op=edit&amp;storyid=" . $eachstory -> storyid() . "'>" . _AM_EDIT . "</a>-<a href='".ZAR_URL."/addons/news/admin/index.php?op=delete&amp;storyid=" . $eachstory -> storyid() . "'>" . _AM_DELETE . "</a>";
        	echo "</td></tr>\n";
    	}
    	echo '</table><br />';
    	echo "<div align='right'>".$pagenav->renderNav().'</div><br />';
    	echo "<form action='index.php' method='get'>
	    	" . _AM_STORYID . " <input type='text' name='storyid' size='10' />
    		<select name='op'>
	    		<option value='edit' selected='selected'>" . _AM_EDIT . "</option>
    			<option value='delete'>" . _AM_DELETE . "</option>
    		</select>
			<input type='hidden' name='returnside' value='1'>
    		<input type='submit' value='" . _AM_GO . "' />
    		</form>
		</div>";
    	echo '</div><br />';
    }
}

/**
 * Delete (purge/prune) old stories
 *
 * You can use this function in the module's admin when you click on the tab named "Prune News"
 * It's useful to remove old stories. It is, of course, recommended
 * to backup (or export) your news before to purge news.
 * You must first specify a date. This date will be used as a reference, everything
 * that was published before this date will be deleted.
 * The option "Only remove stories who have expired" will enable you to only remove
 * expired stories published before the given date.
 * Finally, you can select the topics inside wich you will remove news.
 * Once you have set all the parameters, the script will first show you a confirmation's
 * message with the number of news that will be removed.
 * Note, the topics are not deleted (even if there are no more news inside them).
 */
function PruneManager()
{
    include_once ZAR_ROOT_PATH.'/class/zariliaformloader.php';
    zarilia_cp_header();
    adminmenu(3);
    echo '<br /><br /><br />';
	$sform = new ZariliaThemeForm(_AM_NEWS_PRUNENEWS, 'pruneform', ZAR_URL.'/addons/news/admin/index.php', 'post');
	$sform->addElement(new ZariliaFormTextDateSelect(_AM_NEWS_PRUNE_BEFORE, 'prune_date',15,time()), true);
	$onlyexpired=new ZariliaFormCheckBox('', 'onlyexpired');
	$onlyexpired->addOption(1, _AM_NEWS_PRUNE_EXPIREDONLY);
	$sform->addElement($onlyexpired, false);
	$sform->addElement(new ZariliaFormHidden('op', 'confirmbeforetoprune'), false);
	$topiclist=new ZariliaFormSelect(_AM_NEWS_PRUNE_TOPICS, 'pruned_topics','',5,true);
	$topics_arr=array();
	$xt = new NewsTopic();
	$allTopics = $xt->getAllTopics(false);				// The webmaster can see everything
	$topic_tree = new ZariliaObjectTree($allTopics, 'topic_id', 'topic_pid');
	$topics_arr = $topic_tree->getAllChild(0);
	if(count($topics_arr)) {
		foreach ($topics_arr as $onetopic) {
			$topiclist->addOption($onetopic->topic_id(),$onetopic->topic_title());
		}
	}
	$topiclist->setDescription(_AM_NEWS_EXPORT_PRUNE_DSC);
	$sform->addElement($topiclist,false);
	$button_tray = new ZariliaFormElementTray('' ,'');
	$submit_btn = new ZariliaFormButton('', 'post', _SUBMIT, 'submit');
	$button_tray->addElement($submit_btn);
	$sform->addElement($button_tray);
	$sform->display();
}

// A confirmation is asked before to prune stories
function ConfirmBeforeToPrune()
{
	global $dateformat;
	$story = new NewsStory();
	zarilia_cp_header();
	$topiclist='';
	if(isset($_POST['pruned_topics'])) {
		$topiclist=implode(',',$_POST['pruned_topics']);
	}
	echo '<h4>' . _AM_NEWS_PRUNENEWS . '</h4>';
	$expired=0;
	if(isset($_POST['onlyexpired'])) {
		$expired = intval($_POST['onlyexpired']);
	}
	$date=$_POST['prune_date'];
	$timestamp=mktime(0,0,0,intval(substr($date,5,2)), intval(substr($date,8,2)), intval(substr($date,0,4)));
	$count=$story->GetCountStoriesPublishedBefore($timestamp, $expired, $topiclist);
	if($count) {
		$displaydate=formatTimestamp($timestamp,$dateformat);
		$msg=sprintf(_AM_NEWS_PRUNE_CONFIRM,$displaydate, $count);
		zarilia_confirm(array( 'op' => 'prunenews', 'expired' => $expired, 'pruned_topics' => $topiclist, 'prune_date' => $timestamp, 'ok' => 1), 'index.php', $msg);
	} else {
		printf(_AM_NEWS_NOTHING_PRUNE);
	}
	unset($story);
}

// Effectively delete stories (published before a date), no more confirmation
function PruneNews()
{
	$story = new NewsStory();
	$timestamp=intval($_POST['prune_date']);
	$expired= intval($_POST['expired']);
	$topiclist='';
	if(isset($_POST['pruned_topics'])) {
		$topiclist=$_POST['pruned_topics'];
	}

	if(intval($_POST['ok'])==1) {
		$story = new NewsStory();
		zarilia_cp_header();
		$count=$story->GetCountStoriesPublishedBefore($timestamp,$expired,$topiclist);
		$msg=sprintf(_AM_NEWS_PRUNE_DELETED,$count);
		$story->DeleteBeforeDate($timestamp,$expired,$topiclist);
		unset($story);
		news_updateCache();
		redirect_header( 'index.php', 3, $msg);
	}
}

/**
* Newsletter's configuration
*
* You can create a newsletter's content from the admin part of the News module when you click on the tab named "Newsletter"
* First, let be clear, this module'functionality will not send the newsletter but it will prepare its content for you.
* To send the newsletter, you can use many specialized modules like evennews.
* You first select a range of dates and if you want, a selection of topics to use for the search.
* Once it's done, the script will use the file named /xoops/addons/language/yourlanguage/newsletter.php to create
* the newsletter's content. When it's finished, the script generates a file in the upload folder.
*/
function Newsletter()
{
    include_once ZAR_ROOT_PATH.'/class/zariliaformloader.php';
    zarilia_cp_header();
    adminmenu(5);
    echo '<br /><br /><br />';
	$sform = new ZariliaThemeForm(_AM_NEWS_NEWSLETTER, 'newsletterform', ZAR_URL.'/addons/news/admin/index.php', 'post');
	$dates_tray = new ZariliaFormElementTray(_AM_NEWS_NEWSLETTER_BETWEEN);
	$date1 = new ZariliaFormTextDateSelect('', 'date1',15,time());
	$date2 = new ZariliaFormTextDateSelect(_AM_NEWS_EXPORT_AND, 'date2',15,time());
	$dates_tray->addElement($date1);
	$dates_tray->addElement($date2);
	$sform->addElement($dates_tray);

	$topiclist=new ZariliaFormSelect(_AM_NEWS_PRUNE_TOPICS, 'export_topics','',5,true);
	$topics_arr=array();
	$xt = new NewsTopic();
	$allTopics = $xt->getAllTopics(false);				// The webmaster can see everything
	$topic_tree = new ZariliaObjectTree($allTopics, 'topic_id', 'topic_pid');
	$topics_arr = $topic_tree->getAllChild(0);
	if(count($topics_arr)) {
		foreach ($topics_arr as $onetopic) {
			$topiclist->addOption($onetopic->topic_id(),$onetopic->topic_title());
		}
	}
	$topiclist->setDescription(_AM_NEWS_EXPORT_PRUNE_DSC);
	$sform->addElement($topiclist,false);
	$sform->addElement(new ZariliaFormHidden('op', 'launchnewsletter'), false);
	$sform->addElement(new ZariliaFormRadioYN(_AM_NEWS_REMOVE_BR, 'removebr',1),false);
	$sform->addElement(new ZariliaFormRadioYN(_AM_NEWS_NEWSLETTER_HTML_TAGS, 'removehtml',0),false);
	$sform->addElement(new ZariliaFormTextArea(_AM_NEWS_NEWSLETTER_HEADER, 'header', '', 4, 70), false);
	$sform->addElement(new ZariliaFormTextArea(_AM_NEWS_NEWSLETTER_FOOTER, 'footer', '', 4, 70), false);
	$button_tray = new ZariliaFormElementTray('' ,'');
	$submit_btn = new ZariliaFormButton('', 'post', _SUBMIT, 'submit');
	$button_tray->addElement($submit_btn);
	$sform->addElement($button_tray);
	$sform->display();
}


/**
 * Launch the creation of the newsletter's content
 */
function LaunchNewsletter()
{
	global $zariliaConfig, $dateformat;
	zarilia_cp_header();
	adminmenu(5);
	$newslettertemplate = '';
	if (file_exists(ZAR_ROOT_PATH.'/addons/news/language/'.$zariliaConfig['language'].'/newsletter.php')) {
		include_once ZAR_ROOT_PATH.'/addons/news/language/'.$zariliaConfig['language'].'/newsletter.php';
	} else {
		include_once ZAR_ROOT_PATH.'/addons/news/language/english/newsletter.php';
	}
	echo '<br />';
	$story = new NewsStory();
	$exportedstories = array();
	$topiclist = '';
	$removebr = $removehtml = false;
	$removebr = isset($_POST['removebr']) ? intval($_POST['removebr']) : 0;
	$removehtml = isset($_POST['removehtml']) ? intval($_POST['removehtml']) : 0;
	$header = isset($_POST['header']) ? $_POST['header'] : '';
	$footer = isset($_POST['footer']) ? $_POST['footer'] : '';
	$date1 = $_POST['date1'];
	$date2 = $_POST['date2'];
	$timestamp1 = mktime(0,0,0,intval(substr($date1,5,2)), intval(substr($date1,8,2)), intval(substr($date1,0,4)));
	$timestamp2 = mktime(23,59,59,intval(substr($date2,5,2)), intval(substr($date2,8,2)), intval(substr($date2,0,4)));
	if(isset($_POST['export_topics'])) {
		$topiclist = implode(',',$_POST['export_topics']);
	}
	$tbltopics = array();
	$exportedstories = $story->NewsExport($timestamp1, $timestamp2, $topiclist, 0, $tbltopics);
    $newsfile = ZAR_UPLOAD_PATH.'/newsletter.txt';
	if(count($exportedstories)) {
		$fp = fopen($newsfile,'w');
		if(!$fp) {
			redirect_header('index.php',4,sprintf(_AM_NEWS_EXPORT_ERROR,$newsfile));
		}
		if(zarilia_trim($header) != '') {
			fwrite($fp, $header);
		}
		foreach($exportedstories as $onestory) {
			$content = $newslettertemplate;
			$search_pattern = array('%title%','%uname%','%created%','%published%','%expired%','%hometext%','%bodytext%','%description%','%keywords%','%reads%','%topicid%','%topic_title%','%comments%','%rating%','%votes%','%publisher%','%publisher_id%','%link%');
			$replace_pattern = array($onestory->title(),$onestory->uname(),formatTimestamp($onestory->created(),$dateformat),formatTimestamp($onestory->published(),$dateformat),formatTimestamp($onestory->expired(),$dateformat),$onestory->hometext(),$onestory->bodytext(),$onestory->description(),$onestory->keywords(),$onestory->counter(),$onestory->topicid(),$onestory->topic_title(),$onestory->comments(),$onestory->rating(),$onestory->votes(),$onestory->uname(),$onestory->uid(),ZAR_URL.'/addons/news/article.php?storyid='.$onestory->storyid());
			$content = str_replace($search_pattern, $replace_pattern, $content);
			if($removebr) {
				$content = str_replace('<br />',"\r\n",$content);
			}
			if($removehtml) {
				$content = strip_tags($content);
			}
			fwrite($fp,$content);
		}
		if(zarilia_trim($footer) != '') {
			fwrite($fp, $footer);
		}
		fclose($fp);
		$newsfile=ZAR_UPLOAD_URL.'/newsletter.txt';
		printf(_AM_NEWS_NEWSLETTER_READY,$newsfile,ZAR_URL.'/addons/news/admin/index.php?op=deletefile&amp;type=newsletter');
	} else {
		printf(_AM_NEWS_NOTHING);
	}
}



/**
* News export
*
* You can use this function in the module's admin when you click on the tab named "News Export"
* First select a range of date, possibly a range of topics and if you want, check the option "Include Topics Definitions"
* to also export the topics.
* News, and topics, will be exported to the XML format.
*/
function NewsExport()
{
    include_once ZAR_ROOT_PATH.'/class/zariliaformloader.php';
    zarilia_cp_header();
    adminmenu(4);
    echo '<br /><br /><br />';
	$sform = new ZariliaThemeForm(_AM_NEWS_EXPORT_NEWS, 'exportform', ZAR_URL.'/addons/news/admin/index.php', 'post');
	$dates_tray = new ZariliaFormElementTray(_AM_NEWS_EXPORT_BETWEEN);
	$date1 = new ZariliaFormTextDateSelect('', 'date1',15,time());
	$date2 = new ZariliaFormTextDateSelect(_AM_NEWS_EXPORT_AND, 'date2',15,time());
	$dates_tray->addElement($date1);
	$dates_tray->addElement($date2);
	$sform->addElement($dates_tray);

	$topiclist=new ZariliaFormSelect(_AM_NEWS_PRUNE_TOPICS, 'export_topics','',5,true);
	$topics_arr=array();
	$xt = new NewsTopic();
	$allTopics = $xt->getAllTopics(false);				// The webmaster can see everything
	$topic_tree = new ZariliaObjectTree($allTopics, 'topic_id', 'topic_pid');
	$topics_arr = $topic_tree->getAllChild(0);
	if(count($topics_arr)) {
		foreach ($topics_arr as $onetopic) {
			$topiclist->addOption($onetopic->topic_id(),$onetopic->topic_title());
		}
	}
	$topiclist->setDescription(_AM_NEWS_EXPORT_PRUNE_DSC);
	$sform->addElement($topiclist,false);
	$sform->addElement(new ZariliaFormRadioYN(_AM_NEWS_EXPORT_INCTOPICS, 'includetopics',0),false);
	$sform->addElement(new ZariliaFormHidden('op', 'launchexport'), false);
	$button_tray = new ZariliaFormElementTray('' ,'');
	$submit_btn = new ZariliaFormButton('', 'post', _SUBMIT, 'submit');
	$button_tray->addElement($submit_btn);
	$sform->addElement($button_tray);
	$sform->display();
}


function news_utf8_encode($text)
{
	return zarilia_utf8_encode($text);
}

// Launch stories export (to the xml's format)
function LaunchExport()
{
	zarilia_cp_header();
	adminmenu(4);
	echo '<br />';
	$story = new NewsStory();
	$topic= new NewsTopic();
	$exportedstories=array();
	$date1=$_POST['date1'];
	$date2=$_POST['date2'];
	$timestamp1=mktime(0,0,0,intval(substr($date1,5,2)), intval(substr($date1,8,2)), intval(substr($date1,0,4)));
	$timestamp2=mktime(23,59,59,intval(substr($date2,5,2)), intval(substr($date2,8,2)), intval(substr($date2,0,4)));
	$topiclist='';
	if(isset($_POST['export_topics'])) {
		$topiclist=implode(',',$_POST['export_topics']);
	}
	$topicsexport=intval($_POST['includetopics']);
	$tbltopics=array();
	$exportedstories=$story->NewsExport($timestamp1, $timestamp2, $topiclist, $topicsexport, $tbltopics);
	if(count($exportedstories)) {
		$xmlfile=ZAR_UPLOAD_PATH.'/stories.xml';
		$fp=fopen($xmlfile,'w');
		if(!$fp) {
			redirect_header('index.php',4,sprintf(_AM_NEWS_EXPORT_ERROR,$xmlfile));
		}

		fwrite($fp,news_utf8_encode("<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n"));
		fwrite($fp,news_utf8_encode("<news_stories>\n"));
		if($topicsexport) {
			foreach($tbltopics as $onetopic) {
				$topic->NewsTopic($onetopic);
				$content = "<news_topic>\n";
				$content .= sprintf("\t<topic_id>%u</topic_id>\n",$topic->topic_id());
				$content .= sprintf("\t<topic_pid>%u</topic_pid>\n",$topic->topic_pid());
				$content .= sprintf("\t<topic_imgurl>%s</topic_imgurl>\n",$topic->topic_imgurl());
				$content .= sprintf("\t<topic_title>%s</topic_title>\n",$topic->topic_title('F'));
				$content .= sprintf("\t<menu>%d</menu>\n",$topic->menu());
				$content .= sprintf("\t<topic_frontpage>%d</topic_frontpage>\n",$topic->topic_frontpage());
				$content .= sprintf("\t<topic_rssurl>%s</topic_rssurl>\n",$topic->topic_rssurl('E'));
				$content .= sprintf("\t<topic_description>%s</topic_description>\n",$topic->topic_description());
				$content .= sprintf("</news_topic>\n");
				$content = news_utf8_encode($content);
				fwrite($fp,$content);
			}
		}

		foreach($exportedstories as $onestory) {
			$content = "<zarilia_story>\n";
    		$content .= sprintf("\t<storyid>%u</storyid>\n",$onestory->storyid());
    		$content .= sprintf("\t<uid>%u</uid>\n",$onestory->uid());
    		$content .= sprintf("\t<uname>%s</uname>\n",$onestory->uname());
    		$content .= sprintf("\t<title>%s</title>\n",$onestory->title());
    		$content .= sprintf("\t<created>%u</created>\n",$onestory->created());
    		$content .= sprintf("\t<published>%u</published>\n",$onestory->published());
    		$content .= sprintf("\t<expired>%u</expired>\n",$onestory->expired());
    		$content .= sprintf("\t<hostname>%s</hostname>\n",$onestory->hostname());
    		$content .= sprintf("\t<nohtml>%d</nohtml>\n",$onestory->nohtml());
    		$content .= sprintf("\t<nosmiley>%d</nosmiley>\n",$onestory->nosmiley());
    		$content .= sprintf("\t<hometext>%s</hometext>\n",$onestory->hometext());
    		$content .= sprintf("\t<bodytext>%s</bodytext>\n",$onestory->bodytext());
    		$content .= sprintf("\t<description>%s</description>\n",$onestory->description());
    		$content .= sprintf("\t<keywords>%s</keywords>\n",$onestory->keywords());
    		$content .= sprintf("\t<counter>%u</counter>\n",$onestory->counter());
    		$content .= sprintf("\t<topicid>%u</topicid>\n",$onestory->topicid());
    		$content .= sprintf("\t<ihome>%d</ihome>\n",$onestory->ihome());
    		$content .= sprintf("\t<notifypub>%d</notifypub>\n",$onestory->notifypub());
    		$content .= sprintf("\t<story_type>%s</story_type>\n",$onestory->type());
    		$content .= sprintf("\t<topicdisplay>%d</topicdisplay>\n",$onestory->topicdisplay());
    		$content .= sprintf("\t<topicalign>%s</topicalign>\n",$onestory->topicalign());
    		$content .= sprintf("\t<comments>%u</comments>\n",$onestory->comments());
    		$content .= sprintf("\t<rating>%f</rating>\n",$onestory->rating());
	    	$content .= sprintf("\t<votes>%u</votes>\n",$onestory->votes());
    		$content .= sprintf("</zarilia_story>\n");
    		$content = news_utf8_encode($content);
    		fwrite($fp,$content);
		}
		fwrite($fp,news_utf8_encode("</news_stories>\n"));
		fclose($fp);
		$xmlfile=ZAR_UPLOAD_URL.'/stories.xml';
		printf(_AM_NEWS_EXPORT_READY,$xmlfile,ZAR_URL.'/addons/news/admin/index.php?op=deletefile&amp;type=xml');
	} else {
		printf(_AM_NEWS_EXPORT_NOTHING);
	}
}



/*
* Topics manager
*
* It's from here that you can list, add, modify an delete topics
* At first, you can see a list of all the topics in your databases. This list contains the topic's ID, its name,
* its parent topic, if it should be visible in the Xoops main menu and an action (Edit or Delete topic)
* Below this list you find the form used to create and edit the topics.
* use this form to :
* - Type the topic's title
* - Enter its description
* - Select its parent topic
* - Choose a color
* - Select if it must appear in the Xoops main menu
* - Choose if you want to see in the front page. If it's not the case, visitors will have to use the navigation box to see it
* - And finally you ca select an image to represent the topic
* The text box called "URL of RSS feed" is, for this moment, not used.
*/
function topicsmanager()
{
    global $zariliaDB, $zariliaConfig, $zariliaAddon, $myts;
    include_once ZAR_ROOT_PATH.'/class/zariliaformloader.php';
    zarilia_cp_header();
    adminmenu(0);
    $uploadfolder=sprintf(_AM_UPLOAD_WARNING,ZAR_URL . '/addons/' . $zariliaAddon->getVar('dirname').'/images/topics');
    $uploadirectory='/addons/' . $zariliaAddon->getVar('dirname').'/images/topics';
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;

	$xt = new ZariliaTree($zariliaDB->prefix('topics'), 'topic_id', 'topic_pid');
	$topics_arr = $xt->getChildTreeArray(0,'topic_title');
	$totaltopics = count($topics_arr);
	$class='';

    echo '<h4>' . _AM_CONFIG . '</h4>';
	news_collapsableBar('topicsmanager', 'toptopicsmanager');
	echo "<img onclick=\"toggle('toptable'); toggleIcon('toptableicon');\" id='toptopicsmanager' name='toptopicsmanager' src='" . ZAR_URL . "/addons/news/images/close12.gif' alt='' /></a>&nbsp;"._AM_TOPICSMNGR . ' (' . $totaltopics . ')'."</h4>";
	echo "<div id='topicsmanager'>";
	echo '<br />';
    echo "<div style='text-align: center;'>";
    echo "<table width='100%' cellspacing='1' cellpadding='3' border='0' class='outer'><tr class='bg3'><td align='center'>" . _AM_TOPIC . "</td><td align='left'>" . _AM_TOPICNAME . "</td><td align='center'>" . _AM_PARENTTOPIC . "</td><td align='center'>" . _AM_SUB_MENU_YESNO . "</td><td align='center'>" . _AM_ACTION . "</td></tr>";
	if(is_array($topics_arr) && $totaltopics) {
		$cpt=1;
		$tmpcpt=$start;
		$ok=true;
		$output='';
		while($ok) {
			if($tmpcpt < $totaltopics) {
				$linkedit = ZAR_URL . '/addons/'.$zariliaAddon->getVar('dirname') . '/admin/index.php?op=topicsmanager&amp;topic_id=' . $topics_arr[$tmpcpt]['topic_id'];
				$linkdelete = ZAR_URL . '/addons/'.$zariliaAddon->getVar('dirname') . '/admin/index.php?op=delTopic&amp;topic_id=' . $topics_arr[$tmpcpt]['topic_id'];
				$action=sprintf("<a href='%s'>%s</a> - <a href='%s'>%s</a>",$linkedit,_AM_EDIT , $linkdelete, _AM_DELETE);
				$parent='&nbsp;';
				if($topics_arr[$tmpcpt]['topic_pid']>0)	{
					$xttmp = new ZariliaTopic($zariliaDB->prefix('topics'),$topics_arr[$tmpcpt]['topic_pid']);
					$parent = $xttmp->topic_title();
					unset($xttmp);
				}
				if($topics_arr[$tmpcpt]['topic_pid']!=0) {
					$topics_arr[$tmpcpt]['prefix'] = str_replace('.','-',$topics_arr[$tmpcpt]['prefix']) . '&nbsp;';
				} else {
					$topics_arr[$tmpcpt]['prefix'] = str_replace('.','',$topics_arr[$tmpcpt]['prefix']);
				}
				$submenu=$topics_arr[$tmpcpt]['menu'] ? _YES : _NO;
				$class = ($class == 'even') ? 'odd' : 'even';
				$output  = $output . "<tr class='".$class."'><td>" . $topics_arr[$tmpcpt]['topic_id'] . "</td><td align='left'>" . $topics_arr[$tmpcpt]['prefix'] . $myts->displayTarea($topics_arr[$tmpcpt]['topic_title']) . "</td><td align='left'>" . $parent . "</td><td>" . $submenu . "</td><td>" . $action . "</td></tr>";
			} else {
				$ok=false;
			}
			if($cpt>=news_getmoduleoption('storycountadmin')) {
				$ok=false;
			}
			$tmpcpt++;
			$cpt++;
		}
		echo $output;
	}
	$pagenav = new ZariliaPageNav( $totaltopics, news_getmoduleoption('storycountadmin'), $start, 'start', 'op=topicsmanager');
	echo "</table><div align='right'>".$pagenav->renderNav().'</div><br />';
	echo "</div></div><br />\n";

	$topic_id = isset($_GET['topic_id']) ? intval($_GET['topic_id']) : 0;
	if($topic_id>0) {
		$xtmod = new NewsTopic($topic_id);
		$topic_title=$xtmod->topic_title('E');
		$topic_description=$xtmod->topic_description('E');
		$topic_rssfeed=$xtmod->topic_rssurl('E');
		$op='modTopicS';
		if(zarilia_trim($xtmod->topic_imgurl())!='') {
			$topicimage=$xtmod->topic_imgurl();
		} else {
			$topicimage='blank.png';
		}
		$btnlabel=_AM_MODIFY;
		$parent=$xtmod->topic_pid();
		$formlabel=_AM_MODIFYTOPIC;
		$submenu=$xtmod->menu();
		$topic_frontpage=$xtmod->topic_frontpage();
		$topic_color=$xtmod->topic_color();
		unset($xtmod);
	} else {
		$topic_title='';
		$topic_frontpage=1;
		$topic_description='';
		$op='addTopic';
		$topicimage='xoops.gif';
		$btnlabel=_AM_ADD;
		$parent=-1;
		$submenu=0;
		$topic_rssfeed='';
		$formlabel=_AM_ADD_TOPIC;
		$topic_color='000000';
	}

	$sform = new ZariliaThemeForm($formlabel, 'topicform', ZAR_URL.'/addons/'.$zariliaAddon->getVar('dirname').'/admin/index.php', 'post');
	$sform->setExtra('enctype="multipart/form-data"');
	$sform->addElement(new ZariliaFormText(_AM_TOPICNAME, 'topic_title', 50, 255, $topic_title), true);
	$editor=news_getWysiwygForm(_AM_TOPIC_DESCR,'topic_description', $topic_description, 15, 60, 'hometext_hidden');
	if($editor) {
		$sform->addElement($editor,false);
	}

	$sform->addElement(new ZariliaFormHidden('op', $op), false);
	$sform->addElement(new ZariliaFormHidden('topic_id', $topic_id), false);

	include_once ZAR_ROOT_PATH.'/addons/news/class/class.newstopic.php';
	$xt = new NewsTopic();
	$sform->addElement(new ZariliaFormLabel(_AM_PARENTTOPIC, $xt->MakeMyTopicSelBox(1, $parent,'topic_pid','',false)));
	// Topic's color
	// Code stolen to Zoullou, thank you Zoullou ;-)
	$select_color = "\n<select name='topic_color'  onchange='zariliaGetElementById(\"NewsColorSelect\").style.backgroundColor = \"#\" + this.options[this.selectedIndex].value;'>\n<option value='000000'>"._AM_NEWS_COLOR."</option>\n";
	$color_values = array('000000','000033','000066','000099','0000CC','0000FF','003300','003333','003366','0033CC','0033FF','006600','006633',
							'006666','006699','0066CC','0066FF','009900','009933','009966','009999','0099CC','0099FF','00CC00','00CC33','00CC66','00CC99',
							'00CCCC','00CCFF','00FF00','00FF33','00FF66','00FF99','00FFCC','00FFFF','330000','330033','330066','330099','3300CC','3300FF',
							'333300','333333','333366','333399','3333CC','3333FF','336600','336633','336666','336699','3366CC','3366FF','339900','339933',
							'339966','339999','3399CC','3399FF','33CC00','33CC33','33CC66','33CC99','33CCCC','33CCFF','33FF00','33FF33','33FF66','33FF99',
							'33FFCC','33FFFF','660000','660033','660066','660099','6600CC','6600FF','663300','663333','663366','663399','6633CC','6633FF',
							'666600','666633','666666','666699','6666CC','6666FF','669900','669933','669966','669999','6699CC','6699FF','66CC00','66CC33',
							'66CC66','66CC99','66CCCC','66CCFF','66FF00','66FF33','66FF66','66FF99','66FFCC','66FFFF','990000','990033','990066','990099',
							'9900CC','9900FF','993300','993333','993366','993399','9933CC','9933FF','996600','996633','996666','996699','9966CC','9966FF',
							'999900','999933','999966','999999','9999CC','9999FF','99CC00','99CC33','99CC66','99CC99','99CCCC','99CCFF','99FF00','99FF33',
							'99FF66','99FF99','99FFCC','99FFFF','CC0000','CC0033','CC0066','CC0099','CC00CC','CC00FF','CC3300','CC3333','CC3366','CC3399',
							'CC33CC','CC33FF','CC6600','CC6633','CC6666','CC6699','CC66CC','CC66FF','CC9900','CC9933','CC9966','CC9999','CC99CC','CC99FF',
							'CCCC00','CCCC33','CCCC66','CCCC99','CCCCCC','CCCCFF','CCFF00','CCFF33','CCFF66','CCFF99','CCFFCC','CCFFFF','FF0000','FF0033',
							'FF0066','FF0099','FF00CC','FF00FF','FF3300','FF3333','FF3366','FF3399','FF33CC','FF33FF','FF6600','FF6633','FF6666','FF6699',
							'FF66CC','FF66FF','FF9900','FF9933','FF9966','FF9999','FF99CC','FF99FF','FFCC00','FFCC33','FFCC66','FFCC99','FFCCCC','FFCCFF',
							'FFFF00','FFFF33','FFFF66','FFFF99','FFFFCC','FFFFFF');

	foreach($color_values as $color_value) {
		if($topic_color == $color_value) {
			$selected = " selected='selected'";
		} else {
			$selected = '';
		}
		$select_color .= "<option".$selected." value='".$color_value."' style='background-color:#".$color_value.";color:#".$color_value.";'>#".$color_value."</option>\n";
	}

	$select_color .= "</select>&nbsp;\n<span id='NewsColorSelect'>&nbsp;&nbsp;&nbsp;&nbsp;</span>";
	$sform->addElement( new ZariliaFormLabel( _AM_NEWS_TOPIC_COLOR, $select_color) );
	// Sub menu ?
	$sform->addElement(new ZariliaFormRadioYN(_AM_SUB_MENU, 'submenu', $submenu, _YES, _NO));
	$sform->addElement(new ZariliaFormRadioYN(_AM_PUBLISH_FRONTPAGE, 'topic_frontpage', $topic_frontpage, _YES, _NO));
	// Unused for this moment... sorry
	//$sform->addElement(new ZariliaFormText(_AM_NEWS_RSS_URL, 'topic_rssfeed', 50, 255, $topic_rssfeed), false);
	// ********** Picture
	$imgtray = new ZariliaFormElementTray(_AM_TOPICIMG,'<br />');

	$imgpath=sprintf(_AM_IMGNAEXLOC, 'addons/' . $zariliaAddon->getVar('dirname') . '/images/topics/' );
	$imageselect= new ZariliaFormSelect($imgpath, 'topic_imgurl',$topicimage);
    $topics_array = ZariliaLists :: getImgListAsArray( ZAR_ROOT_PATH . '/addons/news/images/topics/' );
    foreach( $topics_array as $image ) {
        $imageselect->addOption("$image", $image);
    }
	$imageselect->setExtra( "onchange='showImgSelected(\"image3\", \"topic_imgurl\", \"" . $uploadirectory . "\", \"\", \"" . ZAR_URL . "\")'" );
    $imgtray->addElement($imageselect,false);
    $imgtray -> addElement( new ZariliaFormLabel( '', "<br /><img src='" . ZAR_URL . "/" . $uploadirectory . "/" . $topicimage . "' name='image3' id='image3' alt='' />" ) );

    $uploadfolder=sprintf(_AM_UPLOAD_WARNING,ZAR_URL . '/addons/' .$zariliaAddon->getVar('dirname') .'/images/topics');
    $fileseltray= new ZariliaFormElementTray('','<br />');
    $fileseltray->addElement(new ZariliaFormFile(_AM_TOPIC_PICTURE , 'attachedfile', news_getmoduleoption('maxuploadsize')), false);
    $fileseltray->addElement(new ZariliaFormLabel($uploadfolder ), false);
    $imgtray->addElement($fileseltray);
    $sform->addElement($imgtray);

	// Permissions
    $member_handler = & zarilia_gethandler('member');
    $group_list = &$member_handler->getGroupList();
    $gperm_handler = &zarilia_gethandler('groupperm');
    $full_list = array_keys($group_list);

	$groups_ids = array();
    if($topic_id > 0) {		// Edit mode
    	$groups_ids = $gperm_handler->getGroupIds('news_approve', $topic_id, $zariliaAddon->getVar('mid'));
    	$groups_ids = array_values($groups_ids);
    	$groups_news_can_approve_checkbox = new ZariliaFormCheckBox(_AM_APPROVEFORM, 'groups_news_can_approve[]', $groups_ids);
    } else {	// Creation mode
    	$groups_news_can_approve_checkbox = new ZariliaFormCheckBox(_AM_APPROVEFORM, 'groups_news_can_approve[]', $full_list);
    }
    $groups_news_can_approve_checkbox->addOptionArray($group_list);
    $sform->addElement($groups_news_can_approve_checkbox);

	$groups_ids = array();
    if($topic_id > 0) {		// Edit mode
    	$groups_ids = $gperm_handler->getGroupIds('news_submit', $topic_id, $zariliaAddon->getVar('mid'));
    	$groups_ids = array_values($groups_ids);
    	$groups_news_can_submit_checkbox = new ZariliaFormCheckBox(_AM_SUBMITFORM, 'groups_news_can_submit[]', $groups_ids);
    } else {	// Creation mode
    	$groups_news_can_submit_checkbox = new ZariliaFormCheckBox(_AM_SUBMITFORM, 'groups_news_can_submit[]', $full_list);
    }
    $groups_news_can_submit_checkbox->addOptionArray($group_list);
    $sform->addElement($groups_news_can_submit_checkbox);

	$groups_ids = array();
    if($topic_id > 0) {		// Edit mode
    	$groups_ids = $gperm_handler->getGroupIds('news_view', $topic_id, $zariliaAddon->getVar('mid'));
    	$groups_ids = array_values($groups_ids);
    	$groups_news_can_view_checkbox = new ZariliaFormCheckBox(_AM_VIEWFORM, 'groups_news_can_view[]', $groups_ids);
    } else {	// Creation mode
    	$groups_news_can_view_checkbox = new ZariliaFormCheckBox(_AM_VIEWFORM, 'groups_news_can_view[]', $full_list);
    }
    $groups_news_can_view_checkbox->addOptionArray($group_list);
    $sform->addElement($groups_news_can_view_checkbox);

	// Submit buttons
	$button_tray = new ZariliaFormElementTray('' ,'');
	$submit_btn = new ZariliaFormButton('', 'post', $btnlabel, 'submit');
	$button_tray->addElement($submit_btn);
	$sform->addElement($button_tray);
	$sform->display();
	echo "<script type='text/javascript'>\n";
	echo 'zariliaGetElementById("NewsColorSelect").style.backgroundColor = "#' . $topic_color .'";';
	echo "</script>\n";
}


// Save a topic after it has been modified
function modTopicS()
{
    global $zariliaDB, $zariliaAddon, $zariliaAddonConfig;

    $xt = new NewsTopic(intval($_POST['topic_id']));
    if (intval($_POST['topic_pid']) == intval($_POST['topic_id'])) {
        redirect_header( 'index.php?op=topicsmanager', 2, _AM_ADD_TOPIC_ERROR1 );
    }
    $xt->setTopicPid(intval($_POST['topic_pid']));
    if (empty($_POST['topic_title'])) {
        redirect_header( 'index.php?op=topicsmanager', 2, _AM_ERRORTOPICNAME );
    }
    if(isset($_SESSION['items_count'])) {
    	$_SESSION['items_count'] = -1;
    }
    $xt -> setTopicTitle($_POST['topic_title']);
    if (isset($_POST['topic_imgurl']) && $_POST['topic_imgurl']!= '') {
        $xt -> setTopicImgurl($_POST['topic_imgurl']);
    }
   	$xt->setMenu(intval($_POST['submenu']));
   	$xt->setTopicFrontpage(intval($_POST['topic_frontpage']));
   	$xt->setTopicDescription($_POST['topic_description']);
   	//$xt->Settopic_rssurl($_POST['topic_rssfeed']);
   	$xt->setTopic_color($_POST['topic_color']);

	if(isset($_POST['zarilia_upload_file'])) {
		$fldname = $_FILES[$_POST['zarilia_upload_file'][0]];
		$fldname = (get_magic_quotes_gpc()) ? stripslashes($fldname['name']) : $fldname['name'];
		if(zarilia_trim($fldname!='')) {
			$sfiles = new sFiles();
			$dstpath = ZAR_ROOT_PATH . '/addons/' . $zariliaAddon->getVar('dirname') . '/images/topics';
			$destname=$sfiles->createUploadName($dstpath ,$fldname, true);
			$permittedtypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png');
			$uploader = new ZariliaMediaUploader($dstpath, $permittedtypes, $zariliaAddonConfig['maxuploadsize']);
			$uploader->setTargetFileName($destname);
			if ($uploader->fetchMedia($_POST['zarilia_upload_file'][0])) {
				if ($uploader->upload()) {
					$xt->setTopicImgurl(basename($destname));
				} else {
					echo _AM_UPLOAD_ERROR . ' ' . $uploader->getErrors();
				}
			} else {
				echo $uploader->getErrors();
			}
		}
   	}
    $xt->store();

	// Permissions
	$gperm_handler = &zarilia_gethandler('groupperm');
	$criteria = new CriteriaCompo();
	$criteria->add(new Criteria('gperm_itemid', $xt->topic_id(), '='));
	$criteria->add(new Criteria('gperm_modid', $zariliaAddon->getVar('mid'),'='));
	$criteria->add(new Criteria('gperm_name', 'news_approve', '='));
	$gperm_handler->deleteAll($criteria);

	$criteria = new CriteriaCompo();
	$criteria->add(new Criteria('gperm_itemid', $xt->topic_id(), '='));
	$criteria->add(new Criteria('gperm_modid', $zariliaAddon->getVar('mid'),'='));
	$criteria->add(new Criteria('gperm_name', 'news_submit', '='));
	$gperm_handler->deleteAll($criteria);

	$criteria = new CriteriaCompo();
	$criteria->add(new Criteria('gperm_itemid', $xt->topic_id(), '='));
	$criteria->add(new Criteria('gperm_modid', $zariliaAddon->getVar('mid'),'='));
	$criteria->add(new Criteria('gperm_name', 'news_view', '='));
	$gperm_handler->deleteAll($criteria);

	if(isset($_POST['groups_news_can_approve'])) {
		foreach($_POST['groups_news_can_approve'] as $onegroup_id) {
			$gperm_handler->addRight('news_approve', $xt->topic_id(), $onegroup_id, $zariliaAddon->getVar('mid'));
		}
	}

	if(isset($_POST['groups_news_can_submit'])) {
		foreach($_POST['groups_news_can_submit'] as $onegroup_id) {
			$gperm_handler->addRight('news_submit', $xt->topic_id(), $onegroup_id, $zariliaAddon->getVar('mid'));
		}
	}

	if(isset($_POST['groups_news_can_view'])) {
		foreach($_POST['groups_news_can_view'] as $onegroup_id) {
			$gperm_handler->addRight('news_view', $xt->topic_id(), $onegroup_id, $zariliaAddon->getVar('mid'));
		}
	}

    news_updateCache();
    redirect_header( 'index.php?op=topicsmanager', 1, _AM_DBUPDATED );
    exit();
}

// Delete a topic and its subtopics and its stories and the related stories
function delTopic()
{
    global $zariliaDB, $zariliaAddon;
    if (!isset($_POST['ok'])) {
        zarilia_cp_header();
        echo '<h4>' . _AM_CONFIG . '</h4>';
        $xt = new ZariliaTopic( $zariliaDB->prefix('topics'), intval($_GET['topic_id']));
        zarilia_confirm(array( 'op' => 'delTopic', 'topic_id' => intval($_GET['topic_id']), 'ok' => 1), 'index.php', _AM_WAYSYWTDTTAL . '<br />' . $xt->topic_title('S'));
    } else {
        $xt = new ZariliaTopic($zariliaDB->prefix('topics'), intval($_POST['topic_id']));
	    if(isset($_SESSION['items_count'])) {
    		$_SESSION['items_count'] = -1;
    	}
        // get all subtopics under the specified topic
        $topic_arr = $xt->getAllChildTopics();
        array_push( $topic_arr, $xt );
        foreach( $topic_arr as $eachtopic ) {
            // get all stories in each topic
            $story_arr = NewsStory :: getByTopic( $eachtopic -> topic_id() );
            foreach( $story_arr as $eachstory ) {
                if (false != $eachstory->delete()) {
                    zarilia_comment_delete( $zariliaAddon -> getVar( 'mid' ), $eachstory -> storyid() );
                    zarilia_notification_deletebyitem($zariliaAddon->getVar('mid'), 'story', $eachstory->storyid());
                }
            }
            // all stories for each topic is deleted, now delete the topic data
            $eachtopic -> delete();
            // Delete also the notifications and permissions
            zarilia_notification_deletebyitem( $zariliaAddon -> getVar( 'mid' ), 'category', $eachtopic -> topic_id );
			zarilia_groupperm_deletebymoditem($zariliaAddon->getVar('mid'), 'news_approve', $eachtopic -> topic_id);
			zarilia_groupperm_deletebymoditem($zariliaAddon->getVar('mid'), 'news_submit', $eachtopic -> topic_id);
			zarilia_groupperm_deletebymoditem($zariliaAddon->getVar('mid'), 'news_view', $eachtopic -> topic_id);
        }
        news_updateCache();
        redirect_header( 'index.php?op=topicsmanager', 1, _AM_DBUPDATED );
        exit();
    }
}

// Add a new topic
function addTopic()
{
	global $zariliaDB, $zariliaAddon, $zariliaAddonConfig;
    $topicpid = isset($_POST['topic_pid']) ? intval($_POST['topic_pid']) : 0;
    $xt = new NewsTopic();
    if (!$xt->topicExists($topicpid, $_POST['topic_title'])) {
        $xt->setTopicPid($topicpid);
        if (empty($_POST['topic_title']) || zarilia_trim($_POST['topic_title'])=='') {
            redirect_header( 'index.php?op=topicsmanager', 2, _AM_ERRORTOPICNAME );
        }
        $xt->setTopicTitle($_POST['topic_title']);
        //$xt->Settopic_rssurl($_POST['topic_rssfeed']);
        $xt->setTopic_color($_POST['topic_color']);
        if (isset($_POST['topic_imgurl'] ) && $_POST['topic_imgurl'] != '') {
            $xt->setTopicImgurl($_POST['topic_imgurl'] );
        }
		$xt->setMenu(intval($_POST['submenu']));
		$xt->setTopicFrontpage(intval($_POST['topic_frontpage']));
	    if(isset($_SESSION['items_count'])) {
    		$_SESSION['items_count'] = -1;
    	}
		if(isset($_POST['zarilia_upload_file'])) {
			$fldname = $_FILES[$_POST['zarilia_upload_file'][0]];
			$fldname = (get_magic_quotes_gpc()) ? stripslashes($fldname['name']) : $fldname['name'];
			if(zarilia_trim($fldname!='')) {
				$sfiles = new sFiles();
				$dstpath = ZAR_ROOT_PATH . '/addons/' . $zariliaAddon -> getVar('dirname') . '/images/topics';
				$destname=$sfiles->createUploadName($dstpath ,$fldname, true);
				$permittedtypes=array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png');
				$uploader = new ZariliaMediaUploader($dstpath, $permittedtypes, $zariliaAddonConfig['maxuploadsize']);
				$uploader->setTargetFileName($destname);
				if ($uploader->fetchMedia($_POST['zarilia_upload_file'][0])) {
					if ($uploader->upload()) {
						$xt->setTopicImgurl(basename($destname));
					} else {
						echo _AM_UPLOAD_ERROR . ' ' . $uploader->getErrors();
					}
				} else {
					echo $uploader->getErrors();
				}
			}
		}
		$xt->setTopicDescription($_POST['topic_description']);
		$xt->store();
		// Permissions
		$gperm_handler = &zarilia_gethandler('groupperm');
		if(isset($_POST['groups_news_can_approve'])) {
			foreach($_POST['groups_news_can_approve'] as $onegroup_id) {
				$gperm_handler->addRight('news_approve', $xt->topic_id(), $onegroup_id, $zariliaAddon->getVar('mid'));
			}
		}

		if(isset($_POST['groups_news_can_submit'])) {
			foreach($_POST['groups_news_can_submit'] as $onegroup_id) {
				$gperm_handler->addRight('news_submit', $xt->topic_id(), $onegroup_id, $zariliaAddon->getVar('mid'));
			}
		}

		if(isset($_POST['groups_news_can_view'])) {
			foreach($_POST['groups_news_can_view'] as $onegroup_id) {
				$gperm_handler->addRight('news_view', $xt->topic_id(), $onegroup_id, $zariliaAddon->getVar('mid'));
			}
		}
		news_updateCache();

        $notification_handler = & zarilia_gethandler('notification');
        $tags = array();
        $tags['TOPIC_NAME'] = $_POST['topic_title'];
        $notification_handler->triggerEvent( 'global', 0, 'new_category', $tags);
        redirect_header('index.php?op=topicsmanager', 1, _AM_DBUPDATED);
    } else {
        redirect_header('index.php?op=topicsmanager', 2, _AM_ADD_TOPIC_ERROR);
    }
    exit();
}

/**
 * Statistics about stories, topics and authors
 *
 * You can reach the statistics from the admin part of the news module by clicking on the "Statistics" tabs
 * The number of visible elements in each table is equal to the module's option called "storycountadmin"
 * There are 3 kind of different statistics :
 * - Topics statistics
 *   For each topic you can see its number of articles, the number of time each topics was viewed, the number
 *   of attached files, the number of expired articles and the number of unique authors.
 * - Articles statistics
 *   This part is decomposed in 3 tables :
 *   a) Most readed articles
 *      This table resumes, for all the news in your database, the most readed articles.
 *      The table contains, for each news, its topic, its title, the author and the number of views.
 *   b) Less readed articles
 *      That's the opposite action of the previous table and its content is the same
 *   c) Best rated articles
 *      You will find here the best rated articles, the content is the same that the previous tables, the last column is just changing and contains the article's rating
 * - Authors statistics
 *   This part is also decomposed in 3 tables
 *   a) Most readed authors
 *		To create this table, the program compute the total number of reads per author and displays the most readed author and the number of views
 *   b) Best rated authors
 *      To created this table's content, the program compute the rating's average of each author and create a table
 *   c) Biggest contributors
 *      The goal of this table is to know who is creating the biggest number of articles.
 */
function Stats()
{
    global $zariliaAddon, $zariliaConfig;
    zarilia_cp_header();
    $myts = &MyTextSanitizer::getInstance();
	if (file_exists(ZAR_ROOT_PATH.'/addons/news/language/'.$zariliaConfig['language'].'/main.php')) {
		include_once ZAR_ROOT_PATH.'/addons/news/language/'.$zariliaConfig['language'].'/main.php';
	} else {
		include_once ZAR_ROOT_PATH.'/addons/news/language/english/main.php';
	}
    adminmenu(6);
    $news = new NewsStory();
    $stats = array();
    $stats=$news->GetStats(news_getmoduleoption('storycountadmin'));
	$totals=array(0,0,0,0,0);
    printf("<h1>%s</h1>\n",_AM_NEWS_STATS);

    // First part of the stats, everything about topics
	$storiespertopic=$stats['storiespertopic'];
	$readspertopic=$stats['readspertopic'];
	$filespertopic=$stats['filespertopic'];
	$expiredpertopic=$stats['expiredpertopic'];
	$authorspertopic=$stats['authorspertopic'];
	$class='';

	echo "<div style='text-align: center;'><b>" . _AM_NEWS_STATS0 . "</b><br />\n";
	echo "<table border='0' width='100%'><tr class='bg3'><td align='center'>"._AM_TOPIC."</td><td align='center'>" . _NW_ARTICLES . "</td><td>" . _NW_VIEWS . "</td><td>" . _AM_UPLOAD_ATTACHFILE . "</td><td>" . _AM_EXPARTS ."</td><td>" ._AM_NEWS_STATS1 ."</td></tr>";
	foreach ( $storiespertopic as $topicid => $data ) {
		$url=ZAR_URL . '/addons/' . $zariliaAddon->getVar('dirname') . '/index.php?storytopic=' . $topicid;
		$views=0;
		if(array_key_exists($topicid,$readspertopic)) {
			$views=$readspertopic[$topicid];
		}
		$attachedfiles=0;
		if(array_key_exists($topicid,$filespertopic)) {
			$attachedfiles=$filespertopic[$topicid];
		}
		$expired=0;
		if(array_key_exists($topicid,$expiredpertopic)) {
			$expired=$expiredpertopic[$topicid];
		}
		$authors=0;
		if(array_key_exists($topicid,$authorspertopic)) {
			$authors=$authorspertopic[$topicid];
		}
		$articles=$data['cpt'];

        $totals[0]+=$articles;
        $totals[1]+=$views;
        $totals[2]+=$attachedfiles;
        $totals[3]+=$expired;
        $class = ($class == 'even') ? 'odd' : 'even';
		printf("<tr class='".$class."'><td align='left'><a href='%s' target ='_blank'>%s</a></td><td align='right'>%u</td><td align='right'>%u</td><td align='right'>%u</td><td align='right'>%u</td><td align='right'>%u</td></tr>\n",$url,$myts->displayTarea($data['topic_title']),$articles,$views,$attachedfiles,$expired,$authors);
	}
	$class = ($class == 'even') ? 'odd' : 'even';
	printf("<tr class='".$class."'><td align='center'><b>%s</b></td><td align='right'><b>%u</b></td><td align='right'><b>%u</b></td><td align='right'><b>%u</b></td><td align='right'><b>%u</b></td><td>&nbsp;</td>\n",_AM_NEWS_STATS2,$totals[0],$totals[1],$totals[2],$totals[3]);
	echo '</table></div><br /><br /><br />';

	// Second part of the stats, everything about stories
	// a) Most readed articles
	$mostreadednews=$stats['mostreadednews'];
	echo "<div style='text-align: center;'><b>" . _AM_NEWS_STATS3 . '</b><br /><br />' . _AM_NEWS_STATS4 . "<br />\n";
	echo "<table border='0' width='100%'><tr class='bg3'><td align='center'>"._AM_TOPIC."</td><td align='center'>" . _AM_TITLE . "</td><td>" . _AM_POSTER . "</td><td>" . _NW_VIEWS . "</td></tr>\n";
	foreach ( $mostreadednews as $storyid => $data ) {
		$url1=ZAR_URL . '/addons/' . $zariliaAddon->getVar('dirname') . '/index.php?storytopic=' . $data['topicid'];
		$url2=ZAR_URL . '/addons/' . $zariliaAddon->getVar('dirname')  . '/article.php?storyid=' . $storyid;
		$url3=ZAR_URL . '/index.php?page_type=userinfo&uid=' . $data['uid'];
		$class = ($class == 'even') ? 'odd' : 'even';
		printf("<tr class='".$class."'><td align='left'><a href='%s' target ='_blank'>%s</a></td><td align='left'><a href='%s' target='_blank'>%s</a></td><td><a href='%s' target='_blank'>%s</a></td><td align='right'>%u</td></tr>\n",$url1,$myts->displayTarea($data['topic_title']),$url2,$myts->displayTarea($data['title']),$url3,$myts->htmlSpecialChars($news->uname($data['uid'])),$data['counter']);
	}
	echo '</table>';

	// b) Less readed articles
	$lessreadednews=$stats['lessreadednews'];
	echo '<br /><br />'._AM_NEWS_STATS5;
	echo "<table border='0' width='100%'><tr class='bg3'><td align='center'>"._AM_TOPIC."</td><td align='center'>" . _AM_TITLE . "</td><td>" . _AM_POSTER . "</td><td>" . _NW_VIEWS . "</td></tr>\n";
	foreach ( $lessreadednews as $storyid => $data ) {
		$url1=ZAR_URL . '/addons/' . $zariliaAddon->getVar('dirname')  . '/index.php?storytopic=' . $data['topicid'];
		$url2=ZAR_URL . '/addons/' . $zariliaAddon->getVar('dirname') . '/article.php?storyid=' . $storyid;
		$url3=ZAR_URL . '/index.php?page_type=userinfo&uid=' . $data['uid'];
		$class = ($class == 'even') ? 'odd' : 'even';
		printf("<tr class='".$class."'><td align='left'><a href='%s' target ='_blank'>%s</a></td><td align='left'><a href='%s' target='_blank'>%s</a></td><td><a href='%s' target='_blank'>%s</a></td><td align='right'>%u</td></tr>\n",$url1,$myts->displayTarea($data['topic_title']),$url2,$myts->displayTarea($data['title']),$url3,$myts->htmlSpecialChars($news->uname($data['uid'])),$data['counter']);
	}
	echo '</table>';

	// c) Best rated articles (this is an average)
	$besratednews=$stats['besratednews'];
	echo '<br /><br />'._AM_NEWS_STATS6;
	echo "<table border='0' width='100%'><tr class='bg3'><td align='center'>"._AM_TOPIC."</td><td align='center'>" . _AM_TITLE . "</td><td>" . _AM_POSTER . "</td><td>" . _NW_RATING . "</td></tr>\n";
	foreach ( $besratednews as $storyid => $data ) {
		$url1=ZAR_URL . '/addons/' . $zariliaAddon->getVar('dirname')  . '/index.php?storytopic=' . $data['topicid'];
		$url2=ZAR_URL . '/addons/' . $zariliaAddon->getVar('dirname')  . '/article.php?storyid=' . $storyid;
		$url3=ZAR_URL . '/index.php?page_type=userinfo&uid=' . $data['uid'];
		$class = ($class == 'even') ? 'odd' : 'even';
		printf("<tr class='".$class."'><td align='left'><a href='%s' target ='_blank'>%s</a></td><td align='left'><a href='%s' target='_blank'>%s</a></td><td><a href='%s' target='_blank'>%s</a></td><td align='right'>%s</td></tr>\n",$url1,$myts->displayTarea($data['topic_title']),$url2,$myts->displayTarea($data['title']),$url3,$myts->htmlSpecialChars($news->uname($data['uid'])),number_format($data['rating'], 2));
	}
	echo '</table></div><br /><br /><br />';


	// Last part of the stats, everything about authors
	// a) Most readed authors
	$mostreadedauthors=$stats['mostreadedauthors'];
	echo "<div style='text-align: center;'><b>" . _AM_NEWS_STATS10 . '</b><br /><br />' . _AM_NEWS_STATS7 . "<br />\n";
	echo "<table border='0' width='100%'><tr class='bg3'><td>"._AM_POSTER.'</td><td>' . _NW_VIEWS . "</td></tr>\n";
	foreach ( $mostreadedauthors as $uid => $reads) {
		$url=ZAR_URL . '/index.php?page_type=userinfo&uid=' . $uid;
		$class = ($class == 'even') ? 'odd' : 'even';
		printf("<tr class='".$class."'><td align='left'><a href='%s' target ='_blank'>%s</a></td><td align='right'>%u</td></tr>\n",$url,$myts->htmlSpecialChars($news->uname($uid)),$reads);
	}
	echo '</table>';

    // b) Best rated authors
	$bestratedauthors=$stats['bestratedauthors'];
	echo '<br /><br />'._AM_NEWS_STATS8;
	echo "<table border='0' width='100%'><tr class='bg3'><td>"._AM_POSTER."</td><td>" . _NW_RATING . "</td></tr>\n";
	foreach ( $bestratedauthors as $uid => $rating) {
		$url=ZAR_URL . '/index.php?page_type=userinfo&uid=' . $uid;
		$class = ($class == 'even') ? 'odd' : 'even';
		printf("<tr class='".$class."'><td align='left'><a href='%s' target ='_blank'>%s</a></td><td align='right'>%u</td></tr>\n",$url,$myts->htmlSpecialChars($news->uname($uid)),$rating);
	}
	echo '</table>';

	// c) Biggest contributors
	$biggestcontributors=$stats['biggestcontributors'];
	echo '<br /><br />'._AM_NEWS_STATS9;
	echo "<table border='0' width='100%'><tr class='bg3'><td>"._AM_POSTER."</td><td>" . _AM_NEWS_STATS11 . "</td></tr>\n";
	foreach ( $biggestcontributors as $uid => $count) {
		$url=ZAR_URL . '/index.php?page_type=userinfo&uid=' . $uid;
		$class = ($class == 'even') ? 'odd' : 'even';
		printf("<tr class='".$class."'><td align='left'><a href='%s' target ='_blank'>%s</a></td><td align='right'>%u</td></tr>\n",$url,$myts->htmlSpecialChars($news->uname($uid)),$count);
	}
	echo '</table></div><br />';
}


/**
 * Metagen
 *
 * Metagen is a system that can help you to have your page best indexed by search engines.
 * Except if you type meta keywords and meta descriptions yourself, the module will automatically create them.
 * From here you can also manage some other options like the maximum number of meta keywords to create and
 * the keywords apparition's order.
 */
function Metagen()
{
	include_once ZAR_ROOT_PATH."/class/zariliaformloader.php";
    global $zariliaAddon, $zariliaConfig, $zariliaAddonConfig, $cfg;
    zarilia_cp_header();
    $myts = &MyTextSanitizer::getInstance();
	if (file_exists(ZAR_ROOT_PATH.'/addons/news/language/'.$zariliaConfig['language'].'/main.php')) {
		include_once ZAR_ROOT_PATH.'/addons/news/language/'.$zariliaConfig['language'].'/main.php';
	} else {
		include_once ZAR_ROOT_PATH.'/addons/news/language/english/main.php';
	}
    adminmenu(8);
    echo "<h1>"._AM_NEWS_METAGEN."</h1>";
	echo _AM_NEWS_METAGEN_DESC."<br /><br />";

	// Metagen Options
	$registry = new news_registryfile('news_metagen_options.txt');
	$content = '';
	$content = $registry->getfile();
	if(zarilia_trim($content) != '') {
		list($keywordscount, $keywordsorder) = explode(',',$content);
	} else {
		$keywordscount = $cfg['meta_keywords_count'];
		$keywordsorder = $cfg['meta_keywords_order'];
	}
	$sform = new ZariliaThemeForm(_OPTIONS, 'metagenoptions', ZAR_URL.'/addons/news/admin/index.php', 'post');
	$sform->addElement(new ZariliaFormHidden('op', 'metagenoptions'), false);
	$sform->addElement(new ZariliaFormText(_AM_NEWS_META_KEYWORDS_CNT, 'keywordscount', 4, 6, $keywordscount), true);
	$keywordsorder=new ZariliaFormRadio(_AM_NEWS_META_KEYWORDS_ORDER, 'keywordsorder', $keywordsorder);
	$keywordsorder->addOption(0,_AM_NEWS_META_KEYWORDS_INTEXT);
	$keywordsorder->addOption(1,_AM_NEWS_META_KEYWORDS_FREQ1);
	$keywordsorder->addOption(2,_AM_NEWS_META_KEYWORDS_FREQ2);
	$sform->addElement($keywordsorder, false);
	$button_tray = new ZariliaFormElementTray('' ,'');
	$submit_btn = new ZariliaFormButton('', 'post', _AM_MODIFY, 'submit');
	$button_tray->addElement($submit_btn);
	$sform->addElement($button_tray);
	$sform->display();

	// Blacklist
	$sform = new ZariliaThemeForm(_AM_NEWS_BLACKLIST, 'metagenblacklist', ZAR_URL.'/addons/news/admin/index.php', 'post');
	$sform->addElement(new ZariliaFormHidden('op', 'metagenblacklist'), false);

	// Remove words
	$remove_tray = new ZariliaFormElementTray(_AM_NEWS_BLACKLIST);
	$remove_tray->setDescription(_AM_NEWS_BLACKLIST_DESC);
	$blacklist=new ZariliaFormSelect('', 'blacklist','',5,true);
	$words = array();

	$metablack = new news_blacklist();
	$words = $metablack->getAllKeywords();
	if(is_array($words) && count($words)>0) {
		foreach ($words as $key => $value) {
			$blacklist->addOption($key,$value);
		}
	}

	$blacklist->setDescription(_AM_NEWS_BLACKLIST_DESC);
	$remove_tray->addElement($blacklist,false);
	$remove_btn = new ZariliaFormButton('', 'go', _AM_DELETE, 'submit');
	$remove_tray->addElement($remove_btn,false);
	$sform->addElement($remove_tray);

	// Add some words
	$add_tray = new ZariliaFormElementTray(_AM_NEWS_BLACKLIST_ADD);
	$add_tray->setDescription(_AM_NEWS_BLACKLIST_ADD_DSC);
	$add_field = new ZariliaFormTextArea('', 'keywords', '', 5, 70);
	$add_tray->addElement($add_field,false);
	$add_btn = new ZariliaFormButton('', 'go', _AM_ADD, 'submit');
	$add_tray->addElement($add_btn,false);
	$sform->addElement($add_tray);
	$sform->display();
}


/**
 * Save metagen's blacklist words
 */
function MetagenBlackList()
{
	$blacklist = new news_blacklist();
	$words = $blacklist->getAllKeywords();

	if(isset($_POST['go']) && $_POST['go'] == _AM_DELETE) {
		foreach($_POST['blacklist'] as $black_id) {
			$blacklist->delete($black_id);
		}
		$blacklist->store();
	} else {
		if(isset($_POST['go']) && $_POST['go']==_AM_ADD) {
			$p_keywords = $_POST['keywords'];
			$keywords = explode("\n",$p_keywords);
			foreach($keywords as $keyword) {
				if(zarilia_trim($keyword)!='') {
					$blacklist->addkeywords(zarilia_trim($keyword));
				}
			}
			$blacklist->store();
		}
	}
	redirect_header( 'index.php?op=metagen', 0, _AM_DBUPDATED);
}


/**
 * Save Metagen Options
 */
function MetagenSaveOptions()
{
	$registry = new news_registryfile('news_metagen_options.txt');
	$registry->savefile(intval($_POST['keywordscount']).','.intval($_POST['keywordsorder']));
	redirect_header('index.php?op=metagen', 0, _AM_DBUPDATED);
}



// **********************************************************************************************************************************************
// **** Main
// **********************************************************************************************************************************************
$op = 'default';
if(isset($_POST['op'])) {
 $op=$_POST['op'];
} elseif(isset($_GET['op'])) {
	$op=$_GET['op'];
}


switch ($op) {
	case 'deletefile':
		zarilia_cp_header();
		if($_GET['type']=='newsletter')	{
			$newsfile=ZAR_UPLOAD_PATH.'/newsletter.txt';
			if(unlink($newsfile)) {
				redirect_header('index.php', 2, _AM_NEWS_DELETED_OK);
			} else {
				redirect_header('index.php', 2, _AM_NEWS_DELETED_PB);
			}
		} else {
			if($_GET['type']=='xml') {
				$xmlfile=ZAR_UPLOAD_PATH.'/stories.xml';
				if(unlink($xmlfile)) {
					redirect_header( 'index.php', 2, _AM_NEWS_DELETED_OK );
				} else {
					redirect_header( 'index.php', 2, _AM_NEWS_DELETED_PB );
				}
			}
		}
		break;

    case 'newarticle':
        zarilia_cp_header();
        adminmenu(1);
        echo '<h4>' . _AM_CONFIG . '</h4>';
        include_once ZAR_ROOT_PATH . '/class/class.textsanitizer.php';
        newSubmissions();
        autoStories();
        lastStories();
        expStories();
        echo '<br />';
        echo '<h4>' . _AM_POSTNEWARTICLE . '</h4>';
        $type = 'admin';
        $title = '';
        $topicdisplay = 0;
        $topicalign = 'R';
        $ihome = 0;
        $hometext = '';
        $bodytext = '';
        $notifypub = 1;
        $nohtml = 0;
        $approve = 0;
        $nosmiley = 0;
	    $autodate = '';
	    $expired = '';
	    $topicid = 0;
	    $returnside=1;
	    $published = 0;
	    $description='';
	    $keywords='';
        if (file_exists(ZAR_ROOT_PATH.'/addons/news/language/'.$zariliaConfig['language'].'/main.php')) {
            include_once ZAR_ROOT_PATH.'/addons/news/language/'.$zariliaConfig['language'].'/main.php';
        } else {
            include_once ZAR_ROOT_PATH.'/addons/news/language/english/main.php';
        }

		if($zariliaAddonConfig['autoapprove'] == 1) {
			$approve=1;
		}
        $approveprivilege = 1;
        include_once ZAR_ROOT_PATH.'/addons/news/include/storyform.inc.php';
        break;

    case 'delete':
       	$storyid=0;
       	if(isset($_GET['storyid'])) {
			$storyid=intval($_GET['storyid']);
       	} elseif(isset($_POST['storyid'])) {
   			$storyid=intval($_POST['storyid']);
       	}

        if (!empty($_POST['ok'])) {
            if (empty($storyid)) {
                redirect_header( 'index.php?op=newarticle', 2, _AM_EMPTYNODELETE );
                exit();
            }
            $story = new NewsStory($storyid);
            $story->delete();
			$sfiles = new sFiles();
			$filesarr=Array();
			$filesarr=$sfiles->getAllbyStory($storyid);
			if(count($filesarr)>0) {
				foreach ($filesarr as $onefile) {
					$onefile->delete();
				}
			}
            zarilia_comment_delete($zariliaAddon->getVar('mid'),$storyid);
            zarilia_notification_deletebyitem($zariliaAddon->getVar('mid'), 'story', $storyid);
            news_updateCache();
            redirect_header( 'index.php?op=newarticle', 1, _AM_DBUPDATED );
            exit();
        } else {
        	$story = new NewsStory($storyid);
            zarilia_cp_header();
            echo '<h4>' . _AM_CONFIG . '</h4>';
            zarilia_confirm(array('op' => 'delete', 'storyid' => $storyid, 'ok' => 1), 'index.php', _AM_RUSUREDEL .'<br />' . $story->title());
        }
        break;

    case 'topicsmanager':
        topicsmanager();
        break;

    case 'addTopic':
        addTopic();
        break;

    case 'delTopic':
        delTopic();
        break;

    case 'modTopicS':
        modTopicS();
        break;

    case 'edit':
		if (file_exists(ZAR_ROOT_PATH.'/addons/news/language/'.$zariliaConfig['language'].'/main.php')) {
			include_once ZAR_ROOT_PATH.'/addons/news/language/'.$zariliaConfig['language'].'/main.php';
		} else {
			include_once ZAR_ROOT_PATH.'/addons/news/language/english/main.php';
		}
		include_once ZAR_ROOT_PATH.'/addons/news/submit.php';
		break;

    case 'prune':
    	PruneManager();
    	break;

    case 'confirmbeforetoprune':
    	ConfirmBeforeToPrune();
    	break;

    case 'prunenews';
    	PruneNews();
    	break;

    case 'export':
    	NewsExport();
    	break;

    case 'launchexport':
    	LaunchExport();
    	break;

    case 'configurenewsletter':
    	Newsletter();
    	break;

    case 'launchnewsletter':
    	LaunchNewsletter();
    	break;

    case 'stats':
    	Stats();
    	break;

	case 'metagen':
		Metagen();
		break;

	case 'metagenoptions':
		MetagenSaveOptions();
		break;

	case 'metagenblacklist':
		MetagenBlackList();
		break;

    case 'verifydb':
    	zarilia_cp_header();
    	adminmenu();
		$tbllist = $zariliaDB->prefix('stories').','.$zariliaDB->prefix('topics').','.$zariliaDB->prefix('stories_files').','.$zariliaDB->prefix('stories_votedata');
		$zariliaDB->Execute("OPTIMIZE TABLE ".$tbllist);
		$zariliaDB->Execute("CHECK TABLE ".$tbllist);
		$zariliaDB->Execute("ANALYZE TABLE ".$tbllist);
		redirect_header( 'index.php', 3, _AM_DBUPDATED);
		exit;
    	break;

    case 'default':
    default:
        zarilia_cp_header();
        adminmenu(-1);
        if(!news_TableExists($zariliaDB->prefix('stories_votedata')) || !news_TableExists($zariliaDB->prefix('stories_files')) ) {
        	echo "<div align='center'>"._AM_NEWS_PLEASE_UPGRADE.'</div><br/><br />';
        }

        echo '<h4>' . _AM_CONFIG . '</h4>';
        echo"<table width='100%' border='0' cellspacing='1' class='outer'><tr><td class=\"odd\">";
        echo " - <b><a href='index.php?op=topicsmanager'>" . _AM_TOPICSMNGR . '</a></b>';
        echo "<br /><br />\n";
        echo " - <b><a href='index.php?op=newarticle'>" . _AM_PEARTICLES . "</a></b>\n";
        echo "<br /><br />\n";
        echo " - <b><a href='groupperms.php'>" . _AM_GROUPPERM . "</a></b>\n";
        echo "<br /><br />\n";
        echo " - <b><a href='" . ZAR_URL . '/addons/system/index.php?fct=preferences&amp;op=showmod&amp;mod=' . $zariliaAddon -> getVar( 'mid' ) . "'>" . _AM_GENERALCONF . "</a></b>";
        echo "<br /><br />\n";
        echo " - <b><a href='index.php?op=prune'>" . _AM_NEWS_PRUNENEWS . "</a></b>\n";
        echo "<br /><br />\n";
        echo " - <b><a href='index.php?op=export'>" . _AM_NEWS_EXPORT_NEWS . "</a></b>\n";
        echo "<br /><br />\n";
        echo " - <b><a href='index.php?op=configurenewsletter'>" . _AM_NEWS_NEWSLETTER . "</a></b>\n";
        echo "<br /><br />\n";
        echo " - <b><a href='index.php?op=stats'>" . _AM_NEWS_STATS . "</a></b>\n";
        echo "<br /><br />\n";
        echo " - <b><a href='index.php?op=metagen'>" . _AM_NEWS_METAGEN . "</a></b>\n";
        echo"</td></tr></table>";
        break;
}

zarilia_cp_footer();
?>