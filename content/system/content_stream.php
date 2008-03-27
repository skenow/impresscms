<?php
// $Id: content_stream.php,v 1.1 2007/03/16 02:42:48 catzwolf Exp $
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
// no direct access
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

global $zariliaConfig;
/**
 */
$zariliaOption['template_main'] = 'system_stream.html';

$section_handler = &zarilia_gethandler( 'section' );
$section_obj = $section_handler->getSectionWhere( 'stream' );
if ( $section_obj ) {
    $zariliaTpl->assign( 'section_title', $section_obj->getVar( 'section_title' ) );
    $zariliaTpl->assign( 'section_imageside', $section_obj->getVar( 'section_imageside' ) );
    $image = getthumbImage( $section_obj->getVar( 'section_image' ), $section_obj->getVar( 'section_title' ), 'left', 150, 150 );
    $zariliaTpl->assign( 'section_image', $image );
    $zariliaTpl->assign( 'section_description', $section_obj->getVar( 'section_description' ) );
    $zariliaTpl->assign( 'section_type', $section_obj->getVar( 'section_type' ) );
}

/**
 */
$streaming_handler = &zarilia_gethandler( 'streaming' );
$_streaming_obj = $streaming_handler->getStreamObj( $_values );
if ( $_streaming_obj['count'] ) {
    foreach ( $_streaming_obj['list'] as $obj ) {
        $streaming['streaming_id'] = $obj->getVar( 'streaming_id' );
        $streaming['streaming_title'] = $obj->getVar( 'streaming_title' );
        /*
		*
		**/
		$cpUser = $obj->getcpUser( true, false );
        $streaming['streaming_author'] = $cpUser['name'];
        $streaming['streaming_published'] = $obj->formatTimeStamp( 'streaming_published' );
        $streaming['streaming_description'] = $obj->getVar( 'streaming_description', 'r' );
        $blog_image = getthumbImage( $obj->getVar( 'streaming_image' ), $obj->getVar( 'streaming_title' ), 'absmiddle', 75, 75 );
        $streaming['streaming_image'] = $blog_image;
        $streaming['streaming_play'] = zarilia_img_show( 'play', '' );
        $streaming['streaming_download'] = zarilia_img_show( 'download', '' );
        $zariliaTpl->append( 'streaming', $streaming );
    }
    $page_nav = zarilia_pagnav( $_streaming_obj['count'], $_values['limit'], $_values['start'], 'start', 1, @$addonversion['adminpath'] . '&amp;op=' . @$_values['op'] . '&amp;limit=' . $_values['limit'], 1 );
    $zariliaTpl->assign( 'page_nav', $page_nav );
}
$zariliaTpl->assign( 'page_backbutton', $this->do_backbutton() );

?>