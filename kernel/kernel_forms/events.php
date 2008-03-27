<?php
// $Id: events.php,v 1.2 2007/04/21 09:44:54 catzwolf Exp $
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
include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
$form = new ZariliaThemeForm( @$caption, 'addevent', $addonversion['adminpath'], 'post' );
$form->addElement( new ZariliaFormDateTime( _MD_AM_EVENTTIME, 'NextTime', 15, $this->getVar( 'NextTime' ), true ) );
$repeat_num = new ZariliaFormText( 'How many times to repeat this task?', 'RepeatNum', 5, 5, $this->getVar( 'RepeatNum' ), true );
$repeat_num->setExtra( 'onkeypress="setTimeout(function() {var obj = document.getElementById(\'RepeatNum\'); obj.value = parseInt(obj.value);  if (isNaN(obj.value)) obj.value = 0; if (obj.value<0) obj.value = 0; var obj2 = document.getElementById(\'repeat_block\'); if (obj.value == 1) {obj2.style.display = \'none\';} else {obj2.style.display = \'\';} },200);"' );
// (()?\'hidden\':\'\');}, 200);"');
$form->addElement( $repeat_num );
// obj.value = parseInt(obj.value); ;
$tray = new ZariliaFormElementTray( 'When event must be repeated?', "&nbsp;", 'repeat_block', false );
$tray->addElement( new ZariliaFormText( '', 'RepeatInterval', 5, 5, $this->getVar( 'RepeatInterval' ), false ) );
$list = new ZariliaFormSelect( '', 'RepeatSystem', $this->getVar( 'RepeatSystem' ) );
$list->addOptionArray( array( 'Minute', 'Hour', 'Day', 'Month', 'Year' ) );
$tray->addElement( $list, false );
$form->addElement( $tray );
$form->addElement( new ZariliaFormTextArea( _MD_AM_EVENTCODE, 'Code', $this->getVar( 'Code' ) ), true );

$form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
$form->addElement( new ZariliaFormHidden( 'ID', $this->getVar( 'ID' ) ) );
/**
 */
$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
$form->display();

?>