<?php
// $Id: class.newsstory.php,v 1.29 2004/09/02 17:04:08 hthouzard Exp $
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
// ------------------------------------------------------------------------- //
if (!defined('ZAR_ROOT_PATH')) {
	die('Zarilia root path not defined');
}

include_once ZAR_ROOT_PATH.'/class/zariliastory.php';
include_once ZAR_ROOT_PATH.'/include/comment_constants.php';
include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';

class NewsStory extends ZariliaStory
{
	var $newstopic;   	// ZariliaTopic object
	var $rating;		// News rating
  	var $votes;			// Number of votes
  	var $description;	// META, desciption
  	var $keywords;		// META, keywords
  	var $topic_imgurl;
  	var $topic_title;


	/**
 	* Constructor
 	*/
	function NewsStory($storyid=-1)
	{
		global $zariliaDB;
$this->db=&$zariliaDB;
		$this->table = $this->db->prefix('stories');
		$this->topicstable = $this->db->prefix('topics');
		if (is_array($storyid)) {
			$this->makeStory($storyid);
		} elseif($storyid != -1) {
			$this->getStory(intval($storyid));
		}
	}

	/**
 	* Returns the number of stories published before a date
 	*/
	function GetCountStoriesPublishedBefore($timestamp, $expired, $topicslist='')
	{
		global $zariliaDB;
		$db=&$zariliaDB;
		$sql = 'SELECT count(*) as cpt FROM '.$db->prefix('stories').' WHERE published <=' . $timestamp;
		if($expired) {
			$sql .=' AND (expired>0 AND expired<='.time().')';
		}
		if(strlen(trim($topicslist))>0) {
			$sql .=' AND topicid IN ('.$topicslist.')';
		}
		$result = $db->Execute($sql);
		$count = $result->FetchRow();
		return $count['cpt'];
	}


	/**
	 * Load the specified story from the database
	 */
	function getStory($storyid)
	{
		$sql = 'SELECT s.*, t.* FROM '.$this->table.' s, '.$this->db->prefix('topics').' t WHERE (storyid='.intval($storyid).') AND (s.topicid=t.topic_id)';
		$result = $this->db->Execute($sql);		
		$array = $result->FetchRow();
		$this->makeStory($array);
	}


	/**
 	* Delete stories that were published before a given date
 	*/
	function DeleteBeforeDate($timestamp, $expired, $topicslist='')
	{
		global $zariliaAddon;
		$mid= $zariliaAddon->getVar('mid');
		global $zariliaDB;
$db=&$zariliaDB;
		$prefix = $db->prefix('stories');
		$vote_prefix = $db->prefix('stories_votedata');
		$files_prefix = $db->prefix('stories_files');
		$sql = 'SELECT storyid FROM  '.$prefix.' WHERE published <=' . $timestamp;
		if($expired) {
			$sql .=' (AND expired>0 AND expired<='.time().')';
		}
		if(strlen(trim($topicslist))>0) {
			$sql .=' AND topicid IN ('.$topicslist.')';
		}
		$result = $db->Execute($sql);
		while ($myrow = $result->FetchRow()) {
			zarilia_comment_delete($mid, $myrow['storyid']);									// Delete comments
			zarilia_notification_deletebyitem($mid, 'story', $myrow['storyid']);				// Delete notifications
			$db->ExecuteF('DELETE FROM '.$vote_prefix.' WHERE storyid='.$myrow['storyid']);	// Delete votes
			// Remove files and records related to the files
			$result2 = $db->Execute('SELECT * FROM '.$files_prefix.' WHERE storyid='.$myrow['storyid']);
			while ($myrow2 = $result2->FetchRow()) {
				$name = ZAR_ROOT_PATH.'/uploads/'.$myrow2['downloadname'];
				if(file_exists($name)) {
					unlink($name);
				}
				$db->Execute('DELETE FROM '.$files_prefix.' WHERE fileid='.$myrow2['fileid']);
			}
			$db->ExecuteF('DELETE FROM '.$prefix.' WHERE storyid='.$myrow['storyid']);		// Delete the story
		}
		return true;
	}


	/**
 	* Returns published stories according to some options
 	*/
	function getAllPublished($limit=0, $start=0, $checkRight=false, $topic=0, $ihome=0, $asobject=true, $order = 'published', $topic_frontpage=false)
	{
		global $zariliaDB;
$db=&$zariliaDB;
		$myts = &MyTextSanitizer::getInstance();
		$ret = array();
		$sql = 'SELECT s.*, t.* FROM '.$db->prefix('stories').' s, '. $db->prefix('topics').' t WHERE (s.published > 0 AND s.published <= '.time().') AND (s.expired = 0 OR s.expired > '.time().') AND (s.topicid=t.topic_id) ';
		if ($topic != 0) {
		    if (!is_array($topic)) {
		    	if($checkRight) {
        			$topics = news_MygetItemIds('news_view');
		    		if(!in_array ($topic,$topics)) {
		    			return null;
		    		} else {
		    			$sql .= ' AND s.topicid='.intval($topic).' AND (s.ihome=1 OR s.ihome=0)';
		    		}
		    	} else {
		        	$sql .= ' AND s.topicid='.intval($topic).' AND (s.ihome=1 OR s.ihome=0)';
		        }
		    } else {
				if($checkRight) {
					$topics = news_MygetItemIds('news_view');
		    		$topic = array_intersect($topic,$topics);
		    	}
		    	if(count($topic)>0) {
		        	$sql .= ' AND s.topicid IN ('.implode(',', $topic).')';
		    	} else {
		    		return null;
		    	}
		    }
		} else {
		    if($checkRight) {
		        $topics = news_MygetItemIds('news_view');
		        if(count($topics)>0) {
		        	$topics = implode(',', $topics);
		        	$sql .= ' AND s.topicid IN ('.$topics.')';
		        } else {
		        	return null;
		        }
		    }
			if (intval($ihome) == 0) {
				$sql .= ' AND s.ihome=0';
			}
		}
		if($topic_frontpage) {
			$sql .=' AND t.topic_frontpage=1';
		}
 		$sql .= " ORDER BY s.$order DESC";
		$result = $db->Execute($sql,intval($limit),intval($start));

		while ( $myrow = $result->FetchRow() ) {
			if ($asobject) {
				$ret[] = new NewsStory($myrow);
			} else {
				$ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
			}
		}
		return $ret;
	}


	/**
	 * Retourne la liste des articles aux archives (pour une période donnée)
	 */
	function getArchive($publish_start, $publish_end, $checkRight=false, $asobject=true, $order = 'published')
	{
		global $zariliaDB;
$db=&$zariliaDB;
		$myts = &MyTextSanitizer::getInstance();
		$ret = array();
		$sql = 'SELECT s.*, t.* FROM '.$db->prefix('stories').' s, ' .$db->prefix('topics').' t WHERE (s.topicid=t.topic_id) AND (s.published > ' . $publish_start . ' AND s.published <= ' . $publish_end . ') AND (expired = 0 OR expired > '.time().') ';

	    if($checkRight) {
	        $topics = news_MygetItemIds('news_view');
	        if(count($topics)>0) {
	        	$topics = implode(',', $topics);
	        	$sql .= ' AND topicid IN ('.$topics.')';
	        } else {
	        	return null;
	        }
	    }
 		$sql .= " ORDER BY $order DESC";
		$result = $db->Execute($sql);
		while ( $myrow = $result->FetchRow() ) {
			if ($asobject) {
				$ret[] = new NewsStory($myrow);
			} else {
				$ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
			}
		}
		return $ret;
	}


	/**
 	* Get the today's most readed article
 	*
 	* @param int 		$limit			records limit
 	* @param int 		$start 			starting record
 	* @param boolean	$checkRight		Do we need to check permissions (by topics) ?
	* @param int 		$topic 			limit the job to one topic
	* @param int 		$ihome 			Limit to articles published in home page only ?
	* @param boolean	$asobject		Do we have to return an array of objects or a simple array ?
	* @param string		$order			Fields to sort on
 	*/
	function getBigStory($limit=0, $start=0, $checkRight=false, $topic=0, $ihome=0, $asobject=true, $order = 'counter')
	{
		global $zariliaDB;
$db=&$zariliaDB;
		$myts = &MyTextSanitizer::getInstance();
		$ret = array();
		$tdate = mktime(0,0,0,date('n'),date('j'),date('Y'));
		$sql = 'SELECT s.*, t.* FROM '.$db->prefix('stories').' s, '. $db->prefix('topics').' t WHERE (s.topicid=t.topic_id) AND (published > '.$tdate.' AND published < '.time().') AND (expired > '.time().' OR expired = 0) ';

		if ( intval($topic) != 0 ) {
		    if (!is_array($topic)) {
		        $sql .= ' AND topicid='.intval($topic).' AND (ihome=1 OR ihome=0)';
		    }
		    else {
		    	if(count($topic)>0) {
		        	$sql .= ' AND topicid IN ('.implode(',', $topic).')';
		        } else {
		        	return null;
		        }
		    }
		} else {
		    if ($checkRight) {
		        $topics = news_MygetItemIds('news_view');
		        if(count($topics)>0) {
		        	$topics = implode(',', $topics);
		        	$sql .= ' AND topicid IN ('.$topics.')';
		        } else {
		        	return null;
		        }
		    }
			if ( intval($ihome) == 0 ) {
				$sql .= ' AND ihome=0';
			}
		}
 		$sql .= " ORDER BY $order DESC";
		$result = $db->Execute($sql,intval($limit),intval($start));
		while ( $myrow = $result->FetchRow() ) {
			if ( $asobject ) {
				$ret[] = new NewsStory($myrow);
			} else {
				$ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
			}
		}
		return $ret;
	}


	/**
	* Get all articles published by an author
	*
	* @param int $uid author's id
	* @param boolean $checkRight whether to check the user's rights to topics
	*/
	function getAllPublishedByAuthor($uid, $checkRight=false, $asobject=true)
	{
		global $zariliaDB;
$db=&$zariliaDB;
		$myts = &MyTextSanitizer::getInstance();
		$ret = array();
		$tblstory=$db->prefix('stories');
		$tbltopics=$db->prefix('topics');

		$sql = 'SELECT ' . $tblstory . '.*, '. $tbltopics . '.topic_title, '.$tbltopics.'.topic_color FROM '.$tblstory.','.$tbltopics .' WHERE ('.$tblstory.'.topicid='.$tbltopics.'.topic_id) AND (published > 0 AND published <= '.time().') AND (expired = 0 OR expired > '.time().')';
		$sql .= ' AND uid='.intval($uid);
	    if ($checkRight) {
	        $topics = news_MygetItemIds('news_view');
	        $topics = implode(',', $topics);
	        if(zarilia_trim($topics)!='') {
	        	$sql .= ' AND topicid IN ('.$topics.')';
	        }
	    }
 		$sql .= ' ORDER BY '.$tbltopics.'.topic_title ASC, '.$tblstory.'.published DESC';
		$result = $db->Execute($sql);
		while ( $myrow = $result->FetchRow() )
		{
			if ( $asobject ) {
				$ret[] = new NewsStory($myrow);
			} else {
				if ( $myrow['nohtml'] ) {
					$html = 0;
				} else {
					$html = 1;
				}
				if ( $myrow['nosmiley'] ) {
					$smiley = 0;
				} else {
					$smiley = 1;
				}
				$ret[$myrow['storyid']] = array('title'=>$myts->displayTarea($myrow['title'],$html,$smiley,1),
												'topicid'=>intval($myrow['topicid']),
												'storyid'=>intval($myrow['storyid']),
												'hometext'=>$myts->displayTarea($myrow['hometext'],$html,$smiley,1),
												'counter'=>intval($myrow['counter']),
												'created'=>intval($myrow['created']),
												'topic_title'=>$myts->displayTarea($myrow['topic_title'],$html,$smiley,1),
												'topic_color'=>$myts->displayTarea($myrow['topic_color']),
												'published'=>intval($myrow['published']),
												'rating'=>(float)$myrow['rating'],
												'votes'=>intval($myrow['votes']));
			}
		}
		return $ret;
	}


	/**
	 * Get all expired stories
	 */
	function getAllExpired($limit=0, $start=0, $topic=0, $ihome=0, $asobject=true)
	{
		global $zariliaDB;
$db=&$zariliaDB;
		$myts = &MyTextSanitizer::getInstance();
		$ret = array();
		$sql = 'SELECT * FROM '.$db->prefix('stories').' WHERE expired <= '.time().' AND expired > 0';
		if ( !empty($topic) ) {
			$sql .= ' AND topicid='.intval($topic).' AND (ihome=1 OR ihome=0)';
		} else {
			if ( intval($ihome) == 0 ) {
				$sql .= ' AND ihome=0';
			}
		}

 		$sql .= ' ORDER BY expired DESC';
		$result = $db->Execute($sql,intval($limit),intval($start));
		while ( $myrow = $result->FetchRow() ) {
			if ($asobject) {
				$ret[] = new NewsStory($myrow);
			} else {
				$ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
			}
		}
		return $ret;
	}



	/**
	 * Returns an array of object containing all the news to be automatically published.
	 */
	function getAllAutoStory($limit=0, $asobject=true, $start=0)
	{
		global $zariliaDB;
$db=&$zariliaDB;
		$myts = &MyTextSanitizer::getInstance();
		$ret = array();
		$sql = 'SELECT * FROM '.$db->prefix('stories').' WHERE published > '.time().' ORDER BY published ASC';
		$result = $db->Execute($sql,intval($limit),intval($start));
		while ( $myrow = $result->FetchRow() ) {
			if ( $asobject ) {
				$ret[] = new NewsStory($myrow);
			} else {
				$ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
			}
		}
		return $ret;
	}

	/**
	* Get all submitted stories awaiting approval
	*
	* @param int $limit Denotes where to start the query
	* @param boolean $asobject true will returns the stories as an array of objects, false will return storyid => title
	* @param boolean $checkRight whether to check the user's rights to topics
	*/
	function getAllSubmitted($limit=0, $asobject=true, $checkRight = false, $start=0)
	{
		global $zariliaDB;
$db=&$zariliaDB;
		$myts = &MyTextSanitizer::getInstance();
		$ret = array();
		$criteria = new CriteriaCompo(new Criteria('published', 0));
		if ($checkRight) {
		    global $zariliaUser;
		    if (!is_object($zariliaUser)) {
		        return $ret;
		    }
		    $allowedtopics = news_MygetItemIds('news_approve');
		    $criteria2 = new CriteriaCompo();
		    foreach ($allowedtopics as $key => $topicid) {
		        $criteria2->add(new Criteria('topicid', $topicid), 'OR');
		    }
		    $criteria->add($criteria2);
		}
		$sql = 'SELECT s.*, t.* FROM '.$db->prefix('stories').' s, '.$db->prefix('topics').' t ';
		$sql .= ' '.$criteria->renderWhere().' AND (s.topicid=t.topic_id) ORDER BY created DESC';
		$result = $db->Execute($sql,intval($limit),intval($start));
		while ( $myrow = $result->FetchRow() ) {
			if ( $asobject ) {
				$ret[] = new NewsStory($myrow);
			} else {
				$ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
			}
		}
		return $ret;
	}


	/**
	 * Used in the module's admin to know the number of expired, automated or pubilshed news
	 *
  	 * @param int	$storytype	1=Expired, 2=Automated, 3=New submissions, 4=Last published stories
  	 * @param bool	$checkRight	verify permissions or not ?
	 */
	function getAllStoriesCount($storytype=1, $checkRight = false)
	{
		global $zariliaDB;
$db=&$zariliaDB;
		$sql = 'SELECT count(*) as cpt FROM '.$db->prefix('stories').' WHERE ';
		switch($storytype) {
			case 1:	// Expired
				$sql .='(expired <= '.time().' AND expired >0)';
				break;
			case 2:	// Automated
				$sql .='(published > '.time().')';
				break;
			case 3:	// New submissions
				$sql .='(published = 0)';
				break;
			case 4:	// Last published stories
				$sql .='(published > 0 AND published <= '.time().') AND (expired = 0 OR expired > '.time().')';
				break;
		}
		if($checkRight) {
	        $topics = news_MygetItemIds('news_view');
	        if(count($topics)>0) {
	        	$topics = implode(',', $topics);
	        	$sql .= ' AND topicid IN ('.$topics.')';
	        } else {
	        	return 0;
	        }
		}
		$result = $db->Execute($sql);
		$myrow = $result->FetchRow();
		return $myrow['cpt'];
	}


	/**
	 * Get a list of stories (as objects) related to a specific topic
	 */
	function getByTopic($topicid, $limit=0)
	{
		$ret = array();
		global $zariliaDB;
$db=&$zariliaDB;
		$sql = 'SELECT * FROM '.$db->prefix('stories').' WHERE topicid='.intval($topicid).' ORDER BY published DESC';
		$result = $db->Execute($sql, intval($limit), 0);
		while( $myrow = $result->FetchRow() ){
			$ret[] = new NewsStory($myrow);
		}
		return $ret;
	}


	/**
	 * Count the number of news published for a specific topic
	 */
	function countPublishedByTopic($topicid=0, $checkRight = false)
	{
		global $zariliaDB;
$db=&$zariliaDB;
		$sql = 'SELECT COUNT(*) cnt FROM '.$db->prefix('stories').' WHERE published > 0 AND published <= '.time().' AND (expired = 0 OR expired > '.time().')';
		if ( !empty($topicid) ) {
			$sql .= ' AND topicid='.intval($topicid);
		} else {
			$sql .= ' AND ihome=0';
			if ($checkRight) {
		        $topics = news_MygetItemIds('news_view');
		        if(count($topics)>0) {
		        	$topics = implode(',', $topics);
		        	$sql .= ' AND topicid IN ('.$topics.')';
		        } else {
		        	return null;
		        }
		    }
		}
		$result = $db->Execute($sql);
		$count = $result->FetchRow();
		return floatval($count['cnt']);
	}


	/**
	 * Internal function
	 */
	function adminlink()
	{
		$ret = "&nbsp;[ <a href='".ZAR_URL."/addons/news/submit.php?op=edit&amp;storyid=".$this->storyid()."'>"._EDIT."</a> | <a href='".ZAR_URL."/addons/news/admin/index.php?op=delete&amp;storyid=".$this->storyid()."'>"._DELETE."</a> ]&nbsp;";
		return $ret;
	}


	/**
	 * Get the topic image url
	 */
	function topic_imgurl($format='S')
	{
		if(trim($this->topic_imgurl)=='') {
			$this->topic_imgurl='blank.png';
		}
		$myts = &MyTextSanitizer::getInstance();
		switch($format){
			case 'S':
				$imgurl= $myts->displayTarea($this->topic_imgurl);
				break;
			case 'E':
				$imgurl = $myts->makeTboxData4Edit($this->topic_imgurl);
				break;
			case 'P':
				$imgurl = $myts->makeTboxData4Preview($this->topic_imgurl);
				break;
			case 'F':
				$imgurl = $myts->makeTboxData4PreviewInForm($this->topic_imgurl);
				break;
		}
		return $imgurl;
	}

	function topic_title($format='S')
	{
		$myts = &MyTextSanitizer::getInstance();
		switch($format){
			case 'S':
				$title = $myts->displayTarea($this->topic_title);
				break;
			case 'E':
				$title = $myts->makeTboxData4Edit($this->topic_title);
				break;
			case 'P':
				$title = $myts->makeTboxData4Preview($this->topic_title);
				break;
			case 'F':
				$title = $myts->makeTboxData4PreviewInForm($this->topic_title);
				break;
		}
		return $title;
	}

	function imglink()
	{
		$ret = '';
		if ($this->topic_imgurl() != '' && file_exists(ZAR_ROOT_PATH.'/addons/news/images/topics/'.$this->topic_imgurl())) {
			$ret = "<a href='".ZAR_URL."/addons/news/index.php?storytopic=".$this->topicid()."'><img src='".ZAR_URL."/addons/news/images/topics/".$this->topic_imgurl()."' alt='".$this->topic_title()."' hspace='10' vspace='10' align='".$this->topicalign()."' /></a>";
		}
		return $ret;
	}

	function textlink()
	{
		$ret = "<a href='".ZAR_URL."/addons/news/index.php?storytopic=".$this->topicid()."'>".$this->topic_title()."</a>";
		return $ret;
	}

	/**
	 * Function used to prepare an article to be showned
	 */
	function prepare2show($filescount)
	{
	    include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';
	    global $zariliaUser, $zariliaConfig, $zariliaAddonConfig;
	    $myts = &MyTextSanitizer::getInstance();
	    $infotips = news_getmoduleoption('infotips');
	    $story = array();
	    $story['id'] = $this->storyid();
	    $story['poster'] = $this->uname();
	    $story['author_name'] = $this->uname();
	    $story['author_uid'] = $this->uid();
	    if ( $story['poster'] != false ) {
	        $story['poster'] = "<a href='".ZAR_URL."/index.php?page_type=userinfo&uid=".$this->uid()."'>".$story['poster']."</a>";
	    } else {
			if($zariliaAddonConfig['displayname']!=3) {
				$story['poster'] = $zariliaConfig['anonymous'];
			}
	    }
		if ($zariliaAddonConfig['ratenews']) {
			$story['rating'] = number_format($this->rating(), 2);
			if ($this->votes == 1) {
				$story['votes'] = _NW_ONEVOTE;
			} else {
				$story['votes'] = sprintf(_NW_NUMVOTES,$this->votes);
			}
		}
	    $story['posttimestamp'] = $this->published();
	    $story['posttime'] = formatTimestamp($story['posttimestamp']);
		$story['topic_description'] = $myts->displayTarea($this->topic_description);

		$auto_summary = '';
		$tmp = '';
		$auto_summary = $this->auto_summary($this->bodytext(),$tmp);

	    $story['text'] = $this->hometext();
		$story['text'] = str_replace('[summary]', $auto_summary, $story['text']);

	    $introcount = strlen($story['text']);
	    $fullcount = strlen($this->bodytext());
	    $totalcount = $introcount + $fullcount;

	    $morelink = '';
	    if ( $fullcount > 1 ) {
	        $morelink .= '<a href="'.ZAR_URL.'/addons/news/article.php?storyid='.$this->storyid().'';
	        $morelink .= '">'._NW_READMORE.'</a>';
        	$morelink .= ' | '.sprintf(_NW_BYTESMORE,$totalcount);
	        if (ZAR_COMMENT_APPROVENONE != $zariliaAddonConfig['com_rule']) {
	            $morelink .= ' | ';
	        }
	    }
	    if (ZAR_COMMENT_APPROVENONE != $zariliaAddonConfig['com_rule']) {
	        $ccount = $this->comments();
	        $morelink .= '<a href="'.ZAR_URL.'/addons/news/article.php?storyid='.$this->storyid().'';
	        $morelink2 = '<a href="'.ZAR_URL.'/addons/news/article.php?storyid='.$this->storyid().'';
	        if ( $ccount == 0 ) {
	            $morelink .= '">'._NW_COMMENTS.'</a>';
	        } else {
	            if ( $fullcount < 1 ) {
	                if ( $ccount == 1 ) {
	                    $morelink .= '">'._NW_READMORE.'</a> | '.$morelink2.'">'._NW_ONECOMMENT.'</a>';
	                } else {
	                    $morelink .= '">'._NW_READMORE.'</a> | '.$morelink2.'">';
	                    $morelink .= sprintf(_NW_NUMCOMMENTS, $ccount);
	                    $morelink .= '</a>';
	                }
	            } else {
	                if ( $ccount == 1 ) {
	                    $morelink .= '">'._NW_ONECOMMENT.'</a>';
	                } else {
	                    $morelink .= '">';
	                    $morelink .= sprintf(_NW_NUMCOMMENTS, $ccount);
	                    $morelink .= '</a>';
	                }
	            }
	        }
	    }
	    $story['morelink'] = $morelink;
	    $story['adminlink'] = '';

	    $approveprivilege = 0;
	    if(news_is_admin_group()) {
	        $approveprivilege = 1;
	    }

	    if($zariliaAddonConfig['authoredit']==1 && (is_object($zariliaUser) && $zariliaUser->getVar('uid')==$this->uid())) {
	    	$approveprivilege = 1;
	    }
	    if ($approveprivilege) {
	        $story['adminlink'] = $this->adminlink();
	    }
	    $story['mail_link'] = 'mailto:?subject='.sprintf(_NW_INTARTICLE,$zariliaConfig['sitename']).'&amp;body='.sprintf(_NW_INTARTFOUND, $zariliaConfig['sitename']).':  '.ZAR_URL.'/addons/news/article.php?storyid='.$this->storyid();
	    $story['imglink'] = '';
	    $story['align'] = '';
	    if ( $this->topicdisplay() ) {
	        $story['imglink'] = $this->imglink();
	        $story['align'] = $this->topicalign();
	    }
		if($infotips>0) {
			$story['infotips'] = ' title="'.news_make_infotips($this->hometext()).'"';
		} else {
			$story['infotips'] = '';
		}
	    $story['title'] = "<a href='".ZAR_URL."/addons/news/article.php?storyid=".$this->storyid()."'".$story['infotips'].">".$this->title()."</a>";

	    $story['hits'] = $this->counter();
		if($filescount>0) {
			$story['files_attached']= true;
			$story['attached_link']="<a href='".ZAR_URL.'/addons/news/article.php?storyid='.$this->storyid()."' title='"._NW_ATTACHEDLIB."'><img src='".ZAR_URL.'/addons/news/images/attach.gif'."' title='"._NW_ATTACHEDLIB."'></a>";
		} else {
			$story['files_attached']= false;
			$story['attached_link']='';
		}
	    return $story;
	}

	/**
 	* Returns the user's name of the current story according to the module's option "displayname"
 	*/
	function uname($uid=0)
	{
		global $zariliaConfig;
		include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';
		static $tblusers = array();
		$option=-1;
		if($uid == 0) {
			$uid=$this->uid();
		}

		if(is_array($tblusers) && array_key_exists($uid,$tblusers)) {
			return 	$tblusers[$uid];
		}

		$option = news_getmoduleoption('displayname');
		if (!$option) {
			$option=1;
		}

		switch($option) {
			case 1:		// Username
				$tblusers[$uid]=ZariliaUser::getUnameFromId($uid);
				return $tblusers[$uid];

			case 2:		// Display full name (if it is not empty)
				$member_handler = &zarilia_gethandler('member');
				$thisuser = $member_handler->getUser($uid);
				if (is_object($thisuser)) {
					$return = $thisuser->getVar('name');
					if ($return == '') {
						$return=$thisuser->getVar('uname');
					}
				} else {
					$return=$zariliaConfig['anonymous'];
				}
				$tblusers[$uid]=$return;
				return $return;

			case 3:		// Nothing
				$tblusers[$uid]='';
				return '';
		}
	}

	/**
	* Function used to export news (in xml) and eventually the topics definitions
	* Warning, permissions are not exported !
	* @param int 		$fromdate 		Starting date
	* @param int 		$todate 		Ending date
	* @param string		$topiclist		If not empty, a list of topics to limit to
	* @param boolean	$usetopicsdef 	Should we also export topics definitions ?
	* @param boolean	$asobject		Return values as an object or not ?
	*/
	function NewsExport($fromdate, $todate, $topicslist='', $usetopicsdef=0, &$tbltopics, $asobject=true, $order = 'published')
	{
		$ret=Array();
		$myts = &MyTextSanitizer::getInstance();
		if($usetopicsdef) {	// We firt begin by exporting topics definitions
			// Before all we must know wich topics to export
			$sql = 'SELECT distinct topicid FROM '.$this->db->prefix('stories').' WHERE (published >=' . $fromdate . ' AND published <= ' . $todate .')';
			if(strlen(trim($topicslist))>0) {
				$sql .=' AND topicid IN ('.$topicslist.')';
			}
			$result = $this->db->Execute($sql);
			while ( $myrow = $result->FetchRow() ) {
				$tbltopics[]=$myrow['topicid'];
			}
		}

		// Now we can search for the stories
		$sql = 'SELECT s.*, t.* FROM '.$this->table.' s, '.$this->db->prefix('topics').' t WHERE (s.topicid=t.topic_id) AND (s.published >=' . $fromdate . ' AND s.published <= ' . $todate .')';
		if(strlen(trim($topicslist))>0) {
			$sql .=' AND topicid IN ('.$topicslist.')';
		}
		$sql .= " ORDER BY $order DESC";
		$result = $this->db->Execute($sql);
		while ($myrow = $result->FetchRow()) {
			if ($asobject) {
				$ret[] = new NewsStory($myrow);
			} else {
				$ret[$myrow['storyid']] = $myts->htmlSpecialChars($myrow['title']);
			}
		}
		return $ret;
	}


	/**
	 * Create or update an article
	 */
	function store($approved=false)
	{
		$myts = &MyTextSanitizer::getInstance();
		$counter = isset($this->counter) ? $this->counter : 0;
		$title =$myts->censorString($this->title);
		$title = $myts->addSlashes($title);
		$hostname=$myts->addSlashes($this->hostname);
		$type=$myts->addSlashes($this->type);
		$hometext =$myts->addSlashes($myts->censorString($this->hometext));
		$bodytext =$myts->addSlashes($myts->censorString($this->bodytext));
		$description =$myts->addSlashes($myts->censorString($this->description));
		$keywords =$myts->addSlashes($myts->censorString($this->keywords));
		$votes= intval($this->votes);
		$rating = (float)($this->rating);
		if (!isset($this->nohtml) || $this->nohtml != 1) {
			$this->nohtml = 0;
		}
		if (!isset($this->nosmiley) || $this->nosmiley != 1) {
			$this->nosmiley = 0;
		}
		if (!isset($this->notifypub) || $this->notifypub != 1) {
			$this->notifypub = 0;
		}
		if(!isset($this->topicdisplay) || $this->topicdisplay != 0) {
			$this->topicdisplay = 1;
		}
		$expired = !empty($this->expired) ? $this->expired : 0;
		if (!isset($this->storyid)) {
			//$newpost = 1;
			$newstoryid = $this->db->genId($this->table.'_storyid_seq');
			$created = time();
			$published = ( $this->approved ) ? intval($this->published) : 0;
			$sql = sprintf("INSERT INTO %s (storyid, uid, title, created, published, expired, hostname, nohtml, nosmiley, hometext, bodytext, counter, topicid, ihome, notifypub, story_type, topicdisplay, topicalign, comments, rating, votes, description, keywords) VALUES (%u, %u, '%s', %u, %u, %u, '%s', %u, %u, '%s', '%s', %u, %u, %u, %u, '%s', %u, '%s', %u, %u, %u, '%s', '%s')", $this->table, $newstoryid, intval($this->uid()), $title, $created, $published, $expired, $hostname, intval($this->nohtml()), intval($this->nosmiley()), $hometext, $bodytext, $counter, intval($this->topicid()), intval($this->ihome()), intval($this->notifypub()), $type, intval($this->topicdisplay()), $this->topicalign, intval($this->comments()), $rating, $votes, $description, $keywords);
		} else {
			$sql = sprintf("UPDATE %s SET title='%s', published=%u, expired=%u, nohtml=%u, nosmiley=%u, hometext='%s', bodytext='%s', topicid=%u, ihome=%u, topicdisplay=%u, topicalign='%s', comments=%u, rating=%u, votes=%u, uid=%u, description='%s', keywords='%s' WHERE storyid = %u", $this->table, $title, intval($this->published()), $expired, intval($this->nohtml()), intval($this->nosmiley()), $hometext, $bodytext, intval($this->topicid()), intval($this->ihome()), intval($this->topicdisplay()), $this->topicalign, intval($this->comments()), $rating, $votes, intval($this->uid()), $description, $keywords, intval($this->storyid()));
			$newstoryid = intval($this->storyid());
		}
		if (!$this->db->Execute($sql)) {
			return false;
		}
		if (empty($newstoryid)) {
			$newstoryid = $this->db->getInsertId();
			$this->storyid = $newstoryid;
		}
		return $newstoryid;
	}

	function rating()
	{
		return $this->rating;
	}

	function votes()
	{
		return $this->votes;
	}

	function Setdescription($data)
	{
		$this->description=$data;
	}

	function Setkeywords($data)
	{
		$this->keywords=$data;
	}

	function description($format='S')
	{
		$myts = &MyTextSanitizer::getInstance();
		switch(strtoupper($format)) {
			case 'S':
				$description= $myts->htmlSpecialChars($this->description);
				break;
			case 'P':
			case 'F':
				$description = $myts->htmlSpecialChars($myts->stripSlashesGPC($this->description));
				break;
			case 'E':
				$description = $myts->htmlSpecialChars($this->description);
				break;
		}
		return $description;
	}

	function keywords($format='S')
	{
		$myts = &MyTextSanitizer::getInstance();
		switch(strtoupper($format)) {
			case 'S':
				$keywords= $myts->htmlSpecialChars($this->keywords);
				break;
			case 'P':
			case 'F':
				$keywords = $myts->htmlSpecialChars($myts->stripSlashesGPC($this->keywords));
				break;
			case 'E':
				$keywords = $myts->htmlSpecialChars($this->keywords);
				break;
		}
		return $keywords;
	}

	/**
 	* Returns a random number of news
 	*/
	function getRandomNews($limit=0, $start=0, $checkRight=false, $topic=0, $ihome=0, $order='published', $topic_frontpage=false)
	{
		global $zariliaDB;
$db=&$zariliaDB;
		$ret = $rand_keys = $ret3 = array();
		$sql = 'SELECT storyid FROM '.$db->prefix('stories').' WHERE (published > 0 AND published <= '.time().') AND (expired = 0 OR expired > '.time().')';
		if ($topic != 0) {
		    if (!is_array($topic)) {
		    	if($checkRight) {
        			$topics = news_MygetItemIds('news_view');
		    		if(!in_array ($topic,$topics)) {
		    			return null;
		    		} else {
		    			$sql .= ' AND topicid='.intval($topic).' AND (ihome=1 OR ihome=0)';
		    		}
		    	} else {
		        	$sql .= ' AND topicid='.intval($topic).' AND (ihome=1 OR ihome=0)';
		        }
		    } else {
		    	if(count($topic)>0) {
		        	$sql .= ' AND topicid IN ('.implode(',', $topic).')';
		    	} else {
		    		return null;
		    	}
		    }
		} else {
		    if($checkRight) {
		        $topics = news_MygetItemIds('news_view');
		        if(count($topics)>0) {
		        	$topics = implode(',', $topics);
		        	$sql .= ' AND topicid IN ('.$topics.')';
		        } else {
		        	return null;
		        }
		    }
			if (intval($ihome) == 0) {
				$sql .= ' AND ihome=0';
			}
		}
		if($topic_frontpage) {
			$sql .=' AND t.topic_frontpage=1';
		}
 		$sql .= " ORDER BY $order DESC";
		$result = $db->Execute($sql);

		while ( $myrow = $result->FetchRow() ) {
			$ret[] = $myrow['storyid'];
		}
		$cnt=count($ret);
		if($cnt)	{
			srand ((double) microtime() * 10000000);
			if($limit>$cnt) {
				$limit=$cnt;
			}
			$rand_keys = array_rand($ret, $limit);
			if($limit>1) {
				for($i=0;$i<$limit;$i++) {
					$onestory=$ret[$rand_keys[$i]];
					$ret3[]= new NewsStory($onestory);
				}
			} else {
				$ret3[]= new NewsStory($ret[$rand_keys]);
			}
		}
		return $ret3;
	}



	/**
 	* Returns statistics about the stories and topics
 	*/
	function GetStats($limit)
	{
		$ret=array();
		global $zariliaDB;
		$db=&$zariliaDB;
		$tbls=$db->prefix('stories');
		$tblt=$db->prefix('topics');
		$tblf=$db->prefix('stories_files');

		global $zariliaDB;
		$db=&$zariliaDB;
		// Number of stories per topic, including expired and non published stories
		$ret2=array();
		$sql="SELECT count(s.storyid) as cpt, s.topicid, t.topic_title FROM $tbls s, $tblt t WHERE s.topicid=t.topic_id GROUP BY s.topicid ORDER BY t.topic_title";
		$result = $db->Execute($sql);
		while ($myrow = $result->FetchRow() ) {
			$ret2[$myrow['topicid']]=$myrow;
		}
		$ret['storiespertopic']=$ret2;
		unset($ret2);

		// Total of reads per topic
		$ret2=array();
		$sql="SELECT Sum(counter) as cpt, topicid FROM $tbls GROUP BY topicid ORDER BY topicid";
		$result = $db->Execute($sql);
		while ($myrow = $result->FetchRow() ) {
			$ret2[$myrow['topicid']]=$myrow['cpt'];
		}
		$ret['readspertopic']=$ret2;
		unset($ret2);

		// Attached files per topic
		$ret2=array();
		$sql="SELECT Count(*) as cpt, s.topicid FROM $tblf f, $tbls s WHERE f.storyid=s.storyid GROUP BY s.topicid ORDER BY s.topicid";
		$result = $db->Execute($sql);
		while ($myrow = $result->FetchRow() ) {
			$ret2[$myrow['topicid']]=$myrow['cpt'];
		}
		$ret['filespertopic']=$ret2;
        unset($ret2);

		// Expired articles per topic
		$ret2=array();
		$sql="SELECT Count(storyid) as cpt, topicid FROM $tbls WHERE expired>0 AND expired<=".time()." GROUP BY topicid ORDER BY topicid";
		$result = $db->Execute($sql);
		while ($myrow = $result->FetchRow() ) {
			$ret2[$myrow['topicid']]=$myrow['cpt'];
		}
		$ret['expiredpertopic']=$ret2;
		unset($ret2);

		// Number of unique authors per topic
		$ret2=array();
		$sql="SELECT Count(Distinct(uid)) as cpt, topicid FROM $tbls GROUP BY topicid ORDER BY topicid";
		$result = $db->Execute($sql);
		while ($myrow = $result->FetchRow() ) {
			$ret2[$myrow['topicid']]=$myrow['cpt'];
		}
		$ret['authorspertopic']=$ret2;
		unset($ret2);

		// Most readed articles
		$ret2=array();
		$sql="SELECT s.storyid, s.uid, s.title, s.counter, s.topicid, t.topic_title  FROM $tbls s, $tblt t WHERE s.topicid=t.topic_id ORDER BY s.counter DESC";
		$result = $db->Execute($sql,intval($limit));
		while ($myrow = $result->FetchRow() ) {
			$ret2[$myrow['storyid']]=$myrow;
		}
		$ret['mostreadednews']=$ret2;
		unset($ret2);

		// Less readed articles
		$ret2=array();
		$sql="SELECT s.storyid, s.uid, s.title, s.counter, s.topicid, t.topic_title  FROM $tbls s, $tblt t WHERE s.topicid=t.topic_id ORDER BY s.counter";
		$result = $db->Execute($sql,intval($limit));
		while ($myrow = $result->FetchRow() ) {
			$ret2[$myrow['storyid']]=$myrow;
		}
		$ret['lessreadednews']=$ret2;
		unset($ret2);

		// Best rated articles
		$ret2=array();
		$sql="SELECT s.storyid, s.uid, s.title, s.rating, s.topicid, t.topic_title  FROM $tbls s, $tblt t WHERE s.topicid=t.topic_id ORDER BY s.rating DESC";
		$result = $db->Execute($sql,intval($limit));
		while ($myrow = $result->FetchRow() ) {
			$ret2[$myrow['storyid']]=$myrow;
		}
		$ret['besratednews']=$ret2;
		unset($ret2);

		// Most readed authors
		$ret2=array();
		$sql="SELECT Sum(counter) as cpt, uid FROM $tbls GROUP BY uid ORDER BY cpt DESC";
		$result = $db->Execute($sql,intval($limit));
		while ($myrow = $result->FetchRow() ) {
			$ret2[$myrow['uid']]=$myrow['cpt'];
		}
		$ret['mostreadedauthors']=$ret2;
		unset($ret2);

		// Best rated authors
		$ret2=array();
		$sql="SELECT Avg(rating) as cpt, uid FROM $tbls WHERE votes > 0 GROUP BY uid ORDER BY cpt DESC";
		$result = $db->Execute($sql,intval($limit));
		while ($myrow = $result->FetchRow() ) {
			$ret2[$myrow['uid']]=$myrow['cpt'];
		}
		$ret['bestratedauthors']=$ret2;
		unset($ret2);

		// Biggest contributors
		$ret2=array();
		$sql="SELECT Count(*) as cpt, uid FROM $tbls GROUP BY uid ORDER BY cpt DESC";
		$result = $db->Execute($sql,intval($limit));
		while ($myrow = $result->FetchRow() ) {
			$ret2[$myrow['uid']]=$myrow['cpt'];
		}
		$ret['biggestcontributors']=$ret2;
		unset($ret2);

		return $ret;
	}


	/**
	 * Get the date of the older and most recent news
	 */
	function GetOlderRecentNews(&$older, &$recent)
	{
		global $zariliaDB;
$db=&$zariliaDB;
		$sql = 'SELECT min(published) as minpublish, max(published) as maxpublish FROM '.$db->prefix('stories');
		$result = $db->Execute($sql);
		if(!$result) {
			$older = $recent = 0;
		} else {
			list($older, $recent) = $result->FetchRow();
		}
	}


	/*
	 * Returns the author's IDs for the Who's who page
	 */
	function getWhosWho($checkRight=false, $limit=0, $start=0)
	{
		global $zariliaDB;
$db=&$zariliaDB;
		$ret = array();
		$sql = 'SELECT distinct(uid) as uid FROM '.$db->prefix('stories').' WHERE (published > 0 AND published <= '.time().') AND (expired = 0 OR expired > '.time().')';
	    if($checkRight) {
	        $topics = news_MygetItemIds('news_view');
	        if(count($topics)>0) {
	        	$topics = implode(',', $topics);
	        	$sql .= ' AND topicid IN ('.$topics.')';
	        } else {
	        	return null;
	        }
	    }
 		$sql .= " ORDER BY uid";
		$result = $db->Execute($sql);
		while ( $myrow = $result->FetchRow() ) {
			$ret[] = $myrow['uid'];
		}
		return $ret;
	}


	/**
	 * Returns the content of the summary and the titles requires for the list selector
	 */
	function auto_summary($text, &$titles)
	{
		$auto_summary = '';
		if(news_getmoduleoption('enhanced_pagenav')) {
	    	$expr_matches = array();
	    	$posdeb = preg_match_all('/(\[pagebreak:|\[pagebreak).*\]/iU', $text, $expr_matches);
	    	if(count($expr_matches) > 0) {
				$delimiters = $expr_matches[0];
				$arr_search = array('[pagebreak:', '[pagebreak', ']');
				$arr_replace = array('', '', '');
				$cpt = 1;
				if(isset($titles) && is_array($titles)) {
					$titles[] = strip_tags(sprintf(_NW_PAGE_AUTO_SUMMARY,1, $this->title()));
				}
				$item = "<a href='".ZAR_URL.'/addons/news/article.php?storyid='.$this->storyid()."&page=0'>".sprintf(_NW_PAGE_AUTO_SUMMARY,1, $this->title()).'</a><br />';
				$auto_summary .= $item;

				foreach($delimiters as $item) {
					$cpt++;
					$item = str_replace($arr_search, $arr_replace, $item);
					if(zarilia_trim($item) == '') {
						$item = $cpt;
					}
					$titles[] = strip_tags(sprintf(_NW_PAGE_AUTO_SUMMARY,$cpt, $item));
					$item = "<a href='".ZAR_URL.'/addons/news/article.php?storyid='.$this->storyid().'&page='.($cpt-1)."'>".sprintf(_NW_PAGE_AUTO_SUMMARY,$cpt, $item).'</a><br />';
					$auto_summary .= $item;
				}
    		}
		}
		return $auto_summary;
	}

	function hometext($format = 'Show')
	{
		$myts = &MyTextSanitizer::getInstance();
		$html = $smiley = $xcodes = 1;
		if ( $this->nohtml() ) {
			$html = 0;
		}
		if ( $this->nosmiley() ) {
			$smiley = 0;
		}
		switch ( $format ) {
		case 'Show':
			$hometext = $myts->displayTarea($this->hometext,$html,$smiley,$xcodes);
			$tmp = '';
			$auto_summary = $this->auto_summary($this->bodytext('Show'),$tmp);
			$hometext = str_replace('[summary]', $auto_summary, $hometext);
			break;
		case 'Edit':
			$hometext = $myts->htmlSpecialChars($this->hometext);
			break;
		case 'Preview':
			$hometext = $myts->previewTarea($this->hometext,$html,$smiley,$xcodes);
			break;
		case 'InForm':
			$hometext = $myts->htmlSpecialChars($this->hometext);
			break;
		}
		return $hometext;
	}

	function bodytext($format = 'Show')
	{
		$myts = &MyTextSanitizer::getInstance();
		$html = 1;
		$smiley = 1;
		$xcodes = 1;
		if ( $this->nohtml() ) {
			$html = 0;
		}
		if ( $this->nosmiley() ) {
			$smiley = 0;
		}
		switch ( $format ) {
		case 'Show':
			$bodytext = $myts->displayTarea($this->bodytext,$html,$smiley,$xcodes);
			$tmp = '';
			$auto_summary = $this->auto_summary($bodytext,$tmp);
			$bodytext = str_replace('[summary]', $auto_summary, $bodytext);
			break;
		case 'Edit':
			$bodytext = $myts->htmlSpecialChars($this->bodytext);
			break;
		case 'Preview':
			$bodytext = $myts->previewTarea($this->bodytext,$html,$smiley, $xcodes);
			break;
		case 'InForm':
			$bodytext = $this->bodytext;
			break;
		}
		return $bodytext;
	}
}
?>