<?php
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
define( 'ZAR_XMLRPC', 1 );
include './mainfile.php';
error_reporting( 0 );

include_once ZAR_ROOT_PATH . '/class/xml/rpc/xmlrpctag.php';
include_once ZAR_ROOT_PATH . '/class/xml/rpc/xmlrpcparser.php';
$response = new ZariliaXmlRpcResponse();
$parser = new ZariliaXmlRpcParser( $GLOBALS['HTTP_RAW_POST_DATA'] );
if ( !$parser->parse() ) {
    $response->add( new ZariliaXmlRpcFault( 102 ) );
} else {
    $addon_handler = &zarilia_gethandler( 'addon' );
    $addon = &$addon_handler->getByDirname( 'news' );
    if ( !is_object( $addon ) ) {
        $response->add( new ZariliaXmlRpcFault( 110 ) );
    } else {
        $methods = explode( '.', $parser->getMethodName() );
        switch ( $methods[0] ) {
            case 'blogger':
                include_once ZAR_ROOT_PATH . '/class/xml/rpc/bloggerapi.php';
                $rpc_api = new BloggerApi( $parser->getParam(), $response, $addon );
                break;
            case 'metaWeblog':
                include_once ZAR_ROOT_PATH . '/class/xml/rpc/metaweblogapi.php';
                $rpc_api = new MetaWeblogApi( $parser->getParam(), $response, $addon );
                break;
            case 'mt':
                include_once ZAR_ROOT_PATH . '/class/xml/rpc/movabletypeapi.php';
                $rpc_api = new MovableTypeApi( $parser->getParam(), $response, $addon );
                break;
            case 'zarilia':
            default:
                include_once ZAR_ROOT_PATH . '/class/xml/rpc/zariliaapi.php';
                $rpc_api = new ZariliaApi( $parser->getParam(), $response, $addon );
                break;
        }
        $method = $methods[1];
        if ( !method_exists( $rpc_api, $method ) ) {
            $response->add( new ZariliaXmlRpcFault( 107 ) );
        } else {
            $rpc_api->$method();
        }
    }
}
$payload = &$response->render();
// $fp = fopen(ZAR_CACHE_PATH.'/xmllog.txt', 'w');
// fwrite($fp, $payload);
// fclose($fp);
header( 'Server: ZARILIA XML-RPC Server' );
header( 'Content-type: text/xml' );
header( 'Content-Length: ' . strlen( $payload ) );
echo $payload;

?>