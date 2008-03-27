<?php
// $Id: comment.php,v 1.2 2007/04/21 09:44:19 catzwolf Exp $
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
 * A Comment
 *
 * @package kernel
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
/**
 * ZariliaComment
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: comment.php,v 1.2 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 */
class ZariliaComment extends ZariliaObject {
    /**
     * Constructor
     */
    function ZariliaComment() {
        $this->ZariliaObject();
        $this->initVar( 'com_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'com_pid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'com_modid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'com_icon', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'com_title', XOBJ_DTYPE_TXTBOX, null, true, 255, true );
        $this->initVar( 'com_text', XOBJ_DTYPE_TXTAREA, null, true, null, true );
        $this->initVar( 'com_created', XOBJ_DTYPE_LTIME, time(), false );
        $this->initVar( 'com_modified', XOBJ_DTYPE_LTIME, 0, false );
        $this->initVar( 'com_uid', XOBJ_DTYPE_INT, 0, true );
        $this->initVar( 'com_ip', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'com_sig', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'com_itemid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'com_rootid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'com_status', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'com_exparams', XOBJ_DTYPE_OTHER, "", false, 255 );
        /*will be deprecated soon*/
        $this->initVar( 'dohtml', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'dosmiley', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'doxcode', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'doimage', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'dobr', XOBJ_DTYPE_INT, 0, false );
    }

    /**
     * Is this comment on the root level?
     *
     * @return bool
     */
    function isRoot() {
        return ( $this->getVar( 'com_id' ) == $this->getVar( 'com_rootid' ) );
    }

    /**
     * ZariliaComment::getLinkedUserName()
     *
     * @param mixed $linked
     * @return
     */
    function getLinkedUserName( $linked = 1 ) {
        $ret = zarilia_getLinkedUnameFromId( $this->getVar( "com_uid" ), 0, $linked );
        return $ret;
    }
    function comStatus() {
    }
}

/**
 * ZARILIA comment handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of ZARILIA comment class objects.
 *
 * @package kernel
 * @subpackage comment
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaCommentHandler extends ZariliaPersistableObjectHandler {
    function ZariliaCommentHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'zariliacomments', 'ZariliaComment', 'com_id', 'com_title' );
    }

    /**
     * Retrieves comments for an item
     *
     * @param int $addon_id Addons ID
     * @param int $item_id Item ID
     * @param string $order Sort order
     * @param int $status Status of the comment
     * @param int $limit Max num of comments to retrieve
     * @param int $start Start offset
     * @return array Array of {@link ZariliaComment} objects
     */
    function getByItemId( $addon_id, $item_id, $order = null, $status = null, $limit = null, $start = 0 ) {
        $criteria = new CriteriaCompo( new Criteria( 'com_modid', intval( $addon_id ) ) );
        $criteria->add( new Criteria( 'com_itemid', intval( $item_id ) ) );
        if ( isset( $status ) ) {
            $criteria->add( new Criteria( 'com_status', intval( $status ) ) );
        }
        if ( isset( $order ) ) {
            $criteria->setSort( "com_created" );
            $criteria->setOrder( $order );
        }
        if ( isset( $limit ) ) {
            $criteria->setLimit( $limit );
            $criteria->setStart( $start );
        }
        return $this->getObjects( $criteria );
    }

    /**
     * Gets total number of comments for an item
     *
     * @param int $addon_id Addons ID
     * @param int $item_id Item ID
     * @param int $status Status of the comment
     * @return array Array of {@link ZariliaComment} objects
     */
    function getCountByItemId( $addon_id, $item_id, $status = null ) {
        $criteria = new CriteriaCompo( new Criteria( 'com_modid', intval( $addon_id ) ) );
        $criteria->add( new Criteria( 'com_itemid', intval( $item_id ) ) );
        if ( isset( $status ) ) {
            $criteria->add( new Criteria( 'com_status', intval( $status ) ) );
        }

        return $this->getCount( $criteria );
    }

    /**
     * Get the top {@link ZariliaComment}s
     *
     * @param int $addon_id
     * @param int $item_id
     * @param strint $order
     * @param int $status
     * @return array Array of {@link ZariliaComment} objects
     */
    function getTopComments( $addon_id, $item_id, $order, $status = null ) {
        $criteria = new CriteriaCompo( new Criteria( 'com_modid', intval( $addon_id ) ) );
        $criteria->add( new Criteria( 'com_itemid', intval( $item_id ) ) );
        $criteria->add( new Criteria( 'com_pid', 0 ) );
        if ( isset( $status ) ) {
            $criteria->add( new Criteria( 'com_status', intval( $status ) ) );
        }
        $criteria->setOrder( $order );
        return $this->getObjects( $criteria );
    }

    /**
     * Retrieve a whole thread
     *
     * @param int $comment_rootid
     * @param int $comment_id
     * @param int $status
     * @return array Array of {@link ZariliaComment} objects
     */
    function getThread( $comment_rootid, $comment_id, $status = null ) {
        $criteria = new CriteriaCompo( new Criteria( 'com_rootid', intval( $comment_rootid ) ) );
        $criteria->add( new Criteria( 'com_id', intval( $comment_id ), '>=' ) );
        if ( isset( $status ) ) {
            $criteria->add( new Criteria( 'com_status', intval( $status ) ) );
        }
        return $this->getObjects( $criteria );
    }

    /**
     * Update
     *
     * @param object $ &$comment       {@link ZariliaComment} object
     * @param string $field_name Name of the field
     * @param mixed $field_value Value to write
     * @return bool
     */
    function updateByField( &$comment, $field_name, $field_value ) {
        $comment->unsetNew();
        $comment->setVar( $field_name, $field_value );
        return $this->insert( $comment );
    }

    /**
     * Delete all comments for one whole addon
     *
     * @param int $addon_id ID of the addon
     * @return bool
     */
    function deleteByAddon( $addon_id ) {
        return $this->deleteAll( new Criteria( 'com_modid', intval( $addon_id ) ) );
    }

    function getCommentObj( $nav = array(), $status, $addon ) {
        $criteria = new CriteriaCompo();
        if ( $status > 0 ) {
            $criteria->add( new Criteria( 'com_status', $status ) );
        }
        if ( $addon > 0 ) {
            $criteria->add( new Criteria( 'com_modid', $addon ) );
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