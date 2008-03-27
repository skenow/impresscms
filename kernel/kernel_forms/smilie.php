<?php
// $Id: smilie.php,v 1.1 2007/03/16 02:44:13 catzwolf Exp $
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

global $addonversion, $zariliaConfigUser;

require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

$form = new ZariliaThemeForm( $caption, 'smileform', $addonversion['adminpath'] );
$form->setExtra( 'enctype="multipart/form-data"' );

$code = new ZariliaFormText( _AM_SMILECODE, 'code', 50, 60, $this->getVar( 'code', 'e' ) );
$code->setDescription( _AM_SMILECODE_DSC );
$form->addElement( $code, true );

$smile_desc = new ZariliaFormText( _AM_SMILEEMOTION, 'emotion', 50, 60, $this->getVar( 'emotion', 'e' ) );
$smile_desc->setDescription( _AM_SMILEEMOTION_DSC );
$form->addElement( $smile_desc, true );

$smile_tray = new ZariliaFormElementTray( _MA_AD_IMAGEUPLOAD, '&nbsp;' );
$smile_tray->setDescription( sprintf( _MA_AD_IMAGEUPLOAD_DSC, $zariliaConfigUser['avatar_maxsize'], $zariliaConfigUser['avatar_width'], $zariliaConfigUser['avatar_height'] ) );
$smile_tray->addElement( new ZariliaFormFile( '', 'smile_url', 5000000 ) );

if ( file_exists( ZAR_UPLOAD_PATH . '/' . $this->getVar( 'smile_url' ) ) ) {
    $smile_image = '<img src="' . ZAR_UPLOAD_URL . '/' . $this->getVar( 'smile_url' ) . '" alt="" />';
} else {
    $smile_image = '';
}
$smile_tray->addElement( new ZariliaFormLabel( '', $smile_image ) );
$form->addElement( $smile_tray );

$avatar_image_dir = new ZariliaFormSelectImg( _MA_AD_SMILIES_SELECTIMAGE, 'smile_url_dir', $this->getVar( 'smile_url' ), $id = 'zarilia_image', 0 );
$avatar_image_dir->setDescription( _MA_AD_SMILIES_SELECTIMAGE_DSC );
$form->addElement( $avatar_image_dir );

$smile_display = new ZariliaFormRadioYN( _AM_DISPLAYF, 'display', $this->getVar( 'display' ) , ' ' . _YES . '', ' ' . _NO . '' );
$smile_display->setDescription( _AM_DISPLAYF_DSC );
$form->addElement( $smile_display );

/*hidden values*/
$form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
$form->addElement( new ZariliaFormHidden( 'id', $this->getVar( 'id' ) ) );
$form->addElement( new ZariliaFormHidden( 'old_smile', $this->getVar( 'smile_url' ) ) );

$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );

?>