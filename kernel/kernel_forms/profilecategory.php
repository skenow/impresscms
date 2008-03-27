<?php
// $Id: profilecategory.php,v 1.1 2007/03/16 02:44:13 catzwolf Exp $
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

include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

global $addonversion, $profilecat_handler, $zariliaUser;

$caption = ( !$this->isNew() ) ? $caption = sprintf( _MA_AD_EPROFILE_CAT_MODIFY, $this->getVar( 'profilecat_name' ) ) : _MA_AD_EPROFILE_CAT_CREATE;
$form = new ZariliaThemeForm( $caption, 'post', $addonversion['adminpath'] );
if ( $this->isNew() ) {
    $form->addElement( new ZariliaFormSelectGroup( _MA_AD_EPROFILECAT_GROUPS, 'readgroup', false, ZAR_GROUP_ADMIN, 5, true ) );
} else {
    $perm_handler = &zarilia_gethandler( 'groupperm' );
    $form->addElement( new ZariliaFormSelectGroup( _MA_AD_EPROFILECAT_GROUPS, 'readgroup', false, $perm_handler->getGroupIds( 'profilecat_read', $this->getVar( 'profilecat_id' ) ), 5, true ) );
}

$form->addElement( new ZariliaFormText( _MA_AD_EPROFILECAT_TITLE, 'profilecat_name', 50, 255, $this->getVar( 'profilecat_name' ) ), true );

$options['name'] = 'profilecat_desc';
$options['value'] = $this->getVar( 'profilecat_desc', 'e' );
$ele = new ZariliaFormEditor( _MA_AD_EPROFILECAT_DESC, $zariliaUser->getVar( "editor" ), $options, $nohtml = false, $onfailure = "textarea" );
$ele->setNocolspan( 1 );
$form->addElement( $ele );

$form->addElement( new ZariliaFormText( _MA_AD_EPROFILECAT_ORDER, 'profilecat_order', 10, 80, $this->getVar( 'profilecat_order' ) ), false );
// $form->addElement( new ZariliaFormRadioYN( _MA_AD_EPROFILECAT_DISPLAY, 'profilecat_display', $this->getVar( 'profilecat_display' ) , ' ' . _ONLINE . '', ' ' . _OFFLINE . '' ) );
$form->addElement( new ZariliaFormHidden( 'op', 'save_category' ) );
/*buttons*/
$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
?>