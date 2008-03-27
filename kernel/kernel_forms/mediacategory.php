<?php
// $Id: mediacategory.php,v 1.2 2007/04/21 09:44:54 catzwolf Exp $
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

global $addonversion, $groupperm_handler, $zariliaUser;
require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

$form = new ZariliaThemeForm( ( !$this->isNew() ) ? sprintf( _MA_AD_MEDIA_MODIFY, $this->getVar( 'media_ctitle' ) ) : _MA_AD_MEDIA_CREATE, 'imagecat_form', $addonversion['adminpath'] );
$form->setExtra( 'enctype="multipart/form-data"' );
/**
 */
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_ECATTITLE, 'media_ctitle', 50, 255, $this->getVar( 'media_ctitle', 'e' ) ), true );
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_ECDIRNAME, 'media_cdirname', 50, 255, $this->getVar( 'media_cdirname', 'e' ) ), true );
/*
*/
$options['name'] = 'media_cdescription';
$options['value'] = $this->getVar( 'media_cdescription', 'e' );
$ele = new ZariliaFormEditor( _MA_AD_MEDIA_ECDESCRIPTION, $zariliaUser->getVar( "editor" ), $options, $nohtml = false, $onfailure = "textarea" );
$ele->setNocolspan( 1 );
$form->addElement( $ele, false );
/**
 */
$readgroup = ( $this->isNew() ) ? ZAR_GROUP_ADMIN : $groupperm_handler->getGroupIds( 'mediacategory_read', $this->getVar( 'media_cid' ) );
$form->addElement( new ZariliaFormSelectGroup( _MA_AD_MEDIA_ECATRGRP, 'readgroup', true, $readgroup, 5, true ) );
$writegroup = ( $this->isNew() ) ? ZAR_GROUP_ADMIN : $groupperm_handler->getGroupIds( 'mediacategory_write', $this->getVar( 'media_cid' ) );
$form->addElement( new ZariliaFormSelectGroup( _MA_AD_MEDIA_ECATWGRP, 'writegroup', true, $writegroup, 5, true ) );
/**
 */
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_ECMAXSIZE, 'media_cmaxsize', 10, 10, $this->getVar( 'media_cmaxsize', 'e' ) ) );
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_ECMAXWIDTH, 'media_cmaxwidth', 3, 4, $this->getVar( 'media_cmaxwidth', 'e' ) ) );
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_ECMAXHEIGHT, 'media_cmaxheight', 3, 4, $this->getVar( 'media_cmaxheight', 'e' ) ) );
$form->addElement( new ZariliaFormText( _MA_AD_MEDIA_ECWEIGHT, 'media_cweight', 3, 4, $this->getVar( 'media_cweight', 'e' ) ) );
$form->addElement( new ZariliaFormRadioYN( _MA_AD_MEDIA_ECDISPLAY, 'media_cdisplay', $this->getVar( 'media_cdisplay' ), _YES, _NO ) );
$form->addElement( new ZariliaFormHidden( 'op', 'cat_save' ) );
$form->addElement( new ZariliaFormHidden( 'media_cid', $this->getVar( 'media_cid' ) ) );
/*Button Tray*/
$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
$form->display();

?>