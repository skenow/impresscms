<?php
/**
 * $Id: regform_profile.php,v 1.3 2007/05/09 14:14:21 catzwolf Exp $ Untitled 1.php v0.0 14/04/2007 05:47:43 John
 *
 * @Zarilia - 	PHP Content Management System
 * @copyright 2007 Zarilia
 * @Author : 	John (AKA Catzwolf)
 * @URL : 		http://zarilia.com
 * @Project :	Zarilia CMS
 */
global $zariliaConfigUser;

require ZAR_ROOT_PATH . '/class/zariliaformloader.php';

$uname_size = $zariliaConfigUser['maxuname'] < 30 ? $zariliaConfigUser['maxuname'] : 30;

$register_form = new ZariliaThemeForm( _US_REG_PRIVACY_HEADING, 'registerform', 'index.php', 'post' );
$_uname = new ZariliaFormText( _US_NICKNAME, 'uname', 30, 30, @$_POST['uname'] );
$_uname->setDescription( _US_NICKNAME_DSC );
$register_form->addElement( $_uname, true );

$_name = new ZariliaFormText( _US_NAME, 'name', 30, 30, @$_POST['name'] );
$_name->setDescription( _US_NAME_DSC );
$register_form->addElement( $_name, false );

$email_tray = new ZariliaFormElementTray( _US_EMAIL, "<br />" );
$email_text = new ZariliaFormText( '', 'email', 40, 60, @$_POST['email'] );
$email_text->setDescription( _US_NAME_DSC );
$email_option = new ZariliaFormCheckBox( '', 'user_viewemail', 0 );
$email_option->addOption( 0, _US_ALLOWVIEWEMAIL );
$email_tray->addElement( $email_text, true );
$email_tray->addElement( $email_option );
$register_form->addElement( $email_tray, true );

$_url = new ZariliaFormText( _US_WEBSITE, 'url', 30, 30, @$_POST['url'] );
$_url->setDescription( _US_WEBSITE_DSC );
$register_form->addElement( $_url, false );

$_timezone_offset = new ZariliaFormSelectTimezone( _US_TIMEZONE, 'timezone_offset', isset($_POST['timezone_offset'])?$_POST['timezone_offset']:0 );
$_timezone_offset->setDescription( _US_TIMEZONE_DSC );
$register_form->addElement( $_timezone_offset, false );

$register_form->addElement( new ZariliaFormRadioYN( _US_MAILOK, 'user_mailok', isset($_POST['user_mailok'])?$_POST['user_mailok']:1 ) );

$register_form->insertSplit( _US_USER_LOGIN_HEADING );

$ulogin = new ZariliaFormText( _US_ULOGINNAME, 'ulogin', 30, 30, @$_POST['ulogin']  );
$ulogin->setDescription( _US_ULOGINNAME_DSC );
$register_form->addElement( $ulogin, true );

$pass = new ZariliaFormPassword( _US_PASSWORD, 'pass', 10, 32, @$_POST['pass'] , 1 ) ;
$register_form->addElement( $pass, true );

$password = new ZariliaFormGenPassword( _US_CREATEPASSWORD, 'password', 10, 32, 'pass' ) ;
$password->setDescription( _US_CREATEPASSWORD_DSC );
$register_form->addElement( $password, false );

$captcha = new ZariliaFormCaptcha( _US_VERI, 'captacha', 5, 5, @$_POST['captacha'] );
$captcha->setDescription( _US_VERI_DSC );
$register_form->addElement( $captcha, true );

$content['form'] = $register_form;
$content['file'] = 'profile';
$this->addOptions(
    array( 'title' => _US_REGPROFILE,
        'subtitle' => isset($zariliaOption['form.error'])?$zariliaOption['form.error']:_US_REGPROFILE_DSC,
        'content' => $content,
        )
    );

?>