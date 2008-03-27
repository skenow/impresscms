<?php
// $Id: comment_form.php,v 1.1 2007/03/16 02:39:06 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//

if (!defined('ZAR_ROOT_PATH') || !is_object($zariliaAddon) || !is_object($zariliaUser)) {
    //ZariliaErrorHandler_HtmlError( $title = 'Error', $heading = 'Access denied', $description = 'Maybe you don`t have permisions.', $image = '', $show_button = false );	
	exit('Access denied');
}

$com_modid = $zariliaAddon->getVar('mid');
include_once ZAR_ROOT_PATH."/class/zarilialists.php";
include_once ZAR_ROOT_PATH."/class/zariliaformloader.php";
$cform = new ZariliaThemeForm(_CM_POSTCOMMENT, "commentform", 'comment_post.php', 'post', true);
if (isset($zariliaAddonConfig['com_rule'])) {
	include_once ZAR_ROOT_PATH.'/include/comment_constants.php';
	switch ($zariliaAddonConfig['com_rule']) {
	case ZAR_COMMENT_APPROVEALL:
		$rule_text = _CM_COMAPPROVEALL;
		break;
	case ZAR_COMMENT_APPROVEUSER:
		$rule_text = _CM_COMAPPROVEUSER;
		break;
	case ZAR_COMMENT_APPROVEADMIN:
		default:
		$rule_text = _CM_COMAPPROVEADMIN;
		break;
	}
	$cform->addElement(new ZariliaFormLabel(_CM_COMRULES, $rule_text));
}

$cform->addElement(new ZariliaFormText(_CM_TITLE, 'com_title', 50, 255, $com_title), true);
$icons_radio = new ZariliaFormRadio(_MESSAGEICON, 'com_icon', $com_icon);
$subject_icons = ZariliaLists::getSubjectsList();
foreach ($subject_icons as $iconfile) {
	$icons_radio->addOption($iconfile, '<img src="'.ZAR_URL.'/images/subject/'.$iconfile.'" alt="" />');
}
$cform->addElement($icons_radio);
//$cform->addElement(new ZariliaFormDhtmlTextArea(_CM_MESSAGE, 'com_text', $com_text, 10, 50), true);

$options['name'] = 'com_text';
$options['value'] = $com_text;
$ele = new ZariliaFormEditor( _CM_MESSAGE, $zariliaUser->getVar( "editor" ), $options, $nohtml = false, $onfailure = "textarea" );
$ele->setNocolspan( 1 );
$cform->addElement( $ele );

$option_tray = new ZariliaFormElementTray(_OPTIONS,'<br />');
$button_tray = new ZariliaFormElementTray('' ,'&nbsp;');
if (is_object($zariliaUser)) {
    if ($zariliaAddonConfig['com_anonpost'] == 1) {
        $noname = !empty($noname) ? 1 : 0;
        $noname_checkbox = new ZariliaFormCheckBox('', 'noname', $noname);
        $noname_checkbox->addOption(1, _POSTANON);
        $option_tray->addElement($noname_checkbox);
    }
    if (false != $zariliaUser->isAdmin($com_modid)) {
        // show status change box when editing (comment id is not empty)
        if (!empty($com_id)) {
            include_once ZAR_ROOT_PATH.'/include/comment_constants.php';
            $status_select = new ZariliaFormSelect(_CM_STATUS, 'com_status', $com_status);
            $status_select->addOptionArray(array(ZAR_COMMENT_PENDING => _CM_PENDING, ZAR_COMMENT_ACTIVE => _CM_ACTIVE, ZAR_COMMENT_HIDDEN => _CM_HIDDEN));
            $cform->addElement($status_select);
            $button_tray->addElement(new ZariliaFormButton('', 'com_dodelete', _DELETE, 'submit'));
        }
        /*$html_checkbox = new ZariliaFormCheckBox('', 'dohtml', $dohtml);
        $html_checkbox->addOption(1, _CM_DOHTML);
        $option_tray->addElement($html_checkbox);*/
		$option_tray->addElement(new ZariliaFormHidden('dohtml',0));
    }
}
$smiley_checkbox = new ZariliaFormCheckBox('', 'dosmiley', $dosmiley);
$smiley_checkbox->addOption(1, _CM_DOSMILEY);
$option_tray->addElement($smiley_checkbox);
$xcode_checkbox = new ZariliaFormCheckBox('', 'doxcode', $doxcode);
$xcode_checkbox->addOption(1, _CM_DOXCODE);
$option_tray->addElement($xcode_checkbox);
$br_checkbox = new ZariliaFormCheckBox('', 'dobr', $dobr);
$br_checkbox->addOption(1, _CM_DOAUTOWRAP);
$option_tray->addElement($br_checkbox);

$cform->addElement($option_tray);
$cform->addElement(new ZariliaFormHidden('com_pid', intval($com_pid)));
$cform->addElement(new ZariliaFormHidden('com_rootid', intval($com_rootid)));
$cform->addElement(new ZariliaFormHidden('com_id', $com_id));
$cform->addElement(new ZariliaFormHidden('com_itemid', $com_itemid));
$cform->addElement(new ZariliaFormHidden('com_order', $com_order));
$cform->addElement(new ZariliaFormHidden('com_mode', $com_mode));
$cform->addElement(new ZariliaFormHiddenToken());

// add addon specific extra params

if ('system' != $zariliaAddon->getVar('dirname')) {
	$comment_config = $zariliaAddon->getInfo('comments');
 	if (isset($comment_config['extraParams']) && is_array($comment_config['extraParams'])) {
        $myts =& MyTextSanitizer::getInstance();
        foreach ($comment_config['extraParams'] as $extra_param) {
            // This routine is included from forms accessed via both GET and POST
            if (isset($_POST[$extra_param])) {
                $hidden_value = $myts->stripSlashesGPC($_POST[$extra_param]);
            } elseif (isset($_GET[$extra_param])) {
                $hidden_value = $myts->stripSlashesGPC($_GET[$extra_param]);
			} else {
				$hidden_value = '';
			}
 			$cform->addElement(new ZariliaFormHidden($extra_param, $hidden_value));
 		}
 	}
}
$button_tray->addElement(new ZariliaFormButton('', 'com_dopreview', _PREVIEW, 'submit'));
$button_tray->addElement(new ZariliaFormButton('', 'com_dopost', _CM_POSTCOMMENT, 'submit'));
$cform->addElement($button_tray);
$cform->display();
?>