<?php
// $Id: commentform.inc.php,v 1.1 2007/03/16 02:39:06 catzwolf Exp $
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
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );
include_once ZAR_ROOT_PATH . "/class/zarilialists.php";
include ZAR_ROOT_PATH . "/class/zariliaformloader.php";
$cform = new ZariliaThemeForm( _CM_POSTCOMMENT, "commentform", "postcomment.php", "post", true );
if ( !preg_match( "/^re:/i", $subject ) ) {
    $subject = "Re: " . zarilia_substr( $subject, 0, 56 );
}
$cform->addElement( new ZariliaFormText( _CM_TITLE, 'subject', 50, 255, $subject ), true );
$icons_radio = new ZariliaFormRadio( _MESSAGEICON, 'icon', $icon );
$subject_icons = ZariliaLists::getSubjectsList();
foreach ( $subject_icons as $iconfile ) {
    $icons_radio->addOption( $iconfile, '<img src="' . ZAR_URL . '/images/subject/' . $iconfile . '" alt="" />' );
}
$cform->addElement( $icons_radio );

$options['name'] = 'message';
$options['value'] = $message;
$ele = new ZariliaFormEditor( _CM_MESSAGE, $zariliaUser->getVar( "editor" ), $options, $nohtml = false, $onfailure = "textarea" );
$ele->setNocolspan( 1 );
$form->addElement( $ele );

$option_tray = new ZariliaFormElementTray( _OPTIONS, '<br />' );
if ( $zariliaUser ) {
    if ( $zariliaConfig['anonpost'] == 1 ) {
        $noname_checkbox = new ZariliaFormCheckBox( '', 'noname', $noname );
        $noname_checkbox->addOption( 1, _POSTANON );
        $option_tray->addElement( $noname_checkbox );
    }
    if ( $zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) ) {
        $nohtml_checkbox = new ZariliaFormCheckBox( '', 'nohtml', $nohtml );
        $nohtml_checkbox->addOption( 1, _DISABLEHTML );
        $option_tray->addElement( $nohtml_checkbox );
    }
}
$smiley_checkbox = new ZariliaFormCheckBox( '', 'nosmiley', $nosmiley );
$smiley_checkbox->addOption( 1, _DISABLESMILEY );
$option_tray->addElement( $smiley_checkbox );

$cform->addElement( $option_tray );
$cform->addElement( new ZariliaFormHidden( 'pid', intval( $pid ) ) );
$cform->addElement( new ZariliaFormHidden( 'comment_id', intval( $comment_id ) ) );
$cform->addElement( new ZariliaFormHidden( 'item_id', intval( $item_id ) ) );
$cform->addElement( new ZariliaFormHidden( 'order', intval( $order ) ) );
$button_tray = new ZariliaFormElementTray( '' , '&nbsp;' );
$button_tray->addElement( new ZariliaFormButton( '', 'preview', _PREVIEW, 'submit' ) );
$button_tray->addElement( new ZariliaFormButton( '', 'post', _CM_POSTCOMMENT, 'submit' ) );
$cform->addElement( $button_tray );
$cform->display();

?>