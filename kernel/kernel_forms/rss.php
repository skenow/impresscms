<?php
// $Id: rss.php,v 1.1 2007/03/16 02:44:13 catzwolf Exp $
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

$caption = ( !$this->isNew() ) ? $caption = sprintf( _MA_AD_ERSS_MODIFY, $this->getVar( 'rss_name' ) ) : _MA_AD_ERSS_CREATE;
$form = new ZariliaThemeForm( $caption, 'zariliarss_form', $addonversion['adminpath'] );
$form->addElement( new ZariliaFormText( _MA_AD_SITENAME . ": ", 'rss_name', 50, 255, $this->getVar( 'rss_name' ) ), true );
$form->addElement( new ZariliaFormText( _MA_AD_URL . ": ", 'rss_url', 50, 255, $this->getVar( 'rss_url' ) ), true );
$form->addElement( new ZariliaFormText( _MA_AD_URLEDFXML . ": ", 'rss_rssurl', 50, 255, $this->getVar( 'rss_rssurl' ) ), true );
$form->addElement( new ZariliaFormText( _MA_AD_ORDER . ": ", 'rss_weight', 4, 3, $this->getVar( 'rss_weight' ) ) );
$enc_sel = new ZariliaFormSelect( _MA_AD_ENCODING . ": ", 'rss_encoding', $this->getVar( 'rss_encoding' ) );
$enc_sel->addOptionArray( array( 'utf-8' => 'UTF-8', 'iso-8859-1' => 'ISO-8859-1', 'us-ascii' => 'US-ASCII' ) );
$form->addElement( $enc_sel );
$cache_sel = new ZariliaFormSelect( _MA_AD_CACHETIME . ": ", 'rss_cachetime', $this->getVar( 'rss_cachetime' ) );
$cache_sel->addOptionArray( array( '3600' => _HOUR, '18000' => sprintf( _HOURS, 5 ), '86400' => _DAY, '259200' => sprintf( _DAYS, 3 ), '604800' => _WEEK, '2592000' => _MONTH ) );
$form->addElement( $cache_sel );

$form->insertSplit( _MA_AD_MAINSETT );
$form->addElement( new ZariliaFormRadioYN( _MA_AD_DISPLAY . ": ", 'rss_display', $this->getVar( 'rss_display' ), _YES, _NO ) );
$form->addElement( new ZariliaFormRadioYN( _MA_AD_DISPIMG . ": ", 'rss_mainimg', $this->getVar( 'rss_mainimg' ), _YES, _NO ) );
$form->addElement( new ZariliaFormRadioYN( _MA_AD_DISPFULL . ": ", 'rss_mainfull', $this->getVar( 'rss_mainfull' ), _YES, _NO ) );
$mmax_sel = new ZariliaFormSelect( _MA_AD_DISPMAX . ": ", 'rss_mainmax', $this->getVar( 'rss_mainmax' ) );
$mmax_sel->addOptionArray( array( '1' => 1, '5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30 ) );
$form->addElement( $mmax_sel );

$form->insertSplit( _MA_AD_BLOCKSETT );
$form->addElement( new ZariliaFormRadioYN( _MA_AD_ASBLOCK . ": ", 'rss_asblock', $this->getVar( 'rss_asblock' ), _YES, _NO ) );
$form->addElement( new ZariliaFormRadioYN( _MA_AD_DISPIMG . ": ", 'rss_blockimg', $this->getVar( 'rss_blockimg' ), _YES, _NO ) );
$bmax_sel = new ZariliaFormSelect( _MA_AD_DISPMAX . ": ", 'rss_blockmax', $this->getVar( 'rss_blockmax' ) );
$bmax_sel->addOptionArray( array( '1' => 1, '5' => 5, '10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30 ) );
$form->addElement( $bmax_sel );

$form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
/*button_tray*/
$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );

?>