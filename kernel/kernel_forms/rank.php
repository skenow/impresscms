<?php
// $Id: rank.php,v 1.1 2007/03/16 02:44:13 catzwolf Exp $
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

global $addonversion;

require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

$form = new ZariliaThemeForm( $caption, 'rankform', $addonversion['adminpath'] );
$form->setExtra( 'enctype="multipart/form-data"' );

$rank_title = new ZariliaFormText( _MA_AD_RANKTITLE, 'rank_title', 50, 60, $this->getVar( 'rank_title', 'e' ) );
$rank_title->setDescription( _MA_AD_RANKTITLE_DSC );
$form->addElement( $rank_title, true );

$rank_min = new ZariliaFormText( _MA_AD_RANKMIN, 'rank_min', 50, 60, $this->getVar( 'rank_min', 'e' ) );
$rank_min->setDescription( _MA_AD_RANKMIN_DSC );
$form->addElement( $rank_min, true );

$rank_max = new ZariliaFormText( _MA_AD_RANKMAX, 'rank_max', 50, 60, $this->getVar( 'rank_max', 'e' ) );
$rank_max->setDescription( _MA_AD_RANKMAX_DSC );
$form->addElement( $rank_max, true );

$rank_tray = new ZariliaFormElementTray( _MA_AD_RANKIMAGE, '&nbsp;' );
$rank_tray->setDescription( _MA_AD_RANKIMAGE_DSC );
$rank_tray->addElement( new ZariliaFormFile( '', 'rank_image', 5000000 ) );
if ( $this->getVar( 'rank_image' ) != '' && file_exists( ZAR_UPLOAD_PATH . '/' . $this->getVar( 'rank_image' ) ) ) {
    $rank_image = '<img src="' . ZAR_UPLOAD_URL . '/' . $this->getVar( 'rank_image' ) . '" alt="" />';
} else {
    $rank_image = '';
}
$rank_tray->addElement( new ZariliaFormLabel( '', $rank_image ) );
$form->addElement( $rank_tray );

$rank_image_dir = new ZariliaFormSelectImg( _MA_AD_RANKSELECTIMAGE, 'rank_image_dir', $this->getVar( 'rank_image' ), $id = 'zarilia_image', 0 );
$rank_image_dir->setDescription( _MA_AD_RANKSELECTIMAGE_DSC );
$form->addElement( $rank_image_dir );

$rank_special = new ZariliaFormRadioYN( _MA_AD_SPECIAL, 'rank_special', $this->getVar( 'rank_special' ) , ' ' . _YES . '', ' ' . _NO . '' );
$rank_special->setDescription( _MA_AD_SPECIAL_DSC );
$form->addElement( $rank_special );

/*hidden values*/
$form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
if ( !$this->isNew() ) {
    $form->addElement( new ZariliaFormHidden( 'rank_id', $this->getVar( 'rank_id' ) ) );
}

$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );

?>