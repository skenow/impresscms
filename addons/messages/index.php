<?php
/**
 * $Id: index.php,v 1.2 2007/04/21 09:41:14 catzwolf Exp $
 */
/*
* Include Message Addons Header
*/
include_once "header.php";

/*
* 
*/
message_check_user();
/*
* Nav vars
*/
$nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
$nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'id' );
$nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
$nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
$msg = zarilia_cleanRequestVars( $_REQUEST, 'msg', 0 );
$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'inbox' );
if ( is_numeric( $op ) ) {
    $op = 'inbox';
} 
$_sender = ( $op == "inbox" ) ? "from_userid" : "to_userid";
/*
* 
*/
switch ( strtolower( $op ) ) {
    case 'inbox':
    default:
        $pm_arr = $pm_handler->getInbox( $nav, $zariliaUser->getVar( 'uid' ) );
        break;
    case 'trashed':
        $pm_arr = $pm_handler->getTrashed( $nav, $zariliaUser->getVar( 'uid' ) );
        break;
    case 'msaved':
        $pm_arr = $pm_handler->getMSaved( $nav, $zariliaUser->getVar( 'uid' ) );
        break;
    case 'tracker':
        $pm_arr = $pm_handler->getTracked( $nav, $zariliaUser->getVar( 'uid' ), $msg );
        break;
    case 'sent':
        $pm_arr = $msgsent_handler->getSent( $nav, $zariliaUser->getVar( 'uid' ) );
        break;
} // switch
/*
* Start form tables
*/
$tlist = new ZariliaTList();
$tlist->AddFormStart( 'post', 'message_func.php', 'prvmsg' );
$tlist->AddHeader( '', '5%', 'center', false );
$tlist->AddHeader( 'priority', '5%', 'center', false );
$tlist->AddHeader( 'subject', '150px', 'left', false );
$tlist->AddHeader( $_sender, '', 'left', false );
$tlist->AddHeader( 'time', '', 'center', false );
$tlist->AddHeader( '', '', 'center', false );
$tlist->setPrefix( '_PM_' );
/*
* loops through object
*/
foreach ( $pm_arr['list'] as $obj ) {
    $value = ($op != 'tracker') ? "<input type='checkbox' id='pmid' name='id[]' value='" . $obj->getVar( "id" ) . "' />" : '&nbsp;&nbsp;&nbsp;';
    $tlist->add( 
		array( $obj->getreadImage(),
            $obj->getpriorityImage(),
            $obj->getSubject( $op ),
            $obj->getLinkedUserName( $op ),
            $obj->formatTimeStamp(),
            $value, 
            ), 'even' );
} 
/*
* Navigation Footer details
*/
if ( $pm_arr['count'] && $op != "tracker" ) {
    if ( $op == 'sent' ) {
        $op_type = array( "delsent" => "Delete Selected" );
    } else if ( $op == 'tracker' ) {
        $op_type = array( "stoptrackin" => "Stop Tracking" );
    } else {
        $op_type = array( "del" => "Delete Selected", "saved" => "Save Selected", "trash" => "Trash Selected", "read" => "Mark Selected Read", "unread" => "Mark Selected Unread" );
    } 
    $footer = zarilia_getSelection( $op_type, '', 'op', 1, 1, false, 'Select Task...', '', 0, false );
    $footer .= "<input type='submit' class='button' value='Go!' />";
    $footer .= "<input style='text-align: center;' name='allbox' id='allbox' onclick='zariliaCheckAll(\"prvmsg\", \"allbox\");' type='checkbox' value='Check All' />";
    $tlist->addFooter( $footer );
} 

/*
* Display Output
*/
zarilia_mod_header();
message_show_top( $op, $pm_arr['count'], $msg );
$tlist->render();
zarilia_pagnav( $pm_arr['count'], $nav['limit'], $nav['start'], 'start', 1, 'op=' . $op );
message_show_box( $op, $pm_arr['count'] );
zarilia_mod_footer();

?>
