<?php
// $Id: storyform.inc.php,v 1.14 2004/09/03 17:30:43 hthouzard Exp $
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
	die("Zarilia root path not defined");
}

if (file_exists(ZAR_ROOT_PATH.'/language/'.$zariliaConfig['language'].'/calendar.php')) {
	include_once ZAR_ROOT_PATH.'/language/'.$zariliaConfig['language'].'/calendar.php';
} else {
	include_once ZAR_ROOT_PATH.'/language/english/calendar.php';
}
include_once ZAR_ROOT_PATH.'/class/zariliaformloader.php';
include_once ZAR_ROOT_PATH.'/addons/news/include/functions.php';
include_once ZAR_ROOT_PATH.'/addons/news/config.php';

$sform = new ZariliaThemeForm(_NW_SUBMITNEWS, "storyform", ZAR_URL.'/addons/'.$zariliaAddon->getVar('dirname').'/submit.php');
$sform->setExtra('enctype="multipart/form-data"');
$sform->addElement(new ZariliaFormText(_NW_TITLE, 'title', 50, 255, $title), true);

// Topic's selection box
if (!isset($xt)) {
	$xt = new NewsTopic();
}
if($xt->getAllTopicsCount()==0) {
   	redirect_header("index.php",4,_NW_POST_SORRY);
   	exit();
}

include_once ZAR_ROOT_PATH."/class/tree.php";
$allTopics = $xt->getAllTopics($zariliaAddonConfig['restrictindex'],'news_submit');
$topic_tree = new ZariliaObjectTree($allTopics, 'topic_id', 'topic_pid');
$topic_select = $topic_tree->makeSelBox('topic_id', 'topic_title', '-- ', $topicid, false);
$sform->addElement(new ZariliaFormLabel(_NW_TOPIC, $topic_select));

//If admin - show admin form
//TODO: Change to "If submit privilege"
if ($approveprivilege) {
    //Show topic image?
    $sform->addElement(new ZariliaFormRadioYN(_AM_TOPICDISPLAY, 'topicdisplay', $topicdisplay));
    //Select image position
    $posselect = new ZariliaFormSelect(_AM_TOPICALIGN, 'topicalign', $topicalign);
    $posselect->addOption('R', _AM_RIGHT);
    $posselect->addOption('L', _AM_LEFT);
    $sform->addElement($posselect);
    //Publish in home?
    //TODO: Check that pubinhome is 0 = no and 1 = yes (currently vice versa)
    $sform->addElement(new ZariliaFormRadioYN(_AM_PUBINHOME, 'ihome', $ihome, _NO, _YES));
}

// News author
if ($approveprivilege && is_object($zariliaUser) && $zariliaUser->isAdmin($zariliaAddon->getVar('mid'))) {
	if(!isset($newsauthor)) {
		$newsauthor=$zariliaUser->getVar('uid');
	}
	$member_handler = &zarilia_gethandler( 'member' );
	$usercount = $member_handler->getUserCount();
	if ( $usercount < $cfg['config_max_users_list']) {
		$sform->addElement(new ZariliaFormSelectUser(_NW_AUTHOR,'author',true, $newsauthor),false);
	} else {
		$sform->addElement(new ZariliaFormText(_NW_AUTHOR_ID, 'author', 10, 10, $newsauthor), false);
	}
}

$editor=news_getWysiwygForm(_NW_THESCOOP, 'hometext', $hometext, 15, 60, 'hometext_hidden');
$sform->addElement($editor,true);

//Extra info
//If admin -> if submit privilege
if ($approveprivilege) {
    $editor2=news_getWysiwygForm(_AM_EXTEXT, 'bodytext', $bodytext, 15, 60, 'bodytext_hidden');
	$sform->addElement($editor2,false);

    if(news_getmoduleoption('metadata')) {
		$sform->addElement(new ZariliaFormText(_NW_META_DESCRIPTION, 'description', 50, 255, $description), false);
		$sform->addElement(new ZariliaFormText(_NW_META_KEYWORDS, 'keywords', 50, 255, $keywords), false);
    }
}

// Manage upload(s)
$allowupload = false;
switch ($zariliaAddonConfig['uploadgroups'])
{
	case 1: //Submitters and Approvers
		$allowupload = true;
		break;
	case 2: //Approvers only
		$allowupload = $approveprivilege ? true : false;
		break;
	case 3: //Upload Disabled
		$allowupload = false;
		break;
}

if($allowupload)
{
	if($op=='edit') {
		$sfiles = new sFiles();
		$filesarr=Array();
		$filesarr=$sfiles->getAllbyStory($storyid);
		if(count($filesarr)>0) {
			$upl_tray = new ZariliaFormElementTray(_AM_UPLOAD_ATTACHFILE,'<br />');
			$upl_checkbox=new ZariliaFormCheckBox('', 'delupload[]');

			foreach ($filesarr as $onefile)
			{
				$link = sprintf("<a href='%s/%s' target='_blank'>%s</a>\n",ZAR_UPLOAD_URL,$onefile->getDownloadname('S'),$onefile->getFileRealName('S'));
				$upl_checkbox->addOption($onefile->getFileid(),$link);
			}
			$upl_tray->addElement($upl_checkbox,false);
			$dellabel=new ZariliaFormLabel(_AM_DELETE_SELFILES,'');
			$upl_tray->addElement($dellabel,false);
			$sform->addElement($upl_tray);
		}
	}
	$sform->addElement(new ZariliaFormFile(_AM_SELFILE, 'attachedfile', $zariliaAddonConfig['maxuploadsize']), false);
}


$option_tray = new ZariliaFormElementTray(_OPTIONS,'<br />');
//Set date of publish/expiration
if ($approveprivilege) {
	if(is_object($zariliaUser) && $zariliaUser->isAdmin($zariliaAddon->getVar('mid'))) {
		$approve=1;
	}
    $approve_checkbox = new ZariliaFormCheckBox('', 'approve', $approve);
    $approve_checkbox->addOption(1, _AM_APPROVE);
    $option_tray->addElement($approve_checkbox);

    $check=$published>0 ? 1 :0;
    $published_checkbox = new ZariliaFormCheckBox('', 'autodate',$check);
    $published_checkbox->addOption(1, _AM_SETDATETIME);
    $option_tray->addElement($published_checkbox);

    $option_tray->addElement(new ZariliaFormDateTime(_AM_SETDATETIME, 'publish_date', 15, $published));

	$check=$expired>0 ? 1 :0;
    $expired_checkbox = new ZariliaFormCheckBox('', 'autoexpdate',$check);
    $expired_checkbox->addOption(1, _AM_SETEXPDATETIME);
    $option_tray->addElement($expired_checkbox);

    $option_tray->addElement(new ZariliaFormDateTime(_AM_SETEXPDATETIME, 'expiry_date', 15, $expired));
}

if (is_object($zariliaUser)) {
	$notify_checkbox = new ZariliaFormCheckBox('', 'notifypub', $notifypub);
	$notify_checkbox->addOption(1, _NW_NOTIFYPUBLISH);
	$option_tray->addElement($notify_checkbox);
	if ($zariliaUser->isAdmin($zariliaAddon->getVar('mid'))) {
		$nohtml_checkbox = new ZariliaFormCheckBox('', 'nohtml', $nohtml);
		$nohtml_checkbox->addOption(1, _DISABLEHTML);
		$option_tray->addElement($nohtml_checkbox);
	}
}
$smiley_checkbox = new ZariliaFormCheckBox('', 'nosmiley', $nosmiley);
$smiley_checkbox->addOption(1, _DISABLESMILEY);
$option_tray->addElement($smiley_checkbox);


$sform->addElement($option_tray);

//TODO: Approve checkbox + "Move to top" if editing + Edit indicator

//Submit buttons
$button_tray = new ZariliaFormElementTray('' ,'');
$preview_btn = new ZariliaFormButton('', 'preview', _PREVIEW, 'submit');
$preview_btn->setExtra('accesskey="p"');
$button_tray->addElement($preview_btn);
$submit_btn = new ZariliaFormButton('', 'post', _NW_POST, 'submit');
$submit_btn->setExtra('accesskey="s"');
$button_tray->addElement($submit_btn);
$sform->addElement($button_tray);

//Hidden variables
if(isset($storyid)){
    $sform->addElement(new ZariliaFormHidden('storyid', $storyid));
}

if (!isset($returnside)) {
	$returnside=isset($_POST['returnside']) ? intval($_POST['returnside']) : 0;
	if(empty($returnside))	{
		$returnside=isset($_GET['returnside']) ? intval($_GET['returnside']) : 0;
	}
}

if(!isset($returnside)) {
	$returnside=0;
}
$sform->addElement(new ZariliaFormHidden('returnside', $returnside),false);

if (!isset($type)) {
    if ($approveprivilege) {
        $type = "admin";
    }
    else {
        $type = "user";
    }
}
$type_hidden = new ZariliaFormHidden('type', $type);
$sform->addElement($type_hidden);
$sform->display();
?>