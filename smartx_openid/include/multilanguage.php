<?php
/**
* $Id$
* Package : XOOPS Multilanguages
* Author: The SmartFactory <www.smartfactory.ca>
* Licence: GNU
*/

// XOOPS Multilanguage Version
if (!defined('XOOPS_ML_VERSION')) {
	define ('XOOPS_ML_VERSION', '2.0.6 Final');
}
if (!defined('XOOPS_ML_LINK')) {
	define ('XOOPS_ML_LINK', '<a href="http://smartfactory.ca/library.item.8/learn-more-about-xoops-multilanguages.html" target="_blank">Multilanguages ' . XOOPS_ML_VERSION . '</a>');
}
function check_language()
{
	global $xoopsConfig;

	// If user just switched language, do the change
	if ((!empty($_GET['sel_lang'])) && (validateLanguage($_GET['sel_lang']) == 1)) {
		$xoopsConfig['language'] = $_GET['sel_lang'];

		// Save this preference in a cookie, for when user is not logged in yet
		setcookie('selected_language', $_GET['sel_lang'], time()+3600*24*30, '/');

	} else {
		// No change of language occured. Retreive the selected language
		if ((isset($_COOKIE['selected_language'])) && ($_COOKIE['selected_language'] > '')) {
			$xoopsConfig['language'] = $_COOKIE['selected_language'];
		}
	}
}

// Validation to be sure that no malicious code is include in sel_lang
function validateLanguage($sel_lang = '')
{
	include_once XOOPS_ROOT_PATH."/class/xoopslists.php";
	$lang_available = XoopsLists::getLangList();
	$result = 0;
	If ( ($sel_lang != '') && (in_array($sel_lang, $lang_available)) ) {
		$result = 1;
	}
	return $result;
}

function some_ml_tasks() {

	include_once(XOOPS_ROOT_PATH."/class/xoopslists.php");

	global $xoopsConfig, $_SERVER, $xoopsTpl, $xoops_language_tags;

	$languages = XoopsLists::getLangList();
	// Hack by dAWiLbY to prevent too long query strings in your URL.
	if (empty($_SERVER['QUERY_STRING'])) {
		$pagenquery = $_SERVER['PHP_SELF'].'?sel_lang=';
	} elseif (isset($_SERVER['QUERY_STRING'])) {

		$query = explode("&",$_SERVER['QUERY_STRING']);

		$langquery = $_SERVER['QUERY_STRING'];

		// If the last parameter of the QUERY_STRING is sel_lang, delete it so we don't have repeating sel_lang=...
		If (strpos($query[count($query) - 1], 'sel_lang=')  === 0 ) {
			$langquery = str_replace( $query[count($query) - 1], '', $langquery);
		}
		$pagenquery = $_SERVER['PHP_SELF'].'?'.$langquery.'&sel_lang=';
		$pagenquery = str_replace('?&','?',$pagenquery);
		$pagenquery = str_replace('&&','&',$pagenquery);
		$pagenquery = str_replace('&','&amp;',$pagenquery);
	}
	// End of hack by dAWiLbY

	if ( is_array($languages) )
	{
		//show a list of flags to select language
		$imagelist = "";
		$i = 0;
		foreach ( $languages as $v=>$n )
		{
			$flag = '';
			$flag .= "<a href='" . $pagenquery . $v . "'>";

			$language_name = isset($xoops_language_tags[$n]) ? $xoops_language_tags[$n]->caption() : $n;
			if (file_exists(XOOPS_ROOT_PATH."/modules/smartlanguage/flags/$n.gif")) {
				$flag .= '<img src="' . XOOPS_URL . '/modules/smartlanguage/flags/' . $n . '.gif" title="' . $language_name . '" alt="' . $language_name . '" style="vertical-align: middle;" />';
			} else {
				$flag .= '<img src="' . XOOPS_URL . '/modules/smartlanguage/flags/noflag.gif" title="' . $language_name . '" alt="' . $language_name . '" style="vertical-align: middle;" />';
			}
			$flag .= "</a>";

			if (is_object($xoopsTpl)) {
				/* Create a smarty variable that you can put in your theme. For example :
				To display the french flag, put this in your theme.html : <{$lang_image_english}>
				*/
				$xoopsTpl->assign('lang_image_' . $n, $flag);
				/* Create a smarty variable that contains the link to change the language.
				With that, you can put the image or text that you want in your theme.
				For example <a href="<{lang_link_french}>">Click here for the French version !</a>
				*/
				$xoopsTpl->assign('lang_link_' . $n, $pagenquery . $v);
			}
			define ('_LANG_LINK_' . strtoupper($n), $pagenquery . $v);

			$i++;
			$imagelist .= $flag;
		}
	}

	$myts =& MyTextSanitizer::getInstance();
	if (is_object($xoopsTpl)) {
		$xoopsTpl->assign('selectlanguage_lower',$myts->makeTboxData4Show("[fr]English[/fr][en]Français[/en]"));
		$xoopsTpl->assign('selectlanguage_upper',$myts->makeTboxData4Show("[fr]ENGLISH[/fr][en]FRANCAIS[/en]"));
		if (defined('_LANG_LINK_ENGLISH') && defined('_LANG_LINK_FRENCH')) {
			$xoopsTpl->assign('selectlanguagelink',$myts->makeTboxData4Show("[fr]" . _LANG_LINK_ENGLISH. "[/fr]" . "[en]" . _LANG_LINK_FRENCH . "[/en]"));
		}


		// Smarty variable representing the current selected language
		$xoopsTpl->assign('current_language', $xoopsConfig['language']);

		// Adding reference tag to module header
		$ref_ml = "XOOPS Multilanguages is developed by The SmartFactory (http://www.smartfactory.ca), a division of INBOX Solutions (http://inboxinternational.com)";
		if (isset($xoopsTpl->_tpl_vars['xoops_module_header'])) {
			$mod_head = $xoopsTpl->_tpl_vars['xoops_module_header'];
			$xoopsTpl->_tpl_vars['xoops_module_header'] = '<meta name="multilanguages" content="' . $ref_ml . '" />';
			$xoopsTpl->_tpl_vars['xoops_module_header'] .= $mod_head;
		} else {
			$xoopsTpl->_tpl_vars['xoops_module_header'] = '<meta name="multilanguages" content="' . $ref_ml . '" />';
		}
	}

	// Create the smarty tags
	$smartlanguage_smarty_handler =& xoops_getmodulehandler('smarty', 'smartlanguage');
	$smarty_tags = $smartlanguage_smarty_handler->getTags($xoopsConfig['language']);
	if (is_object($xoopsTpl)) {
		foreach($smarty_tags as $tagid=>$value) {
			$xoopsTpl->assign($tagid, $value);
		}

		$xoopsTpl->assign('xoopsml_version', XOOPS_ML_VERSION);
		$xoopsTpl->assign('xoopsml_link', '<a href="http://www.smartfactory.ca/modules/mydownloads/singlefile.php?cid=5&lid=30" target="_blank">XOOPS Multilanguages ' . XOOPS_ML_VERSION . '</a>');
	}
}

// Set the separator for PHP generated tags to be &amp; instead of & for XHTML compliance
// Thanks to djnz
ini_set("arg_separator.output","&amp;");
// Retreive the language tags within SmartLanguage

static $xoops_language_tags;

if(!isset($xoops_language_tags)) {

	// Check to see if SmartLanguage is installed
	include_once XOOPS_ROOT_PATH."/kernel/module.php";
	$h_module =& xoops_gethandler('module');
	if (!$h_module->getByDirname('smartlanguage')) {
		$xoops_language_tags = false;
	} else {
		include_once(XOOPS_ROOT_PATH . "/modules/smartlanguage/class/tag.php");
		$smartlanguage_tag_handler =& xoops_getmodulehandler('tag', 'smartlanguage');
		$xoops_language_tags =& $smartlanguage_tag_handler->getObjects();

		// Get the default language
		foreach($xoops_language_tags as $tagObj) {
			if ($tagObj->is_default())	{
				$xoops_default_language = $tagObj->languageid();
			}
		}

	}
}



?>