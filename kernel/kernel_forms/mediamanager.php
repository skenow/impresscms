<?php
// $Id: mediamanager.php,v 1.1 2007/03/16 02:44:13 catzwolf Exp $
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

global $addonversion, $media_cat_handler, $zariliaUser, $media_cat_handler;
require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

$target = zarilia_cleanRequestVars( $_REQUEST, 'target', '', XOBJ_DTYPE_TXTBOX );

$form = new ZariliaThemeForm( _MA_AD_MEDIA_CREATE, 'media_form', $addonversion['adminpath'] );
$form->setExtra( 'enctype="multipart/form-data"' );

$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_NAME, 'media_nicename', 50, 255 ), false );
$select = new ZariliaFormSelect( _MA_AD_MEDIA_CAT, 'media_cid', $this->getVar( 'media_cid' ) );
$select->addOptionArray( $media_cat_handler->getList() );
$form->addElement( $select, true );

$form->addElement( new ZariliaFormFile( _MA_AD_MEDIA_FILE, 'media_file', 5000000 ) );
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_WEIGHT, 'media_weight', 3, 4, 0 ) );
$form->addElement( new ZariliaFormRadioYN( _MA_AD_MEDIA_DISPLAY, 'media_display', 1, ' ' . _YES . '', ' ' . _NO . '' ) );
//$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );

//$form->insertSplit( _MA_AD_MEDIA_RESIZEOPTIONS );
$form->addElement( new ZariliaFormRadioYN( _MA_AD_MEDIA_RESIZE, 'media_resize', 0, ' ' . _YES . '', ' ' . _NO . '' ) );
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_NEWPREFIX, 'media_ext', 10, 80, @$zariliaMediaConfig['media_prefix'] ), false );
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_NEWWIDTH, 'media_width', 10, 80, '300' ), false );
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_NEWHEIGHT, 'media_height', 10, 80, '250' ), false );
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_NEWQUALITY, 'media_quality', 3, 30, '100' ), false );
$form->addElement( new ZariliaFormRadioYN( _MA_AD_MEDIA_KEEPASPECT, 'media_aspect', 0, ' ' . _YES . '', ' ' . _NO . '' ) );

$form->addElement( new ZariliaFormHidden( 'target', $target ) );
$form->addElement( new ZariliaFormHidden( 'fct', 'media' ) );
$form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
$form->assign( $zariliaTpl );

?>