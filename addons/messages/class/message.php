<?php
// $Id: message.php,v 1.1 2007/03/16 02:34:59 catzwolf Exp $
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
 * @version $Id: message.php,v 1.1 2007/03/16 02:34:59 catzwolf Exp $
 * @access public
 */
class ZariliaMessage extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaMessage() {
        $this->ZariliaObject();
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
        $this->initVar( 'is_attachment', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'attachment_type', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'trash_date', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'read_date', XOBJ_DTYPE_OTHER, null, false );
    }
    /**
     * Message form
     */
    function messageForm( $caption, $to_userid = 0 ) {
        global $zariliaUser, $buddy_handler;

        if ( $this->isNew() ) {
            $from_userid = $zariliaUser->getVar( 'uid' );
        } else {
            $from_userid = ( $this->getVar( 'to_userid' ) ) ? $this->getVar( 'to_userid' ) : $zariliaUser->getVar( 'uid' ) ;
            $to_userid = $this->getVar( 'from_userid' );
        }

        $buddy_arr = $buddy_handler->getBuddy( $from_userid, '' );
        $members = $members_n = array();
        if ( is_array( $buddy_arr ) && count( $buddy_arr ) > 0 ) {
            foreach ( array_keys( $buddy_arr ) as $i ) {
                $uid = $buddy_arr[$i]->getVar( 'buddy_uid' );
                $members_n[] = $buddy_arr[$i]->getVar( 'buddy_uid' );
                $members[$uid] = $buddy_arr[$i]->getVar( 'buddy_name' );
            }
            unset( $members[$from_userid], $members_n[$from_userid] );
            natcasesort( $members );
        }

        $buddy_name = '';
        if ( !in_array( $to_userid, $members_n ) ) {
            $member_handler = &zarilia_gethandler( 'member' );
            $thisUser = $member_handler->getUser( $to_userid );
            if ( is_object( $thisUser ) ) {
                $buddy_name = $thisUser->getVar( 'uname' );
            }
            unset( $thisUser );
        }

        include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
        $sform = new ZariliaThemeForm( $caption, "buddyform", zarilia_getenv( 'PHP_SELF' ) );
        $ele = new ZariliaFormSelect( _PM_PMSENTTO, 'buddy_name', $to_userid );
        $ele->addOption( 0, '------------------' );
        $ele->addOptionArray( $members );
        $sform->addElement( $ele );
        unset( $members[$to_userid] );
        natcasesort( $members );

        $sform->addElement( new ZariliaFormText( _PM_PMMEMBERNAME, 'to_userid', 50, 255, $buddy_name ), false );

        $elecc = new ZariliaFormSelect( _PM_PMCCSELECTED, 'ccto_userid', null, 5, true );
        $elecc->addOption( '', '------------------' );
        $elecc->addOptionArray( $members );
        $sform->addElement( $elecc );

        $priority_type = array( 1 => _PM_PMPRIORITY1, 2 => _PM_PMPRIORITY2, 3 => _PM_PMPRIORITY3 );
        $priority = new ZariliaFormSelect( _PM_PMPRIORITY, 'priority', 2 );
        $priority->addOption( '', '------------------' );
        $priority->addOptionArray( $priority_type );
        $sform->addElement( $priority );

        $sform->addElement( new ZariliaFormText( _PM_PMTITLE, 'subject', 50, 255, $this->getVar( 'subject', "e" ) ), true );
        $sform->addElement( new ZariliaFormDhtmlTextArea( _PM_PMBODY, 'text', $this->getVar( 'text', 'e' ), 15, 40 ), true );
        $options['name'] = 'text';
        $options['value'] = $this->getVar( 'text', 'e' );
        $ele = new ZariliaFormEditor( _PM_PMBODY, $zariliaUser->getVar( "editor" ), $options, $nohtml = false, $onfailure = "textarea" );
        $ele->setNocolspan( 1 );
        $form->addElement( $ele );


        $options_tray = new ZariliaFormElementTray( _PM_PMOPTIONS, '<br />' );
        $cleanhtml_checkbox = new ZariliaFormCheckBox( '', 'tracker', 0, '', 0 );
        $cleanhtml_checkbox->addOption( 1, _PM_PMSELECTTOTRACK );
        $options_tray->addElement( $cleanhtml_checkbox );

        $striphtml_checkbox = new ZariliaFormCheckBox( '', 'savebox', 0, '', 0 );
        $striphtml_checkbox->addOption( 1, _PM_PMSAVEMESSAGELATER );
        $options_tray->addElement( $striphtml_checkbox );
        $sform->addElement( $options_tray );

        $button_tray = new ZariliaFormElementTray( '', '' );
        $button_tray->addElement( new ZariliaFormHidden( 'op', 'save' ) );
        $button_tray->addElement( new ZariliaFormHidden( "time", time() ) );
        $button_tray->addElement( new ZariliaFormButton( '', 'submit', _SUBMIT, 'submit' ), true );
        $button_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ), true );
        $button_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
        $sform->addElement( $button_tray );
        return $sform;
    }

    function getLinkedUserName( $type, $linked = 1 ) {
        $_sender = ( $type == "inbox" ) ? $this->getVar( "from_userid" ) : $this->getVar( "to_userid" );
        $ret = zarilia_getLinkedUnameFromId( $_sender, 0, $linked );
        return $ret;
    }

    function gettoUserName( $linked = 1 ) {
        $ret = zarilia_getLinkedUnameFromId( $this->getVar( 'to_userid' ), 0, $linked );
        return $ret;
    }

    function getpriorityImage() {
        $priority = $this->getVar( 'priority' );
        $ret = zarilia_img_show( "priority$priority", '', 'middle' );
        return $ret;
    }

    function getreadImage() {
        $_read_image = ( $this->getVar( 'msg' ) == 0 ) ? 'pmmailnew' : 'pmmail';
        $ret = zarilia_img_show( $_read_image, '', 'middle' );
        return $ret;
    }

    function getSubject( $op = '' ) {
        $_read_status = ( isset( $op ) && $op == "saved" ) ? "create" : "read_message";
        $url = ( $op == 'msaved' ) ? "message_create.php?id=" . $this->getVar( "id" ) . "&op=create" : "message_read.php?id=" . $this->getVar( "id" ) . "&amp;op=" . $op;
        $url = "<a href='" . ZAR_URL . "/addons/messages/$url'>" . $this->getVar( "subject" ) . "</a>";
        return $url;
    }

    function formatTimeStamp( $time = '' ) {
        if ( $time == '' ) {
            $time = $this->getVar( 'time' );
        }
        $return_time = ( $time ) ? formatTimestamp( $time ) : '';
        return $return_time;
    }
}

/**
 * ZariliaMessageHandler
 *
 * @package
 * @author Catzwolf
 * @copyright Copyright (c) 2005
 * @version $Id: message.php,v 1.1 2007/03/16 02:34:59 catzwolf Exp $
 * @access public
 */
class ZariliaMessageHandler extends ZariliaPersistableObjectHandler {
    /**
     * categoryHandler::categoryHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaMessageHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'messages', 'ZariliaMessage', 'id' );
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
            $pm->setVar( 'from_userid', 2 );
            $pm->setVar( 'to_userid', 1 );
            $pm->setVar( 'time', time() );
            $pm->setVar( 'msg', 0 );
            $pm->setVar( 'text', "this is dome test data, let's test for stripslashes here too" );
            $this->insert( $pm );
        }
    }

    function getInbox( $nav, $zariliaUser ) {
        $ret = array();
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'to_userid', $zariliaUser ) );
        $criteria->add( new Criteria( 'msg', '9', '<>' ) );
        $criteria->add( new Criteria( 'is_trash', '0' ) );
        $criteria->add( new Criteria( 'is_saved', '0' ) );
        $ret['count'] = $this->getCount( $criteria );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $ret['list'] = $this->getObjects( $criteria );
        return $ret;
    }

    function getTrashed( $nav, $zariliaUser ) {
        $ret = array();
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'to_userid', $zariliaUser ) );
        $criteria->add( new Criteria( 'is_trash', $zariliaUser ) );
        $ret['count'] = $this->getCount( $criteria );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $ret['list'] = $this->getObjects( $criteria );
        return $ret;
    }

    function getMSaved( $nav, $zariliaUser ) {
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'is_saved', $zariliaUser ) );
        $criteria->add( new Criteria( 'is_trash', '0' ) );
        $ret['count'] = $this->getCount( $criteria );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $ret['list'] = $this->getObjects( $criteria );
        return $ret;
    }

    function getTracked( $nav, $zariliaUser, $msg = 0 ) {
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'from_userid', $zariliaUser ) );
        $criteria->add( new Criteria( 'msg', $msg ) );
        $criteria->add( new Criteria( 'is_trash', '0' ) );
        $criteria->add( new Criteria( 'is_saved', '0' ) );
        $criteria->add( new Criteria( 'track', '1' ) );
        $ret['count'] = $this->getCount( $criteria );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $ret['list'] = $this->getObjects( $criteria );
        return $ret;
    }

    function getMessageNums() {
        $sql = "SELECT count(msg) as msg_total, sum(is_trash) as is_trash_total, MAX(time) AS posted FROM " . $this->db->prefix( 'messages' );
        if ( $result = $this->db->Execute( $sql ) ) {
            $myrow = $result->FetchRow();
        }
        return $myrow;
    }

    function getMessagePruneForm() {
        include_once( ZAR_ROOT_PATH . "/class/zariliaformloader.php" );
        $sform = new ZariliaThemeForm( _MS_PM_PRUNE, 'pruneform', 'prune.php' );
        $time = new ZariliaFormDateTime( _MS_PM_TIMEBEFORE, 'timeafter' );
        $time->setDescription( _MS_PM_TIMEBEFORE_DSC );
        $sform->addElement( $time );

        $readonly = new ZariliaFormRadioYN( _MS_PM_ONLYREADMESSAGES, 'readonly', 1 );
        $readonly->setDescription( _MS_PM_ONLYREADMESSAGES_DSC );
        $sform->addElement( $readonly );

		// $sform->addElement( new ZariliaFormDateTime( _MS_PM_TIMEAFTER, 'timeafter' ) );
        $sform->addElement( new ZariliaFormRadioYN( _MS_PM_ONLYREADMESSAGES, 'readonly', 1 ) );
        $sform->addElement( new ZariliaFormRadioYN( _MS_PM_ONLYINBOX, 'inbox', 1 ) );
        $sform->addElement( new ZariliaFormRadioYN( _MS_PM_ONLYTRASH, 'trash', 1 ) );
        $sform->addElement( new ZariliaFormRadioYN( _MS_PM_ONLYTACKER, 'tracker', 1 ) );
        $sform->addElement( new ZariliaFormRadioYN( _MS_PM_NOTIFYUSERS, 'notify', 0 ) );

        $button_tray = new ZariliaFormElementTray( '', '' );
        $button_tray->addElement( new ZariliaFormHidden( 'op', 'doprune' ) );
        $button_tray->addElement( new ZariliaFormHidden( "time", time() ) );
        $button_tray->addElement( new ZariliaFormButton( '', 'submit', _SUBMIT, 'submit' ), true );
        $button_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ), true );
        $button_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
        $sform->addElement( $button_tray );
        return $sform;
    }
}

?>