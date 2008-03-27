<?php
// $Id: control.func.php,v 1.1 2007/03/16 02:40:18 catzwolf Exp $
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
//ob_start();

if ( !defined( 'ZAR_ROOT_PATH' ) ) {
    if ( is_string( $_POST["xajaxargs"][3] ) ) {
        $_POST["xajaxargs"][3] = ( strtolower( $_POST["xajaxargs"][3] ) == 'false' )?false:true;
    }
	$zariliaOption['isAjax'] = true; 
    if ( !$_POST["xajaxargs"][3] ) $zariliaOption['nocommon'] = true;
    include_once '../../../mainfile.php';
}
$zariliaOption['isAjax'] = true; 
if ( !isset( $zariliaAjax ) ) {
    //require_once dirname( realpath( __FILE__ ) ) . '\xajax\xajax.inc.php';
	if (!defined('ZAR_FRAMEWORK_PATH')) require_once ZAR_ROOT_PATH.'/include/defines.php';
    require_once ZAR_FRAMEWORK_PATH . '/xajax/xajax_core/xajax.inc.php';
    $zariliaAjax = new xajax();
//    $zariliaAjax->errorHandlerOff();
    $zariliaAjax->registerFunction( 'ZariliaControlHandler' );
    $zariliaAjax->processRequest();
}

function ZariliaControlHandler( $name, $type, $function )
{
    global $zariliaAjax, $zariliaConfig, $zariliaUser, $zariliaDB;
    include_once ZAR_CONTROLS_PATH . '/' . strtolower( $type ) . '/handler.inc.php';
//	echo ZAR_ROOT_PATH . "/class/controls/" . strtolower( $type ) . "/handler.inc.php";
    $objResponse = '';
    $temp = "\$objResponse = $function('$name'";
    $array = func_get_args();
    unset( $array[0], $array[1], $array[2], $array[3] );
    foreach( $array as $value ) {
        $value2 = @strtolower( $value );
        if ( $value2 == 'true' ) {
            $temp .= ", true";
            continue;
        }
        if ( $value2 == 'false' ) {
            $temp .= ", false";
            continue;
        }
        if ( $value2 == 'null' ) {
            $temp .= ", null";
            continue;
        }
        if ( strval( intval( $value ) ) == $value ) {
            $temp .= ", $value";
            continue;
        }
		if ( is_array($value)) {
			$temp .= ",".var_export($value, true);
			continue;
		}
        $type = @substr( $value, 0, 1 );
        $value = @substr( $value, 2 );
        if ( $type == 0 ) {
            $temp .= ",\"$value\"";
        } else {
            $temp .= ",unserialize(base64_decode('$value'))";
        }
    }
    $temp .= ");";
//	echo $temp;
    eval( $temp );
    return $objResponse;
}

//ob_end_clean();

?>