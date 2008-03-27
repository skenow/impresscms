<?php
// $Id: content_rss.php,v 1.1 2007/03/16 02:42:48 catzwolf Exp $
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

$rss_handler = &zarilia_gethandler( 'rss' );
$zariliaOption['template_main'] = 'system_rssindex.html';
$_rss_objs = &$rss_handler->getRssMainObj( array(), 1 );
/**
 */
$zariliaTpl->assign( 'lang_headlines', _HL_HEADLINES );
for ( $i = 0; $i < $_rss_objs['count']; $i++ ) {
    $zariliaTpl->append( 'feed_sites', array( 'id' => $_rss_objs['list'][$i]->getVar( 'rss_id' ), 'name' => $_rss_objs['list'][$i]->getVar( 'rss_name' ) ) );
}
if ( $this->_id == 0 && $_rss_objs['count'] > 0 ) {
    $this->_id = $_rss_objs['list'][0]->getVar( 'rss_id' );
}
if ( $this->_id > 0 ) {
    $_rss_obj2 = &$rss_handler->get( $this->_id );
    if ( is_object( $_rss_obj2 ) ) {
        $renderer = &$rss_handler->zariliarss_getrenderer( $_rss_obj2 );
        if ( !$renderer->renderFeed() ) {
            if ( $zariliaConfig['debug_mode'] == 2 ) {
                $zariliaTpl->assign( 'headline', '<p>' . sprintf( _HL_FAILGET, $_rss_obj2->getVar( 'rss_name' ) ) . '<br />' . $renderer->getErrors() . '</p>' );
            }
        } else {
            $zariliaTpl->assign( 'headline', $renderer->getFeed() );
        }
    }
} else {
    $zariliaTpl->assign( 'headline', 'error' );
}

?>