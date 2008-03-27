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

require_once $root_path . '/language/' . $zariliaConfig['language'] . '.php';

return $config = array( "name" => "widgeditor",
    "class" => "widgEditor",
    "file" => $root_path . "/editor.php",
    "title" => 'widgEditor',
    "order" => 3,
    "nohtml" => 1
    );

?>