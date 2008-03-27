<?php
// $Id: editor_registry.php,v 1.1 2007/03/16 02:42:25 catzwolf Exp $
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
/**
 * ZARILIA editor registry
 *
 * @author phppp (D.J.)
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
global $zariliaConfig;

$current_path = __FILE__;
if ( DIRECTORY_SEPARATOR != "/" ) {
    $current_path = str_replace( strpos( $current_path, "\\\\", 2 ) ? "\\\\" : DIRECTORY_SEPARATOR, "/", $current_path );
}
$root_path = dirname( $current_path );

if ( file_exists( $root_path . '/language/' . $zariliaConfig['language'] . '.php' ) ) {
    include_once $root_path . '/language/' . $zariliaConfig['language'] . '.php';
}elseif ( file_exists( $root_path . '/language/english.php' ) ) {
    include_once $root_path . '/language/english.php';
} else {
    trigger_error( 'Could not find language file for Editor Registry, language defines for this page will not be used', E_USER_WARNING );
}

return $config = array( "name" => "dhtmltextarea",
    "class" => "FormDhtmlTextArea",
    "file" => $root_path . "/dhtmltextarea.php",
    "title" => _ZAR_EDITOR_DHTMLTEXTAREA,
    "order" => 2,
    "nohtml" => 1
    );

?>