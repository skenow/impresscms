<?php
// $Id: category.php,v 1.2 2007/04/21 09:44:19 catzwolf Exp $
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

/**
 * ZariliaCategory
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: category.php,v 1.2 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 */
class ZariliaCategory extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaCategory() {
        $this->zariliaObject();
        $this->initVar( 'category_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'category_pid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'category_sid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'category_title', XOBJ_DTYPE_TXTBOX, null, false, 150 );
        $this->initVar( 'category_description', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'category_image', XOBJ_DTYPE_IMAGE, '', false, 150 );
        $this->initVar( 'category_weight', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'category_display', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'category_published', XOBJ_DTYPE_LTIME, time(), false );
        $this->initVar( 'category_imageside', XOBJ_DTYPE_OTHER, 'left', false );
        $this->initVar( 'category_type', XOBJ_DTYPE_TXTBOX, null, false, 10 );
        $this->initVar( 'category_header', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'category_footer', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'category_body', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
    }

    /**
     * ZariliaCategory::formEdit()
     *
     * @return
     */
    function formEdit() {
        if ( is_readable( ZAR_ROOT_PATH . '/kernel/kernel_forms/category.php' ) ) {
            include ZAR_ROOT_PATH . '/kernel/kernel_forms/category.php';
        }
    }
}

/**
 * ZariliaCategoryHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: category.php,v 1.2 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 */
class ZariliaCategoryHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaCategoryHandler::ZariliaCategoryHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaCategoryHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'category', 'ZariliaCategory', 'category_id', 'category_title', 'category_read' );
    }

    /**
     * ZariliaCategoryHandler::getCategoryObj()
     *
     * @param array $nav
     * @param mixed $category_id
     * @return
     */
    function getCategoryObj( $nav = array(), $category_id = null, $category_sid = null, $category_display = 3 ) {
        $criteria = new CriteriaCompo();

        if ( $category_display == 0 || $category_display == 1 ) {
            $criteria->add( new Criteria( 'category_display', $category_display ) );
        }
        if ( $category_sid != null ) {
            $criteria->add( new Criteria( 'category_sid', $category_sid ) );
            /*
			if ( is_array( $category_sid ) ) {
                $criteria->add( new Criteria( 'category_sid', "('" . implode( ',', $category_sid ) . "')", "IN" ) );
            } else {
                $criteria->add( new Criteria( 'category_sid', "('" . implode( ',', array( $category_sid ) ) . "')", "IN" ) );
            }
*/
        }
        if ( $category_id > 0 ) {
            $criteria->add( new Criteria( 'category_id', $category_id ) );
        }

        $object['count'] = $this->getCount( $criteria, false );
        /**
         */
        if ( !empty( $nav ) ) {
            $criteria->setSort( @$nav['sort'] );
            $criteria->setOrder( @$nav['order'] );
            $criteria->setStart( @$nav['start'] );
            $criteria->setLimit( @$nav['limit'] );
        }
        $object['list'] = $this->getObjects( $criteria, false );
        return $object;
    }

    function getCategorybySection( $category_type ) {
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'category_type', $category_type ) );
        $criteria->add( new Criteria( 'category_display', 1 ) );
        // $object['count'] = $this->getCount( $criteria, false );
        if ( !empty( $nav ) ) {
            $criteria->setSort( @$nav['sort'] );
            $criteria->setOrder( @$nav['order'] );
            $criteria->setStart( @$nav['start'] );
            $criteria->setLimit( @$nav['limit'] );
        }
        $object['list'] = $this->getObjects( $criteria, false );
        return $object;
    }

    /**
     * ZariliaCategoryHandler::getAllImages()
     *
     * @return
     */
    function getAllImages() {
        $db = &ZariliaDatabaseFactory::getDatabaseConnection();
        $sql = 'SELECT DISTINCT category_id, category_image FROM ' . $db->prefix( 'category' );
        $result = $db->Execute( $sql );
        $ret = '';
        while ( $myrow = $result->FetchRow() ) {
            if ( $myrow['category_image'] != '||' ) {
                $ret[$myrow['category_id']] = htmlSpecialChars( $myrow['category_image'], ENT_QUOTES );
            }
        }
        return $ret;
    }

    function sectionObj() {
        static $sections_array;
        if ( $sections_array ) {
            return $sections_array;
        }
        if ( !isset( $section_handler ) ) {
            $section_handler = &zarilia_gethandler( 'section' );
        }
        $sections_array = &$section_handler->getList( null, null, null, null, 1 );
        return $sections_array;
    }
}

?>