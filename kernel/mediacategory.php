<?php
// $Id: mediacategory.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
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
 * ZariliaMediaCategory
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: mediacategory.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 */
class ZariliaMediaCategory extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaMediaCategory() {
        $this->zariliaObject();
        $this->initVar( 'media_cid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'media_ctitle', XOBJ_DTYPE_TXTBOX, null, true, 100 );
        $this->initVar( 'media_cmaxsize', XOBJ_DTYPE_INT, 500000, false );
        $this->initVar( 'media_cmaxwidth', XOBJ_DTYPE_INT, 350, false );
        $this->initVar( 'media_cmaxheight', XOBJ_DTYPE_INT, 250, false );
        $this->initVar( 'media_cdisplay', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'media_cweight', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'media_ctype', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'media_cdirname', XOBJ_DTYPE_TXTBOX, 'uploads', true, 255 );
        $this->initVar( 'media_cdescription', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'media_cupdated', XOBJ_DTYPE_LTIME, time(), false );
        $this->initVar( 'media_cprefix', XOBJ_DTYPE_TXTBOX, null, false, 10 );
    }

    function formEdit() {
        if ( is_readable( ZAR_ROOT_PATH . '/kernel/kernel_forms/mediacategory.php' ) ) {
            include ZAR_ROOT_PATH . '/kernel/kernel_forms/mediacategory.php';
        }
    }
}

/**
 * ZariliaMediaHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: mediacategory.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 */
class ZariliaMediaCategoryHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaMediaHandler::ZariliaMediaHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaMediaCategoryHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'mediacategory', 'ZariliaMediaCategory', 'media_cid', 'media_ctitle', 'mediacategory_read' );
    }

    /**
     * ZariliaSectionHandler::getSectionObj()
     *
     * @param array $nav
     * @param mixed $section_id
     * @return
     */
    function &getMediaCatObj( $nav = null ) {
        $criteria = new CriteriaCompo();
        if ( $nav['media_cdisplay'] != 3 ) {
            $criteria->add( new Criteria( 'media_cdisplay', $nav['media_cdisplay'] ) );
        }
        $object['count'] = $this->getCount( $criteria, false );
        if ( isset( $nav ) ) {
            $criteria->setSort( @$nav['sort'] );
            $criteria->setOrder( @$nav['order'] );
            $criteria->setStart( @$nav['start'] );
            $criteria->setLimit( @$nav['limit'] );
        }
        $object['list'] = $this->getObjects( $criteria, false );
        return $object;
    }
}

?>