<?php
// $Id: avatar.php,v 1.2 2007/04/21 09:44:54 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

global $addonversion, $zariliaConfigUser;

require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
$form = new ZariliaThemeForm( ( !$this->isNew() ) ? sprintf( _MA_AD_EAVATAR_MODIFY, $this->getVar( 'avatar_name' ) ) : _MA_AD_EAVATAR_CREATE, 'avatar_form', $addonversion['adminpath'] );
$form->setExtra( 'enctype="multipart/form-data"' );

/*Set display name*/
$ele1 = new ZariliaFormText( _MA_AD_EAVATAR_NAME, 'avatar_name', 50, 60, $this->getVar( 'avatar_name', 'e' ) );
$ele1->setDescription( _MA_AD_EAVATAR_NAME_DSC );
$form->addElement( $ele1, true );

$avatar_tray = new ZariliaFormElementTray( _MA_AD_EAVATAR_FILE, '&nbsp;' );
$avatar_tray->setDescription( sprintf( _MA_AD_EAVATAR_FILE_DSC, $zariliaConfigUser['avatar_maxsize'], $zariliaConfigUser['avatar_width'], $zariliaConfigUser['avatar_height'] ) );
$avatar_tray->addElement( new ZariliaFormFile( '', 'upload_file', $zariliaConfigUser['avatar_maxsize'] ) );

if ( file_exists( ZAR_UPLOAD_PATH . '/' . $this->getVar( 'avatar_file' ) ) ) {
    $avatar_image = '<img src="' . ZAR_UPLOAD_URL . '/' . $this->getVar( 'avatar_file' ) . '" width="40" height="40" alt="" />';
} else {
    $avatar_image = '';
}
$avatar_tray->addElement( new ZariliaFormLabel( '', $avatar_image ) );
$form->addElement( $avatar_tray );

$avatar_image_dir = new ZariliaFormSelectImg( _MA_AD_EAVATAR_SELECTIMAGE, 'avatar_image_dir', $this->getVar( 'avatar_file' ), $id = 'zarilia_image', 0 );
$avatar_image_dir->setDescription( _MA_AD_EAVATAR_SELECTIMAGE_DSC );
$form->addElement( $avatar_image_dir );

/*Set display name*/
$ele3 = new ZariliaFormText( _MA_AD_EAVATAR_WEIGHT, 'avatar_weight', 3, 4, $this->getVar( 'avatar_weight', 'e' ) );
$ele3->setDescription( _MA_AD_EAVATAR_WEIGHT_DSC );
$form->addElement( $ele3, true );

/*Set display name*/
$ele4 = new ZariliaFormRadioYN( _MA_AD_EAVATAR_DISPLAY, 'avatar_display', $this->getVar( 'avatar_display' ) , ' ' . _YES . '', ' ' . _NO . '' );
$ele4->setDescription( _MA_AD_EAVATAR_DISPLAY_DSC );
$form->addElement( $ele4 );

/*hidden values*/
if ( $this->getVar( 'avatar_file' ) ) {
    $form->addElement( new ZariliaFormHidden( 'avatar_file', $this->getVar( 'avatar_file' ) ) );
}

$form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
$form->addElement( new ZariliaFormHidden( 'type', 'S' ) );

/*button_tray*/
$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
$form->display();

?>