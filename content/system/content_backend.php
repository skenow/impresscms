<?php
// $Id: content_backend.php,v 1.1 2007/03/31 04:03:27 catzwolf Exp $
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
// no direct access
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

global $zariliaConfig;

include_once ZAR_ROOT_PATH . '/class/template.php';
if ( function_exists( 'mb_http_output' ) ) {
    mb_http_output( 'pass' );
}
header ( 'Content-Type:text/xml; charset=utf-8' );

$zariliaTpl = new ZariliaTpl();
$zariliaTpl->zarilia_setCaching( 2 );
$zariliaTpl->zarilia_setCacheTime( 3600 );
// if ( !$zariliaTpl->is_cached( 'db:system_rss.html' ) ) {
$zariliaOption['template_main'] = 'system_rss.html';

$_obj = $this->getContentObj( $_values );

if ( $_obj['count'] ) {
    $zariliaTpl->assign( 'channel_title', zarilia_utf8_encode( htmlspecialchars( $zariliaConfig['sitename'], ENT_QUOTES ) ) );
    $zariliaTpl->assign( 'channel_link', ZAR_URL . '/' );
    $zariliaTpl->assign( 'channel_desc', zarilia_utf8_encode( htmlspecialchars( $zariliaConfig['slogan'], ENT_QUOTES ) ) );
    $zariliaTpl->assign( 'channel_lastbuild', formatTimestamp( time(), 'rss' ) );
    $zariliaTpl->assign( 'channel_webmaster', $zariliaConfig['adminmail'] );
    $zariliaTpl->assign( 'channel_editor', $zariliaConfig['adminmail'] );
    $zariliaTpl->assign( 'channel_category', 'News' );
    $zariliaTpl->assign( 'channel_generator', 'Zarilia' );
    $zariliaTpl->assign( 'channel_language', _LANGCODE );
    $zariliaTpl->assign( 'image_url', ZAR_URL . '/images/logo.gif' );
    $dimention = getimagesize( ZAR_ROOT_PATH . '/images/logo.gif' );
    if ( empty( $dimention[0] ) ) {
        $width = 88;
    } else {
        $width = ( $dimention[0] > 144 ) ? 144 : $dimention[0];
    }
    if ( empty( $dimention[1] ) ) {
        $height = 31;
    } else {
        $height = ( $dimention[1] > 400 ) ? 400 : $dimention[1];
    }
    $zariliaTpl->assign( 'image_width', $width );
    $zariliaTpl->assign( 'image_height', $height );

    foreach ( $_obj['list'] as $_content_obj ) {
        $zariliaTpl->append( 'items',
            array( 'title' => zarilia_utf8_encode( $_content_obj->getVar( 'content_title' ) ),
                'link' => ZAR_URL . '/index.php?page_type=' . $_content_obj->getVar( 'content_type' ) . '&amp;id=' . $_content_obj->getVar( 'content_id' ),
                'guid' => ZAR_URL . '/index.php?page_type=' . $_content_obj->getVar( 'content_type' ) . '&amp;id=' . $_content_obj->getVar( 'content_id' ),
                'pubdate' => formatTimestamp( $_content_obj->getVar( 'content_published' ), 'rss' ),
                'description' => zarilia_utf8_encode( $_content_obj->getVar( 'content_body' ) )
                )
            );
    }
}
// }
$zariliaTpl->display( 'db:system_rss.html' );

?>