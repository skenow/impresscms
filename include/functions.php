<?php
// $Id: functions.php,v 1.5 2007/05/09 14:14:28 catzwolf Exp $
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
// ################## Various functions from here ################

function themecenterposts($title, $content) {
      echo '<table cellpadding="4" cellspacing="1" width="98%" class="outer"><tr><td class="head">'.$title.'</td></tr><tr><td><br />'.$content.'<br /></td></tr></table>';
}


function zarilia_header( $closehead = true )
{
    global $zariliaConfig;

    if ( !headers_sent() )
    {
        header ( 'Content-Type:text/html; charset=' . _CHARSET );
        header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
        header( 'Cache-Control: no-store, no-cache, max-age=1, s-maxage=1, must-revalidate, post-check=0, pre-check=0' );
        header( "Pragma: no-cache" );
    }

    echo "<!DOCTYPE html PUBLIC '//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";
    echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . _LANGCODE . '" lang="' . _LANGCODE . '">
	<head>
	<meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '" />
	<meta http-equiv="content-language" content="' . _LANGCODE . '" />
	<meta name="generator" content="Zarilia" />
	<title>' . $zariliaConfig['sitename'] . '</title>
	<script type="text/javascript" src="' . ZAR_URL . '/include/javascript/zarilia.js"></script>
    <link rel="stylesheet" type="text/css" media="all" href="' . ZAR_URL . '/themes/' . $zariliaConfig['theme_set'] . '/css/style.css" />';
    if ( $closehead )
    {
        echo '</head><body>';
    }
}

function zarilia_footer()
{
    echo '</body></html>';
    //  @ob _end_flush();
}

function zarilia_confirm( $hiddens, $op, $msg, $submit = '', $cancel = '', $noarray = false, $echo = true )
{
    $submit = ( $submit != '' ) ? trim( $submit ) : _SUBMIT;
    $cancel = ( $cancel != '' ) ? "onclick=\"location='" . htmlspecialchars( trim( $cancel ), ENT_QUOTES ) . "'\"" : 'onclick="history.go(-1);return true;"';
    $ret = '
	<form method="post" action="' . $op . '">
	<div class="confirmMsg">' . $msg . '';
	if (isset($hiddens['op']) && is_array($hiddens['op']))  {
		$op2 = unserialize(serialize($hiddens['op']));
		unset($hiddens['op']);
	}
    foreach ( $hiddens as $name => $value )
    {
        if ( is_array( $value ) && $noarray == true )
        {
            foreach ( $value as $caption => $newvalue )
            {
                $ret .= '<input type="radio" name="' . $name . '" value="' . htmlspecialchars( $newvalue ) . '" /> ' . $caption;
                $ret .= '<br />';
            }
        }
        else
        {
            if ( is_array( $value ) )
            {
                foreach ( $value as $new_value )
                {
                    $ret .= '<input type="hidden" name="' . $name . '[]" value="' . $new_value . '" />';
                }
            }
            else
            {
                $ret .= '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars( $value, ENT_QUOTES ) . '" />';
            }
        }
    }
    $ret .= "</div>";
    $ret .= "<div class='confirmButtons'>
			 <input type='button' class='formbutton' name='confirm_back' $cancel value='"._CANCEL."' />
			 <input type='submit' class='formbutton' name='confirm_submit' value='$submit'".(isset($op2)?' style="display:none;"':'')." />";
	if (isset($op2)) {
			 $name = 'zcOp'.time().'_id';
			 $ret .= '<input type="hidden" name="op" id="'.$name.'" value="" />';
			 foreach ($op2 as $caption => $value) {
				 $ret .= '<input type="submit" onclick="document.getElementById(\''.$name.'\').value=\''.htmlentities($value).'\'; this.form.submit();" value="'.$caption.'" /> ';
			 }
	}
    $ret .= "</div></form>";
    if ( $echo )
    {
        echo $ret;
    }
    else
    {
        return $ret;
    }
}
// calling ZariliaLocal::{$func}()
function &zarilia_local( $func )
{
    $par_string = "";
    // get parameters
    if ( func_num_args() > 1 )
    {
        $params = array_slice( func_get_args(), 1 );
        $count = count( $params );
        $par_string .= '$params[0]';
        for( $i = 1;$i < $count;$i++ )
        {
            $par_string .= ',$params[' . $i . ']';
        }
    }
    // local method defined
    if ( is_callable( array( "ZariliaLocal", $func ) ) )
    {
        $code = "return ZariliaLocal::{$func}(";
        if ( !empty( $par_string ) ) $code .= $par_string;
        $code .= ');';
        return eval( $code );
    }
    // php function defined
    if ( function_exists( $func ) )
    {
        $code = "return {$func}(";
        if ( !empty( $par_string ) ) $code .= $par_string;
        $code .= ');';
        return eval( $code );
    }
    // nothing
    return null;
}

function zarilia_refcheck( $docheck = 1 )
{
    if ( $docheck == 0 )
    {
        return true;
    }
    $ref = zarilia_getenv( 'HTTP_REFERER' );
    if ( $ref == '' )
    {
        return false;
    }
    $pref = parse_url( $ref );
    if ( $pref['host'] != $_SERVER['HTTP_HOST'] )
    {
        return false;
    }
    return true;
}

function zarilia_getUserTimestamp( $time, $timeoffset = 0 )
{
    global $zariliaConfig, $zariliaUser;
    if ( $timeoffset == 0 )
    {
        $timeoffset = ( $zariliaUser ) ? $zariliaUser->getVar( "timezone_offset" ) : $zariliaConfig['default_TZ'];
    }
    $usertimestamp = intval( $time ) + ( intval( $timeoffset ) - $zariliaConfig['server_TZ'] ) * 3600;
    return $usertimestamp;
}

/**
 * Function to display formatted times in user timezone
 */
function formatTimestamp( $time, $format = "l", $timeoffset = "" )
{
    global $zariliaConfig, $zariliaUser;

    $usertimestamp = zarilia_getUserTimestamp( $time, $timeoffset );
    switch ( strtolower( $format ) )
    {
        case 's':
            $datestring = _SHORTDATESTRING;
            break;
        case 'm':
            $datestring = _MEDIUMDATESTRING;
            break;
        case 'mysql':
            $datestring = "Y-m-d H:i:s";
            break;
        case 'rss':
            $datestring = "D, j M Y H:i:s T";
            break;
        case 'l':
            $datestring = _DATESTRING;
            break;
        default:
            $datestring = "D M-d-Y H:i:s";
            break;
    }
    return ucfirst( date( $datestring, abs($usertimestamp) ) );
}

/**
 * Function to calculate server timestamp from user entered time (timestamp)
 */
function userTimeToServerTime( $timestamp, $userTZ = null )
{
    global $zariliaConfig;
    if ( !isset( $userTZ ) )
    {
        $userTZ = $zariliaConfig['default_TZ'];
    }
    $timestamp = $timestamp - ( ( $userTZ - $zariliaConfig['server_TZ'] ) * 3600 );
    return $timestamp;
}

/**
 * Function to calculate password
 */
function zarilia_makepass()
{
    $makepass = '';
    $syllables = array( "er", "in", "tia", "wol", "fe", "pre", "vet", "jo", "nes", "al", "len", "son", "cha", "ir", "ler", "bo", "ok", "tio", "nar", "sim", "ple", "bla", "ten", "toe", "cho", "co", "lat", "spe", "ak", "er", "po", "co", "lor", "pen", "cil", "li", "ght", "wh", "at", "the", "he", "ck", "is", "mam", "bo", "no", "fi", "ve", "any", "way", "pol", "iti", "cs", "ra", "dio", "sou", "rce", "sea", "rch", "pa", "per", "com", "bo", "sp", "eak", "st", "fi", "rst", "gr", "oup", "boy", "ea", "gle", "tr", "ail", "bi", "ble", "brb", "pri", "dee", "kay", "en", "be", "se" );
    srand( ( double )microtime() * 1000000 );
    for ( $count = 1; $count <= 4; $count++ )
    {
        if ( rand() % 10 == 1 )
        {
            $makepass .= sprintf( "%0.0f", ( rand() % 50 ) + 1 );
        }
        else
        {
            $makepass .= sprintf( "%s", $syllables[rand() % 62] );
        }
    }
    return $makepass;
}

function checkEmail( $email, $antispam = false )
{
    if ( !$email || strrpos( $email, ' ' ) > 0 || !preg_match( "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $email ) )
    {
        return false;
    }

    if ( $antispam )
    {
        $email = str_replace( "@", " at ", $email );
        $email = str_replace( ".", " dot ", $email );
        return $email;
    }
    else
    {
        return true;
    }
}

function formatURL( $url )
{
    $url = trim( $url );
    if ( false == checkURL( $url ) )
    {
        $url = 'http://' . $url;
    }
    return $url;
}

function checkURL( $url )
{
    $url = trim( $url );
    if ( ( !preg_match( "/^http[s]*:\/\//i", $url ) ) && ( !preg_match( "/^ftp*:\/\//i", $url ) ) && ( !preg_match( "/^ed2k*:\/\//i", $url ) ) )
    {
        return false;
    }
    else
    {
        return true;
    }
}

/**
 * Function to redirect a user to certain pages
 */
function redirect_header( $url, $time = 1, $message = '', $addredirect = false )
{
    global $zariliaConfig, $zariliaRequestUri, $zariliaUserIsAdmin, $zariliaOption;

    if ( preg_match( "/[\\0-\\31]|about:|script:/i", $url ) ) {
        if ( !preg_match( '/^\b(java)?script:([\s]*)history\.go\(-[0-9]*\)([\s]*[;]*[\s]*)$/si', $url ) )
        {
            $url = ZAR_URL;
        }
    }
    if ( $addredirect && strstr( $url, 'index.php?page_type=user' ) )
    {
        if ( !strstr( $url, '?' ) )
        {
            $url .= '?zarilia_redirect=' . urlencode( $zariliaRequestUri );
        }
        else
        {
            $url .= '&amp;zarilia_redirect=' . urlencode( $zariliaRequestUri );
        }
    }
    if ( defined( 'SID' ) && ( ! isset( $_COOKIE[session_name()] ) || ( $zariliaConfig['use_mysession'] && $zariliaConfig['session_name'] != '' && !isset( $_COOKIE[$zariliaConfig['session_name']] ) ) ) )
    {
        if ( !strstr( $url, '?' ) )
        {
            $url .= '?' . SID;
        }
        else
        {
            $url .= '&amp;' . SID;
        }
    }
    $url = preg_replace( "/&amp;/i", '&', $url );
    if ( $zariliaConfig['quickredirect'] == 1 OR $time == 0 ) {
		if (isset($zariliaOption['isAjax'])) {
			$objResponse = new xajaxResponse();
			$objResponse->redirect($url);
			return $objResponse;
		}
        header( "Location: $url" );
        exit();
    }

	if (isset($zariliaOption['isAjax'])) {
		$objResponse = new xajaxResponse();
/*		$crlf  = "\r\n";
		$code  = 'window.zarilia_function = function() {'.$crlf;
/*		$code .= '	var newDoc=document.open("text/html","replace");'.$crlf;
		$exp = explode(">", ob_get_contents());
		foreach ($exp as $k) {
			$code .= '	newDoc.write(unescape("'.rawurlencode($k).')"+">");'.$crlf;
		}
		$code .= '	newDoc.close();'.$crlf;*/
/*		$code .= 'var e = document.createElement("div");
								e.style.position = "absolute";
								e.style.left="0px";
								e.style.top="0px";
								e.innerHTML="'.addslashes(ob_get_contents()).'";
								document.body.appendChild(e);'.$crlf;
		$code .= '}'.$crlf;
		$code .= 'window.zarilia_function();'.$crlf;
		$code .= 'window.zarilia_function = null;'.$crlf;
		ob_clean();*/
//		$objResponse->script($code);
//		$filename = '/zr-r'.time().'.html';
//		file_put_contents(ZAR_CACHE_PATH.$filename, ob_get_clean());
//		$objResponse->redirect(ZAR_CACHE_URL.$filename);
		$message = trim( $message ) != '' ? $message : _TAKINGBACK;
		$objResponse->alert($message);
		$objResponse->redirect($url);
		return $objResponse;
	} else {
	    require_once ZAR_ROOT_PATH . '/class/template.php';
		$zariliaTpl = new ZariliaTpl();
	    $zariliaTpl->addCss( ZAR_THEME_URL . '/' . $zariliaConfig['theme_set'] . '/css/style.css' );

	    $zariliaTpl->assign( 'url', $url );
	    $message = trim( $message ) != '' ? $message : _TAKINGBACK;
	    $zariliaTpl->assign( 'is_redirect', 1 );
	    $zariliaTpl->assign( 'message', $message );
		$zariliaTpl->assign( 'time', $time );
	    $zariliaTpl->assign( 'lang_ifnotreload', sprintf( _IFNOTRELOAD, $url ) );
//		if (isset($zariliaOption['isAjax'])) ob_start();
	    $zariliaTpl->display( ZAR_THEME_PATH . '/' . $zariliaConfig['theme_set'] . '/addons/system/system_redirect.html' );
	}
    exit();
}

function zarilia_getenv( $key )
{
    $ret = ( isset( $_SERVER[$key] ) ) ? $_SERVER[$key] : ( isset( $_ENV[$key] )? $_ENV[$key]: "" );
    switch ( $key )
    {
        case 'PHP_SELF':
        case 'PATH_INFO':
        case 'PATH_TRANSLATED':
            $ret = htmlspecialchars( $ret, ENT_QUOTES );
            break;
    }
    return $ret;
}

function &getMailer()
{
    global $zariliaConfig;
    $ret = false;
    include_once ZAR_ROOT_PATH . "/class/class.mailer.php";
    if ( file_exists( ZAR_ROOT_PATH . "/language/" . $zariliaConfig['language'] . "/zariliamailerlocal.php" ) )
    {
        include_once ZAR_ROOT_PATH . "/language/" . $zariliaConfig['language'] . "/zariliamailerlocal.php";
        if ( class_exists( "ZariliaMailerLocal" ) )
        {
            $ret = &new ZariliaMailerLocal();
        }
    }
    if ( !$ret )
    {
        $ret = &new ZariliaMailer();
    }
    return $ret;
}

function &zarilia_gethandler( $name, $optional = false )
{
    static $handlers;
    $name = strtolower( trim( $name ) );
    if ( !isset( $handlers[$name] ) )
    {
        if ( file_exists( $hnd_file = ZAR_ROOT_PATH . '/kernel/' . $name . '.php' ) )
        {
            require_once $hnd_file;
        }
        $class = 'Zarilia' . ucfirst( $name ) . 'Handler';
        if ( class_exists( $class ) )
        {
            $handlers[$name] = new $class( $GLOBALS['zariliaDB'] );
        }
    }
    if ( !isset( $handlers[$name] ) && !$optional )
    {
        trigger_error( 'Class <b>' . $class . '</b> does not exist<br />Handler Name: ' . $name, E_USER_ERROR );
    }
    $val = isset( $handlers[$name] ) ? $handlers[$name] : false;
    return $val;
}

function &zarilia_getaddonhandler( $name = null, $addon_dir = null, $optional = false )
{
    static $handlers;
    // if $addon_dir is not specified
    if ( !isset( $addon_dir ) )
    {
        // if a addon is loaded
        if ( isset( $GLOBALS['zariliaAddon'] ) && is_object( $GLOBALS['zariliaAddon'] ) )
        {
            $addon_dir = $GLOBALS['zariliaAddon']->getVar( 'dirname' );
        }
        else
        {
            trigger_error( 'No Addons is loaded', E_USER_ERROR );
        }
    }
    else
    {
        $addon_dir = trim( $addon_dir );
    }
    $name = ( !isset( $name ) ) ? $addon_dir : trim( $name );
    if ( !isset( $handlers[$addon_dir][$name] ) )
    {
        if ( file_exists( $hnd_file = ZAR_ROOT_PATH . "/addons/{$addon_dir}/class/{$name}.php" ) )
        {
            include_once $hnd_file;
        }
        $class = ucfirst( strtolower( $addon_dir ) ) . ucfirst( $name ) . 'Handler';
        if ( class_exists( $class ) )
        {
            $handlers[$addon_dir][$name] = new $class( $GLOBALS['zariliaDB'] );
        }
        else
        {
            $class = ucfirst( 'zarilia' ) . ucfirst( $name ) . 'Handler';
            $handlers[$addon_dir][$name] = new $class( $GLOBALS['zariliaDB'] );
        }
    }
    if ( !isset( $handlers[$addon_dir][$name] ) && !$optional )
    {
        trigger_error( 'Handler does not exist<br />Addons: ' . $addon_dir . '<br />Name: ' . $name, E_USER_ERROR );
    }
    $ret = isset( $handlers[$addon_dir][$name] ) ? $handlers[$addon_dir][$name] : false;
    return $ret;
}

/**
 * Returns the portion of string specified by the start and length parameters. If $trimmarker is supplied, it is appended to the return string. This function works fine with multi-byte characters if mb_* functions exist on the server.
 *
 * @param string $str
 * @param int $start
 * @param int $length
 * @param string $trimmarker
 * @return string
 */
function zarilia_substr( $str, $start, $length, $trimmarker = '...' )
{
    if ( is_callable( array( "ZariliaLocal", "substr" ) ) )
    {
        return ZariliaLocal::substr( $str, $start, $length );
    }
    if ( !ZAR_USE_MULTIBYTES )
    {
        return ( strlen( $str ) - $start <= $length ) ? substr( $str, $start, $length ) : substr( $str, $start, $length - strlen( $trimmarker ) ) . $trimmarker;
    }
    if ( function_exists( 'mb_internal_encoding' ) && @mb_internal_encoding( _CHARSET ) )
    {
        $str2 = mb_strcut( $str , $start , $length - strlen( $trimmarker ) );
        return $str2 . ( mb_strlen( $str ) != mb_strlen( $str2 ) ? $trimmarker : '' );
    }
}
// RMV-NOTIFY
// ################ Notification Helper Functions ##################
// We want to be able to delete by addon, by user, or by item.
// How do we specify this??
function zarilia_notification_deletebyaddon ( $addon_id )
{
    $notification_handler = &zarilia_gethandler( 'notification' );
    return $notification_handler->unsubscribeByAddon ( $addon_id );
}

function zarilia_notification_deletebyuser ( $user_id )
{
    $notification_handler = &zarilia_gethandler( 'notification' );
    return $notification_handler->unsubscribeByUser ( $user_id );
}

function zarilia_notification_deletebyitem ( $addon_id, $category, $item_id )
{
    $notification_handler = &zarilia_gethandler( 'notification' );
    return $notification_handler->unsubscribeByItem ( $addon_id, $category, $item_id );
}
// ################### Comment helper functions ####################
function zarilia_comment_count( $addon_id, $item_id = null )
{
    $comment_handler = &zarilia_gethandler( 'comment' );
    $criteria = new CriteriaCompo( new Criteria( 'com_modid', intval( $addon_id ) ) );
    if ( isset( $item_id ) )
    {
        $criteria->add( new Criteria( 'com_itemid', intval( $item_id ) ) );
    }
    return $comment_handler->getCount( $criteria );
}

function zarilia_comment_delete( $addon_id, $item_id )
{
    if ( intval( $addon_id ) > 0 && intval( $item_id ) > 0 )
    {
        $comment_handler = &zarilia_gethandler( 'comment' );
        $comments = &$comment_handler->getByItemId( $addon_id, $item_id );
        if ( is_array( $comments ) )
        {
            $count = count( $comments );
            $deleted_num = array();
            for ( $i = 0;
                $i < $count;
                $i++ )
            {
                if ( false != $comment_handler->delete( $comments[$i] ) )
                {
                    // store poster ID and deleted post number into array for later use
                    $poster_id = $comments[$i]->getVar( 'com_uid' );
                    if ( $poster_id != 0 )
                    {
                        $deleted_num[$poster_id] = !isset( $deleted_num[$poster_id] ) ? 1 : ( $deleted_num[$poster_id] + 1 );
                    }
                }
            }
            $member_handler = &zarilia_gethandler( 'member' );
            foreach ( $deleted_num as $user_id => $post_num )
            {
                // update user posts
                $com_poster = $member_handler->getUser( $user_id );
                if ( is_object( $com_poster ) )
                {
                    $member_handler->updateUserByField( $com_poster, 'posts', $com_poster->getVar( 'posts' ) - $post_num );
                }
            }
            return true;
        }
    }
    return false;
}
// ################ Group Permission Helper Functions ##################
function zarilia_groupperm_deletebymoditem( $addon_id, $perm_name, $item_id = null )
{
    // do not allow system permissions to be deleted
    if ( intval( $addon_id ) <= 1 )
    {
        return false;
    }
    $gperm_handler = &zarilia_gethandler( 'groupperm' );
    return $gperm_handler->deleteByAddon( $addon_id, $perm_name, $item_id );
}
// */
function &zarilia_utf8_encode( &$text )
{
    if ( is_callable( array( "ZariliaLocal", "utf8_encode" ) ) )
    {
        return ZariliaLocal::utf8_encode( $text );
    }
    if ( ZAR_USE_MULTIBYTES == 1 )
    {
        if ( function_exists( 'mb_convert_encoding' ) )
        {
            return mb_convert_encoding( $text, 'UTF-8', 'auto' );
        }
        return $text;
    }
    return utf8_encode( $text );
}

function &zarilia_convert_encoding( &$text, $to = 'utf-8', $from = '' )
{
    if ( is_callable( array( "ZariliaLocal", "convert_encoding" ) ) )
    {
        return ZariliaLocal::convert_encoding( $text, $to, $from );
    }
    if ( strtolower( $to ) != "utf-8" )
        return $text;
    return zarilia_utf8_encode( $text );
}

function zarilia_getLinkedUnameFromId( $userid = 0, $usereal = 0, $is_linked = 1 )
{
    global $zariliaUser;
    $member_handler = &zarilia_gethandler( 'member' );
    $name = '';
    $userid = intval( $userid ) > 0 ? intval( $userid ) : (is_object($zariliaUser)?$zariliaUser->getVar( 'uid' ):0);
    $user = &$member_handler->getUser( $userid );
    if ( is_object( $user ) )
    {
        if ( intval( $usereal ) )
        {
            $name = $user->getVar( 'name' );
        }
        else
        {
            $name = $user->getVar( 'uname' );
        }
        if ( $is_linked )
        {
            $name = '<a href="' . ZAR_URL . '/index.php?page_type=userinfo&uid=' . $userid . '">' . $name . '</a>';
        }
    }
    else
    {
        $name = $GLOBALS['zariliaConfig']['anonymous'];
    }
    return $name;
}

function zarilia_trim( $text )
{
    if ( is_callable( array( "ZariliaLocal", "trim" ) ) )
    {
        return ZariliaLocal::trim( $text );
    }
    return trim( $text );
}

function zarilia_getSelection( $this_array = array(), $selected = 0, $value = '', $size = '', $emptyselect = false , $multipule = false, $noselecttext = "------------------", $extra = '', $vvalue = 0, $echo = true )
{
    if ( $multipule == true )
        $ret = "<select size='" . $size . "' name='" . $value . "[]' id='" . $value . "[]' multiple='multiple' $extra>";
    else
        $ret = "<select size='" . $size . "' name='" . $value . "' id='" . $value . "' $extra>";
    if ( $emptyselect )
        $ret .= "<option value='0'>$noselecttext</option>";
	if (is_array($this_array))
    foreach( $this_array as $key => $content )
    {
        $opt_selected = "";
        $newKey = ( intval( $vvalue ) == 1 ) ? $content : $key;
        if ( is_array( $selected ) && in_array( $newKey, $selected ) )
        {
            $opt_selected .= " selected='selected'";
        }
        else
        {
            if ( $key == $selected )
            {
                $opt_selected = "selected='selected'";
            }
        }
        $ret .= "<option value='" . $newKey . "' $opt_selected>" . htmlspecialchars( stripslashes( $content ), ENT_QUOTES ) . "</option>";
    }
    $ret .= "</select>";
    if ( $echo == true )
    {
        echo "<div>" . $ret . "</div><br />";
    }
    else
    {
        return $ret;
    }
}

function getip()
{
    if ( isset( $_SERVER ) )
    {
        if ( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) )
        {
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif ( isset( $_SERVER["HTTP_CLIENT_IP"] ) )
        {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else
        {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    }
    else
    {
        if ( getenv( 'HTTP_X_FORWARDED_FOR' ) )
        {
            $realip = getenv( 'HTTP_X_FORWARDED_FOR' );
        } elseif ( getenv( 'HTTP_CLIENT_IP' ) )
        {
            $realip = getenv( 'HTTP_CLIENT_IP' );
        }
        else
        {
            $realip = getenv( 'REMOTE_ADDR' );
        }
    }
    return $realip;
}

function zarilia_admin_mkdir( $target, $mode = 0777 )
{
    return is_dir( $target ) || ( zarilia_admin_mkdir( dirname( $target ), $mode ) && mkdir( $target, $mode ) );
    if ( is_dir( $target ) || empty( $target ) )
        return true; // best case check first
    if ( file_exists( $target ) && !is_dir( $target ) )
        return false;
    if ( zarilia_admin_mkdir( substr( $target, 0, strrpos( $target, '/' ) ), $mode ) )
    {
        if ( !file_exists( $target ) ) return mkdir( $target, $mode ); // crawl back up & create dir tree
    }
    return false;
}

function zarilia_pagnav( $tot_num = 0, $num_dis = 10, $start = 0, $from = 'start', $nav_type = 1, $nav_path = '', $returns = false )
{
    $output = '';
    $ret = '';
    $from_result = $start + 1;
    if ( $num_dis == 0 )
    {
        $num_dis = $tot_num;
    }

    if ( $start + $num_dis < $tot_num )
    {
        $to_result = $start + $num_dis;
    }
    else
    {
        $to_result = $tot_num;
    }
    if ( $tot_num > 0 )
    {
        $output .= "Displaying Results " . $from_result . " - " . $to_result . " of " . $tot_num . " entries";
    }
    else
    {
//        $output .= "No records found.";
    }

    $ret = "<div class='navresults'>$output</div><br />";
    if ( intval( $tot_num ) > intval( $num_dis ) )
    {
        include_once ZAR_ROOT_PATH . '/class/pagenav.php';
        $pagenav = new ZariliaPageNav( intval( $tot_num ), intval( $num_dis ), intval( $start ), $from, $nav_path );
        $page = ( $tot_num > $num_dis ) ? _PAGE : '';
        $ret .= "<div style='text-align: right; margin-top: 5px;'>$page";
        switch ( intval( $nav_type ) )
        {
            case 1:
            default:
                $_page_nav = $pagenav->renderNav();
                $ret .= $_page_nav;
                break;
            case 2:
                $_page_nav = $pagenav->renderImageNav();
                $ret .= $_page_nav;
                break;
            case 3:
                $ret .= "&nbsp;" . $pagenav->renderSelect() . "";
                break;
        } // switch
        $ret .= "</div><br />";
    }
    if ( $returns == false )
    {
        echo $ret;
    }
    else
    {
        return $ret;
    }
}

function zarilia_show_buttons( $butt_align = 'right', $butt_id = 'button', $class_id = 'formbutton' , $button_array = array() )
{
    if ( !is_array( $button_array ) )
    {
        return false;
    }
    $ret = "<div style='text-align: $butt_align; margin-bottom: 12px;'>\n";
    $ret .= "<form id='{$butt_id}' action='showbuttons'>\n";
    foreach ( $button_array as $k => $v )
    {
        $ret .= "<input type='button' style='cursor: hand;' class='{$class_id}'  name='" . trim( $v ) . "' onclick=\"location='" . htmlspecialchars( trim( $k ), ENT_QUOTES ) . "'\" value='" . trim( $v ) . "' />&nbsp;&nbsp;";
    }
    $ret .= "</form>\n";
    $ret .= "</div>\n";
    echo $ret;
}

function zarilia_img_show( $_name = '', $_title = '', $_align = 'absmiddle', $ext = 'png', $path = 'images/small' )
{
    if ( $_name )
    {
        return "<img src='" . ZAR_URL . "/$path/$_name.$ext' border='0' title='$_title' alt='$_title' align='$_align' />";
    }
    else
    {
        return '';
    }
}

function zarilia_cleanRequestVars( &$array, $key, $def = null, $type = 0, $length = 255, $gpc = false, $strip = false, $allowed_tags = array() )
{
    if ( !isset( $array[$key] ) || !array_key_exists( $key, $array ) )
    {
        return $def;
    }
    else
    {
        $value = $array[$key];
    }
    if ( $value == '' )
    {
        return '';
    }
    if ( $strip == true )
    {
        $value = strip_tags( $value, $allowed_tags );
    }
    switch ( intval( $type ) )
    {
        case XOBJ_DTYPE_TXTBOX:
            if ( strlen( $value ) > intval( $length ) )
            {
                return null;
            }
            return stripslashes( zarilia_trim( $value ) );
            break;
        case XOBJ_DTYPE_TXTAREA:
            return stripslashes( zarilia_trim( $value ) );
            break;
        case XOBJ_DTYPE_INT:
            return intval( $value );
            break;
        case XOBJ_DTYPE_URL:
            if ( $value != '' && !preg_match( "/^http[s]*:\/\//i", zarilia_trim( $value ) ) )
            {
                $value = 'http://' . $value;
            }
            return stripslashes( zarilia_trim( $value ) );
            break;
        case XOBJ_DTYPE_EMAIL:
            return stripslashes( zarilia_trim( $value ) );
            break;
        case XOBJ_DTYPE_ARRAY:
            return $value;
            return serialize( $value );
            break;
        case XOBJ_DTYPE_SOURCE:
            return stripslashes( $value );
            break;
        case XOBJ_DTYPE_STIME:
        case XOBJ_DTYPE_MTIME:
        case XOBJ_DTYPE_LTIME:
            return !is_string( $value ) ? intval( $value ) : strtotime( $value );
            break;
        case XOBJ_DTYPE_OTHER:
        default:
            return $value;
            break;
    } // switch
}

function zarilia_legend( $led_array )
{
    $legend = '';
    /**
     * show legend
     */
    if ( function_exists( 'zarilia_cp_header' ) )
    {
        if ( is_array( $led_array ) )
        {
            foreach( $led_array as $key )
            {
                $legend .= "<div style='padding: 3;'>" . zarilia_img_show( $key ) . " " . zarilia_constants( "_MA_AD_ICO_" . $key . "_LEG" ) . "</div>\n";
            }
            $_SESSION['administration']['blocks']['\$\$Legend'] = array( 'title' => _BOX_LEGEND_TITLE, 'items' => $legend, 'id' => count( $_SESSION['administration']['blocks'] ) + 1 );
        }
    }
    else
    {
        if ( is_array( $led_array ) )
        {
            $legend .= "<h5 style='margin-bottom: 2px;'>" . _LEGEND . "</h5></b>\n";
            foreach( $led_array as $key => $value )
            {
                $legend .= "<small><div style='text-indent: 5px; padding-bottom: 2px;'>" . trim( $key ) . " - " . trim( $value ) . "</div></small>\n";
            }
        }
        echo $legend;
    }
}

function zarilia_getFileExtension( $value = '' )
{
    $filename = explode( '.', basename( $value ) );
    $ret['basename'] = $filename['0'];
    $ret['ext'] = $filename['1'];
    return $ret;
}

function zarilia_constants( $_title, $prefix = '', $suffix = '' )
{
    $prefix = ( $prefix != "" || $_title != 'op' ) ? trim( $prefix ) : "";
    $suffix = trim( $suffix );
    return constant( strtoupper( "$prefix$_title$suffix" ) );
}

function print_r_html( $value = '', $debug = false, $extra = false )
{
    echo '<div>' . str_replace( array( "\n" , " " ), array( '<br>', '&nbsp;' ), print_r( $value, true ) ) . '</div>';
    if ( $extra != false )
    {
        foreach ( $_SERVER as $k => $v )
        {
            if ( $k != "HTTP_REFERER" )
            {
                echo "<div><b>Server:</b> $k value: $v</div>";
            }
            else
            {
                echo "<div><b>Server:</b> $k value: $v</div>";
                $v = strpos( $_SERVER[$k], ZAR_URL );
                echo "<div><b>Server:</b> $k value: $v</div>";
            }
        }
    }
}

function whois( $ipadress )
{
    return "<a href='http://www.whois.sc/$ipadress' target='blank'>" . $ipadress . "</a>";
}

function zariliaCheckBrowser( $get_isie = true )
{
    global $_SERVER;
    $comp = false;
    $isie = false;
    if ( eregi( "opera", $_SERVER['HTTP_USER_AGENT'] ) )
    {
        $comp = false;
        $isie = false;
    } elseif ( eregi( "msie", $_SERVER['HTTP_USER_AGENT'] ) )
    {
        $val = explode( " ", stristr( $_SERVER['HTTP_USER_AGENT'], "msie" ) );
        if ( ( float )
                str_replace( ";", "", $val[1] ) >= 5.5 )$comp = true;
        $isie = true;
    } elseif ( eregi( "mozilla", $_SERVER['HTTP_USER_AGENT'] ) )
    {
        $comp = true;
        $isie = false;
    } elseif ( eregi( "netscape", $_SERVER['HTTP_USER_AGENT'] ) )
    {
        $val = explode( "Netscape/", $_SERVER['HTTP_USER_AGENT'] );
        $version = str_replace( " (ax)", "", $val[1] );
        if ( $version >= 7.1 )
            $comp = true;
        $isie = false;
    }
    if ( $get_isie )
        return( $isie );
    else
        return $comp;
}

function zarilia_get_dir_status( $target = '' )
{
    if ( !is_dir( $target ) || !is_readable( $target ) )
    {
        if ( !@mkdir( $target, 0777 ) )
        {
            return '301'; // best case check first
        }
    }

    if ( file_exists( $target ) && !is_dir( $target ) )
    {
        return '301';
    }

    if ( !is_writable( $target ) )
    {
        if ( !@chmod( $target, 0777 ) )
        {
            return '301';
        }
    }

    if ( !is_readable( $target ) )
    {
        return '303';
    }
    return true;
}

function zarilia_destroy( $dir )
{
    $mydir = opendir( $dir );
    while ( false !== ( $file = readdir( $mydir ) ) )
    {
        if ( $file != "." && $file != ".." )
        {
            chmod( $dir . $file, 0777 );
            if ( is_dir( $dir . $file ) )
            {
                chdir( '.' );
                zarilia_destroy( $dir . $file . '/' );
                if ( !@rmdir( $dir . $file ) )
                {
                    return false;
                }
            }
            else
            {
                if ( !@unlink( $dir . $file ) )
                {
                    return false;
                }
            }
        }
    }
    closedir( $mydir );
    return true;
}

function tep_check_gzip()
{
    global $HTTP_ACCEPT_ENCODING;
    if ( headers_sent() || connection_aborted() )
    {
        return false;
    }
    if ( strpos( $HTTP_ACCEPT_ENCODING, 'x-gzip' ) !== false ) return 'x-gzip';
    if ( strpos( $HTTP_ACCEPT_ENCODING, 'gzip' ) !== false ) return 'gzip';
    return false;
}

function dieDebug( $sError )
{
    echo "<hr /><div>" . $sError . "<br /><table border='1'>";
    $sOut = "";
    $aCallstack = debug_backtrace();
    echo "<thead><tr><th>file</th><th>line</th><th>function</th>" . "</tr></thead>";
    foreach( $aCallstack as $aCall )
    {
        if ( !isset( $aCall['file'] ) ) $aCall['file'] = '[PHP Kernel]';
        if ( !isset( $aCall['line'] ) ) $aCall['line'] = '';
        echo "<tr><td>{$aCall['file']}</td><td>{$aCall['line']}</td>" . "<td>{$aCall['function']}</td></tr>";
    }
    echo "</table></div><hr /></p>";
    die();
}

function getthumbImage( $value = '', $title = '', $align = 'right', $img_width = '150', $img_height = '100', $img_quality = '100', $img_aspect = false, $dir = 'uploads' )
{
    if ( !$value )
    {
        return '';
    }
    $title = htmlspecialchars( $title, ENT_QUOTES );
    require_once ZAR_ROOT_PATH . '/class/class.thumbnail.php';
    $_thumb_image = new ZariliaThumbs( $value, $dir );
    if ( is_object( $_thumb_image ) )
    {
        $_thumb_image->setUseThumbs( 1 );
        $_thumb_image->setLibType( 1 );
        $_image = $_thumb_image->do_thumb( $img_width, $img_height, $img_quality, $img_aspect, true, true );
        if ( $_image != false )
        {
            $image = '<img align="' . $align . '" src="' . $_image['imgTitle'] . '" width="' . $_image['imgWidth'] . '" height="' . $_image['imgHeight'] . '" alt="' . $title . '" hspace="3px" vspace="0px" border="0" />';
        }
        else
        {
            $image = '';
        }
    }
    unset( $align );
    return $image;
}
?>