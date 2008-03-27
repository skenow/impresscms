<?php
// $Id: article.php,v 1.15 2004/09/02 17:04:07 hthouzard Exp $
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
 * Article's page
 *
 * This page is used to see an article (or story) and is mainly called from
 * the module's index page.
 *
 * If no story Id has been placed on the URL or if the story is not yet published
 * then the page will redirect user to the module's index.
 * If the user does not have the permissions to see the article, he is also redirected
 * to the module's index page but with a error message saying :
 *     "Sorry, you don't have the permission to access this area"
 *
 * Each time a page is seen, and only if we are on the first page, its counter of hits is
 * updated
 *
 * Each file(s) attached to the article is visible at the bottom of the article and can
 * be downloaded
 *
 * Notes :
 * - To create more than one page in your story, use the tag [pagebreak]
 * - If you are a module's admin, you have the possibility to see two links at the bottom
 *   of the article, "Edit & Delete"
 *
 * @package News
 * @author Xoops Modules Dev Team
 * @copyright (c) The Xoops Project - www.xoops.org
 *
 * Parameters received by this page :
 * @param int storyid	Id of the story we want to see
 * @param int page		page's number (in the case where there are more than one page)
 *
 * @page_title			Article's title - Topic's title - Module's name
 *
 * @template_name		news_article.html wich will call news_item.html
 *
 * Template's variables :
 * @template_var	string	pagenav	some links to navigate thru pages
 * @template_var	array 	story	Contains all the information about the story
 *									Structure :
 * @template_var					int		id			Story's ID
 * @template_var					string	posttime	Story's date of publication
 * @template_var					string	title		A link to go and see all the articles in the same topic and the story's title
 * @template_var					string	news_title	Just the news title
 * @template_var					string	topic_title	Just the topic's title
 * @template_var					string	text		Defined as "The scoop"
 * @template_var					string	poster		A link to see the author's profil and his name or "Anonymous"
 * @template_var					int		posterid	Author's uid (or 0 if it's an anonymous or a user wich does not exist any more)
 * @template_var					string	morelink	Never used ???? May be it could be deleted
 * @template_var					string	adminlink	A link to Edit or Delete the story or a blank string if you are not the module's admin
 * @template_var					string	topicid		News topic's Id
 * @template_var					string	topic_color	Topic's color
 * @template_var					string	imglink		A link to go and see the topic of the story with the topic's picture (if it exists)
 * @template_var					string	align		Topic's image alignement
 * @template_var					int		hits		Story's counter of visits
 * @template_var					string	mail_link	A link (with a mailto) to email the story's URL to someone
 * @template_var	string	lang_printerpage	Used in the link and picture to have a "printable version" (fixed text)
 * @template_var	string 	lang_on		Fixed text "On" ("published on")
 * @template_var	string	lang_postedby	Fixed text "Posted by"
 * @template_var	string	lang_reads	Fixed text "Reads"
 * @template_var	string	news_by_the_same_author_link	According the the module's option named "newsbythisauthor", it contains a link to see all the article's stories
 * @template_var	int		summary_count	Number of stories really visibles in the summary table
 * @template_var	boolean	showsummary	According to the module's option named "showsummarytable", this contains "True" of "False"
 * @template_var	array	summary	Contains the required information to create a summary table at the bottom of the article. Note, we use the module's option "storyhome" to determine the maximum number of stories visibles in this summary table
 * 									Structure :
 * @template_var					int		story_id		Story's ID
 * @template_var					string	story_title		Story's title
 * @template_var					int		story_hits		Counter of hits
 * @template_var					string	story_published	Story's date of creation
 * @template_var	string	lang_attached_files	Fixed text "Attached Files:"
 * @template_var	int		attached_files_count	Number of files attached to the story
 * @template_var	array	attached_files	Contains the list of all the files attached to the story
 *									Structure :
 * @template_var					int		file_id				File's ID
 * @template_var					string	visitlink			Link to download the file
 * @template_var					string	file_realname		Original filename (not the real one use to store the file but the one it have when it was on the user hard disk)
 * @template_var					string	file_attacheddate	Date to wich the file was attached to the story (in general that's equal to the article's creation date)
 * @template_var					string	file_mimetype		File's mime type
 * @template_var					string	file_downloadname	Real name of the file on the webserver's disk (changed by the module)
 * @template_var	boolean	nav_links	According to the module's option named "showprevnextlink" it contains "True" or "False" to know if we have to show two links to go to the previous and next article
 * @template_var	int		previous_story_id	Id of the previous story (according to the published date and to the perms)
 * @template_var	int		next_story_id		Id of the next story (according to the published date and to the perms)
 * @template_var	string	previous_story_title	Title of the previous story
 * @template_var	string	next_story_title		Title of the next story
 * @template_var	string	lang_previous_story		Fixed text "Previous article"
 * @template_var	string	lang_next_story			Fixed text "Next article"
 * @template_var	string	lang_other_story		Fixed text "Other articles"
 * @template_var	boolean	rates	To know if rating is enable or not
 * @template_var	string	lang_ratingc	Fixed text "Rating: "
 * @template_var	string	lang_ratethisnews	Fixed text "Rate this News"
 * @template_var	float	rating	Article's rating
 * @template_var	string	votes	"1 vote" or "X votes"
 * @template_var	string	topic_path	A path from the root to the current topic (of the current news)
 */
include_once '../../mainfile.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.newsstory.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.sfiles.php';
include_once ZAR_ROOT_PATH.'/class/zariliatree.php';
include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/class.newstopic.php';
include_once ZAR_ROOT_PATH.'/addons/news/class/keyhighlighter.class.php';
include_once ZAR_ROOT_PATH.'/addons/news/config.php';

$storyid = (isset($_GET['storyid'])) ? intval($_GET['storyid']) : 0;

if (empty($storyid)) {
    redirect_header(ZAR_URL.'/addons/news/index.php',2,_NW_NOSTORY);
    exit();
}

$myts = &MyTextSanitizer::getInstance();

// Not yet published
$article = new NewsStory($storyid);
if ( $article->published() == 0 || $article->published() > time() ) {
    redirect_header(ZAR_URL.'/addons/news/index.php', 2, _NW_NOTYETSTORY);
    exit();
}
// Expired
if ( $article->expired() != 0 && $article->expired() < time() ) {
    redirect_header(ZAR_URL.'/addons/news/index.php', 2, _NW_NOSTORY);
    exit();
}

$gperm_handler = &zarilia_gethandler('groupperm');
if (is_object($zariliaUser)) {
    $groups = $zariliaUser->getGroups();
} else {
	$groups = ZAR_GROUP_ANONYMOUS;
}
if (!$gperm_handler->checkRight('news_view', $article->topicid(), $groups, $zariliaAddon->getVar('mid'))) {
	redirect_header(ZAR_URL.'/addons/news/index.php', 3, _NOPERM);
	exit();
}

$storypage = isset($_GET['page']) ? intval($_GET['page']) : 0;
$dateformat = news_getmoduleoption('dateformat');
$hcontent='';

/**
 * update counter only when viewing top page and when you are not the author or an admin
 */
if (empty($_GET['com_id']) && $storypage == 0) {
	if(is_object($zariliaUser)) {
		if( ($zariliaUser->getVar('uid')==$article->uid()) || news_is_admin_group()) {
			// nothing ! ;-)
		} else {
    		$article->updateCounter();
    	}
    } else {
        $article->updateCounter();
	}
}
$zariliaOption['template_main'] = 'news_article.html';
include_once ZAR_ROOT_PATH.'/header.php';

$story['id'] = $storyid;
$story['posttime'] = formatTimestamp($article->published(),$dateformat);
$story['news_title'] = $article->title();
$story['title'] = $article->textlink().'&nbsp;:&nbsp;'.$article->title();
$story['topic_title'] = $article->textlink();


$story['text'] = $article->hometext();
$bodytext = $article->bodytext();

if (zarilia_trim($bodytext) != '') {
    $articletext = array();
	if(news_getmoduleoption('enhanced_pagenav')) {
	    $articletext = preg_split('/(\[pagebreak:|\[pagebreak)(.*)(\])/iU', $bodytext);
	    $arr_titles = array();
		$auto_summary = $article->auto_summary($bodytext, $arr_titles);
		$bodytext = str_replace('[summary]', $auto_summary, $bodytext);
		$articletext[$storypage] = str_replace('[summary]', $auto_summary, $articletext[$storypage]);
		$story['text'] = str_replace('[summary]', $auto_summary, $story['text']);
	} else {
		$articletext = explode('[pagebreak]', $bodytext);
	}

    $story_pages = count($articletext);

    if ($story_pages > 1) {
        include_once ZAR_ROOT_PATH.'/addons/news/include/pagenav.php';
        $pagenav = new ZariliaPageNav($story_pages, 1, $storypage, 'page', 'storyid='.$storyid);
        if(news_isbot()) { 		// A bot is reading the articles, we are going to show him all the links to the pages
        	$zariliaTpl->assign('pagenav', $pagenav->renderNav($story_pages));
        } else {
			if(news_getmoduleoption('enhanced_pagenav')) {
				$zariliaTpl->assign('pagenav', $pagenav->renderEnhancedSelect(true, $arr_titles));
			} else {
				$zariliaTpl->assign('pagenav', $pagenav->renderNav());
			}
    	}

        if ($storypage == 0) {
			if ($adds = news_getmoduleoption('advertisement')) {
				$story['text'] = $story['text'].'<br />'.$adds.'<br />'.$articletext[$storypage];
			} else {
	            $story['text'] = $story['text'].$articletext[$storypage];
			}
        } else {
            $story['text'] = $articletext[$storypage];
        }
    } else {
		if ($adds = news_getmoduleoption('advertisement')) {
			$story['text'] = $story['text'].'<br />'.$adds.'<br />'.$bodytext;
		} else {
			$story['text'] = $story['text'].$bodytext;
		}        
    }
}
// Publicité
$zariliaTpl->assign('advertisement', news_getmoduleoption('advertisement'));

// ****************************************************************************************************************
function my_highlighter ($matches) {
	$color = news_getmoduleoption('highlightcolor');
	if(substr($color,0,1)!='#') {
		$color='#'.$color;
	}
	return '<span style="font-weight: bolder; background-color: '.$color.';">' . $matches[0] . '</span>';
}

$highlight = false;
$highlight = news_getmoduleoption('keywordshighlight');

if($highlight && isset($_GET['keywords']))
{
	$keywords=$myts->htmlSpecialChars(trim(urldecode($_GET['keywords'])));
	$h= new keyhighlighter ($keywords, true , 'my_highlighter');
	$story['text'] = $h->highlight($story['text']);
}
// ****************************************************************************************************************

$story['poster'] = $article->uname();
if ( $story['poster'] ) {
    $story['posterid'] = $article->uid();
    $story['poster'] = '<a href="'.ZAR_URL.'/index.php?page_type=userinfo&uid='.$story['posterid'].'">'.$story['poster'].'</a>';
    $tmp_user = new ZariliaUser($article->uid());
    $story['poster_avatar'] = ZAR_UPLOAD_URL.'/'.$tmp_user->getVar('user_avatar');
    $story['poster_signature'] = $tmp_user->getVar('user_sig');
    $story['poster_email'] = $tmp_user->getVar('email');
    $story['poster_url'] = $tmp_user->getVar('url');
    $story['poster_from'] = $tmp_user->getVar('user_from');
    unset($tmp_user);
} else {
    $story['poster'] = '';
    $story['posterid'] = 0;
    $story['poster_avatar'] = '';
    $story['poster_signature'] = '';
    $story['poster_email'] = '';
    $story['poster_url'] = '';
    $story['poster_from'] = '';
    if(news_getmoduleoption('displayname')!=3) {
    	$story['poster'] = $zariliaConfig['anonymous'];
    }
}
$story['morelink'] = '';
$story['adminlink'] = '';
unset($isadmin);

if(is_object($zariliaUser)) {
	if( $zariliaUser->isAdmin($zariliaAddon->getVar('mid')) || (news_getmoduleoption('authoredit') && $article->uid() == $zariliaUser->getVar('uid')) ) {
    	$isadmin = true;
    	$story['adminlink'] = $article->adminlink();
    }
}
$story['topicid'] = $article->topicid();
$story['topic_color'] = '#'.$myts->displayTarea($article->topic_color);

$story['imglink'] = '';
$story['align'] = '';
if ( $article->topicdisplay() ) {
    $story['imglink'] = $article->imglink();
    $story['align'] = $article->topicalign();
}
$story['hits'] = $article->counter();
$story['mail_link'] = 'mailto:?subject='.sprintf(_NW_INTARTICLE,$zariliaConfig['sitename']).'&amp;body='.sprintf(_NW_INTARTFOUND, $zariliaConfig['sitename']).':  '.ZAR_URL.'/addons/news/article.php?storyid='.$article->storyid();
$zariliaTpl->assign('lang_printerpage', _NW_PRINTERFRIENDLY);
$zariliaTpl->assign('lang_sendstory', _NW_SENDSTORY);
$zariliaTpl->assign('lang_pdfstory', _NW_MAKEPDF);
$zariliaTpl->assign('lang_on', _ON);
$zariliaTpl->assign('lang_postedby', _POSTEDBY);
$zariliaTpl->assign('lang_reads', _READS);
$zariliaTpl->assign('mail_link', 'mailto:?subject='.sprintf(_NW_INTARTICLE,$zariliaConfig['sitename']).'&amp;body='.sprintf(_NW_INTARTFOUND, $zariliaConfig['sitename']).':  '.ZAR_URL.'/addons/news/article.php?storyid='.$article->storyid());

$zariliaTpl->assign('lang_attached_files',_NW_ATTACHEDFILES);
$sfiles = new sFiles();
$filesarr=Array();
$newsfiles=Array();
$filesarr=$sfiles->getAllbyStory($storyid);
$filescount=count($filesarr);
$zariliaTpl->assign('attached_files_count',$filescount);
if($filescount>0) {
	foreach ($filesarr as $onefile)	{
		$newsfiles[]=Array('file_id'=>$onefile->getFileid(), 'visitlink' => ZAR_URL.'/addons/news/visit.php?fileid='.$onefile->getFileid(),'file_realname'=>$onefile->getFileRealName(), 'file_attacheddate'=>formatTimestamp($onefile->getDate(),$dateformat), 'file_mimetype'=>$onefile->getMimetype(), 'file_downloadname'=>ZAR_UPLOAD_URL.'/'.$onefile->getDownloadname());
	}
	$zariliaTpl->assign('attached_files',$newsfiles);
}

/**
 * Create page's title
*/
$complement = '';
if(news_getmoduleoption('enhanced_pagenav') && (is_array($arr_titles) && isset($arr_titles,$storypage) && $storypage>0)) {
	$complement = ' - '.$arr_titles[$storypage];
}
$zariliaTpl->addTitle( $article->title() . $complement. ' - ' . $article->topic_title() . ' - ' . $myts->htmlSpecialChars($zariliaAddon->getVar('name')));

if(news_getmoduleoption('newsbythisauthor')) {
	$zariliaTpl->assign('news_by_the_same_author_link',sprintf("<a href='%s?uid=%d'>%s</a>",ZAR_URL.'/addons/news/newsbythisauthor.php',$article->uid(),_NW_NEWSSAMEAUTHORLINK));
}

/**
 * Create a clickable path from the root to the current topic (if we are viewing a topic)
 * Actually this is not used in the default's templates but you can use it as you want
 * Uncomment the code to be able to use it
 */
$mytree = new ZariliaTree($zariliaDB->prefix('topics'),'topic_id','topic_pid');
$topicpath = $mytree->getNicePathFromId($article->topicid(), 'topic_title', 'index.php?op=1');
$zariliaTpl->assign('topic_path', $topicpath);
unset($mytree);


/**
 * Summary table
 *
 * When you are viewing an article, you can see a summary table containing
 * the first n links to the last published news.
 * This summary table is visible according to a module's option (showsummarytable)
 * The number of items is equal to the module's option "storyhome" ("Select the number
 * of news items to display on top page")
 * We also use the module's option "restrictindex" ("Restrict Topics on Index Page"), like
 * this you (the webmaster) select if users can see restricted stories or not.
 */
if (news_getmoduleoption('showsummarytable')) {
	$zariliaTpl->assign('showsummary', true);
	$zariliaTpl->assign('lang_other_story',_NW_OTHER_ARTICLES);
	$count=0;
	$tmparticle = new NewsStory();
	$infotips=news_getmoduleoption('infotips');
	$sarray = $tmparticle->getAllPublished($cfg['article_summary_items_count'], 0, $zariliaAddonConfig['restrictindex']);
	if(count($sarray)>0) {
		foreach ($sarray as $onearticle) {
			$count++;
			$htmltitle='';
			$tooltips='';
			$htmltitle='';
			if($infotips>0) {
				$tooltips = news_make_infotips($onearticle->hometext());
				$htmltitle=' title="'.$tooltips.'"';
			}
 			$zariliaTpl->append('summary', array('story_id'=>$onearticle->storyid(), 'htmltitle'=>$htmltitle, 'infotips'=>$tooltips, 'story_title'=>$onearticle->title(), 'story_hits'=>$onearticle->counter(), 'story_published'=>formatTimestamp($onearticle->published,$dateformat)));
   		}
   	}
   	$zariliaTpl->assign('summary_count',$count);
	unset($tmparticle);
} else {
	$zariliaTpl->assign('showsummary', false);
}


/**
 * Show a link to go to the previous article and to the next article
 *
 * According to a module's option "showprevnextlink" ("Show Previous and Next link ?")
 * you can display, at the bottom of each article, two links used to navigate thru stories.
 * This feature uses the module's option "restrictindex" so that we can, or can't see
 * restricted stories
 */
if (news_getmoduleoption('showprevnextlink')) {
	$previous=-1;
	$next=-1;
	$lastread=-1;
	$warning=false;
	$previoustitle='';
	$nexttitle='';
	$lasttitle='';
	$zariliaTpl->assign('nav_links', true);
	$tmparticle = new NewsStory();
	$sarray = $tmparticle->getAllPublished(0, 0, $zariliaAddonConfig['restrictindex'],0,0,false);
	if(is_array($sarray)) {
		while(list($storyid, $storytitle) = each($sarray)) {
	   		if($warning) {
   				$next=$storyid;
   				$nexttitle=$storytitle;
   				$warning=false;
   			}

   			if($storyid==$article->storyid()) {
				if($previous==-1) {
					$previous=$lastread;
					$previoustitle=$lasttitle;
				}
				$warning=true;
   			}
   			$lastread=$storyid;
   			$lasttitle=$storytitle;
		}
	}

   	$zariliaTpl->assign('previous_story_id',$previous);
   	$zariliaTpl->assign('next_story_id',$next);
   	if($previous!=-1) {
		$previoustitle = $myts->displayTarea($previoustitle);
   		$zariliaTpl->assign('previous_story_title',$previoustitle);
   		$hcontent.=sprintf("<link rel=\"Prev\" title=\"%s\" href=\"%s/\" />\n",$previoustitle,ZAR_URL.'/addons/news/article.php?storyid='.$previous);
   	}

   	if($next!=-1) {
		$nexttitle = $myts->displayTarea($nexttitle);
   		$zariliaTpl->assign('next_story_title',$nexttitle);
   		$hcontent.=sprintf("<link rel=\"Next\" title=\"%s\" href=\"%s/\" />\n",$nexttitle,ZAR_URL.'/addons/news/article.php?storyid='.$next);
   	}
   	$zariliaTpl->assign('lang_previous_story',_NW_PREVIOUS_ARTICLE);
   	$zariliaTpl->assign('lang_next_story',_NW_NEXT_ARTICLE);
   	unset($tmparticle);
} else {
	$zariliaTpl->assign('nav_links', false);
}

/**
 * Manage all the meta datas
 */
news_CreateMetaDatas($article);



/**
 * Show a "Bookmark this article at these sites" block ?
 */
if(news_getmoduleoption('bookmarkme')) {
	$zariliaTpl->assign('bookmarkme', true);
	$zariliaTpl->assign('encoded_title', rawurlencode($article->title()));
} else {
	$zariliaTpl->assign('bookmarkme', false);
}


/**
* Enable users to vote
*
* According to a module's option, "ratenews", you can display a link to rate the current news
* The actual rate in showed (and the number of votes)
* Possible modification, restrict votes to registred users
*/
$other_test = true;
if($cfg['config_rating_registred_only']) {
	if(isset($zariliaUser) && is_object($zariliaUser)) {
		$other_test = true;
	} else {
		$other_test = false;
	}
}

if (news_getmoduleoption('ratenews') && $other_test) {
	$zariliaTpl->assign('rates', true);
	$zariliaTpl->assign('lang_ratingc', _NW_RATINGC);
	$zariliaTpl->assign('lang_ratethisnews', _NW_RATETHISNEWS);
	$story['rating'] = number_format($article->rating(), 2);
	if ($article->votes == 1) {
		$story['votes'] = _NW_ONEVOTE;
	} else {
		$story['votes'] = sprintf(_NW_NUMVOTES,$article->votes);
	}
} else {
	$zariliaTpl->assign('rates', false);
}

$zariliaTpl->assign('story', $story);

include_once ZAR_ROOT_PATH.'/include/comment_view.php';
include_once ZAR_ROOT_PATH.'/footer.php';
?>