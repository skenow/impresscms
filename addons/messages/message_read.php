<?php
include_once "header.php";
message_check_user();

$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'inbox' );

$id = zarilia_cleanRequestVars( $_REQUEST, 'id', 0 );
$zariliaPMConfig = &$config_handler->getConfigsByCat( 7 );
if ( !is_array( $id ) && $id > 0 ) {
    if ( $op != 'sent' || $op != 'tracker' ) {
        $_handler = 'pm_handler';
        $type = ( $op != 'tracker' ) ? 'to_userid' : 'from_userid';
    } else {
        $_handler = 'msgsent_handler';
        $type = 'from_userid';
    } 
    $pmessage = $$_handler->get( $id );
	if ( !is_object( $pmessage ) || $pmessage->getVar( $type ) != $zariliaUser->getVar( 'uid' ) ) {
		zarilia_mod_header();
        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_NOPERMISSION );
        $GLOBALS['zariliaLogger']->sysRender();
        zarilia_mod_footer();
        exit();
    } else {
        $message_header = 'Personal Message';
        if ( is_object( $pmessage ) && $id > 0 ) {
            if ( $pmessage->getVar( 'msg' ) == 0 || $pmessage->getVar( 'read_date' ) == 0 ) {
                $criteria = new CriteriaCompo();
                $criteria->add( new Criteria( 'id', $id ) );
                $msg_array = array( 'msg' => 1, 'read_date' => time() );
                $pm_handler->updateAll( $msg_array, 0, $criteria );
            } 
        } 
    } 
} 

$ret = $zariliaConfig['anonymous'];
$member_handler = &zarilia_gethandler( 'member' );
$poster = $member_handler->getUser( $pmessage->getVar( "from_userid" ) );
if ( $poster->isActive() ) {
    if ( $poster->isOnline() && $poster->getVar( "uid" ) != $zariliaUser->getVar( "uid" ) ) {
        $ret = "<div><span style='color: #ee0000; font-weight: bold;'>" . $zariliaUser->getUnameFromId( $pmessage->getVar( "from_userid" ) ) . "</span></div>\n";
        $useronline = "<div><span style='color: #ee0000; font-weight: bold;'>" . _PM_ONLINE . "</span></div>\n";
    } else {
        $ret = "<div><a href='userinfo.php?uid=" . $poster->getVar( "uid" ) . "'>" . $poster->getVar( "uname" ) . "</a></div>\n";
        $useronline = "<div><span font-weight: bold;'>" . _PM_OFFLINE . "</span></div>\n";
    } 
} 

$toolbar = "";
if ( $op == "inbox" ) {
    $toolbar .= "
	 <div style='text-align: right; padding-bottom: 12px;'>
	  <a href='message_create.php?op=reply&id=" . $id . "'>" . zarilia_img_show( 'rounded', _PM_PMREPLY, 'middle' ) . "</a> 
	  <a href='message_create.php?op=forward&id=" . $id . "'>" . zarilia_img_show( 'rounded', _PM_PMFORWARD, 'middle' ) . "</a>
	  <a href='message_create.php?op=quote&id=" . $id . "'>" . zarilia_img_show( 'rounded', _PM_PMQUOTE, 'middle' ) . "</a>
	  <a href='message_func.php?op=del&id=" . $id . "'>" . zarilia_img_show( 'rounded', _PM_PMDELETE, 'middle' ) . "</a>
	  <a href='message_func.php?op=trash&id=" . $id . "'>" . zarilia_img_show( 'rounded', _PM_PMTRASH, 'middle' ) . "</a> 
	 </div>\n";
} 
$rank = $poster->rank();

zarilia_mod_header();
message_show_top( $op, 0, '' );

$rank_image = "";
if ( is_file( ZAR_UPLOAD_PATH . "/" . $rank->getVar('rank_image') ) ) {
    $rank_image = "<img src='" . ZAR_UPLOAD_URL . "/" . $rank->getVar('rank_image') . "' alt='" . $rank->getVar('rank_title') . "' />";
} 

$avatar = "";
if ( $poster->getVar( 'user_avatar' ) ) {
    $avatar = "<img style='border: 1px solid #FF9900;' src='" . ZAR_UPLOAD_URL . '/' . $poster->getVar( 'user_avatar' ) . "' alt='" . $rank->getVar('rank_title') . "' />";
} 
$buddy_add_url = 'message_buddy.php?op=buddy_save&amp;buddy_uid=' . $poster->getVar( 'uid' );

/*
* html output
*/ 
echo $toolbar;
echo '
<table width="100%" border="0" cellspacing="1" cellpadding="2">
  <tr><th colspan="2" style="text-align: left;">' . $message_header . '</th></tr>
  <tr>
    <td width="35%" class="head">' . $ret . '</td>
    <td width="65%" class="head"><strong>' . $pmessage->getVar( 'subject' ) . '</strong></td>
  </tr>
  <tr>
    <td class="even">
	 <div>' . $avatar . '</div>';
if ( $rank_image ) {
    echo '<div>' . $rank_image . '</div>';
} 
echo '<br />
	 <div>' . sprintf( _PM_PMMEMBERID, $poster->getVar( 'uid' ) ) . '</div>
	 <div>' . sprintf( _PM_RANKTITLE, $rank->getVar('rank_title') ) . '</div>
	 <div>' . sprintf( _PM_PMPOSTS, $poster->getVar( 'posts' ) ) . '</div>
	 <div>' . sprintf( _PM_PMJOINED, formatTimestamp( $poster->getVar( 'user_regdate' ), 's' ) ) . '</div><br />
	 ' . $useronline . '
	</td>
    <td class="even"><div>Sent: ' . formatTimestamp( $pmessage->getVar( 'time' ) ) . '</div><br /><div>' . $pmessage->getVar( 'text', "S" ) . '</div></td>
  </tr>
  <tr>
    <td class="head">';
if ( $op == "inbox" ) {
    echo '<a href="' . $buddy_add_url . '">' . _PM_BUDDY_ADDFRIEND . '</a>';
} 
echo '</td>
    <td class="head" style="text-align: right; padding-right: 10px;"><a href="javascript:scroll(0,0)">' . _PM_BUDDY_TOP . '</a></td>
  </tr>
  <tr>
    <td colspan="2" class="foot">&nbsp;</td>
  </tr>
</table>';
echo $toolbar;

zarilia_mod_footer();

?>