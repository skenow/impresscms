<?php
// $Id: buddy.php,v 1.1 2007/03/16 02:34:59 catzwolf Exp $
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
 * Buddy Class
 * 
 * @package kernel
 * @author John Neill AKA Catzwolf 
 * @copyright (c) 2006 Zarilia
 */
class ZariliaBuddy extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaBuddy( $id = null ) {
        $this->zariliaObject();
        $this->initVar( 'buddy_id', XOBJ_DTYPE_INT, null, true );
        $this->initVar( 'buddy_owner', XOBJ_DTYPE_INT, null, true );
        $this->initVar( 'buddy_uid', XOBJ_DTYPE_INT, null, true );
        $this->initVar( 'buddy_name', XOBJ_DTYPE_TXTBOX, null, true, 120 );
        $this->initVar( 'buddy_fname', XOBJ_DTYPE_TXTBOX, null, false, 120 );
        $this->initVar( 'buddy_desc', XOBJ_DTYPE_TXTAREA, null, false, null );
        $this->initVar( 'buddy_allow', XOBJ_DTYPE_INT, null, true );
        $this->initVar( 'buddy_date', XOBJ_DTYPE_INT, null, true );
    } 

    function buddyForm( $caption ) {
        include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

        global $zariliaUser;

        $options['name'] = 'buddy_desc';
        $options['value'] = $this->getVar( 'buddy_desc' );
        $options['rows'] = 10;
        $options['cols'] = 60;

        $sform = new ZariliaThemeForm( $caption, "buddyform", zarilia_getenv( 'PHP_SELF' ) );
        $sform->addElement( new ZariliaFormText( _PM_BUDDY_ENAME, 'buddy_name', 50, 255, $this->getVar( 'buddy_name' ) ), true );
        $sform->addElement( new ZariliaFormText( _PM_BUDDY_EFNAME, 'buddy_fname', 50, 255, $this->getVar( 'buddy_fname' ) ), false );
        $sform->addElement( new ZariliaFormEditor( _PM_BUDDY_EDESCRIPT, $zariliaUser->getVar( 'editor' ), $options, $nohtml = false, $onfailure = "textarea", true ), false );
        $question_array = array( 0 => _NO, 1 => _YES );
        $ele = new ZariliaFormSelect( _PM_BUDDY_BLOCK, 'buddy_allow', $this->getVar( 'buddy_allow' ) );
        $ele->addOptionArray( $question_array );
        $sform->addElement( $ele );

        $button_tray = new ZariliaFormElementTray( '', '' );
        $button_tray->addElement( new ZariliaFormHidden( 'op', 'buddy_save' ) );
        $button_tray->addElement( new ZariliaFormHidden( "buddy_id", $this->getVar( 'buddy_id' ) ) );
        $button_tray->addElement( new ZariliaFormHidden( "buddy_uid", $this->getVar( 'buddy_uid' ) ) );
        $button_tray->addElement( new ZariliaFormButton( '', 'submit', _SUBMIT, 'submit' ), true );
        $button_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ), true );
        $button_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
        $sform->addElement( $button_tray );
        return $sform;
    } 

    function formatTimeStamp( $time = '' ) {
        if ( $time == '' ) {
            $time = $this->getVar( 'buddy_date' );
        } 
        $return_time = ( $time ) ? formatTimestamp( $time ) : '';
        return $return_time;
    } 
} 

/**
 * ZariliaBuddyHandler
 * 
 * @package 
 * @author Catzwolf 
 * @copyright Copyright (c) 2005
 * @version $Id: buddy.php,v 1.1 2007/03/16 02:34:59 catzwolf Exp $
 * @access public 
 */
class ZariliaBuddyHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaBuddyHandler::ZariliaBuddyHandler()
     * 
     * @param  $db 
     * @return 
     */
    function ZariliaBuddyHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'messages_buddy', 'ZariliaBuddy', 'buddy_id' );
    } 

    /**
     * ZariliaBuddyHandler::getInstance()
     * 
     * @param mixed $db 
     * @return 
     */
    function &getInstance( &$db ) {
        static $instance;
        if ( !isset( $instance ) ) {
            $instance = new ZariliaBuddyHandler( $db );
        } 
        return $instance;
    } 

    /**
     * ZariliaBuddyHandler::getContestants()
     * 
     * @param integer $limit 
     * @param integer $start 
     * @param string $sort 
     * @param string $order 
     * @param mixed $id_as_key 
     * @return 
     */
    function &getBuddy( $buddy_owner = 0, $nav, $id_as_key = false ) {
        $criteria = new CriteriaCompo();
        if ( isset( $nav['sort'] ) ) {
            $criteria->setSort( $nav['sort'] );
            $criteria->setOrder( $nav['order'] );
            $criteria->setStart( $nav['start'] );
            $criteria->setLimit( $nav['limit'] );
        } 
        $criteria->add ( new Criteria( 'buddy_owner', $buddy_owner, '=' ) );
        $buddy_obj = $this->getObjects( $criteria, $id_as_key );
        return $buddy_obj;
    } 

    /**
     * ZariliaBuddyHandler::getblockedBuddy()
     * 
     * @param integer $buddy_sender 
     * @param integer $uid 
     * @return 
     */
    function &getblockedBuddy( $buddy_sender = 0, $uid = 0 ) {
        $criteria = new CriteriaCompo();
        $criteria->add ( new Criteria( 'buddy_owner', $buddy_sender, '=' ) ); 
        // The person who we are blocking
        $criteria->add ( new Criteria( 'buddy_uid', $uid, '=' ) );
        $criteria->add ( new Criteria( 'buddy_allow', 0, '=' ) );
        $this_name = $this->getObjects( $criteria );
        $ret = ( !isset( $this_name[0] ) ) ? 0 : 1;
        return $ret;
    } 

    function &getbuddycount( $buddy_name = null ) {
        global $zariliaUser;

        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'buddy_name', $buddy_name ) );
        $criteria->add( new Criteria( 'buddy_owner', $zariliaUser->getVar( 'uid' ) ) );
        $buddy_count = $this->getCount( $criteria );
        return $buddy_count;
    } 

    /**
     * html output from here
     */
    function buddy_header( $op = '' ) {
        echo '<h3 style="text-align:left;">' . _PM_PMCP . '</h3>';
        if ( $op ) {
            echo '<div><b>' . constant( "_PM_BUDDY_W" . strtoupper( $op ) ) . '</b></div>
		 		  <div style="margin-bottom: 12px;">' . constant( "_PM_BUDDY_W" . strtoupper( $op ) . "DSC" ) . '</div>';
        } 
    } 
} 

?>