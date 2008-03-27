<?php
// $Id: section.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
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
 * ZariliaSection
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: section.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
 * @access public
 */
class ZariliaSection extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaSection() {
        $this->zariliaObject();
        $this->initVar( 'section_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'section_title', XOBJ_DTYPE_TXTBOX, null, false, 150 );
        $this->initVar( 'section_description', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'section_image', XOBJ_DTYPE_TXTBOX, 'blank.png', false, 150 );
        $this->initVar( 'section_weight', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'section_display', XOBJ_DTYPE_OTHER, 1, false );
        $this->initVar( 'section_published', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'section_imageside', XOBJ_DTYPE_OTHER, 'left', false );
        $this->initVar( 'section_type', XOBJ_DTYPE_TXTBOX, null, false, 10 );
        $this->initVar( 'section_is', XOBJ_DTYPE_INT, 1, false );
    }

    /**
     * ZariliaSection::formEdit()
     *
     * @return
     */
    function formEdit() {
        require_once ZAR_ROOT_PATH . '/kernel/kernel_forms/section.php';
    }

    /**
     * ZariliaSection::formEdit()
     *
     * @return
     */
    function formEditHeading( $section_type = null, $return = 'section' ) {
        Global $zariliaConfig;

        if ( is_readable( ZAR_ROOT_PATH . '/addons/system/language/' . $zariliaConfig['language'] . '/admin/headings.php' ) ) {
            require_once ZAR_ROOT_PATH . '/addons/system/language/' . $zariliaConfig['language'] . '/admin/headings.php';
        } else {
            require_once ZAR_ROOT_PATH . '/addons/system/language/english/admin/headings.php';
        }

        require_once ZAR_ROOT_PATH . '/kernel/kernel_forms/headings.php';
    }

    /**
     * Display a human readable date form
     * parm: intval: 	$time	- unix timestamp
     */
    function formatTimeStamp( $time = null, $format = 'D, M-d-Y', $var = '', $err = '---------------' ) {
        $_time = ( $time == null ) ? $this->getVar( 'section_published' ) : $this->getVar( $time );
        $ret = ( $_time ) ? formatTimestamp( $_time ) : $err;
        return $ret;
    }
}

/**
 * ZariliaSectionHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: section.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
 * @access public
 */
class ZariliaSectionHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaSectionHandler::ZariliaSectionHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaSectionHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'section', 'ZariliaSection', 'section_id', 'section_title', 'section_read' );
    }

    /**
     * ZariliaSectionHandler::getbyType()
     *
     * @param mixed $section_type
     * @return
     */
    function getbyType( $section_type = null ) {
        $criteria = new CriteriaCompo();
        if ( $section_type == null ) {
            return;
        }
        $criteria->add( new Criteria( 'section_type', $section_type ) );
        $criteria->add( new Criteria( 'section_is', 1 ) );
        $criteria->setStart( 0 );
        $criteria->setLimit( 1 );
        $object = $this->getObjects( $criteria, false );
        return $object[0];
    }

    /**
     * ZariliaSectionHandler::getSectionObj()
     *
     * @param array $nav
     * @param mixed $section_id
     * @return
     */
    function getSectionObj( $nav = null ) {
        $criteria = new CriteriaCompo();
        if ( $nav['section_display'] != 3 ) {
            $criteria->add( new Criteria( 'section_display', $nav['section_display'] ) );
        }
        if ( $nav['section_id'] > 0 ) {
            $criteria->add( new Criteria( 'section_id', $nav['section_id'] ) );
        }
        $criteria->add( new Criteria( 'section_is', 1 ) );
        $object['count'] = $this->getCount( $criteria, false );
        if ( isset( $nav ) ) {
            $criteria->setSort( $nav['sort'] );
            $criteria->setOrder( $nav['order'] );
            $criteria->setStart( $nav['start'] );
            $criteria->setLimit( $nav['limit'] );
        }
        $object['list'] = $this->getObjects( $criteria, false );
        return $object;
    }

    function getSectionWhere( $type = null ) {
        $criteria = new CriteriaCompo();
        if ( $type != null ) {
            $criteria->add( new Criteria( 'section_type', zarilia_trim( $type ) ) );
        }
        $criteria->add( new Criteria( 'section_is', 0 ) );
        $criteria->setOrder( 'ASC' );
        $criteria->setStart( 0 );
        $criteria->setLimit( 1 );
        // }
        $object = $this->getObjects( $criteria, false );

        return $object[0];
    }
}

?>