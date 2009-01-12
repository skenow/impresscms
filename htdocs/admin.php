<?php
/**
* Admin control panel entry page
*
* This page is responsible for
* - displaying the home of the Control Panel
* - checking for cache/adminmenu.php
* - displaying RSS feed of the ImpressCMS Project
*
* @copyright	http://www.xoops.org/ The XOOPS Project
* @copyright	XOOPS_copyrights.txt
* @copyright	http://www.impresscms.org/ The ImpressCMS Project
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @package		core
* @since		XOOPS
* @author		http://www.xoops.org The XOOPS Project
* @author		modified by marcan <marcan@impresscms.org>
* @version		$Id$
*/

$xoopsOption['pagetype'] = 'admin';
include 'mainfile.php';
include ICMS_ROOT_PATH.'/include/cp_functions.php';

// Admin Authentication
if($xoopsUser)
{
	if(!$xoopsUser->isAdmin(-1)) {redirect_header('index.php',2,_AD_NORIGHT);}
}
else {redirect_header('index.php',2,_AD_NORIGHT);}
// end Admin Authentication

$op = isset($_GET['rssnews']) ? intval($_GET['rssnews']) : 0;
if(!empty($_GET['op'])) {$op = intval($_GET['op']);}
if(!empty($_POST['op'])) {$op = intval($_POST['op']);}

if(!file_exists(ICMS_CACHE_PATH.'/adminmenu_'.$xoopsConfig['language'].'.php') && $op != 2)
{
	xoops_header();
	xoops_confirm(array('op' => 2), 'admin.php', _RECREATE_ADMINMENU_FILE);
	xoops_footer();
	exit();
}

switch($op)
{
	case 1:
		xoops_cp_header();
		showRSS(1);
	break;
	case 2:
		xoops_module_write_admin_menu(impresscms_get_adminmenu());
		redirect_header('javascript:history.go(-1)', 1, _AD_LOGINADMIN);
	break;
	case 10:
		$rssurl = 'http://www.impresscms.org/modules/smartsection/backend.php?categoryid=1';
		$rssfile = ICMS_CACHE_PATH.'/www_smartsection_category1.xml';
		$caching_time = 1;
		$items_to_display = 1;
		
		$rssdata = '';
		if(!file_exists($rssfile) || filemtime($rssfile) < time() - $caching_time)
		{
			require_once ICMS_ROOT_PATH.'/class/snoopy.php';
			$snoopy = new Snoopy;
			if($snoopy->fetch($rssurl))
			{
				$rssdata = $snoopy->results;
				if(false !== $fp = fopen($rssfile, 'w')) {fwrite($fp, $rssdata);}
				fclose($fp);
	        	}
		}
		else
		{
			if(false !== $fp = fopen($rssfile, 'r'))
			{
				while(!feof ($fp)) {$rssdata .= fgets($fp, 4096);}
				fclose($fp);
			}
		}
		if($rssdata != '')
		{
			include_once ICMS_ROOT_PATH.'/class/xml/rss/xmlrss2parser.php';
			$rss2parser = new XoopsXmlRss2Parser($rssdata);
			if(false != $rss2parser->parse())
			{
				$items =& $rss2parser->getItems();
				$count = count($items);
				$myts =& MyTextSanitizer::getInstance();
				for($i = 0; $i < $items_to_display; $i++)
				{
					?>
					<div>
						<img style="vertical-align: middle;" src="<?php ICMS_URL?>/modules/smartsection/images/icon/doc.png" alt="<?php $items[$i]['title']?>">&nbsp;<a href="<?php $items[$i]['guid']?>"><?php $items[$i]['title']?></a>
					</div>
					<div>
						<img class="smartsection_item_image" src="<?php ICMS_URL?>/uploads/smartsection/images/item/impresscms_news.gif" alt="<?php $items[$i]['title']?>" title="<?php $items[$i]['title']?>" align="right">
						<?php $items[$i]['description']?>
					</div>
					<div style="clear: both;">&nbsp;</div>
					<div style="font-size: 10px; text-align: right;"><a href="http://www.impresscms.org/modules/smartsection/category.php?categoryid=1">All ImpressCMS Project news...</a></div>
					<?php
				}
			}
			else
			{
				//echo $rss2parser->getErrors();
			}
		}
	break;
	case 11:
		include_once ICMS_ROOT_PATH.'/include/checkadminlogin.php';
		exit();
	break;
	default:
		$mods = xoops_cp_header(1);

		include_once XOOPS_ROOT_PATH . '/class/template.php';
		$admintemplate->_tpl =& new XoopsTpl();

		$admintemplate->_tpl->assign('lang_adminlogin', _ADMINLOGIN);
		$admintemplate->_tpl->assign('lang_login', _LOGIN);
		$admintemplate->_tpl->assign('lang_username', $xoopsUser->getVar('uname'));
		$admintemplate->_tpl->assign('redirect_page', ICMS_URL.'/modules/system/admin.php');
		$admintemplate->_tpl->assign('lang_password', _PASSWORD);
		$admintemplate->_tpl->display(XOOPS_ROOT_PATH . '/modules/system/templates/admin/system_adm_loginform.html');
	break;
}

function showRSS($op=1)
{
	switch($op)
	{
		case 1:
			$config_handler =& xoops_gethandler('config');
			$xoopsConfigPersona =& $config_handler->getConfigsByCat(XOOPS_CONF_PERSONA);
			$rssurl = $xoopsConfigPersona['rss_local'];
			$rssfile = ICMS_CACHE_PATH.'/adminnews_'._LANGCODE.'.xml';
		break;
	}
	$rssdata = '';
	if(!file_exists($rssfile) || filemtime($rssfile) < time() - 86400)
	{
		require_once ICMS_ROOT_PATH.'/class/snoopy.php';
        	$snoopy = new Snoopy;
        	if($snoopy->fetch($rssurl))
		{
            		$rssdata = $snoopy->results;
            		if(false !== $fp = fopen($rssfile, 'w')) {fwrite($fp, $rssdata);}
            		fclose($fp);
        	}
	}
	else
	{
		if(false !== $fp = fopen($rssfile, 'r'))
		{
			while(!feof ($fp)) {$rssdata .= fgets($fp, 4096);}
			fclose($fp);
		}
	}
	if($rssdata != '')
	{
		include_once ICMS_ROOT_PATH.'/class/xml/rss/xmlrss2parser.php';
		$rss2parser = new XoopsXmlRss2Parser($rssdata);
		if(false != $rss2parser->parse())
		{
			echo '<table class="outer" width="100%">';
			$items =& $rss2parser->getItems();
			$count = count($items);
			$myts =& MyTextSanitizer::getInstance();
			for($i = 0; $i < $count; $i++)
			{
				echo '<tr class="head"><td><a href="'.htmlspecialchars($items[$i]['link']).'" rel="external">';
				echo htmlspecialchars($items[$i]['title']).'</a> ('.htmlspecialchars($items[$i]['pubdate']).')</td></tr>';
				if($items[$i]['description'] != '')
				{
					echo '<tr><td class="odd">'.utf8_decode($items[$i]['description']);
					if($items[$i]['guid'] != '')
					{
						echo '&nbsp;&nbsp;<a href="'.htmlspecialchars($items[$i]['guid']).'" rel="external">'._MORE.'</a>';
					}
					echo '</td></tr>';
				}
				elseif($items[$i]['guid'] != '')
				{
					echo '<tr><td class="even" valign="top"></td><td colspan="2" class="odd"><a href="'.htmlspecialchars($items[$i]['guid']).'" rel="external">'._MORE.'</a></td></tr>';
				}
			}
			echo '</table>';
		}
		else {echo $rss2parser->getErrors();}
	}
}
xoops_cp_footer();
?>