<?php
// $Id: functions.php,v 1.5 2004/09/02 17:04:08 hthouzard Exp $
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

/**
 * Returns a module's option
 *
 * Return's a module's option (for the news module)
 *
 * @package News
 * @author Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 * @param string $option	module option's name
 */
function news_getmoduleoption($option, $repmodule='news')
{
	global $zariliaAddonConfig, $zariliaAddon;
	static $tbloptions= Array();
	if(is_array($tbloptions) && array_key_exists($option,$tbloptions)) {
		return $tbloptions[$option];
	}

	$retval = false;
	if (isset($zariliaAddonConfig) && (is_object($zariliaAddon) && $zariliaAddon->getVar('dirname') == $repmodule && $zariliaAddon->getVar('isactive'))) {
		if(isset($zariliaAddonConfig[$option])) {
			$retval= $zariliaAddonConfig[$option];
		}
	} else {
		$module_handler = &zarilia_gethandler('addon');
		$module = &$module_handler->getByDirname($repmodule);
		$config_handler = &zarilia_gethandler('config');
		if ($module) {
		    $moduleConfig = &$config_handler->getConfigsByCat(0, $module->getVar('mid'));
	    	if(isset($moduleConfig[$option])) {
	    		$retval= $moduleConfig[$option];
	    	}
		}
	}
	$tbloptions[$option]=$retval;
	return $retval;
}


/**
 * Updates rating data in item table for a given item
 *
 * @package News
 * @author Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 */
function news_updaterating($storyid)
{
	global $zariliaDB;
	$query = 'SELECT rating FROM '.$zariliaDB->prefix('stories_votedata').' WHERE storyid = '.$storyid;
	$voteresult = $zariliaDB->Execute($query);
	$votesDB = $zariliaDB->getRowsNum($voteresult);
	$totalrating = 0;
	while(list($rating)=$zariliaDB->fetchRow($voteresult)){
		$totalrating += $rating;
	}
	$finalrating = $totalrating/$votesDB;
	$finalrating = number_format($finalrating, 4);
	$sql = sprintf("UPDATE %s SET rating = %u, votes = %u WHERE storyid = %u", $zariliaDB->prefix('stories'), $finalrating, $votesDB, $storyid);
	$zariliaDB->Execute($sql);
}



/**
 * Internal function for permissions
 *
 * Returns a list of all the permitted topics Ids for the current user
 *
 * @return array $topics	Permitted topics Ids
 *
 * @package News
 * @author Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 */
function news_MygetItemIds($permtype='news_view')
{
	global $zariliaUser;
	static $tblperms = array();
	if(is_array($tblperms) && array_key_exists($permtype,$tblperms)) {
		return $tblperms[$permtype];
	}

   	$module_handler = &zarilia_gethandler('addon');
   	$newsModule = &$module_handler->getByDirname('news');
   	$groups = is_object($zariliaUser) ? $zariliaUser->getGroups() : ZAR_GROUP_ANONYMOUS;
   	$gperm_handler = &zarilia_gethandler('groupperm');
   	$topics = $gperm_handler->getItemIds($permtype, $groups, $newsModule->getVar('mid'));
   	$tblperms[$permtype] = $topics;
    return $topics;
}

function news_html2text($document)
{
	// PHP Manual:: function preg_replace
	// $document should contain an HTML document.
	// This will remove HTML tags, javascript sections
	// and white space. It will also convert some
	// common HTML entities to their text equivalent.

	$search = array ("'<script[^>]*?>.*?</script>'si",  // Strip out javascript
	                 "'<[\/\!]*?[^<>]*?>'si",          // Strip out HTML tags
	                 "'([\r\n])[\s]+'",                // Strip out white space
	                 "'&(quot|#34);'i",                // Replace HTML entities
	                 "'&(amp|#38);'i",
	                 "'&(lt|#60);'i",
	                 "'&(gt|#62);'i",
	                 "'&(nbsp|#160);'i",
	                 "'&(iexcl|#161);'i",
	                 "'&(cent|#162);'i",
	                 "'&(pound|#163);'i",
	                 "'&(copy|#169);'i",
	                 "'&#(\d+);'e");                    // evaluate as php

	$replace = array ("",
	                 "",
	                 "\\1",
	                 "\"",
	                 "&",
	                 "<",
	                 ">",
	                 " ",
	                 chr(161),
	                 chr(162),
	                 chr(163),
	                 chr(169),
	                 "chr(\\1)");

	$text = preg_replace($search, $replace, $document);
	return $text;
}


/**
 * Retreive an editor according to the module's option "form_options"
 */
function &news_getWysiwygForm($caption, $name, $value = '', $width = '100%', $height = '400px', $supplemental='')
{
	$editor = false;
	$x22=false;
	$xv=str_replace('Zarilia ','',ZARILIA_VERSION);
	if(substr($xv,2,1)=='2') {
		$x22=true;
	}

	$options['name'] = $name;
    $options['value'] = $value;
    $editor = new ZariliaFormEditor($caption, $GLOBALS['zariliaUser']->getVar( 'editor' ), $options );
	return $editor;

	$editor_configs=array();
	$editor_configs['name'] =$name;
	$editor_configs['value'] = $value;
	$editor_configs['rows'] = 35;
	$editor_configs['cols'] = 60;
	$editor_configs['width'] = '100%';
	$editor_configs['height'] = '400px';


	switch(strtolower(news_getmoduleoption('form_options'))) {
		case 'spaw':
			if(!$x22) {
				if (is_readable(ZAR_ROOT_PATH . '/class/spaw/formspaw.php'))	{
					include_once(ZAR_ROOT_PATH . '/class/spaw/formspaw.php');
					$editor = new ZariliaFormSpaw($caption, $name, $value);
				}
			} else {
				$editor = new ZariliaFormEditor($caption, 'spaw', $editor_configs);
			}
			break;

		case 'fck':
			if(!$x22) {
				if ( is_readable(ZAR_ROOT_PATH . '/class/fckeditor/formfckeditor.php'))	{
					include_once(ZAR_ROOT_PATH . '/class/fckeditor/formfckeditor.php');
					$editor = new ZariliaFormFckeditor($caption, $name, $value);
				}
			} else {
				$editor = new ZariliaFormEditor($caption, 'fckeditor', $editor_configs);
			}
			break;

		case 'htmlarea':
			if(!$x22) {
				if ( is_readable(ZAR_ROOT_PATH . '/class/htmlarea/formhtmlarea.php'))	{
					include_once(ZAR_ROOT_PATH . '/class/htmlarea/formhtmlarea.php');
					$editor = new ZariliaFormHtmlarea($caption, $name, $value);
				}
			} else {
				$editor = new ZariliaFormEditor($caption, 'htmlarea', $editor_configs);
			}
			break;

		case 'dhtml':
			if(!$x22) {
				$editor = new ZariliaFormDhtmlTextArea($caption, $name, $value, 10, 50, $supplemental);
			} else {
				$editor = new ZariliaFormEditor($caption, 'dhtmltextarea', $editor_configs);
			}
			break;

		case 'textarea':
			$editor = new ZariliaFormTextArea($caption, $name, $value);
			break;

		case 'tinyeditor':
			if ( is_readable(ZAR_ROOT_PATH.'/class/zariliaeditor/tinyeditor/formtinyeditortextarea.php')) {
				include_once ZAR_ROOT_PATH.'/class/zariliaeditor/tinyeditor/formtinyeditortextarea.php';
				$editor = new ZariliaFormTinyeditorTextArea(array('caption'=> $caption, 'name'=>$name, 'value'=>$value, 'width'=>'100%', 'height'=>'400px'));
			}
			break;

		case 'koivi':
			if(!$x22) {
				if ( is_readable(ZAR_ROOT_PATH . '/class/wysiwyg/formwysiwygtextarea.php')) {
					include_once(ZAR_ROOT_PATH . '/class/wysiwyg/formwysiwygtextarea.php');
					$editor = new ZariliaFormWysiwygTextArea($caption, $name, $value, '100%', '450px', '');
				}
			} else {
				$editor = new ZariliaFormEditor($caption, 'koivi', $editor_configs);
			}
			break;
		}
		return $editor;
}

/**
 * Internal function
 *
 * @package News
 * @author Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 */
function DublinQuotes($text) {
	return str_replace("\"", ' ',$text);
}


/**
 * Creates all the meta datas :
 * - For Mozilla/Netscape and Opera the site navigation's bar
 * - The Dublin's Core Metadata
 * - The link for Firefox 2 micro summaries
 * - The meta keywords
 * - The meta description
 *
 * @package News
 * @author Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 */
function news_CreateMetaDatas($story = null)
{
	global $zariliaConfig, $xoTheme, $zariliaTpl;
	$content = '';
	$myts = &MyTextSanitizer::getInstance();
	include_once ZAR_ROOT_PATH.'/addons/news/class/class.newstopic.php';

	/**
	 * Firefox and Opera Navigation's Bar
	 */
	if(news_getmoduleoption('sitenavbar')) {
		$content .= sprintf("<link rel=\"Home\" title=\"%s\" href=\"%s/\" />\n",$zariliaConfig['sitename'],ZAR_URL);
		$content .= sprintf("<link rel=\"Contents\" href=\"%s\" />\n",ZAR_URL.'/addons/news/index.php');
		$content .= sprintf("<link rel=\"Search\" href=\"%s\" />\n",ZAR_URL.'/search.php');
		$content .= sprintf("<link rel=\"Glossary\" href=\"%s\" />\n",ZAR_URL.'/addons/news/archive.php');
		$content .= sprintf("<link rel=\"%s\" href=\"%s\" />\n",$myts->htmlSpecialChars(_NW_SUBMITNEWS),ZAR_URL.'/addons/news/submit.php');
		$content .= sprintf("<link rel=\"alternate\" type=\"application/rss+xml\" title=\"%s\" href=\"%s/\" />\n",$zariliaConfig['sitename'],ZAR_URL.'/backend.php');

		// Create chapters
		include_once ZAR_ROOT_PATH.'/class/tree.php';
		include_once ZAR_ROOT_PATH.'/addons/news/class/class.newstopic.php';
		$xt = new NewsTopic();
		$allTopics = $xt->getAllTopics(news_getmoduleoption('restrictindex'));
		$topic_tree = new ZariliaObjectTree($allTopics, 'topic_id', 'topic_pid');
		$topics_arr = $topic_tree->getAllChild(0);
		foreach ($topics_arr as $onetopic) {
			$content .= sprintf("<link rel=\"Chapter\" title=\"%s\" href=\"%s\" />\n",$onetopic->topic_title(),ZAR_URL.'/addons/news/index.php?storytopic='.$onetopic->topic_id());
		}
	}

	/**
	 * Meta Keywords and Description
 	 * If you have set this module's option to 'yes' and if the information was entered, then they are rendered in the page else they are computed
 	 */
	$meta_keywords = '';
	if(isset($story) && is_object($story)) {
		if(zarilia_trim($story->keywords()) != '') {
			$meta_keywords = $story->keywords();
		} else {
			$meta_keywords = news_createmeta_keywords($story->hometext().' '.$story->bodytext());
		}
		if(zarilia_trim($story->description())!='') {
			$meta_description = $story->description();
		} else {
			$meta_description = strip_tags($story->title());
		}
		if(isset($xoTheme) && is_object($xoTheme)) {
			$xoTheme->addMeta( 'meta', 'keywords', $meta_keywords);
			$xoTheme->addMeta( 'meta', 'description', $meta_description);
		} elseif(isset($zariliaTpl) && is_object($zariliaTpl)) {	// Compatibility for old Xoops versions
			$zariliaTpl->addMeta('keywords',  $meta_keywords);
			$zariliaTpl->addMeta('description',  $meta_description);
		}
	}

	/**
	 * Dublin Core's meta datas
	 */
	if(news_getmoduleoption('dublincore') && isset($story) && is_object($story)) {
		$config_handler = &zarilia_gethandler('config');
		$zariliaConfigMetaFooter = &$config_handler->getConfigsByCat(ZAR_CONF_METAFOOTER);
		$content .= '<meta name="DC.Title" content="'.DublinQuotes($story->title())."\" />\n";
		$content .= '<meta name="DC.Creator" content="'.DublinQuotes($story->uname())."\" />\n";
		$content .= '<meta name="DC.Subject" content="'.DublinQuotes($meta_keywords)."\" />\n";
		$content .= '<meta name="DC.Description" content="'.DublinQuotes($story->title())."\" />\n";
		$content .= '<meta name="DC.Publisher" content="'.DublinQuotes($zariliaConfig['sitename'])."\" />\n";
		$content .= '<meta name="DC.Date.created" scheme="W3CDTF" content="'.date('Y-m-d',$story->created)."\" />\n";
		$content .= '<meta name="DC.Date.issued" scheme="W3CDTF" content="'.date('Y-m-d',$story->published)."\" />\n";
		$content .= '<meta name="DC.Identifier" content="'.ZAR_URL.'/addons/news/article.php?storyid='.$story->storyid()."\" />\n";
		$content .= '<meta name="DC.Source" content="'.ZAR_URL."\" />\n";
		$content .= '<meta name="DC.Language" content="'._LANGCODE."\" />\n";
		$content .= '<meta name="DC.Relation.isReferencedBy" content="'.ZAR_URL.'/addons/news/index.php?storytopic='.$story->topicid()."\" />\n";
		if(isset($zariliaConfigMetaFooter['meta_copyright'])) {
			$content .= '<meta name="DC.Rights" content="'.DublinQuotes($zariliaConfigMetaFooter['meta_copyright'])."\" />\n";
		}
	}

	/**
	 * Firefox 2 micro summaries
	 */
	if(news_getmoduleoption('firefox_microsummaries')) {
		$content .= sprintf("<link rel=\"microsummary\" href=\"%s\" />\n",ZAR_URL.'/addons/news/micro_summary.php');
	}

	if(isset($zariliaTpl) && is_object($zariliaTpl)) {
		$zariliaTpl->assign('zarilia_module_header', $content);
	}
}



/**
 * Create the meta keywords based on the content
 *
 * @package News
 * @author Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 */
function news_createmeta_keywords($content)
{
	include ZAR_ROOT_PATH.'/addons/news/config.php';
	include_once ZAR_ROOT_PATH.'/addons/news/class/blacklist.php';
	include_once ZAR_ROOT_PATH.'/addons/news/class/registryfile.php';

	if(!$cfg['meta_keywords_auto_generate']) {
		return '';
	}
	$registry = new news_registryfile('news_metagen_options.txt');
	$tcontent = '';
	$tcontent = $registry->getfile();
	if(zarilia_trim($tcontent) != '') {
		list($keywordscount, $keywordsorder) = explode(',',$tcontent);
	} else {
		$keywordscount = $cfg['meta_keywords_count'];
		$keywordsorder = $cfg['meta_keywords_order'];
	}

	$tmp = array();
	// Search for the "Minimum keyword length"
	if(isset($_SESSION['news_keywords_limit'])) {
		$limit = $_SESSION['news_keywords_limit'];
	} else {
		$config_handler = &zarilia_gethandler('config');
		$zariliaConfigSearch = &$config_handler->getConfigsByCat(ZAR_CONF_SEARCH);
		$limit = $zariliaConfigSearch['keyword_min'];
		$_SESSION['news_keywords_limit'] = $limit;
	}
	$myts = &MyTextSanitizer::getInstance();
	$content = str_replace ("<br />", " ", $content);
	$content= $myts->undoHtmlSpecialChars($content);
	$content= strip_tags($content);
	$content=strtolower($content);
	$search_pattern=array("&nbsp;","\t","\r\n","\r","\n",",",".","'",";",":",")","(",'"','?','!','{','}','[',']','<','>','/','+','-','_','\\','*');
	$replace_pattern=array(' ',' ',' ',' ',' ',' ',' ',' ','','','','','','','','','','','','','','','','','','','');
	$content = str_replace($search_pattern, $replace_pattern, $content);
	$keywords = explode(' ',$content);
	switch($keywordsorder) {
		case 0:	// Ordre d'apparition dans le texte
			$keywords = array_unique($keywords);
			break;
		case 1:	// Ordre de fréquence des mots
			$keywords = array_count_values($keywords);
			asort($keywords);
			$keywords = array_keys($keywords);
			break;
		case 2:	// Ordre inverse de la fréquence des mots
			$keywords = array_count_values($keywords);
			arsort($keywords);
			$keywords = array_keys($keywords);
			break;
	}
	// Remove black listed words
	$metablack = new news_blacklist();
	$words = $metablack->getAllKeywords();
	$keywords = $metablack->remove_blacklisted($keywords);

	foreach($keywords as $keyword) {
		if(strlen($keyword)>=$limit && !is_numeric($keyword)) {
			$tmp[] = $keyword;
		}
	}
	$tmp = array_slice($tmp, 0, $keywordscount);
	if(count($tmp) > 0) {
		return implode(',',$tmp);
	} else {
		if(!isset($config_handler) || !is_object($config_handler)) {
			$config_handler = &zarilia_gethandler('config');
		}
		$zariliaConfigMetaFooter = &$config_handler->getConfigsByCat(ZAR_CONF_METAFOOTER);
		if(isset($zariliaConfigMetaFooter['meta_keywords'])) {
			return $zariliaConfigMetaFooter['meta_keywords'];
		} else {
			return '';
		}
	}
}


/**
 * Remove module's cache
 *
 * @package News
 * @author Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
*/
function news_updateCache() {
	global $zariliaAddon;
	$folder = $zariliaAddon->getVar('dirname');
	$tpllist = array();
	include_once ZAR_ROOT_PATH.'/class/zariliablock.php';
	include_once ZAR_ROOT_PATH.'/class/template.php';
	$tplfile_handler = &zarilia_gethandler('tplfile');
	$tpllist = $tplfile_handler->find(null, null, null, $folder);
	$zariliaTpl = new ZariliaTpl();
	zarilia_template_clear_addon_cache($zariliaAddon->getVar('mid'));			// Clear module's blocks cache

	// Remove cache for each page.
	foreach ($tpllist as $onetemplate) {
		if( $onetemplate->getVar('tpl_type') == 'module' ) {
			// Note, I've been testing all the other methods (like the one of Smarty) and none of them run, that's why I have used this code
			$files_del = array();
			$files_del = glob(ZAR_CACHE_PATH.'/*'.$onetemplate->getVar('tpl_file').'*');
			if(count($files_del) >0) {
				foreach($files_del as $one_file) {
					unlink($one_file);
				}
			}
		}
	}
}

/**
 * Verify that a mysql table exists
 *
 * @package News
 * @author Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
*/
function news_TableExists($tablename)
{
	global $zariliaDB;
	$result=$zariliaDB->Execute("SHOW TABLES LIKE '$tablename'");
	return($result->NumRows() > 0);
}

/**
 * Verify that a field exists inside a mysql table
 *
 * @package News
 * @author Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
*/
function news_FieldExists($fieldname,$table)
{
	global $zariliaDB;
	$result=$zariliaDB->Execute("SHOW COLUMNS FROM	$table LIKE '$fieldname'");
	return($result->NumRows() > 0);
}

/**
 * Add a field to a mysql table
 *
 * @package News
 * @author Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 */
function news_AddField($field, $table)
{
	global $zariliaDB;
	$result=$zariliaDB->Execute('ALTER TABLE ' . $table . " ADD $field;");
	return $result;
}

/**
 * Verify that the current user is a member of the Admin group
 */
function news_is_admin_group()
{
	global $zariliaUser, $zariliaAddon;
	if(is_object($zariliaUser)) {
		if(in_array('1',$zariliaUser->getGroups())) {
			return true;
		} else {
			if($zariliaUser->isAdmin($zariliaAddon->getVar('mid'))) {
				return true;
			} else {
				return false;
			}
		}
	} else {
		return false;
	}
}


/**
 * Verify if the current "user" is a bot or not
 *
 * If you have a problem with this function, insert the folowing code just before the line if(isset($_SESSION['news_cache_bot'])) { :
 * return false;
 *
 * @package News
 * @author Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 */
function news_isbot()
{
	if(isset($_SESSION['news_cache_bot'])) {
		return $_SESSION['news_cache_bot'];
	} else {
		// Add here every bot you know separated by a pipe | (not matter with the upper or lower cases)
		// If you want to see the result for yourself, add your navigator's user agent at the end (mozilla for example)
		$botlist='AbachoBOT|Arachnoidea|ASPSeek|Atomz|cosmos|crawl25-public.alexa.com|CrawlerBoy Pinpoint.com|Crawler|DeepIndex|EchO!|exabot|Excalibur Internet Spider|FAST-WebCrawler|Fluffy the spider|GAIS Robot/1.0B2|GaisLab data gatherer|Google|Googlebot-Image|googlebot|Gulliver|ia_archiver|Infoseek|Links2Go|Lycos_Spider_(modspider)|Lycos_Spider_(T-Rex)|MantraAgent|Mata Hari|Mercator|MicrosoftPrototypeCrawler|Mozilla@somewhere.com|MSNBOT|NEC Research Agent|NetMechanic|Nokia-WAPToolkit|nttdirectory_robot|Openfind|Oracle Ultra Search|PicoSearch|Pompos|Scooter|Slider_Search_v1-de|Slurp|Slurp.so|SlySearch|Spider|Spinne|SurferF3|Surfnomore Spider|suzuran|teomaagent1|TurnitinBot|Ultraseek|VoilaBot|vspider|W3C_Validator|Web Link Validator|WebTrends|WebZIP|whatUseek_winona|WISEbot|Xenu Link Sleuth|ZyBorg';
		$botlist=strtoupper($botlist);
		$currentagent=strtoupper(zarilia_getenv('HTTP_USER_AGENT'));
		$retval=false;
		$botarray=explode('|',$botlist);
		foreach($botarray as $onebot) {
			if(strstr($currentagent,$onebot)) {
				$retval=true;
				break;
			}
		}
	}
	$_SESSION['news_cache_bot']=$retval;
	return $retval;
}


/**
 * Create an infotip
 *
 * @package News
 * @author Instant Zero (http://xoops.instant-zero.com)
 * @copyright (c) Instant Zero
 */
function news_make_infotips($text)
{
	$infotips = news_getmoduleoption('infotips');
	if($infotips>0) {
		$myts = &MyTextSanitizer::getInstance();
		return $myts->htmlSpecialChars(zarilia_substr(strip_tags($text),0,$infotips));
	}
}
?>