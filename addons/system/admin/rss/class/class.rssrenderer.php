<?php
// $Id: class.rssrenderer.php,v 1.3 2007/04/21 09:43:13 catzwolf Exp $
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

include_once ZAR_ROOT_PATH . '/class/template.php';

class ZariliaRssRenderer {
    var $_hl;
    var $_tpl;
    // XoopTemplate object
    var $_feed;
    var $_block;
    // RSS2 SAX parser
    var $_parser;

    function ZariliaRssRenderer( $headline )
    {
        $this->_hl = $headline;
        $this->_tpl = new ZariliaTpl();
    }

    function updateCache( $type = 0 )
    {
        $rss_handler = &zarilia_gethandler( 'rss' );
        if ( $type == 0 ) {
			$ch = curl_init();
            $timeout = 5; // set to zero for no timeout
            curl_setopt ( $ch, CURLOPT_URL, $this->_hl->getVar( 'rss_rssurl' ) );
            curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
            $file_contents = curl_exec( $ch );
            curl_close( $ch );
            if ( !$file_contents ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'Could not open file: ' . $this->_hl->getVar( 'rss_rssurl' ) . ' located at the address you supplied. Please verify the address of the RDF/RSS file and try again.' );
                // $rss_handler->delete( $this->_hl );
                $this->updateCache( 1 );
                return false;
            }
        } else {
            if ( !$fp = fopen( $this->_hl->getVar( 'rss_rssurl' ), 'r' ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'Could not open file: ' . $this->_hl->getVar( 'rss_rssurl' ) . ' located at the address you supplied. Please verify the address of the RDF/RSS file and try again.' );
                return false;
            }
            $data = '';
            while ( !feof ( $fp ) ) {
                $data .= fgets( $fp, 4096 );
            }
            fclose ( $fp );
            $this->_hl->setVar( 'headline_xml', $this->convertToUtf8( $data ) );
        }
        $_rss_obj = $rss_handler->get( $this->_hl->getVar( 'rss_id' ) );
        $_rss_obj->setVar( 'rss_xml', $this->convertToUtf8( $file_contents ) );
        $_rss_obj->setVar( 'rss_updated', time() );
        return $rss_handler->insert( $_rss_obj );
    }

    function renderFeed( $force_update = false )
    {
		if ( $force_update || $this->_hl->cacheExpired() ) {
            if ( !$this->updateCache( 0 ) ) {
                return false;
            }
        }
        if ( !$this->_parse() ) {
            return false;
        }
        $this->_tpl->clear_all_assign();
        $this->_tpl->assign( 'zarilia_url', ZAR_URL );
        $channel_data = &$this->_parser->getChannelData();
        array_walk( $channel_data, array( $this, 'convertFromUtf8' ) );
        $this->_tpl->assign_by_ref( 'channel', $channel_data );
        if ( $this->_hl->getVar( 'rss_mainimg' ) == 1 ) {
            $image_data = &$this->_parser->getImageData();
            array_walk( $image_data, array( $this, 'convertFromUtf8' ) );
            $this->_tpl->assign_by_ref( 'image', $image_data );
        }
        if ( $this->_hl->getVar( 'rss_mainfull' ) == 1 ) {
            $this->_tpl->assign( 'show_full', true );
        } else {
            $this->_tpl->assign( 'show_full', false );
        }
        $items = &$this->_parser->getItems();
        $count = count( $items );
        $max = ( $count > $this->_hl->getVar( 'rss_mainmax' ) ) ? $this->_hl->getVar( 'rss_mainmax' ) : $count;
        for ( $i = 0; $i < $max; $i++ ) {
            array_walk( $items[$i], array( $this, 'convertFromUtf8' ) );
            $this->_tpl->append_by_ref( 'items', $items[$i] );
        }
        $this->_tpl->assign( array( 'lang_lastbuild' => _HL_LASTBUILD, 'lang_language' => _HL_LANGUAGE, 'lang_description' => _HL_DESCRIPTION, 'lang_webmaster' => _HL_WEBMASTER, 'lang_category' => _HL_CATEGORY, 'lang_generator' => _HL_GENERATOR, 'lang_title' => _HL_TITLE, 'lang_pubdate' => _HL_PUBDATE, 'lang_description' => _HL_DESCRIPTION, 'lang_more' => _MORE ) );
        $this->_feed =& $this->_tpl->fetch( 'db:system_rssfeed.html' );
        return true;
    }

    function renderBlock( $force_update = false )
    {
        $this->_block = array();
        if ( $force_update || $this->_hl->cacheExpired() ) {
            if ( !$this->updateCache( 0 ) ) {
                return false;
            }
        }
        if ( !$this->_parse() ) {
            return false;
        }
        $channel_data = &$this->_parser->getChannelData();
        array_walk( $channel_data, array( $this, 'convertFromUtf8' ) );
        $this->_block['channel'] = $channel_data;
        if ( $this->_hl->getVar( 'rss_blockimg' ) == 1 ) {
            $image_data = &$this->_parser->getImageData();
            array_walk( $image_data, array( $this, 'convertFromUtf8' ) );
			//$this->_block['image'] = ( $image_data ) ? $image_data : ZAR_URL.'/images/icons/xml.gif';
            $this->_block['image'] = $image_data;
        }
        $items = &$this->_parser->getItems();
        $count = count( $items );
        $max = ( $count > $this->_hl->getVar( 'rss_blockmax' ) ) ? $this->_hl->getVar( 'rss_blockmax' ) : $count;
        for ( $i = 0; $i < $max; $i++ ) {
            array_walk( $items[$i], array( $this, 'convertFromUtf8' ) );
            $this->_block['items'][$i] = $items[$i];
        }
        $this->_block['site_name'] = $this->_hl->getVar( 'rss_name' );
        $this->_block['site_url'] = $this->_hl->getVar( 'rss_url' );
        $this->_block['site_id'] = $this->_hl->getVar( 'rss_id' );
        return $this->_block;
    }

    function _parse()
    {
        if ( isset( $this->_parser ) ) {
            return true;
        }
        require_once ZAR_ROOT_PATH . '/class/xml/rss/xmlrss2parser.php';
        $this->_parser = new ZariliaXmlRss2Parser( $this->_hl->getVar( 'rss_xml' ) );
        switch ( $this->_hl->getVar( 'rss_encoding' ) ) {
            case 'utf-8':
                $this->_parser->useUtfEncoding();
                break;
            case 'us-ascii':
                $this->_parser->useAsciiEncoding();
                break;
            default:
                $this->_parser->useIsoEncoding();
                break;
        }
        $result = $this->_parser->parse();
        if ( !$result ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $this->_parser->getErrors( false ) );
            unset( $this->_parser );
            return false;
        }
        return true;
    }

    function &getFeed()
    {
        return $this->_feed;
    }

    function &getBlock()
    {
        return $this->_block;
    }
    // abstract
    // overide this method in /language/your_language/headlinerenderer.php
    // this method is called by the array_walk function
    // return void
    function convertFromUtf8( &$value, $key )
    {
    }
    // abstract
    // overide this method in /language/your_language/headlinerenderer.php
    // return string
    function &convertToUtf8( &$xmlfile )
    {
        return $xmlfile;
    }
}

?>