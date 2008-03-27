<?php
/**
 * $Id: message_create.php,v 1.3 2007/04/21 09:41:14 catzwolf Exp $
 */
/*
* Include Message Addons Header
*/
include_once "header.php";
/*
* Nav vars
*/
$nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
$nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'id' );
$nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
$nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 1 );
$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'inbox' );
if ( is_numeric( $op ) ) {
    $op = 'inbox';
} 

/*
* Display Inbox Items
*/
switch ( strtolower( $op ) ) {
    case "reply":
    case "forward":
    case "quote":
    case "create":
        $id = zarilia_cleanRequestVars( $_REQUEST, 'id', 0 );
        $to_user = zarilia_cleanRequestVars( $_REQUEST, 'uid', 0 );
        $message_obj = ( $id ) ? $pm_handler->get( $id ) : $pm_handler->create();
        switch ( $op ) {
            case 'reply':
                if ( !preg_match( "/^Re:/i", $message_obj->getVar( 'subject' ) ) ) {
                    $subject = 'Re: ' . $message_obj->getVar( 'subject' );
                } 
                $message = '';
                break;
            case 'forward':
                if ( !preg_match( "/^Fwd:/i", $message_obj->getVar( 'subject' ) ) ) {
                    $subject = 'Fwd: ' . $message_obj->getVar( 'subject' );
                } 
                $message = "----- Original Message -----";
                $message .= "\nFrom: " . $message_obj->getfromUserName( 0 );
                $message .= "\nTo: " . $message_obj->gettoUserName( 0 );
                $message .= "\nSent:" . $message_obj->formatTimeStamp();
                $message .= "\nSubject: " . $message_obj->getVar( 'subject' ) . "\n\n";
                $message .= ">" . strip_tags( $message_obj->getVar( "text" ) );
                $message .= "\n----- End Original Message -----";
                break;
            case 'quote':
                $subject = $message_obj->getVar( 'subject' );
                $message = "[quote]\n" . strip_tags( $message_obj->getVar( "text" ) ) . "\n[/quote]";
                break;
            default:
                $subject = $message_obj->getVar( 'subject' );
                $message = strip_tags( $message_obj->getVar( "text" ) );
                break;
        } // switch
        $message_obj->setVar( 'subject', $subject );
        $message_obj->setVar( 'text', $message );
        /*
		* Display create Message here
		*/
        zarilia_mod_header();
        message_show_top( $op, 0, $nav );
        $caption = ( !$message_obj->isNew() ) ? $caption = sprintf( _PM_REPLYPM, $message_obj->getVar( 'subject' ) ) : _PM_NEWPM;
        $form = $message_obj->messageForm( $caption, $to_user );
        $form->display();
        zarilia_mod_footer();
        break;

    case "add":
    case "save":
        $ret = '';
        if ( isset( $_REQUEST['to_userid'] ) && !empty( $_REQUEST['to_userid'] ) ) {
            $user_obj = $member_handler->getUserByName( strval( trim( $_REQUEST['to_userid'] ) ) );
            if ( is_object( $user_obj ) ) {
                if ( $user_obj->getVar( 'uid' ) == $zariliaUser->getVar( 'uid' ) ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_CANNOTSENDYOURSELF );
                } else {
                    $_REQUEST['buddy_name'] = $user_obj->getVar( 'uid' );
                } 
            } 
        } 
        if ( isset( $_REQUEST['savebox'] ) && $_REQUEST['savebox'] ) {
            $message_obj = $pm_handler->create();
            $message_obj->setVar( "subject", $_REQUEST['subject'] );
            $message_obj->setVar( "text", $_REQUEST['text'] );
            $message_obj->setVar( "time", $_REQUEST['time'] );
            $message_obj->setVar( "is_saved", 1 );
            $message_obj->setVar( "from_userid", $zariliaUser->getVar( "uid" ) );
            $message_obj->setVar( "to_userid", $_REQUEST['buddy_name'] );
            if ( !$pm_handler->insert( $message_obj ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $message_obj->getErrors() );
            } 
			redirect_header( "index.php", 1, _PM_MESSAGESAVED );
        } else {
            $_users_array = array();
            if ( intval( $_REQUEST['buddy_name'] ) == 0 ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, "No buddy selected to send a private message" );
                zarilia_mod_header();
                $GLOBALS['zariliaLogger']->sysRender();
                zarilia_mod_footer();
                exit();
            } 
            /*
			* Get blocked buddy list
			*/
            if ( isset( $_REQUEST['buddy_cc'] ) && is_array( $_REQUEST['buddy_cc'] ) ) {
                $_users_array = $_REQUEST['buddy_cc'];
                if ( !in_array( intval( $_REQUEST['buddy_name'] ), $_users_array ) ) {
                    array_push( $_users_array, $_REQUEST['buddy_name'] );
                } 
            } else {
                $_users_array[] = intval( $_REQUEST['buddy_name'] );
            } 
            if ( count( $_users_array ) == 0 ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, "No buddy selected to send a private message" );
                zarilia_mod_header();
                $GLOBALS['zariliaLogger']->sysRender();
                zarilia_mod_footer();
                exit();
            } 

            for ( $i = 0; $i < count( $_users_array ); $i++ ) {
                $buddy_obj = $buddy_handler->getblockedBuddy( $_users_array[$i], $zariliaUser->getVar( "uid" ) );
                if ( $buddy_obj ) {
                     $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_YOUAREBLOCKED );
	            } else {
                    $criteria = new CriteriaCompo();
                    $criteria->add( new Criteria( 'to_userid', $_users_array[$i] ) );
                    $criteria->add( new Criteria( 'is_trash', '0' ) );
                    $criteria->add( new Criteria( 'is_saved', '0' ) );
                    $pm_arr = $pm_handler->getCount( $criteria );
                    $pm_arr_count = ( count( $pm_arr ) > 0 ) ? count( $pm_arr ) : 0;
                    if ( $pm_arr_count > $zariliaConfig['inboxlimit'] ) {
                        $_senders_name = $zariliaUser->getUnameFromId( $zariliaUser->getVar( "uid", 1 ) );
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( "<b>" . _PM_CR_PMCANNOTSEND . "</b>", $_senders_name ) );
                    } else {
                        $message_obj = $pm_handler->create();
                        $message_obj->setVar( "subject", $_REQUEST['subject'] );
                        $message_obj->setVar( "text", $_REQUEST['text'] );
                        $message_obj->setVar( "to_userid", $_users_array[$i] );
                        $message_obj->setVar( "from_userid", $zariliaUser->getVar( "uid" ) );
                        $message_obj->setVar( "priority", $_REQUEST['priority'] );
                        $message_obj->setVar( "track", isset( $_REQUEST['tracker'] ) );
                        $message_obj->setVar( "time", $_REQUEST['time'] );
                        $_message = _PM_MESSAGEPOSTED;
                        if ( !$pm_handler->insert( $message_obj ) ) {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $message_obj->getErrors() );
                        } else {
                            $msg_obj = $msgsent_handler->create();
							$msg_id = $message_obj->getVar('id');
							$msg_obj->setVar( "id", $msg_id );
							$msg_obj->setVar( "subject", $_REQUEST['subject'] );
                            $msg_obj->setVar( "text", $_REQUEST['text'] );
                            $msg_obj->setVar( "to_userid", $_users_array[$i] );
                            $msg_obj->setVar( "from_userid", $zariliaUser->getVar( "uid" ) );
                            $msg_obj->setVar( "priority", $_REQUEST['priority'] );
                            $msg_obj->setVar( "track", isset( $_REQUEST['tracker'] ) );
                            $msg_obj->setVar( "time", $_REQUEST['time'] );
                            $msgsent_handler->insert( $msg_obj );
                        } 
                    } 
                } 
            } 

            if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
                zarilia_mod_header();
                $GLOBALS['zariliaLogger']->sysRender();
                zarilia_mod_footer();
            } else {
                redirect_header( "index.php", 1, _PM_MESSAGEPOSTED );
                break;
            } 
        } 
        break;
} // switch

?>
