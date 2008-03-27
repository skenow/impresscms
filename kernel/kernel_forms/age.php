<?php
// $Id: age.php,v 1.2 2007/04/21 09:44:54 catzwolf Exp $
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

global $addonversion;

$caption = ( !$this->isNew() ) ? $caption = sprintf( _MA_AD_EAVATAR_MODIFY, $this->getVar( 'age_dtitle' ) ) : _MA_AD_EAVATAR_CREATE;

require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
$form = new ZariliaThemeForm( $caption, 'age_form', $addonversion['adminpath'] );
$form->setExtra( 'enctype="multipart/form-data"' );

$age_dtitle = new ZariliaFormText( _MA_AD_EAVATAR_NAME, 'age_dtitle', 50, 60, $this->getVar( 'age_dtitle', 'e' ) );
$age_dtitle->setDescription( _MA_AD_EAVATAR_NAME_DSC );
$form->addElement( $age_dtitle, true );

$age_ip = new ZariliaFormText( _MA_AD_EAVATAR_NAME, 'age_ip', 50, 60, $this->getVar( 'age_ip', 'e' ) );
$age_ip->setDescription( _MA_AD_EAVATAR_NAME_DSC );
$form->addElement( $age_ip, true );

$age_text = new ZariliaFormTextDateSelect( _MD_AM_BIRTHDATE, 'age_date', 15, $this->getVar( 'age_date', 'e' ) );
$age_text->setDescription( _MA_AD_EAVATAR_NAME_DSC );
$form->addElement( $age_text, true );

$age_agreed = new ZariliaFormRadioYN( _MA_AD_EAVATAR_DISPLAY, 'age_agreed', $this->getVar( 'age_agreed' ) , ' ' . _YES . '', ' ' . _NO . '' );
$age_agreed->setDescription( _MA_AD_EAVATAR_DISPLAY_DSC );
$form->addElement( $age_agreed, true );

$age_date = new ZariliaFormTextDateSelect( _MD_AM_BIRTHDATE, 'age_gdate', 15, $this->getVar( 'age_gdate', 'e' ) );
$age_date->setDescription( _MD_AM_BIRTHDATE_DSC );
$form->addElement( $age_date, true );

$age_dtitle = new ZariliaFormText( _MA_AD_EAVATAR_NAME, 'age_dtitle', 50, 60, $this->getVar( 'age_dtitle', 'e' ) );
$age_dtitle->setDescription( _MA_AD_EAVATAR_NAME_DSC );
$form->addElement( $age_dtitle, true );

$age_itemid = new ZariliaFormText( _MA_AD_EAVATAR_NAME, 'age_itemid', 5, 5, $this->getVar( 'age_itemid', 'e' ) );
$age_itemid->setDescription( _MA_AD_EAVATAR_NAME_DSC );
$form->addElement( $age_itemid, true );

$age_dtitle = new ZariliaFormText( _MA_AD_EAVATAR_NAME, 'age_dtitle', 50, 60, $this->getVar( 'age_dtitle', 'e' ) );
$age_dtitle->setDescription( _MA_AD_EAVATAR_NAME_DSC );
$form->addElement( $age_dtitle, true );
/*
        $ele3 = new ZariliaFormText( _MA_AD_EAVATAR_WEIGHT, 'avatar_weight', 3, 4, $this->getVar( 'avatar_weight', 'e' ) );
        $ele3->setDescription( _MA_AD_EAVATAR_WEIGHT_DSC );
        $form->addElement( $ele3, true );
*/

/*hidden values*/
// if ( $this->getVar( 'age_id' ) ) {
$form->addElement( new ZariliaFormHidden( 'age_id', $this->getVar( 'age_id' ) ) );
// }
$form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
/*button_tray*/
$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
$form->display();

?>