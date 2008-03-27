<?php
// $Id: footer.php,v 1.2 2007/03/30 22:03:33 catzwolf Exp $
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

global $zariliaOption, $zariliaCachedTemplateId, $zariliaTpl, $zariliaConfig;

//$phpver = phpversion();
//$useragent = ( isset( $_SERVER["HTTP_USER_AGENT"] ) ) ? $_SERVER["HTTP_USER_AGENT"] : $HTTP_USER_AGENT;
/*
if ( $phpver >= '4.0.4pl1' && ( strstr( $useragent, 'compatible' ) || strstr( $useragent, 'Gecko' ) ) ) {
    if ( extension_loaded( 'zlib' ) ) {
        ob_start( 'ob_gzhandler' );
    }
} else if ( $phpver > '4.0' ) {
    if ( strstr( $HTTP_SERVER_VARS['HTTP_ACCEPT_ENCODING'], 'gzip' ) ) {
        if ( extension_loaded( 'zlib' ) ) {
            $do_gzip_compress = true;
            ob_start();
            ob_implicit_flush( 0 );
            header( 'Content - Encoding: gzip' );
        }
    }
}
*/

if ( !defined( "ZAR_FOOTER_INCLUDED" ) ) {
    define( "ZAR_FOOTER_INCLUDED", 1 );

    require_once ZAR_ROOT_PATH . '/include/notification_select.php';
    if ( isset( $zariliaOption['template_main'] ) ) {
        if ( isset( $zariliaCachedTemplateId ) ) {
            $contents = $zariliaTpl->fetch( 'db:' . $zariliaOption['template_main'], $zariliaCachedTemplateId );
        } else {
            $contents = $zariliaTpl->fetch( 'db:' . $zariliaOption['template_main'] );
        }
    } else {
        if ( isset( $zariliaCachedTemplate ) ) {
            $contents = $zariliaTpl->fetch( $zariliaCachedTemplate, $zariliaCachedTemplateId );
        } else {
            $contents = ob_get_contents();
        }
        ob_end_clean();
    }

    if ( !headers_sent() ) {
        header( 'Content-Type:text/html; charset=' . _CHARSET );
        header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
    }

    if ( $zariliaConfig['gzip_compression'] == 1 && $encoding = tep_check_gzip() ) {
        header( 'Content-Encoding: ' . $encoding );
        $size = strlen( $contents );
        $crc = crc32( $contents );
        $contents = gzcompress( $contents, 9 );
        $contents = substr( $contents, 0, strlen( $contents ) - 4 );
        echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
        $zariliaTpl->assign( 'zarilia_contents', $contents );
        echo pack( 'V', $crc );
        echo pack( 'V', $size );
    } else {
        $zariliaTpl->assign( 'zarilia_contents', $contents );
    }

    $zariliaTpl->assign( 'zarilia_contents', $contents );
    $zariliaTpl->zarilia_setCaching( 0 );
    $zariliaTpl->display( $zariliaConfig['theme_set'] . '/theme.html' );
    /**
     */
    $zariliaLogger->stopTime();
    $zariliaLogger->render();
}

?>