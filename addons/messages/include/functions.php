<?php
if ( !defined( 'ZAR_ROOT_PATH' ) ) {
    exit( 'You cannot access this file directly' );
} 
/**
 * 
 * @version $Id: functions.php,v 1.3 2007/04/21 09:41:12 catzwolf Exp $
 * @copyright 2005
 */
function message_show_top( $op, $pm_arr_count, $msg ) {
    global $zariliaConfig;

    echo '<h3 style="text-align:left;">' . _PM_PMCP . '</h3>';
	 
    zarilia_show_buttons( 'right', 'files_button', 'formbutton' ,
        array( "message_create.php?op=create" => _PM_CREATE,
            "message_buddy.php" => _PM_BUDDIES, 
            ) 
        );

	echo '<div><b>' . constant( "_PM_" . strtoupper( $op ) . "_BOX" ) . '</b></div>
	 <div style="margin-bottom: 12px;">' . constant( "_PM_" . strtoupper( $op ) . "_BOXDSC" ) . '</div>';

    if ( ( $zariliaConfig['inboxlimit'] && $pm_arr_count ) && $pm_arr_count >= $zariliaConfig['inboxlimit'] ) {
        echo '<div style="margin-bottom: 12px;" class="warning">' . _PM_WARNING . '</div>';
    } 

	if ($op == "tracker") {
	   	$select_type = array( 0 => "Read Messages", 1 => "Non-read Messages" );
		echo "<div style='text-align: left; class='jumpmenu'>" . zarilia_getSelection( $select_type, $msg, 'ccmember', 1, 1, false, 'Selected Tracked Type', $extra = "onchange='location.href=\"index.php?op=tracker&amp;msg=\"+this.options[this.selectedIndex].value'", 0 ) . "</div>";

	}
} 

function message_show_box( $op, $pm_arr_count ) {
    global $zariliaConfig, $zariliaUser, $pm_handler;

    $select_type = array( "inbox" => _PM_PMINBOX, "sent" => _PM_PMSENT, "msaved" => _PM_PMMSAVED, 
	"trashed" => _PM_PMTRASHED, "tracker" => _PM_PMTRACKER );
    /**
     * This is the botom panel
     */
    $percent = ( !empty( $pm_arr_count ) && intval( $pm_arr_count ) > 0 ) ? 100 * intval( $pm_arr_count ) / $zariliaConfig['inboxlimit'] : 0;
    $criteria = new CriteriaCompo();
    $criteria->add( new Criteria( 'to_userid', $zariliaUser->getVar( 'uid' ) ) );
    $criteria->add( new Criteria( 'msg', '0' ) );
    $pm_arr_new_count = $pm_handler->getCount( $criteria );

    $criteria = new CriteriaCompo();
    $criteria->add( new Criteria( 'to_userid', $zariliaUser->getVar( 'uid' ) ) );
    $criteria->add( new Criteria( 'msg', '1' ) );
    $pm_arr_read_count = $pm_handler->getCount( $criteria );
    $pm_arr_total_count = $pm_arr_new_count + $pm_arr_read_count;

    echo "<br />
	  <table width='100%' cellpadding='4' cellspacing='1' summary=''> 
	   <tr> 
	    <td style='text-align: left; width: 50%;'>
		 <table id='pmpanel' style='width: 250px;' cellspacing='1' summary=''> 
	      <tr> 
	       <td class='row1' style='text-align: left;' colspan='3'>" . sprintf(_PM_PMISFULL,ucfirst( $op ), $percent."%" )  . "</td> 
	      </tr> 
	      <tr> 
	       <td style='text-align: left;' valign='top' class='row2' colspan='3'>";
    if ( $percent > 0 ) {
        $percent2 = ( $percent >= 100 ) ? 100 : $percent;
        $width = intval( $percent2 ) * 2.5;
        echo "<img src='" . ZAR_IMAGE_URL . "/colorbars/" . $zariliaConfig['barcolour'] . ".gif' height='14' width='" . $width . "' align='middle' alt='" . intval( $percent ) . "%' />";
    } 

    printf( " %d %% (%d)", $percent, $pm_arr_count );
    echo "</td> 
	     </tr> 
	     <tr class='even'> 
	      <td style='text-align: left; width: 33%;'>0%</td> 
	      <td style='text-align: center; width: 33%;'>50%</td> 
	      <td style='text-align: right;  width: 33%;'>100%</td> 
	     </tr> 
	    </table>	
		</td> 
	   	<td style='text-align: right; text-valign: top; width: 50%;' class='jumpmenu'>" . zarilia_getSelection( $select_type, $op, 'ccmember', 1, 1, false, 'Jump to folder.........', $extra = "onchange='location.href=\"index.php?op=\"+this.options[this.selectedIndex].value'", 0 ) . "</td> 
	   </tr> 
	   <tr> 
	    <td>&nbsp;</td>
	   </tr>	 
	   <tr> 
	    <td>
		 <div>" . zarilia_img_show( 'pmmailnew', 'New', 'middle' ) . " ".sprintf(_PM_PMNEW, $pm_arr_new_count)."</div> 
	     <div>" . zarilia_img_show( 'pmmail', 'Read', 'middle' ) . " ".sprintf(_PM_PMREAD, $pm_arr_read_count)."</div> 
	     <div>" . zarilia_img_show( 'pmmailtot', 'Total', 'middle' ) . " ".sprintf(_PM_PMTOTAL, $pm_arr_total_count)."</div>
	    </td> 
	    <td><div>You have $pm_arr_count messages out of {$zariliaConfig['inboxlimit']} maximum storable messages</div></td> 
	   </tr> 
	 </table>";
} 

function message_check_user() {
    global $zariliaUser, $zariliaConfig;

    $allowed = false;
    if ( is_object( $zariliaUser ) ) {
        if ( array_intersect( $zariliaUser->getGroups(), $zariliaConfig['message_okgrp'] ) || in_array( ZAR_GROUP_ADMIN, $zariliaUser->getGroups() ) ) {
            $allowed = true;
        } 
    } 
    if ( $allowed !== true ) {
        include ZAR_ROOT_PATH . "/header.php";
        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_NOPERMISSION );
        $GLOBALS['zariliaLogger']->sysRender();
        include ZAR_ROOT_PATH . "/footer.php";
        exit();
    } 
    return $allowed;
} 

?>