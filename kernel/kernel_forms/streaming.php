<?php
// $Id: streaming.php,v 1.2 2007/04/21 09:44:54 catzwolf Exp $
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

require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

global $addonversion, $zariliaUser, $perm_handler;

$caption = ( !$this->isNew() ) ? $caption = sprintf( _MA_AD_ESTREAMING_MODIFY, $this->getVar( 'link_title' ) ) : _MA_AD_ESTREAMING_CREATE;

$form = new ZariliaThemeForm( $caption, 'link_form', $addonversion['adminpath'] );
$form->setExtra( 'enctype="multipart/form-data"' );

/*Set display name*/
if ( $this->isNew() ) {
    $form->addElement( new ZariliaFormSelectGroup( _MD_AM_ESTREAMING_RGRP, 'readgroup', true, ZAR_GROUP_ADMIN, 5, true ) );
} else {
    $form->addElement( new ZariliaFormSelectGroup( _MD_AM_ESTREAMING_RGRP, 'readgroup', true, $perm_handler->getGroupIds( 'link_read', $this->getVar( 'link_id' ) ), 5, true ) );
}

$link_uid = new ZariliaFormSelectUser( _MA_AD_ESTREAMING_SELECTUID, 'link_uid', true, $this->getVar( 'link_uid' ), 1, false );
$link_uid->setDescription( _MA_AD_ESTREAMING_SELECTUID_DSC );
$form->addElement( $link_uid );

$content_alias = new ZariliaFormText( _MD_AM_ESTREAMING_ALIAS, 'link_alias', 50, 100, $this->getVar( 'link_alias', 'e' ) );
$content_alias->setDescription( _MD_AM_ESTREAMING_ALIAS_DSC );
$form->addElement( $content_alias, false );

$link_title = new ZariliaFormText( _MA_AD_ESTREAMING_TITLE, 'link_title', 50, 60, $this->getVar( 'link_title', 'e' ) );
$link_title->setDescription( _MA_AD_ESTREAMING_TITLE_DSC );
$form->addElement( $link_title, true );

$link_file = new ZariliaFormSelectDirList( _MA_AD_ESTREAMING_SELECT, 'link_file', $this->getVar( 'link_file', 'e' ), 5, false, ZAR_UPLOAD_PATH . '/streams', '', array( 'mp3', 'flv', 'swf', 'wmv' ) );
$link_file->setDescription( _MA_AD_ESTREAMING_SELECT_DSC );
$form->addElement( $link_file, false );

$zariliaConfigUser['avatar_maxsize'] = '10000000000';
$upload_file = new ZariliaFormFile( _MA_AD_ESTREAMING_UPLOAD, 'upload_file2', $zariliaConfigUser['avatar_maxsize'] );
$upload_file->setDescription( _MA_AD_ESTREAMING_UPLOAD_DSC );
$form->addElement( $upload_file, false );

$options['name'] = 'link_description';
$options['value'] = $this->getVar( 'link_description', 'e' );
$ele = new ZariliaFormEditor( _MD_AM_ESTREAMING_TEXT, $zariliaUser->getVar( "editor" ), $options, $nohtml = false, $onfailure = "textarea" );
$ele->setNocolspan( 1 );
$form->addElement( $ele );

$link_image = new ZariliaFormSelectImg( _MA_AD_ESTREAMING_SELECTIMAGE, 'link_image', $this->getVar( 'link_image' ), 'zarilia_image', 1 );
$link_image->setDescription( _MA_AD_ESTREAMING_SELECTIMAGE_DSC );
$form->addElement( $link_image );

$link_published = new ZariliaFormTextDateSelect( _MA_AD_ESTREAMING_PUBLISHED, 'link_published', 20, $this->getVar( 'link_published' ), true );
$link_published->setDescription( _MA_AD_ESTREAMING_PUBLISHED_DSC );
$form->addElement( $link_published, false );

/*Set display name*/
$link_weight = new ZariliaFormText( _MA_AD_ESTREAMING_WEIGHT, 'link_weight', 3, 4, $this->getVar( 'link_weight', 'e' ) );
$link_weight->setDescription( _MA_AD_ESTREAMING_WEIGHT_DSC );
$form->addElement( $link_weight, true );

/*Set display name*/
$link_display = new ZariliaFormRadioYN( _MA_AD_ESTREAMING_DISPLAY, 'link_display', $this->getVar( 'link_display' ) , ' ' . _YES . '', ' ' . _NO . '' );
$link_display->setDescription( _MA_AD_ESTREAMING_DISPLAY_DSC );
$form->addElement( $link_display, false );

$form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
$form->addElement( new ZariliaFormHidden( 'link_id', $this->getVar( 'link_id' ) ) );

/*button_tray*/
$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
$form->display();

?>