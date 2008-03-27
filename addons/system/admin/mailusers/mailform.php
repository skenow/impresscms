<?php
// $Id: mailform.php,v 1.1 2007/03/16 02:36:39 catzwolf Exp $
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

echo "<br clear=\"all\" />";
$form = new ZariliaThemeForm( _AM_SENDMTOUSERS, "mailusers", $addonversion['adminpath'] );
// from finduser section
if ( !empty( $_REQUEST['memberslist_id'] ) ) {
    $user_count = count( $_POST['memberslist_id'] );
    $display_names = "";
    for ( $i = 0; $i < $user_count; $i++ ) {
        $uid_hidden = new ZariliaFormHidden( "mail_to_user[]", $_POST['memberslist_id'][$i] );
        $form->addElement( $uid_hidden );
        $display_names .= "<a href='" . ZAR_URL . "/index.php?page_type=userinfo&uid=" . $_POST['memberslist_id'][$i] . "' target='_blank'>" . $_POST['memberslist_uname'][$_POST['memberslist_id'][$i]] . "</a>, ";
        unset( $uid_hidden );
    }
    $users_label = new ZariliaFormLabel( _AM_SENDTOUSERS2, substr( $display_names, 0, -2 ) );
    $form->addElement( $users_label );
    $display_criteria = 0;
}

if ( !empty( $_REQUEST['uid'] ) ) {
    $uid = $_REQUEST['uid'];
    $member_handler = &zarilia_gethandler( 'member' );
    $thisUser = &$member_handler->getUser( $uid );
    $display_names = "";
    $uid_hidden = new ZariliaFormHidden( "mail_to_user[]", $uid );
    $form->addElement( $uid_hidden );
    $display_name = "<a href='" . ZAR_URL . "/index.php?page_type=userinfo&uid=" . $uid . "' target='_blank'>" . $thisUser->getVar( 'uname' ) . "</a>";
    $users_label = new ZariliaFormLabel( _AM_SENDTOUSERS2, $display_name );
    $form->addElement( $users_label );
    $display_criteria = 0;
}

if ( !empty( $display_criteria ) ) {
    $selected_groups = array();
    $group_select = new ZariliaFormSelectGroup( _AM_GROUPIS, "mail_to_group", false, $selected_groups, 5, true );
    $form->addElement( $group_select );

    $lastlog_min = new ZariliaFormText( _AM_LASTLOGMIN . "<br />" . _AM_TIMEFORMAT . "<br />", "mail_lastlog_min", 20, 10 );
    $lastlog_max = new ZariliaFormText( _AM_LASTLOGMAX . "<br />" . _AM_TIMEFORMAT . "<br />", "mail_lastlog_max", 20, 10 );
    $regd_min = new ZariliaFormText( _AM_REGDMIN . "<br />" . _AM_TIMEFORMAT . "<br />", "mail_regd_min", 20, 10 );
    $regd_max = new ZariliaFormText( _AM_REGDMAX . "<br />" . _AM_TIMEFORMAT . "<br />", "mail_regd_max", 20, 10 );
    $idle_more = new ZariliaFormText( _AM_IDLEMORE . "<br />", "mail_idle_more", 10, 5 );
    $idle_less = new ZariliaFormText( _AM_IDLELESS . "<br />", "mail_idle_less", 10, 5 );
    $mailok_cbox = new ZariliaFormCheckBox( '', 'mail_mailok' );
    $mailok_cbox->addOption( 1, _AM_MAILOK );
    $inactive_cbox = new ZariliaFormCheckBox( _AM_INACTIVE . "<br />", "mail_inactive" );
    $inactive_cbox->addOption( 1, _AMIFCHECKD );
    $inactive_cbox->setExtra( "onclick='javascript:disableElement(\"mail_lastlog_min\");disableElement(\"mail_lastlog_max\");disableElement(\"mail_idle_more\");disableElement(\"mail_idle_less\");disableElement(\"mail_to_group[]\");'" );

    $criteria_tray = new ZariliaFormElementTray( _AM_SENDTOUSERS, "<br /><br />", "", true );
    $criteria_tray->addElement( $group_select );
    $criteria_tray->addElement( $lastlog_min );
    $criteria_tray->addElement( $lastlog_max );
    $criteria_tray->addElement( $idle_more );
    $criteria_tray->addElement( $idle_less );
    $criteria_tray->addElement( $mailok_cbox );
    $criteria_tray->addElement( $inactive_cbox );
    $criteria_tray->addElement( $regd_min );
    $criteria_tray->addElement( $regd_max );
    $form->addElement( $criteria_tray );
}

$fname_text = new ZariliaFormText( _AM_MAILFNAME, "mail_fromname", 30, 255, $zariliaConfig['sitename'] );
$fromemail = !empty( $zariliaConfig['adminmail'] ) ? $zariliaConfig['adminmail'] : $zariliaUser->getVar( "email", "E" );
$femail_text = new ZariliaFormText( _AM_MAILFMAIL, "mail_fromemail", 30, 255, $fromemail );
$subject_caption = _AM_MAILSUBJECT."<br /><br /><span style='font-size:x-small;font-weight:bold;'>"._AM_MAILTAGS."</span><br /><span style='font-size:x-small;font-weight:normal;'>"._AM_MAILTAGS1."<br />"._AM_MAILTAGS2."<br />"._AM_MAILTAGS3."</span>";
$subject_caption = _AM_MAILSUBJECT . "<br /><br /><span style='font-size:x-small;font-weight:bold;'>" . _AM_MAILTAGS . "</span><br /><span style='font-size:x-small;font-weight:normal;'>" . _AM_MAILTAGS2 . "</span>";
$subject_text = new ZariliaFormText( $subject_caption, "mail_subject", 50, 255 );
$body_caption = _AM_MAILBODY . "<br /><br /><span style='font-size:x-small;font-weight:bold;'>" . _AM_MAILTAGS . "</span><br /><span style='font-size:x-small;font-weight:normal;'>" . _AM_MAILTAGS1 . "<br />" . _AM_MAILTAGS2 . "<br />" . _AM_MAILTAGS3 . "<br />" . _AM_MAILTAGS4 . "</span>";
$body_text = new ZariliaFormTextArea( $body_caption, "mail_body", "", 10 );

$type = !isset( $_REQUEST['type'] ) ? 'mail' : 'pm';
$to_checkbox = new ZariliaFormCheckBox( _AM_SENDTO, "mail_send_to", $type );
$to_checkbox->addOption( "mail", _AM_EMAIL );
$to_checkbox->addOption( "pm", _AM_PM );

$start_hidden = new ZariliaFormHidden( "mail_start", 0 );
$fct_hidden = new ZariliaFormHidden( "fct", "mailusers" );
$op_hidden = new ZariliaFormHidden( "op", "send" );
$submit_button = new ZariliaFormButton( "", "mail_submit", _SEND, "submit" );

$form->addElement( $fname_text );
$form->addElement( $femail_text );
$form->addElement( $subject_text );
$form->addElement( $body_text );
$form->addElement( $to_checkbox );
$form->addElement( $fct_hidden );
$form->addElement( $op_hidden );
$form->addElement( $start_hidden );
$form->addElement( $submit_button );
$form->setRequired( $subject_text );
$form->setRequired( $body_text );
$form->setRequired($to_checkbox);
?>