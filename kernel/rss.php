<?php
// $Id: rss.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
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
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * ZariliaRss
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: rss.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
 * @access public
 */
class ZariliaRss extends ZariliaObject {
    /**
     * Constructor
     */
    function ZariliaRss() {
        $this->ZariliaObject();
        $this->initVar( 'rss_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'rss_name', XOBJ_DTYPE_TXTBOX, null, true, 255 );
        $this->initVar( 'rss_url', XOBJ_DTYPE_TXTBOX, null, true, 255 );
        $this->initVar( 'rss_rssurl', XOBJ_DTYPE_TXTBOX, null, true, 255 );
        $this->initVar( 'rss_cachetime', XOBJ_DTYPE_INT, 600, false );
        $this->initVar( 'rss_asblock', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'rss_display', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'rss_encoding', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'rss_weight', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'rss_mainimg', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'rss_mainfull', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'rss_mainmax', XOBJ_DTYPE_INT, 10, false );
        $this->initVar( 'rss_blockimg', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'rss_blockmax', XOBJ_DTYPE_INT, 10, false );
        $this->initVar( 'rss_xml', XOBJ_DTYPE_SOURCE, null, false );
        $this->initVar( 'rss_updated', XOBJ_DTYPE_INT, 0, false );
    }

    /**
     * ZariliaRss::formEdit()
     *
     * @return
     */
    function formEdit() {
        require_once ZAR_ROOT_PATH . '/kernel/kernel_forms/rss.php';
        return $form;
    }

    /**
     * ZariliaRss::cacheExpired()
     *
     * @return
     **/
    function cacheExpired() {
        if ( time() - $this->getVar( 'rss_updated' ) > $this->getVar( 'rss_cachetime' ) ) {
            return true;
        }
        return false;
    }

    /**
     * ZariliaRss::getCachetime()
     *
     * @return
     */
    function getCachetime( $id = '' ) {
        $i = $this->getVar( $id );
        $ret = '<select name="rss_cachetime[' . $i . ']">';
        $cachetime = array( '3600' => sprintf( _HOUR, 1 ), '18000' => sprintf( _HOURS, 5 ), '86400' => sprintf( _DAY, 1 ), '259200' => sprintf( _DAYS, 3 ), '604800' => sprintf( _WEEK, 1 ), '2592000' => sprintf( _MONTH, 1 ) );
        foreach ( $cachetime as $value => $name ) {
            $ret .= '<option value="' . $value . '"';
            if ( $value == $this->getVar( 'rss_cachetime' ) ) {
                $ret .= ' selected="selected"';
            }
            $ret .= '>' . $name . '</option>';
        }
        $ret .= '</select>';
        return $ret;
    }

    /**
     * ZariliaRss::getEncoding()
     *
     * @return
     */
    function getEncoding( $id = '' ) {
        $i = $this->getVar( $id );
        $ret = '<select name="rss_encoding[' . $i . ']">';
        $encodings = array( 'utf-8' => 'UTF-8', 'iso-8859-1' => 'ISO-8859-1', 'us-ascii' => 'US-ASCII' );
        foreach ( $encodings as $value => $name ) {
            $ret .= '<option value="' . $value . '"';
            if ( $value == $this->getVar( 'rss_encoding' ) ) {
                $ret .= ' selected="selected"';
            }
            $ret .= '>' . $name . '</option>';
        }
        $ret .= '</select>';
        return $ret;
    }
}

/**
 * ZariliaRssHandler
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: rss.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
 * @access public
 **/
class ZariliaRssHandler extends ZariliaPersistableObjectHandler {
    var $db;

    /**
    * Constructor
    *
    **/
	function ZariliaRssHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'rss', 'zariliarss', 'rss_id', 'rss_name' );
    }

    /**
     * ZariliaRssHandler::getRssObj()
     *
     * @param array $nav
     * @param mixed $_mid
     * @return
     **/
    function getRssObj( $nav = array(), $_mid = null ) {
        $criteria = new CriteriaCompo();
        $obj['count'] = $this->getCount( $criteria, false );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $obj['list'] = $this->getObjects( $criteria, false );
        return $obj;
    }

    /**
     * ZariliaRssHandler::getRssMainObj()
     *
     * @param array $nav
     * @param mixed $rss_display
     * @return
     **/
    function getRssMainObj( $nav = array(), $rss_display = 0 ) {
        $criteria = new CriteriaCompo();
        if ( isset( $rss_display ) ) {
            $criteria->add( new Criteria( 'rss_display', intval( $rss_display ) ) );
        }
        $obj['count'] = $this->getCount( $criteria, false );
        $obj['list'] = $this->getObjects( $criteria, false );
        return $obj;
    }

    /**
     * ZariliaRssHandler::zariliarss_getrenderer()
     *
     * @param mixed $headline
     * @return
     **/
    function zariliarss_getrenderer( $headline ) {
        $language = ( $GLOBALS['zariliaConfig']['language'] ) ? $GLOBALS['zariliaConfig']['language'] : 'english';
        require_once ZAR_ROOT_PATH . '/addons/system/admin/rss/class/class.rssrenderer.php';
        if ( file_exists( ZAR_ROOT_PATH . '/addons/system/language/' . $language . '/admin/headlinerenderer.php' ) ) {
            include_once ZAR_ROOT_PATH . '/addons/system/language/' . $language . '/admin/headlinerenderer.php';
            if ( class_exists( 'ZariliaHeadlineRendererLocal' ) ) {
                return new ZariliaRssRendererLocal( $headline );
            }
        }
        return new ZariliaRssRenderer( $headline );
    }
}

?>