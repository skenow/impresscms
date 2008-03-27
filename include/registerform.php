<?php
// $Id: registerform.php,v 1.2 2007/03/30 22:06:42 catzwolf Exp $
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
$uname_size = $zariliaConfigUser['maxuname'] < 30 ? $zariliaConfigUser['maxuname'] : 30;

$reg_form = new ZariliaThemeForm( _US_USER_DETAILS_HEADING, "userinfo", "register.php" );
$reg_form->addElement( new ZariliaFormText( _US_NICKNAME, "uname", 30, $uname_size, $uname ), true );
$reg_form->addElement( new ZariliaFormText( _US_NAME, "name", 30, $uname_size, $name ), false );

$email_tray = new ZariliaFormElementTray( _US_EMAIL, "<br />" );
$email_text = new ZariliaFormText( "", "email", 40, 60, $email );
$email_option = new ZariliaFormCheckBox( "", "user_viewemail", $user_viewemail );
$email_option->addOption( 1, _US_ALLOWVIEWEMAIL );
$email_tray->addElement( $email_text );
$email_tray->addElement( $email_option );
$reg_form->addElement( $email_tray );

$reg_form->addElement( new ZariliaFormText( _US_WEBSITE, "url", 40, 255, $url ), false );
$reg_form->addElement( new ZariliaFormSelectTimezone( _US_TIMEZONE, "timezone_offset", $timezone_offset ) );
$reg_form->addElement( new ZariliaFormRadioYN( _US_MAILOK, 'user_mailok', 1 ) );
// $reg_form -> addElement( new ZariliaFormPassword( _US_VERIFYPASS, "pass2", 10, 32, '' ), true );
$reg_form->insertSplit( _US_USER_LOGIN_HEADING );
$reg_form->addElement( new ZariliaFormText( _US_LOGINNAME, "login", 30, $uname_size, $login ), true );
$reg_form->addElement( new ZariliaFormPassword( _US_PASSWORD, "pass", 10, 32, '', 1 ), true );
$reg_form->addElement( new ZariliaFormGenPassword( _US_CREATEPASSWORD, "password", 10, 32, '' ), false );

$rand = 0;
if ( $zariliaConfigUser['use_img_ver'] ) {
    $type = 2;
    $bgNum = 0;
    switch ( $type ) {
        case 2:
            $alphanum = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $rand = substr( str_shuffle( $alphanum ), 0, 5 );
            break;
        case 3:
            $alphanum = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $rand = substr( str_shuffle( $alphanum ), 0, 5 );
            $bgNum = rand( 1, 4 );
            break;
        case 1:
        default:
            $rand = rand( 10000, 99999 );
            break;
    } // switch
    if ( function_exists( 'gd_info' ) ) {
        $veri_image = "<img src='" . ZAR_URL . "/include/verification/randomimage.php?rand=" . $rand . "&amp;bgNum=" . $bgNum . "' alt='' title='' />";
        $veri_tray = new ZariliaFormElementTray( _US_VERI, "" );
        $veri_text = new ZariliaFormText( "", "verification", 5, 5, '' );
        $veri_tray->addElement( $veri_text );
        $veri_option = new ZariliaFormLabel( '', "<div style='padding: 8px;'>$veri_image</div>" );
        $veri_tray->addElement( $veri_option );
        $reg_form->addElement( $veri_tray );
    } else {
        $reg_form->addElement( new ZariliaFormHidden( "verification", $rand ) );
    }
}
$reg_form->addElement( new ZariliaFormHidden( "verification_ver", $rand ) );
$reg_form->addElement( new ZariliaFormHidden( "agree_disc", $agree_disc ) );
$reg_form->addElement( new ZariliaFormHidden( "user_coppa_agree", $user_coppa_agree ) );
if ( !$user_coppa_dob ) {
    $user_coppa_dob = mktime ( 0, 0, 0, intval( @$_REQUEST['mon'] ), intval( @$_REQUEST['day'] ), intval( @$_REQUEST['year'] ) );
}
$reg_form->addElement( new ZariliaFormHidden( "user_coppa_dob", $user_coppa_dob ) );
/**
 * $reg_form->addElement( new ZariliaFormButton( "", "submit", _US_CONTINUE, "submit" ) );
 * $reg_form->addElement( new ZariliaFormButton( "", "button", _US_BACK, "onclick=\"javascript:history.back();\"" ) );
 * //
 */

$reg_form->setRequired( $email_text );
$button_tray = new ZariliaFormElementTray( '', '' );
$button_tray->addElement( new ZariliaFormHidden( 'op', "newuser" ) );
$button_tray->addElement( new ZariliaFormButton( '', 'cancel', _BACK, 'button', 'onClick="history.go(-1);return true;"' ) );
$button_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ), true );
$button_tray->addElement( new ZariliaFormButton( '', 'submit', _CONTINUE, 'submit' ), true );
$reg_form->addElement( $button_tray );

$reg_form->display();
unset( $rand );
echo "<div>" . _US_DISPLAY_PRIVACY . "</div>";

?>