<?php
// $Id: trash.php,v 1.1 2007/03/16 02:39:12 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Trash Management System                      			//
// Copyright (c) 2006 zarilia.com                           				//
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
 * ZariliaTrash
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: trash.php,v 1.1 2007/03/16 02:39:12 catzwolf Exp $
 * @access public
 */
class ZariliaTrash extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaTrash() {
        $this->zariliaObject();
        $this->initVar( 'trash_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'trash_sid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'trash_cid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'trash_uid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'trash_alias', XOBJ_DTYPE_TXTBOX, null, false, 60 );
        $this->initVar( 'trash_created', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'trash_published', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'trash_updated', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'trash_expired', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'trash_title', XOBJ_DTYPE_TXTBOX, null, false, 100 );
        $this->initVar( 'trash_subtitle', XOBJ_DTYPE_TXTBOX, null, false, 150 );
        $this->initVar( 'trash_intro', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'trash_body', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'trash_images', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'trash_summary', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'trash_counter', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'trash_type', XOBJ_DTYPE_TXTBOX, null, false, 60 );
        $this->initVar( 'trash_hits', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'trash_version', XOBJ_DTYPE_OTHER, 1.00, false );
        $this->initVar( 'trash_approved', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'trash_weight', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'trash_display', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'trash_meta', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'trash_keywords', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'trash_spotlight', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'trash_spotlightmain', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'trash_date', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'trash_userid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'trash_mid', XOBJ_DTYPE_INT, null, false );
    }

    /**
     * ZariliaTrash::formEdit()
     *
     * @return
     */
    function formEdit( $pagetype = '' ) {
        require_once ZAR_ROOT_PATH . '/kernel/kernel_forms/trash.php';
    }

    /**
     * ZariliaTrash::formatTimeStamp()
     *
     * @param mixed $time
     * @param string $format
     * @param string $err
     * @return
     */
    function formatTimeStamp( $time = null, $format = 'D, M-d-Y', $err = '---------------' ) {
        $_time = ( $time == null ) ? $this->getVar( 'trash_published' ) : $this->getVar( $time );
        $ret = ( $_time ) ? formatTimestamp( $_time ) : $err;
        return $ret;
    }
}

/**
 * ZariliaTrashHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: trash.php,v 1.1 2007/03/16 02:39:12 catzwolf Exp $
 * @access public
 */
class ZariliaTrashHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaTrashHandler::ZariliaTrashHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaTrashHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'trash', 'ZariliaTrash', 'trash_id', 'trash_title', 'trash_read' );
    }

    /**
     * ZariliaTrashHandler::getTrashObj()
     *
     * @param array $nav
     * @param mixed $_mid
     * @param mixed $trash_display
     * @return
     */
    function getTrashObj( $nav = array() ) {
        $criteria = new CriteriaCompo();
        if ( $nav['type'] != '' ) {
            $criteria->add( new Criteria( 'trash_type', $nav['type'] ) );
        }
        if ( $nav['trash_display'] != 3 ) {
            $criteria->add( new Criteria( 'trash_display', $nav['trash_display'] ) );
        }
        $obj['count'] = $this->getCount( $criteria, false );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $obj['list'] = $this->getObjects( $criteria, false );
        return $obj;
    }
}

?>