<?php
// $Id: misc.php,v 1.4 2007/05/05 11:10:55 catzwolf Exp $
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
include "mainfile.php";
include_once ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/misc.php';
// $op = zarilia_cleanRequestVars( $_REQUEST, 'op', '', XOBJ_DTYPE_TXTBOX );
$type = zarilia_cleanRequestVars( $_REQUEST, 'type', '', XOBJ_DTYPE_TXTBOX );

zarilia_header( false );
$closebutton = 1;
switch ( $type ) {
    case "smilies":
        $target = isset( $_GET['target'] ) ? trim( $_GET['target'] ) : '';
        echo "<script type=\"text/javascript\">
		<!--
		function doSmilie(addSmilie) {
		var currentMessage = window.opener.zariliaGetElementById(\"" . $target . "\").value;
		window.opener.zariliaGetElementById(\"" . $target . "\").value=currentMessage+addSmilie;
		return;
		}
		-->
		</script>";

        echo '</head><body>
		<table width="100%" class="outer">
		<tr>
		<th colspan="3">' . _MSC_SMILIES . '</th>
		</tr>
		<tr class="head">
		<td>' . _MSC_CODE . '</td>
		<td>' . _MSC_EMOTION . '</td>
		<td>' . _IMAGE . '</td>
		</tr>';
        if ( $getsmiles = $zariliaDB->Execute( "SELECT * FROM " . $zariliaDB->prefix( "smiles" ) ) ) {
            $rcolor = 'even';
            while ( $smile = $getsmiles->FetchRow() ) {
                echo "<tr class='$rcolor'><td>" . $smile['code'] . "</td><td>" . $smile['emotion'] . "</td><td><img onmouseover='style.cursor=\"hand\"' onclick='doSmilie(\" " . $smile['code'] . " \");' src='" . ZAR_UPLOAD_URL . "/" . $smile['smile_url'] . "' alt='' /></td></tr>";
                $rcolor = ( $rcolor == 'even' ) ? 'odd' : 'even';
            }
        } else {
            echo "Could not retrieve data from the database.";
        }
        echo '</table>' . _MSC_CLICKASMILIE;
        break;

    case "friend":
        if ( !isset( $_POST['op'] ) || $_POST['op'] == "sendform" ) {
            if ( $zariliaUser ) {
                $yname = $zariliaUser->getVar( "uname", 'e' );
                $ymail = $zariliaUser->getVar( "email", 'e' );
                $fname = "";
                $fmail = "";
            } else {
                $yname = "";
                $ymail = "";
                $fname = "";
                $fmail = "";
            }
            printCheckForm();
            echo '</head><body>
				<form action="' . ZAR_URL . '/misc.php" method="post" onsubmit="return checkForm();"><table  width="100%" class="outer" cellspacing="1"><tr><th colspan="2">' . _MSC_RECOMMENDSITE . '</th></tr>';
            echo "<tr><td class='head'>
	   				<input type='hidden' name='op' value='sendsite' />
					<input type='hidden' name='op' value='showpopups' />
					<input type='hidden' name='type' value='friend' />\n";
            echo _MSC_YOURNAMEC . "</td><td class='even'><input type='text' name='yname' value='$yname' id='yname' /></td></tr>
	   				<tr><td class='head'>" . _MSC_YOUREMAILC . "</td><td class='odd'><input type='text' name='ymail' value='" . $ymail . "' id='ymail' /></td></tr>
	   				<tr><td class='head'>" . _MSC_FRIENDNAMEC . "</td><td class='even'><input type='text' name='fname' value='$fname' id='fname' /></td></tr>
					<tr><td class='head'>" . _MSC_FRIENDEMAILC . "</td><td class='odd'><input type='text' name='fmail' value='$fmail' id='fmail' /></td></tr>
	   				<tr><td class='head'>&nbsp;</td><td class='even'><input type='submit' value='" . _SEND . "' />&nbsp;<input value='" . _CLOSE . "' type='button' onclick='javascript:window.close();' /></td></tr>
					</table></form>\n";
            $closebutton = 0;
        } elseif ( $_POST['op'] == "sendsite" ) {
            if ( $zariliaUser ) {
                $ymail = $zariliaUser->getVar( "email" );
            } else {
                $ymail = isset( $_POST['ymail'] ) ? stripslashes( trim( $_POST['ymail'] ) ) : '';
            }
            if ( !isset( $_POST['yname'] ) || trim( $_POST['yname'] ) == "" || $ymail == '' || !isset( $_POST['fname'] ) || trim( $_POST['fname'] ) == "" || !isset( $_POST['fmail'] ) || trim( $_POST['fmail'] ) == '' ) {
                redirect_header( ZAR_URL . "/misc.php?type=friend&amp;op=sendform", 2, _MSC_NEEDINFO );
                exit();
            }
            $yname = stripslashes( trim( $_POST['yname'] ) );
            $fname = stripslashes( trim( $_POST['fname'] ) );
            $fmail = stripslashes( trim( $_POST['fmail'] ) );
            if ( !checkEmail( $fmail ) || !checkEmail( $ymail ) ) {
                $errormessage = _MSC_INVALIDEMAIL1 . "<br />" . _MSC_INVALIDEMAIL2 . "";
                redirect_header( ZAR_URL . "/misc.php?type=friend&amp;op=sendform", 2, $errormessage );
                exit();
            }
            $zariliaMailer = &getMailer();
            $zariliaMailer->setTemplate( "tellfriend.tpl" );
            $zariliaMailer->assign( "SITENAME", $zariliaConfig['sitename'] );
            $zariliaMailer->assign( "ADMINMAIL", $zariliaConfig['adminmail'] );
            $zariliaMailer->assign( "SITEURL", ZAR_URL . "/" );
            $zariliaMailer->assign( "YOUR_NAME", $yname );
            $zariliaMailer->assign( "FRIEND_NAME", $fname );
            $zariliaMailer->setToEmails( $fmail );
            $zariliaMailer->setFromEmail( $ymail );
            $zariliaMailer->setFromName( $yname );
            $zariliaMailer->setSubject( sprintf( _MSC_INTSITE, $zariliaConfig['sitename'] ) );
            // OpenTable();
            if ( !$zariliaMailer->send() ) {
                echo $zariliaMailer->getErrors();
            } else {
                echo "<div><h4>" . _MSC_REFERENCESENT . "</h4></div>";
            }
        }
        break;

    case 'online':
        $isadmin = $zariliaUserIsAdmin;

        $start = isset( $_GET['start'] ) ? intval( $_GET['start'] ) : 0;
        $online_handler = &zarilia_gethandler( 'online' );
        $online_total = &$online_handler->getCount();
        $limit = ( $online_total > 20 ) ? 20 : $online_total;

        $member_handler = &zarilia_gethandler( 'member' );

        $criteria = new CriteriaCompo();
        $criteria->setLimit( $limit );
        $criteria->setStart( $start );
        $onlines = &$online_handler->getAll( $criteria );

        $addon_handler = &zarilia_gethandler( 'addon' );
        $addons = &$addon_handler->getList( new Criteria( 'isactive', 1 ) );
        $class = 'even';

        $ret = '
		<div style="padding: 12px;">
		<h3 style="text-align: left;">' . _WHOSONLINE . '</h3>';
        foreach( $onlines as $online ) {
            $online_user = &$member_handler->getUser( $online->getVar( 'online_uid' ) );

            if ( $online->getVar( 'online_hidden' ) == 1 ) {
                $show = false;
            }
            if ( is_object( $online_user ) && $isadmin ) {
                $show = true;
            }
            if ( is_object( $online_user ) && $show ) {
                $online_avatar = $online_user->getVar( 'user_avatar' ) ? '<img src="' . ZAR_UPLOAD_URL . '/' . $online_user->getVar( 'user_avatar' ) . '" alt="" />' : '&nbsp;';
                $online_name = "<a href=\"javascript:window.opener.location='" . ZAR_URL . "/index.php?page_type=userinfo&amp;uid=" . $online_user->getVar( 'uid' ) . "';window.close();\">" . $online_user->getVar( 'uname' ) . "</a>";
                $online_rank = $online_user->rank( true );
            } else {
                $online_avatar = '';
                $online_name = $zariliaConfig['anonymous'];
                $online_rank = '';
            }
            $online_time = formatTimestamp( $online->getVar( 'online_updated' ) );
            $online_timeonline = getTimeOnline( $online->getVar( 'online_updated' ) );
            $online_module = $online->getVar( 'online_addon' ) ? $addons[ $online->getVar( 'online_addon' ) ] : '';

            $ret .= '<table width="100%" cellpadding="5" cellspacing="1" class="outers">';
            $ret .= '<tr>
				    <td width="30%"><b>' . _MSC_ONLINE_NAME . '</b></td>
				    <td>' . $online_name . '</td>';
            if ( !empty( $online_avatar ) ) {
                $ret .= '<td rowspan="3" align="center">' . $online_avatar . '</td>';
            }
            $ret .= '</tr>
				  <tr>
				    <td><b>' . _MSC_ONLINE_RANK . '</b></td>
				    <td>' . $online_rank . '</td>
				  </tr>
				  <tr>';
            if ( $isadmin == 1 ) {
                $ret .= '<td><b>' . _MSC_ONLINE_IPADDRESS . '</b></td>
				    <td>' . $online->getVar( 'online_ip' ) . '</td>
				  </tr>';
            }
            $ret .= '<tr>
				    <td><b>' . _MSC_ONLINE_TIME . '</b></td>
				    <td>' . $online_time . ' (' . $online_timeonline . ')</td>
				  </tr>';
            if ( $online_module ) {
                $ret .= '<tr>
				    <td><b>' . _MSC_ONLINE_MODULE . '</b></td>
				    <td>' . $online_time . '</td>
				  </tr>';
            }
            $ret .= '</table><br />';
        }
        echo $ret;
        if ( $online_total > 20 ) {
            include_once ZAR_ROOT_PATH . '/class/pagenav.php';
            $nav = new ZariliaPageNav( $online_total, 20, $start, 'start', 'type=online' );
            $ret .= '<div style="text-align: right;">' . $nav->renderNav() . '</div>';
        }
        $ret .= '</div>';
        break;

    case 'debug':
        if ( $zariliaUser && $zariliaUser->isAdmin() ) {
            $file = ZAR_CACHE_PATH . '/' . trim( str_replace( '..', '', $_GET['file'] ) );
            if ( file_exists( $file ) ) {
                include( $file );
                @unlink( $file );
            }
        }
        break;

    case 'ssllogin':
        if ( $zariliaConfig['use_ssl'] && isset( $_POST[$zariliaConfig['sslpost_name']] ) && is_object( $zariliaUser ) ) {
            include_once ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/user.php';
            echo sprintf( _US_LOGGINGU, $zariliaUser->getVar( 'uname' ) );
            echo '<div style="text-align:center;"><input class="formButton" value="' . _CLOSE . '" type="button" onclick="window.opener.location.reload();window.close();" /></div>';
            $closebutton = false;
        }
        break;

    case 'play':
        $id = zarilia_cleanRequestVars( $_REQUEST, 'id', 1, XOBJ_DTYPE_INT );
        $streaming_handler = &zarilia_gethandler( 'streaming' );
        $_streaming_obj = $streaming_handler->get( $id );
        $ext = pathinfo( $_streaming_obj->getVar( 'streaming_file' ), PATHINFO_EXTENSION );
        $file = ZAR_UPLOAD_URL . '/streams/' . $_streaming_obj->getVar( 'streaming_file' );

        echo "<div style=\"text-align:center;\"><br />";
        echo '<img align="absmiddle" src="' . ZAR_URL . '/images/logo.gif" alt="' . $_streaming_obj->getVar( 'streaming_file' ) . '" hspace="3px" vspace="0px" border="0" /><br /><br />';
        switch ( $ext ) {
            case 'wmv':
            case 'wma':
                echo "
						      <OBJECT id='mediaPlayer' width='320' height='285'
						      classid='CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95'
						      codebase='http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701'
						      standby='Loading Microsoft Windows Media Player components...' type='application/x-oleobject'>
						      <param name='fileName' value='" . $file . "'>
						      <param name='animationatStart' value='true'>
						      <param name='transparentatStart' value='true'>
						      <param name='autoStart' value='true'>
						      <param name='showControls' value='true'>
						      <param name='loop' value='true'>
						      <EMBED type='application/x-mplayer2'
						        pluginspage='http://microsoft.com/windows/mediaplayer/en/download/'
						        id='mediaPlayer' name='mediaPlayer' displaysize='4' autosize='-1'
						        bgcolor='darkblue' showcontrols='true' showtracker='-1'
						        showdisplay='0' showstatusbar='-1' videoborder3d='-1' width='320' height='285'
						        src='" . $file . "' autostart='false' designtimesp='5311' loop='true'>
						      </EMBED></div><div style=\"text-align:center;\">
						      </OBJECT>
						      <!-- ...end embedded WindowsMedia file -->
						    	<!-- begin link to launch external media player... -->
						        <a href='" . $file . "' style='font-size: 85%;' target='_blank'>Launch in external player</a>
						        <!-- ...end link to launch external media player... --></div>";
                break;

            case 'mp3':
                echo "
				        <!-- begin video window... -->
				        <tr><td>
				        <OBJECT classid='clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B' width='320' height='255' codebase='http://www.apple.com/qtactivex/qtplugin.cab'>
				        <param name='src' value='" . $file . "'>
				        <param name='autoplay' value='true'>
				        <param name='controller' value='true'>
				        <param name='loop' value='true'>
				        <EMBED src='" . $file . "' width='320' height='255' autoplay='true'
				        controller='true' loop='true' pluginspage='http://www.apple.com/quicktime/download/'>
				        </EMBED>
				        </OBJECT>
				        <!-- ...end embedded QuickTime file -->";
                break;

            case 'mp3':
                echo "
				        <!-- begin video window... -->
				        <tr><td>
				        <OBJECT classid='clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B' width='320' height='255' codebase='http://www.apple.com/qtactivex/qtplugin.cab'>
				        <param name='src' value='" . $file . "'>
				        <param name='autoplay' value='true'>
				        <param name='controller' value='true'>
				        <param name='loop' value='true'>
				        <EMBED src='" . $file . "' width='320' height='255' autoplay='true'
				        controller='true' loop='true' pluginspage='http://www.apple.com/quicktime/download/'>
				        </EMBED>
				        </OBJECT>
				        <!-- ...end embedded QuickTime file -->";
                break;

            case 'flv':
            case 'swf':
            default:
                echo "<script type=\"text/javascript\" src=\"" . ZAR_URL . "/class/streaming/ufo.js\"></script>\n";
                echo "<p id=\"ZariliaPlayer\"><a href=\"http://www.macromedia.com/go/getflashplayer\">Get the Flash Player</a> to see this player.</p>";
                echo '<script type="text/javascript">
							var FO = {	movie:"' . ZAR_URL . '/class/streaming/mediaplayer.swf",width:"300",height:"140",majorversion:"7",build:"0",bgcolor:"#FFFFFF",
										flashvars:"file=' . $file . '&showeq=true&showdigits=true&autostart=false&logo=' . ZAR_URL . '/class/streaming/logo.png" };
							UFO.create(	FO, "ZariliaPlayer");
						</script>';

                break;
        } // switch
        echo "</div>";
        break;

    default:
        break;
}
if ( $closebutton ) {
    echo '<div style="text-align:center;"><input class="formButton" value="' . _CLOSE . '" type="button" onclick="javascript:window.close();" /></div>';
}
zarilia_footer();

function printCheckForm()
{

    ?>
	<script language='javascript'>
		<!--
		function checkForm()
		{
			if ( zariliaGetElementById("yname").value == "" ){
				alert( "<?php echo _MSC_ENTERYNAME;

    ?>" );
				zariliaGetElementById("yname").focus();
				return false;
			} else if ( zariliaGetElementById("fname").value == "" ){
				alert( "<?php echo _MSC_ENTERFNAME;

    ?>" );
				zariliaGetElementById("fname").focus();
				return false;
			} else if ( zariliaGetElementById("fmail").value ==""){
				alert( "<?php echo _MSC_ENTERFMAIL;

    ?>" );
				zariliaGetElementById("fmail").focus();
				return false;
			} else {
				return true;
			}
		}
		-->
	</script>
	<?php
}

function getTimeOnline( $timestamp )
{
    // Store the current time
    $current_time = time();
    // Determine the difference, between the time now and the timestamp
    $difference = $current_time - $timestamp;
    // Set the periods of time
    $periods = array( "sec", "min", "hour", "day", "week", "month", "year", "decade" );
    // Set the number of seconds per period
    $lengths = array( 1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600 );
    // Determine which period we should use, based on the number of seconds lapsed.
    // If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
    // Go from decades backwards to seconds
    for ( $val = sizeof( $lengths ) - 1; ( $val >= 0 ) && ( ( $number = $difference / $lengths[$val] ) <= 1 ); $val-- );
    // Ensure the script has found a match
    if ( $val < 0 ) $val = 0;
    // Determine the minor value, to recurse through
    $new_time = $current_time - ( $difference % $lengths[$val] );
    // Set the current value to be floored
    $number = floor( $number );
    // If required create a plural
    if ( $number != 1 ) $periods[$val] .= "s";
    // Return text
    $text = sprintf( "%d %s ", $number, $periods[$val] );
    // Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
    if ( ( $val >= 1 ) && ( ( $current_time - $new_time ) > 0 ) ) {
        $text .= getTimeOnline( $new_time );
    }
    return $text;
}

?>