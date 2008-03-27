<?php
// $Id: media.php,v 1.1 2007/03/16 02:44:13 catzwolf Exp $
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

global $addonversion, $media_cat_handler, $zariliaUser;
require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

$form = new ZariliaThemeForm( ( !$this->isNew() ) ? $caption = sprintf( _MA_AD_MEDIA_MODIFY, $this->getVar( 'media_name' ) ) : _MA_AD_MEDIA_CREATE, 'media_form', $addonversion['adminpath'] );
$form->setExtra( 'enctype="multipart/form-data"' );

/**
 * Start of form
 */
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_ENAME, 'media_name', 50, 255, $this->getVar( 'media_name', 'e' ) ), true );
$media_dirname = new ZariliaFormSelect( _MA_AD_MEDIA_ECATEGORY, 'media_cid', $this->getVar( 'media_cid' ) );
$cat = $media_cat_handler->getList();
$media_dirname->addOptionArray( $cat );
$form->addElement( $media_dirname );

$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_ENICENAME, 'media_nicename', 50, 255, $this->getVar( 'media_nicename', 'e' ) ), true );
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_EEXT, 'media_ext', 5, 5, $this->getVar( 'media_ext', 'e' ) ), true );
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_EMIMETYPE, 'media_mimetype', 30, 50, $this->getVar( 'media_mimetype', 'e' ) ), true );
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_EFILESIZE, 'media_filesize', 20, 20, $this->getVar( 'media_filesize', 'e' ) ), true );

/*
*
*/
$options['name'] = 'media_caption';
$options['value'] = $this->getVar( 'media_caption', 'e' );
$ele = new ZariliaFormEditor( _MA_AD_MEDIA_ECAPTION, '', $options, $nohtml = false, $onfailure = "textarea" );
$ele->setNocolspan( 1 );
$form->addElement( $ele, false );

$media_created = new ZariliaFormTextDateSelect( _MA_AD_MEDIA_ECREATED, 'media_created', 20, $this->getVar( 'media_created', 'e' ), true );
$media_created->setDescription( _MA_AD_MEDIA_ECREATED_DSC );
$form->addElement( $media_created, false );

/*Set display name*/
$media_weight = new ZariliaFormText( _MA_AD_MEDIA_EWEIGHT, 'media_weight', 3, 4, $this->getVar( 'media_weight', 'e' ) );
$media_weight->setDescription( _MA_AD_MEDIA_EWEIGHT_DSC );
$form->addElement( $media_weight, true );

/*Set display name*/
$media_display = new ZariliaFormRadioYN( _MA_AD_MEDIA_EDISPLAY, 'media_display', $this->getVar( 'media_display' ) , ' ' . _YES . '', ' ' . _NO . '' );
$media_display->setDescription( _MA_AD_MEDIA_EDISPLAY_DSC );
$form->addElement( $media_display, false );

$form->addElement( new ZariliaFormHidden( 'op', 'media_save' ) );
$form->addElement( new ZariliaFormHidden( 'media_id', $this->getVar( 'media_id' ) ) );
$form->addElement( new ZariliaFormHidden( 'media_uid', $this->getVar( 'media_uid' ) ) );
/*Button Tray*/
$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
$form->display();

?>