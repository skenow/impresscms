<?php
// $Id: api.php,v 1.2 2007/04/21 09:44:16 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           					//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               	//
// -------------------------------------------------------------------------//
// Xlanguage: eXtensible Language Management For Zarilia               		//
// Copyright (c) 2004 xoops China Community                      			//
// <http://www.xoops.org.cn/>                             					//
// Author: D.J.(phppp) php_pp@hotmail.com                                   //
// URL: http://www.xoops.org.cn                                             //
// -------------------------------------------------------------------------//
global $xlanguage;

require_once ZAR_ROOT_PATH . '/addons/system/admin/multilanguage/vars.php';
require_once ZAR_ROOT_PATH . '/addons/system/admin/multilanguage/functions.php';

$cookie_prefix = preg_replace( "/[^a-z_0-9]+/i", "_", preg_replace( "/(http(s)?:\/\/)?(www.)?/i", "", ZAR_URL ) );
$cookie_var = $cookie_prefix . "_" . XLANGUAGE_LANG_TAG;

$xlanguage["op"] = false;
if ( !empty( $_GET[XLANGUAGE_LANG_TAG] ) ) {
    $cookie_path = "/";
    setcookie( $cookie_var, $_GET[XLANGUAGE_LANG_TAG], time() + 3600 * 24 * 30, $cookie_path, '', 0 );
    $xlanguage["lang"] = $_GET[XLANGUAGE_LANG_TAG];
} elseif ( isset( $_COOKIE[$cookie_var] ) ) {
    $xlanguage["lang"] = $_COOKIE[$cookie_var];
    if ( preg_match( "/[&|?]\b" . XLANGUAGE_LANG_TAG . "\b=/i", $_SERVER['REQUEST_URI'] ) ) {
    } elseif ( strpos( $_SERVER['REQUEST_URI'], "?" ) ) {
        $_SERVER['REQUEST_URI'] .= "&" . XLANGUAGE_LANG_TAG . "=" . $xlanguage["lang"];
    } else {
        $_SERVER['REQUEST_URI'] .= "?" . XLANGUAGE_LANG_TAG . "=" . $xlanguage["lang"];
    }
} elseif ( $lang = xlanguage_detectLang() ) {
    $xlanguage["lang"] = $lang;
} else {
    $xlanguage["lang"] = $zariliaConfig['language'];
}
$xlanguage_handler = &zarilia_gethandler( 'language' );
$xlanguage_handler->loadConfig();
$lang = $xlanguage_handler->getByName( $xlanguage["lang"] );
if ( !is_object( $lang ) ) {
//    $lang = $xlanguage_handler->get( 1, true );
	$lang = &$xlanguage_handler->getFirst();
    $zariliaConfig['language'] = ( is_object( $lang ) ) ? $lang->getVar( 'lang_name' ) : 'lithuanian';
    $zariliaConfig['lang_code'] = ( is_object( $lang ) ) ? $lang->getVar( 'lang_code' ) : 'lt';
}

if ( is_object( $lang )/*&& strcasecmp( $lang->getVar( 'lang_name' ), $zariliaConfig['language'] )*/ ) {
    if ( $lang->isBase() ) {
        $zariliaConfig['language'] = $lang->getVar( 'lang_name' );
		$zariliaConfig['lang_code'] = $lang->getVar( 'lang_code' );
    } else {
        $lang_base = $xlanguage_handler->getByName( $lang->getVar( 'lang_base' ) );
        if ( is_object( $lang_base ) ) {
            $xlanguage['charset_base'] = $lang_base->getVar( 'lang_charset' );
            $xlanguage['charset'] = $lang->getVar( 'lang_charset' );
            $xlanguage['code'] = $lang->getVar( 'lang_code' );
            $xlanguage['op'] = true;
            $zariliaConfig['language'] = $lang_base->getVar( 'lang_name' );
            unset( $lang_base );
        }
    }
    unset( $lang );
}


//var_dump($zariliaConfig['lang_code']);

if ( $xlanguage["op"] ) {
    // if(CONV_REQUEST && (!empty($_GET)||!empty($_POST))){
    if ( !empty( $_POST ) ) {
        $in_charset = $xlanguage["charset"];
        $out_charset = $xlanguage["charset_base"];
        // $CONV_REQUEST_array=array("_GET", "_POST");
        $CONV_REQUEST_array = array( "_POST" );
        foreach ( $CONV_REQUEST_array as $HV ) {
            if ( !empty( ${$HV} ) ) {
                ${$HV} = xlanguage_convert_encoding( ${$HV}, $out_charset, $in_charset );
            }
            $GLOBALS["HTTP" . $HV . "_VARS"] = ${$HV};
        }
    }
    // ob_start( "xlanguage_encoding" );
} else {
    // ob_start( "xlanguage_ml" );
}

?>