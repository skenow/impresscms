<?php
// $Id: functions.php,v 1.1 2007/03/16 02:36:44 catzwolf Exp $
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

function xlanguage_convert_encoding( $value, $out_charset, $in_charset )
{
    if ( is_array( $value ) ) {
        foreach( $value as $key => $val ) {
            $value[$key] = xlanguage_convert_encoding( $val, $out_charset, $in_charset );
        } 
    } else {
        $value = xlanguage_convert_item( $value, $out_charset, $in_charset );
    } 
    return $value;
} 

function xlanguage_convert_item( $value, $out_charset, $in_charset )
{
    if ( strtolower( $in_charset ) == strtolower( $out_charset ) ) {
        return $value;
    } 
    $xconv_handler = @zarilia_getaddonhandler( 'xconv', 'xconv', true );
    if ( is_object( $xconv_handler ) && $converted_value = @$xconv_handler->convert_encoding( $value, $out_charset, $in_charset ) ) {
        // echo "<br />converted_value:$converted_value";
        return $converted_value;
    } 
    if ( ZAR_USE_MULTIBYTES && function_exists( 'mb_convert_encoding' ) ) {
        $converted_value = @mb_convert_encoding( $value, $out_charset, $in_charset );
    } elseif ( function_exists( 'iconv' ) ) {
        $converted_value = @iconv( $in_charset, $out_charset, $value );
    } 
    $value = empty( $converted_value )?$value:$converted_value;

    return $value;
} 

function xlanguage_createConfig( $xhandler = null )
{
    $file_config = XLANGUAGE_CONFIG_FILE;
    if ( !$fp = fopen( $file_config, 'w' ) ) {
        echo "<br> the config file can not be created: " . $file_config;
        return false;
    } 

    $file_content = "<?php";
    if ( is_object( $xhandler ) ) $xlang_handler = &$xhandler;
    else $xlang_handler = &zarilia_gethandler( 'language' );
    $baseArray = &$xlang_handler->getAll();
    if ( is_array( $baseArray ) && count( $baseArray ) > 0 ) {
        $file_content .= "\n	\$" . XLANGUAGE_CONFIG_VAR . "['xlanguage_base'] = array(";
        foreach( $baseArray as $lang ) {
            $file_content .= "\n		\"" . $lang->getVar( 'lang_name' ) . "\"=>array(";
            $file_content .= "\n			\"lang_name\"=>\"" . $lang->getVar( 'lang_name' ) . "\",";
            $file_content .= "\n			\"lang_desc\"=>\"" . $lang->getVar( 'lang_desc' ) . "\",";
            $file_content .= "\n			\"lang_charset\"=>\"" . $lang->getVar( 'lang_charset' ) . "\",";
            $file_content .= "\n			\"lang_code\"=>\"" . $lang->getVar( 'lang_code' ) . "\",";
            $file_content .= "\n			\"lang_image\"=>\"" . $lang->getVar( 'lang_image' ) . "\",";
            $file_content .= "\n			\"lang_id\"=>" . $lang->getVar( 'lang_id' ) . ",";
            $file_content .= "\n			\"weight\"=>" . $lang->getVar( 'weight' ) . "";
            $file_content .= "\n		),";
        } 
        $file_content .= "\n	);";
    } 

    $extArray = &$xlang_handler->getAll( false );
    if ( is_array( $extArray ) && count( $extArray ) > 0 ) {
        $file_content .= "\n	\$" . XLANGUAGE_CONFIG_VAR . "['xlanguage_ext'] = array(";
        foreach( $extArray as $lang ) {
            $file_content .= "\n		\"" . $lang->getVar( 'lang_name' ) . "\"=>array(";
            $file_content .= "\n			\"lang_name\"=>\"" . $lang->getVar( 'lang_name' ) . "\",";
            $file_content .= "\n			\"lang_desc\"=>\"" . $lang->getVar( 'lang_desc' ) . "\",";
            $file_content .= "\n			\"lang_charset\"=>\"" . $lang->getVar( 'lang_charset' ) . "\",";
            $file_content .= "\n			\"lang_code\"=>\"" . $lang->getVar( 'lang_code' ) . "\",";
            $file_content .= "\n			\"lang_image\"=>\"" . $lang->getVar( 'lang_image' ) . "\",";
            $file_content .= "\n			\"lang_base\"=>\"" . $lang->getVar( 'lang_base' ) . "\",";
            $file_content .= "\n			\"lang_id\"=>" . $lang->getVar( 'lang_id' ) . ",";
            $file_content .= "\n			\"weight\"=>" . $lang->getVar( 'weight' ) . "";
            $file_content .= "\n		),";
        } 
        $file_content .= "\n	);";
    } 

    $file_content .= "\n?>";
    fputs( $fp, $file_content );
    fclose( $fp );
    return true;
} 

function &xlanguage_loadConfig( $xhandler = null )
{
    $file_config = XLANGUAGE_CONFIG_FILE;
    if ( !file_exists( $file_config ) ) xlanguage_createConfig( $xhandler );
    if ( !is_readable( $file_config ) ) {
        // echo "<br> the config file can not be read: ".$file_config;
        return false;
    } else {
        include_once( $file_config ); 
        // echo "<br> load config";
        // echo "<pre>";print_r($cached_config);echo "</pre>";
        return ${XLANGUAGE_CONFIG_VAR};
    } 
} 

/**
 * Analyzes some PHP environment variables to find the most probable language
 * that should be used
 * 
 * @param string $ string to analyze
 * @param integer $ type of the PHP environment variable which value is $str
 * @global array    the list of available translations
 * @global string   the retained translation keyword
 * @access private 
 */
function xlanguage_lang_detect( $str = '', $envType = '' )
{
    global $available_languages;

    foreach ( $available_languages AS $key => $value ) {
        // $envType =  1 for the 'HTTP_ACCEPT_LANGUAGE' environment variable,
        // 2 for the 'HTTP_USER_AGENT' one
        $expr = $value[0];
        if ( strpos( $expr, '[-_]' ) === false ) {
            $expr = str_replace( '|', '([-_][[:alpha:]]{2,3})?|', $expr );
        } 
        if ( ( $envType == 1 && eregi( '^(' . $expr . ')(;q=[0-9]\\.[0-9])?$', $str ) ) || ( $envType == 2 && eregi( '(\(|\[|;[[:space:]])(' . $expr . ')(;|\]|\))', $str ) ) ) {
            $lang = $key; 
            // echo "<br>detected lang:$lang";
            // if($lang != 'en')
            break;
        } 
    } 
    return $lang;
} 

function xlanguage_detectLang()
{
    global $available_languages, $_SERVER;

    if ( !empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
        $HTTP_ACCEPT_LANGUAGE = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    } 

    if ( !empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
        $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
    } 
    // echo "<br>HTTP_ACCEPT_LANGUAGE:$HTTP_ACCEPT_LANGUAGE";
    // echo "<br>HTTP_USER_AGENT:$HTTP_USER_AGENT";
    $lang = '';
    $zarilia_lang = ''; 
    // 1. try to findout user's language by checking its HTTP_ACCEPT_LANGUAGE
    // variable
    if ( empty( $lang ) && !empty( $HTTP_ACCEPT_LANGUAGE ) ) {
        $accepted = explode( ',', $HTTP_ACCEPT_LANGUAGE );
        $acceptedCnt = count( $accepted );
        reset( $accepted );
        for ( $i = 0; $i < $acceptedCnt; $i++ ) {
            $lang = xlanguage_lang_detect( $accepted[$i], 1 );
            if ( strncasecmp( $lang, 'en', 2 ) ) {
                break;
            } 
        } 
    } 
    // 2. try to findout user's language by checking its HTTP_USER_AGENT variable
    if ( empty( $lang ) && !empty( $HTTP_USER_AGENT ) ) {
        $lang = xlanguage_lang_detect( $HTTP_USER_AGENT, 2 );
    } 
    // 3. If we catch a valid language, configure it
    if ( !empty( $lang ) ) {
        $zarilia_lang = $available_languages[$lang][1];
    } 
    return $zarilia_lang;
} 

function xlanguage_encoding( $output )
{
    global $xlanguage;
    $output = xlanguage_ml( $output );
    $in_charset = $xlanguage["charset_base"];
    $out_charset = $xlanguage["charset"];

    return $output = xlanguage_convert_encoding( $output, $out_charset, $in_charset );
} 

function xlanguage_get2replace($text, $language, $p = false) {
	$rez = array();
	if (!is_array($language)) $language = array($language);
	$pattern = array();
	foreach ($language as $it) {
		$pattern[] = "(\[$it\]([^\[].*)\[\/$it\])";
	}
	$pattern = '/'.implode('|',$pattern).'/Ui';
	if ($p) {
		preg_match_all($pattern, $text, $rez, PREG_SET_ORDER);
		$r2 = array();
		$count = count($rez);
		for($i=0;$i<$count;$i++) {
			$c2 = count($rez[$i]);
			$r2[0][] = $rez[$i][$c2-2];
			$r2[1][] = $rez[$i][$c2-1];
		}
		unset($rez);
		return $r2;	
	}
	preg_match_all($pattern, $text, $rez);
	return $rez[0];
}

function draw_array($arr) {
	if (!is_array($arr)) return $arr;
	$rez = "<table>";
	foreach ($arr as $key => $value) {
		$rez .= "<tr>";
		$rez .= "<td>[$key]-----&gt;</td>";
		$rez .= "<td>".draw_array($value)."</td>";
		$rez .= "</tr>";
	}
	$rez .= "</table>";
	return $rez;
}

function xlanguage_ml( $s )
{
	global $zariliaConfig;

	$langz = array();
	$langs = &$GLOBALS["xlanguage_handler"]->getAll( true );
	$lang = $langs[$zariliaConfig['language']]->getVar( "lang_code" );
	foreach ($langs as $language) {
		$it = $language->getVar( "lang_code" );
		if ($it==$lang) continue;
		$langz[] = $it;
	}
	$rez = xlanguage_get2replace($s, $langz);
	$rez2 = xlanguage_get2replace($s, $lang, true);
	if (!isset($rez2[0])) 
		return $s;
	unset($langz);
	return str_replace($rez2[0], $rez2[1], str_replace($rez, '', $s));
} 

function xlanguage_ml_escape_bracket( $matches )
{
    return str_replace( '[', '&#91;', $matches[1] ) ;
} 

// added by MekDrop 2006.06.29
function translate($text, $language=null) {
	global $zariliaConfig;
	if (!strstr($text,'[')) return $text;
	if ($language!=null) {
		$tmp_language = $zariliaConfig['language'];
		$zariliaConfig['language'] = $language;
		$rez = xlanguage_ml($text);
		$zariliaConfig['language'] = $tmp_language;
		unset($tmp_language);
	} else {
		$rez = xlanguage_ml($text);
	}
	return $rez;
}

?>