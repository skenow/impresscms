<?php
// $Id: messagesent.php,v 1.1 2007/03/16 02:34:59 catzwolf Exp $
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

if ( !defined( 'ZAR_ROOT_PATH' ) ) {
    exit( 'You cannot access this file directly' );
} 

 

/**
 * 
 * @author John Neill AKA Catzwolf <catzwolf@zarilia.com> 
 * @copyright copyright (c) 2006 Zarilia
 */

/**
 * ZariliaMessage
 * 
 * @package 
 * @author Catzwolf 
 * @copyright Copyright (c) 2005
 * @version $Id: messagesent.php,v 1.1 2007/03/16 02:34:59 catzwolf Exp $
 * @access public 
 */
class ZariliaMessageSent extends ZariliaObject {
    /**
     * Class constructor
     */
    function ZariliaMessageSent() {
        $this->ZariliaObject();
        $this->initVar( 'mid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'subject', XOBJ_DTYPE_TXTBOX, null, true, 255 );
        $this->initVar( 'from_userid', XOBJ_DTYPE_INT, null, true );
        $this->initVar( 'to_userid', XOBJ_DTYPE_INT, null, true );
        $this->initVar( 'is_trash', XOBJ_DTYPE_INT, null, true );
        $this->initVar( 'is_saved', XOBJ_DTYPE_INT, null, true );
        $this->initVar( 'time', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'text', XOBJ_DTYPE_TXTAREA, null, true );
        $this->initVar( 'msg', XOBJ_DTYPE_INT, 0, true );
        $this->initVar( 'priority', XOBJ_DTYPE_INT, 3, false );
        $this->initVar( 'track', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'trash_date', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'read_date', XOBJ_DTYPE_OTHER, null, false );
    } 

    function getLinkedUserName( $type, $linked = 1 ) {
        $_sender = ( $type == "inbox" ) ? $this->getVar( "from_userid" ) : $this->getVar( "to_userid" );
        $ret = zarilia_getLinkedUnameFromId( $_sender, 0, $linked );
        return $ret;
    } 

    function getpriorityImage() {
        $priority = $this->getVar( 'priority' );
        return zarilia_img_show( "priority$priority", '', 'middle' );
    } 

    function getreadImage() {
        $_read_image = ( $this->getVar( 'msg' ) == 0 ) ? 'pmmailnew' : 'pmmail';
        return zarilia_img_show( $_read_image, '', 'middle' );
    } 

    function getSubject( $op = '' ) {
        $_read_status = ( isset( $op ) && $op == "saved" ) ? "create" : "read_message";
        $url = ( $op == 'msaved' ) ? "index.php?id=" . $this->getVar( "id" ) . "&op=create" : "message_read.php?id=" . $this->getVar( "id" ) . "&amp;op=" . $op;
        $url = "<a href='" . ZAR_URL . "/addons/messages/$url'>" . $this->getVar( "subject" ) . "</a>";
        return $url;
    } 

    function formatTimeStamp( $time = '' ) {
        if ( $time == '' ) {
            $time = $this->getVar( 'time' );
        } 
        return ( $time ) ? formatTimestamp( $time ) : '';
    } 
} 

/**
 * ZariliaMessageHandler
 * 
 * @package 
 * @author Catzwolf 
 * @copyright Copyright (c) 2005
 * @version $Id: messagesent.php,v 1.1 2007/03/16 02:34:59 catzwolf Exp $
 * @access public 
 */
class ZariliaMessageSentHandler extends ZariliaPersistableObjectHandler {
    /**
     * 
     * @param  $db 
     * @return 
     */
    function ZariliaMessageSentHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'messages_sent', 'ZariliaMessageSent', 'id' );
    } 

    /**
     * categoryHandler::getInstance()
     * 
     * @param  $db 
     * @return 
     */
    function &getInstance( &$db ) {
        static $instance;
        if ( !isset( $instance ) ) {
            $instance = new ZariliaMessageHandler( $db );
        } 
        return $instance;
    } 

    /**
     * ZariliaMessageHandler::upload_attach()
     * 
     * @return 
     */
    function upload_attach() {
    } 

    function do_id( $var = array() ) {
        $id = array();
        if ( is_array( $var ) ) {
            foreach ( $var as $k => $v ) {
                $id[$v] = intval( $v );
            } 
        } else {
            if ( intval( $var ) > 0 ) {
                $id[$var] = $var;
            } 
        } 
        return $id;
    } 

    function create_test_data() {
        for ( $i = 0; $i < 50; $i++ ) {
            $pm = &$this->create();
            $pm->setVar( 'subject', 'Test data' );
            $pm->setVar( 'from_userid', 1 );
            $pm->setVar( 'to_userid', 2 );
            $pm->setVar( 'time', time() );
            $pm->setVar( 'msg', $i );
            $pm->setVar( 'text', "this is dome test data, let's test for stripslashes here too" );
            $this->insert( $pm );
        } 
    } 

    function getSent( $nav, $zariliaUser ) {
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'from_userid', $zariliaUser ) );
        $ret['count'] = $this->getCount( $criteria );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $ret['list'] = $this->getObjects( $criteria );
        return $ret;
    } 
} 

?>