<?php
/*
* userpersonal
*/
$form->addElement( new ZariliaFormText( _MA_AD_NICKNAME, 'uname', 25, 25, $user->getVar( "uname", "E" ) ), true );
$form->addElement( new ZariliaFormText( _MA_AD_NAME, 'name', 50, 50, $user->getVar( "name", "E" ) ), false );

$email_tray = new ZariliaFormElementTray( _MA_AD_EMAIL, "<br />" );
$email_text = new ZariliaFormText( "", "email", 30, 60, $user->getVar( "email", "E" ) );
$email_tray->addElement( $email_text, true );

$level_select = new ZariliaFormSelect( _MA_AD_USERLEVEL, "level", $user->getVar( "level", "E" ) );
$level_select->addOption( 1, "Active" );
$level_select->addOption( 0, "Inactive" );
$level_select->addOption( 6, "suspended" );
$form->addElement( $level_select, true );

$email_cbox_value = $user->getVar( "user_viewemail" ) ? 1 : 0;
$email_cbox = new ZariliaFormCheckBox( "", "user_viewemail", $email_cbox_value );
$email_cbox->addOption( 1, _MA_AD_AOUTVTEAD );
$email_tray->addElement( $email_cbox );
$form->addElement( $email_tray, true );

$form->addElement( new ZariliaFormSelectLang( _MA_AD_LANGUAGECHOICE, 'user_language', $user->getVar( "user_language", "E" ) ) );
$form->addElement( new ZariliaFormSelectTheme( _MA_AD_THEME, 'theme', $user->getVar( "theme", "E" )) );
$form->addElement( new ZariliaFormSelectTimezone( _MA_AD_TIMEZONE, 'timezone_offset', $user->getVar( "timezone_offset", "E" ) ) );
$form->addElement( new ZariliaFormSelectEditor( _MA_AD_EDITOR, "editor", $zariliaUser->getVar( "editor", "e" ), false, 0, 0, true ) );
$form->addElement( new ZariliaFormSelectGroup( _MA_AD_GROUPS, 'groups', true, $groups, 5, true ), false );

$rank = $user->rank( false );
$rank_select = new ZariliaFormSelect( _MA_AD_RANKS, "rank", $rank->getVar('rank_id') );
$ranklist = ZariliaLists::getUserRankList();
if ( count( $ranklist ) > 0 ) {
    $rank_select->addOption( 0, "--------------" );
    $rank_select->addOptionArray( $ranklist );
} else {
    $rank_select->addOption( 0, _MA_AD_NSRID );
}
$form->addElement( $rank_select );

$age_tray = new ZariliaFormElementTray( _MA_AD_BIRTHDATE, "<br />" );
$age_text = new ZariliaFormTextDateSelect( '', 'user_coppa_dob', 15, $user->getVar( "user_coppa_dob" ) );
$age_tray->addElement( $age_text, false );

$age_cbox_value = $user->getVar( "user_coppa_agree" ) ? 1 : 0;
$age_cbox = new ZariliaFormCheckBox( "", "user_coppa_agree", $age_cbox_value );
$age_cbox->addOption( 1, _MA_AD_IAMOVER );
$age_tray->addElement( $age_cbox );
$form->addElement( $age_tray, true );
/*
* Login details
*/
$form->insertSplit( 'Login Details' );
$form->addElement( new ZariliaFormText( _MA_AD_LOGIN, 'login', 25, 25, $user->getVar( "login", "E" ) ), true );
$form->addElement( new ZariliaFormPassword( _MA_AD_PASSWORD, "pass", 10, 32, '', 1 ), false );
$form->addElement( new ZariliaFormGenPassword( _MA_AD_CREATEPASSWORD, "password", 10, 32, '' ), false );

if ( !$form_isedit ) {
    $form->addElement( $pwd_text, true );
    $form->addElement( $pwd_text2, true );
} else {
    $form->addElement( $pwd_text );
    $form->addElement( $pwd_text2 );
}

?>