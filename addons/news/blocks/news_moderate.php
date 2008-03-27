<?php
// $Id: news_moderate.php,v 1.8 2004/09/01 17:48:07 hthouzard Exp $
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
if (!defined('ZAR_ROOT_PATH')) {
	die('Zarilia root path not defined');
}


/**
 * Dispay a block where news moderators can show news that need to be moderated.
 */
function b_news_topics_moderate() {
	include_once ZAR_ROOT_PATH.'/addons/news/class/class.newsstory.php';
	include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';
	$block = array();
	$dateformat=news_getmoduleoption('dateformat');
	$infotips=news_getmoduleoption('infotips');

    $storyarray = NewsStory :: getAllSubmitted(0, true, news_getmoduleoption('restrictindex'));
    if ( count( $storyarray ) > 0 )
    {
		$block['lang_story_title'] = _MB_TITLE;
		$block['lang_story_date'] = _MB_POSTED;
		$block['lang_story_author'] =_MB_POSTER;
		$block['lang_story_action'] =_MB_ACTION;
		$block['lang_story_topic'] =_MB_TOPIC;
		$myts = &MyTextSanitizer::getInstance();
        foreach( $storyarray as $newstory )
        {
            $title = $newstory -> title();
			$htmltitle='';
			if($infotips>0) {
				$story['infotips'] = news_make_infotips($newstory->hometext());
				$htmltitle=' title="'.$story['infotips'].'"';
			}

            if (!isset( $title ) || ($title == '')) {
                $linktitle = "<a href='" . ZAR_URL . "/addons/news/index.php?op=edit&amp;storyid=" . $newstory->storyid() . "' target='_blank'".$htmltitle.">" . _AD_NOSUBJECT . "</a>";
            } else {
                $linktitle = "<a href='" . ZAR_URL . "/addons/news/submit.php?op=edit&amp;storyid=" . $newstory->storyid() . "' target='_blank'".$htmltitle.">" . $title . "</a>";
            }
			$story=array();
            $story['title'] = $linktitle;
            $story['date'] = formatTimestamp($newstory->created(),$dateformat);
            $story['author'] = "<a href='" . ZAR_URL . "/index.php?page_type=userinfo&uid=" . $newstory -> uid() . "'>" . $newstory->uname() . "</a>";
            $story['action'] = "<a href='" . ZAR_URL . "/addons/news/admin/index.php?op=delete&amp;storyid=" . $newstory->storyid() . "'>" . _MB_DELETE . "</a>";
            $story['topic_title'] = $newstory->topic_title();
            $story['topic_color']= '#'.$myts->displayTarea($newstory->topic_color);
            $block['stories'][] = &$story;
            unset($story);
        }
    }
	return $block;
}

function b_news_topics_moderate_onthefly($options)
{
	$options = explode('|',$options);
	$block = & b_news_topics_moderate($options);

	$tpl = new ZariliaTpl();
	$tpl->assign('block', $block);
	$tpl->display('db:news_block_moderate.html');
}
?>