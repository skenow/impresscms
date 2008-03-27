<?php
// $Id: content_news.php,v 1.4 2007/05/05 11:11:53 catzwolf Exp $
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
if ( $this->_cid ) {
    include_once ZAR_ROOT_PATH . '/class/tree.php';
    /*This is the category to be shown*/
    $category_handler = &zarilia_gethandler( 'category' );
    $categories = $category_handler->getCategorybySection( $this->_page_type );
    if ( $categories['list'] ) {
        $_mytree = new ZariliaObjectTree( $categories['list'], "content_cid", "content_pid" );
        $selection = $_mytree->makeSelBox( 'content_pid', 'content_title', "-", 1, true, '', true );
    }
} else if ( $this->_id ) {
    /*this is the article to be shown*/
    $this->_content_obj = &$this->getContentItem();
    if ( $this->_content_obj->getVar( 'content_meta' ) ) {
        $zariliaTpl->addMeta( 'meta_description', htmlspecialchars( $this->_content_obj->getVar( 'content_meta' ), ENT_QUOTES ) );
    }
    if ( $this->_content_obj->getVar( 'content_keywords' ) ) {
        $zariliaTpl->addMeta( 'meta_keywords', htmlspecialchars( $this->_content_obj->getVar( 'content_keywords' ), ENT_QUOTES ) );
    }
    $cpUser = $this->_content_obj->getcpUser();
    $zariliaTpl->assign( 'content_author', $cpUser['name'] );
    if ( $cpUser['avatar'] ) {
        $cpUser['avatar'] = getthumbImage( $cpUser['avatar'], $cpUser['name'], 'left', 75, 75 );
        $zariliaTpl->assign( 'content_avatar', $cpUser['avatar'] );
    }
    if ( $cpUser['online'] ) {
        $zariliaTpl->assign( 'content_online', $cpUser['online'] );
    }
    $zariliaTpl->assign( 'content_title', $this->_content_obj->getVar( 'content_title' ) );
    $zariliaTpl->assign( 'content_subtitle', $this->_content_obj->getVar( 'content_subtitle' ) );
    $zariliaTpl->assign( 'content_published', $this->_content_obj->formatTimeStamp( 'content_published' ) );
    $zariliaTpl->assign( 'content_updated', $this->_content_obj->formatTimeStamp( 'content_updated' ) );
    $zariliaTpl->assign( 'content_intro', $this->_content_obj->getVar( 'content_intro' ) );
    $zariliaTpl->assign( 'content_body', $this->_content_obj->getVar( 'content_body' ) );
    $zariliaTpl->assign( 'content_icons', $this->_content_obj->getIcons() );
    $zariliaTpl->assign( 'zarilia_pagetitle', $this->_content_obj->getVar( 'content_title' ) );
    $zariliaTpl->addTitle( $this->_content_obj->getVar( 'content_title' ) );
    $zariliaTpl->display( ZAR_ROOT_PATH . '/themes/' . $zariliaConfig['theme_set'] . '/addons/system/system_newspage.html' );
} else {
    /*this is the index page to be shown*/
    $section_handler = &zarilia_gethandler( 'section' );
    $section_obj = $section_handler->getbyType( 'news' );

    if ( $section_obj ) {
        $zariliaOption['template_main'] = 'system_newsindex.html';
        $zariliaTpl->assign( 'section_title', $section_obj->getVar( 'section_title' ) );
        $zariliaTpl->assign( 'section_imageside', $section_obj->getVar( 'section_imageside' ) );
        $image = getthumbImage( $section_obj->getVar( 'section_image' ), $section_obj->getVar( 'section_title' ), 'left', 75, 75 );
        $zariliaTpl->assign( 'section_image', $image );
        $zariliaTpl->assign( 'section_description', $section_obj->getVar( 'section_description' ) );
        $zariliaTpl->assign( 'section_type', $section_obj->getVar( 'section_type' ) );
    } else {
    }
    $content_handler = &zarilia_gethandler( 'content' );
    $category_handler = &zarilia_gethandler( 'category' );
    $category_images = $category_handler->getAllImages();

    $_values['order'] = 'DESC';
    $_content_obj = $content_handler->getContentObj( $_values, false );
    if ( $_content_obj['count'] ) {
        foreach ( $_content_obj['list'] as $obj ) {
            $content['content_id'] = $obj->getVar( 'content_id' );
            $content['content_title'] = $obj->getVar( 'content_title' );
            $cpUser = $obj->getcpUser();
            $content['content_author'] = $cpUser['name'];
            if ( $cpUser['avatar'] ) {
                $content['content_avatar'] = $cpUser['avatar'];
            }
            if ( $cpUser['online'] ) {
                $content['content_online'] = $cpUser['online'];
            }
            $content['content_published'] = $obj->formatTimeStamp( 'content_published' );
            $content['content_updated'] = $obj->formatTimeStamp( 'content_updated' );
            $content['content_intro'] = $obj->getVar( 'content_intro', 'r' );
            $news_image = getthumbImage( $obj->getVar( 'content_images' ), $obj->getVar( 'content_title' ), 'absmiddle', 75, 75 );
            $content['content_images'] = $news_image;
            $content['content_icons'] = $obj->getIcons();
            $zariliaTpl->append( 'content', $content );
        }
    }
    $page_nav = zarilia_pagnav( $_content_obj['count'], $_values['limit'], $_values['start'], 'start', 1, 'index.php?page_type=news&amp;limit=' . $_values['limit'], 1 );
    $zariliaTpl->assign( 'page_nav', $page_nav );
    $zariliaTpl->assign( 'page_backbutton', $content_handler->do_backbutton() );
    $zariliaTpl->addCss( ZAR_THEME_URL . '/' . $zariliaConfig['theme_set'] . '/css/news.css' );
}

?>