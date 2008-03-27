<?php
// $Id: langform.inc.php,v 1.1 2007/03/16 02:36:44 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Xlanguage: eXtensible Language Management For Zarilia               //
// Copyright (c) 2004 Zarilia China Community                      //
// <http://www.zarilia.org.cn/>                             //
// ------------------------------------------------------------------------ //
// This program is free software; you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License, or        //
// (at your option) any later version.                                      //
// //
// You may not change or alter any portion of this comment or credits       //
// of supporting developers from this source code or any supporting         //
// source code which is considered copyrighted (c) material of the          //
// original comment or credit authors.                                      //
// //
// This program is distributed in the hope that it will be useful,          //
// but WITHOUT ANY WARRANTY; without even the implied warranty of           //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
// GNU General Public License for more details.                             //
// //
// You should have received a copy of the GNU General Public License        //
// along with this program; if not, write to the Free Software              //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------ //
// Author: D.J.(phppp) php_pp@hotmail.com                                    //
// URL: http://www.zarilia.org.cn                                              //
// ------------------------------------------------------------------------- //
include ZAR_ROOT_PATH . "/class/zariliaformloader.php";
$sform = new ZariliaThemeForm( _AM_XLANG_EDITLANG, "langform", zarilia_getenv( 'PHP_SELF' ) . "?fct=" . $fct );
if ( $isBase == false) {
    $lang_select = new ZariliaFormSelect( _MA_AD_LANG_NAME, 'lang_name', $lang_name );
    $lang_select->addOptionArray( $xlanguage_handler->getZariliaLangList() );
    $sform->addElement( $lang_select, true );
} else {
    $sform->addElement( new ZariliaFormText( _MA_AD_LANG_NAME, 'lang_name', 50, 255, $lang_name ), true );
}

$sform->addElement( new ZariliaFormText( _AM_XLANG_DESC, 'lang_desc', 50, 255, $lang_desc ) );
$sform->addElement( new ZariliaFormText( _MA_AD_LANG_CHARSET, 'lang_charset', 50, 255, $lang_charset ), true );
$sform->addElement( new ZariliaFormText( _MA_AD_LANG_CODE, 'lang_code', 4, 4, $lang_code ), true );
if ( !$isBase ) {
    $baseList = &$xlanguage_handler->getAll();
    $base_list = array();
    foreach( $baseList as $base => $baselang ) {
        $base_list[$base] = $base;
    }
    $base_select = new ZariliaFormSelect( _MA_AD_LANG_BASE, 'lang_base', $lang_base );
    $base_select->addOptionArray( $base_list );
    $sform->addElement( $base_select, true );
}
$sform->addElement( new ZariliaFormText( _MA_AD_WEIGHT, 'weight', 5, 5, $weight ) );

$image_option_tray = new ZariliaFormElementTray( _MA_AD_LANG_IMAGE, '' );
$image_array = &ZariliaLists::getImgListAsArray( ZAR_ROOT_PATH . "/images/flags/" );
$lang_image = ( empty( $lang_image ) ) ? 'noflag.gif' : $lang_image;
$image_select = new ZariliaFormSelect( '', 'lang_image', $lang_image );
$image_select->addOptionArray( $image_array );
$image_select->setExtra( "onchange='showImgSelected(\"image\", \"lang_image\", \"/images/flags/\", \"\", \"" . ZAR_URL . "\")'" );
$image_tray = new ZariliaFormElementTray( '', '&nbsp;' );
$image_tray->addElement( $image_select );
if ( !empty( $lang_image ) ) {
    $image_tray->addElement( new ZariliaFormLabel( '', "<div style='padding: 8px;'><img src='" . ZAR_URL . "/images/flags/" . $lang_image . "' name='image' id='image' alt='' /></div>" ) );
} else {
    $image_tray->addElement( new ZariliaFormLabel( '', "<div style='padding: 8px;'><img src='" . ZAR_URL . "/images/blank.gif' name='image' id='image' alt='' /></div>" ) );
}
$image_option_tray->addElement( $image_tray );
$sform->addElement( $image_option_tray );

if ( isset( $lang_id ) ) {
    $sform->addElement( new ZariliaFormHidden( 'lang_id', $lang_id ) );
}
$sform->addElement( new ZariliaFormHidden( 'type', $type ) );

$button_tray = new ZariliaFormElementTray( '', '' );
$button_tray->addElement( new ZariliaFormHidden( 'op', 'save' ) );
$button_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
$button_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ) );
$button_tray->addElement( new ZariliaFormButton( '', 'submit', _SUBMIT, 'submit' ) );
$sform->addElement( $button_tray );
$sform->display();

?>