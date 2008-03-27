<?php
/**
 * $Id: regform_newuser.php,v 1.1 2007/04/21 09:45:41 catzwolf Exp $ Untitled 1.php v0.0 14/04/2007 05:47:43 John
 *
 * @Zarilia - 	PHP Content Management System
 * @copyright 2007 Zarilia
 * @Author : 	John (AKA Catzwolf)
 * @URL : 		http://zarilia.com
 * @Project :	Zarilia CMS
 */

require( ZAR_ROOT_PATH . '/class/captcha/php-captcha.inc.php' );

$user = array();
$user['uname'] = zarilia_cleanRequestVars( $_POST, 'uname', '' );
$user['email'] = zarilia_cleanRequestVars( $_POST, 'email', '' );
$user['login'] = zarilia_cleanRequestVars( $_POST, 'ulogin', '' );
$user['timezone_offset'] = zarilia_cleanRequestVars( $_POST, 'timezone_offset', '' );

$onErrorExecuteCode = "
 	global \$zariliaConfig;
    require_once ZAR_ROOT_PATH . '/language/' . \$zariliaConfig['language'] . '/error.php';
	\$zariliaOption['form.error'] = _ERR_PG_ERROR.': %s';
	\$this->_pointer = 'profile';
    include ZAR_ROOT_PATH . '/class/user/forms/regform_profile.php';
	return true;
	";

if ( isset( $_REQUEST['user_coppa_dob'] ) ) {
	$user['user_coppa_agree'] = zarilia_cleanRequestVars( $_POST, 'user_coppa_agree', null, XOBJ_DTYPE_INT );
	$user['user_coppa_dob'] = zarilia_cleanRequestVars( $_POST, 'user_coppa_dob', 1, XOBJ_DTYPE_INT );
}

$pass = zarilia_cleanRequestVars( $_REQUEST, 'pass', '', XOBJ_DTYPE_TXTBOX );
$pass2 = zarilia_cleanRequestVars( $_REQUEST, 'pass2', '', XOBJ_DTYPE_TXTBOX );
if ($pass != $pass2) {
	return eval(sprintf($onErrorExecuteCode,_US_PASSWORDWRONG));
}
$user['pass'] = $pass.'';
unset($pass, $pass2);

foreach ($user as $key => $value) {
   if ((trim($value) == '') || ($value === null)) {
		$error = strtoupper($key);
		$error = '_US_'.$error.'REQUIRED';
		$error = defined($error)?constant($error):$error;   
		return eval(sprintf($onErrorExecuteCode,$error));        
   }
}

if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $user['email'])) {
    return eval(sprintf($onErrorExecuteCode,_US_EMAILWRONG));	    
}

$member_handler = &zarilia_gethandler( 'member' );
$such_users = $member_handler->getUsers(new Criteria( 'uname', $user['uname'] ));
if (count($such_users) > 0) {
	return eval(sprintf($onErrorExecuteCode,_US_UNAMEEXISTS));	
}

$such_users = $member_handler->getUsers(new Criteria( 'login', $user['login'] ));
if (count($such_users) > 0) {
	return eval(sprintf($onErrorExecuteCode,_US_ULOGINEXISTS));	
}

$such_users = $member_handler->getUsers(new Criteria( 'email', $user['email'] ));
if (count($such_users) > 0) {
	return eval(sprintf($onErrorExecuteCode,_US_EMAILEXISTS));	
}
unset($such_users);

if ( !PhpCaptcha::Validate( $_POST['captacha'] ) ) {	
    return eval(sprintf($onErrorExecuteCode,_US_CAPTCHAWRONG));		
}

$user['name'] = zarilia_cleanRequestVars( $_POST, 'name', '', XOBJ_DTYPE_TXTBOX );
$user['user_viewemail'] = zarilia_cleanRequestVars( $_POST, 'user_mailok', 0, XOBJ_DTYPE_INT );
$user['url'] = zarilia_cleanRequestVars( $_POST, 'url','', XOBJ_DTYPE_TXTBOX );


$newuser = &$member_handler->createUser();
$newuser->setVars( $user );
$newuser->setVar( "pass", $GLOBALS['zariliaSecurity']->execEncryptionFunc('encrypt', $user['pass'] ) );
$newuser->setVar( 'uorder', $zariliaConfig['com_order'] );
$newuser->setVar( 'umode', $zariliaConfig['com_mode'] );
$newuser->setVar( 'ipaddress', $_SERVER['REMOTE_ADDR'] );
$actkey = substr( md5( uniqid( mt_rand(), 1 ) ), 0, 8 );
$newuser->setVar( 'actkey', $actkey );
$newuser->setVar( 'user_regdate', time() );

if ( $zariliaConfigUser['activation_type'] == 1 ) {
	$newuser->setVar( 'level', 1 );
}


 if ( !$member_handler->insertUser( $newuser ) )  {
    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $newuser->getErrors() );
	return false;
 }

$newid = $newuser->getVar( 'uid' );
if ( !$member_handler->addUserToGroup( ZAR_GROUP_USERS, $newid ) ) {
    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_CANTADDUSERTOGROUP );	
	return false;
}

switch ($zariliaConfigUser['activation_type']) {
	case 1:
	   redirect_header( 'index.php', 4, _US_ACTLOGIN );
	   return true;
	break;
	case 0:
		$zariliaMailer = &getMailer();
        $zariliaMailer->useMail();
        $zariliaMailer->setTemplate( 'register.tpl' );
        $zariliaMailer->assign( 'SITENAME', $zariliaConfig['sitename'] );
        $zariliaMailer->assign( 'ADMINMAIL', $zariliaConfig['adminmail'] );
        $zariliaMailer->assign( 'SITEURL', ZAR_URL . "/" );
        $zariliaMailer->setToUsers( $newuser );
        $zariliaMailer->setFromEmail( $zariliaConfig['adminmail'] );
        $zariliaMailer->setFromName( $zariliaConfig['sitename'] );
        $zariliaMailer->setSubject( sprintf( _US_USERKEYFOR, stripslashes( $user['uname'] ) ) );
        if ( !$zariliaMailer->send( true ) ) {
			$zariliaOption['form.error'] = sprintf( _US_YOURREGMAILNG, $user['uname'], $zariliaConfig['adminmail'] );
        } else {
			$zariliaOption['form.error'] = sprintf( _US_YOURREGISTERED, $user['uname'], $zariliaConfig['sitename'], $zariliaConfig['adminmail'] );
        }
	break;
	case 2:
		$zariliaMailer = &getMailer();
        $zariliaMailer->useMail();
        $zariliaMailer->setTemplate( 'adminactivate.tpl' );
        $zariliaMailer->assign( 'USERNAME', stripslashes( $user['uname'] ) );
        $zariliaMailer->assign( 'USEREMAIL', stripslashes( $user['email'] ) );
        $zariliaMailer->assign( 'USERACTLINK', ZAR_URL . '/user.php?op=actv&id=' . $newid . '&actkey=' . $actkey );
        $zariliaMailer->assign( 'SITENAME', $zariliaConfig['sitename'] );
        $zariliaMailer->assign( 'ADMINMAIL', $zariliaConfig['adminmail'] );
        $zariliaMailer->assign( 'SITEURL', ZAR_URL );
 
        $zariliaMailer->setToGroups( $member_handler->getGroup( $zariliaConfigUser['activation_group'] ) );
        $zariliaMailer->setFromEmail( $zariliaConfig['adminmail'] );
        $zariliaMailer->setFromName( $zariliaConfig['sitename'] );
        $zariliaMailer->setSubject( sprintf( _US_USERKEYFOR, stripslashes( $user['uname'] ) ) );
        if ( !$zariliaMailer->send() ) {
            $zariliaOption['form.error'] = sprintf( _US_YOURREGMAILNG, stripslashes( $user['uname'] ), $zariliaConfig['adminmail'] ) ;
        } else {
            $zariliaOption['form.error'] = sprintf( _US_YOURREGISTERED2, stripslashes( $user['uname'] ), $zariliaConfig['adminmail'] ) ;
        }	
	break;
}

if ( $zariliaConfigUser['new_user_notify'] == 1 && !empty( $zariliaConfigUser['new_user_notify_group'] ) ) {
	$zariliaMailer = &getMailer();
	$zariliaMailer->useMail();
	$member_handler = &zarilia_gethandler( 'member' );
	$zariliaMailer->setToGroups( $member_handler->getGroup( $zariliaConfigUser['new_user_notify_group'] ) );
	$zariliaMailer->setFromEmail( $zariliaConfig['adminmail'] );
	$zariliaMailer->setFromName( $zariliaConfig['sitename'] );
	$zariliaMailer->setSubject( sprintf( _US_NEWUSERREGAT, $zariliaConfig['sitename'] ) );
	$zariliaMailer->setBody( sprintf( _US_HASJUSTREG, stripslashes( $user['uname'] ) ) );
	$zariliaMailer->send();
}
if ( $zariliaConfigUser['pm_user'] == 1 && !empty( $zariliaConfigUser['pm_user'] ) ) {
	$zariliaMailer = &getMailer();
	$zariliaMailer->useMail();
	$member_handler = &zarilia_gethandler( 'member' );
	$zariliaMailer->setToGroups( $member_handler->getGroup( $zariliaConfigUser['new_user_notify_group'] ) );
	$zariliaMailer->setFromEmail( $zariliaConfig['adminmail'] );
	$zariliaMailer->setFromName( $zariliaConfig['sitename'] );
	$zariliaMailer->setSubject( sprintf( _US_NEWUSERREGAT, $zariliaConfig['sitename'] ) );
	$zariliaMailer->setBody( sprintf( _US_HASJUSTREG, stripslashes( $user['uname'] ) ) );
	$zariliaMailer->send();
}
 
$this->_pointer = 'saveuser';

if ($zariliaOption['form.error']) {
  redirect_header( 'index.php', 20, $zariliaOption['form.error'] );
}


//$content['form'] = $register_form;
$content['file'] = 'saveuser';
$this->addOptions(
    array( 'title' => _US_REGSAVEUSER,
        'subtitle' => '',
        'content' => $zariliaOption['form.error'],
        )
    );

//echo 'zzzzzzzzzzzzzzzzzzzzzz';
 
/*if ( !array( $array_error ) || count( $array_error ) == 0 ) {
	show_heading();
    echo "<h4>" . _US_REG_COMPLETE . "</h4>";
             
             $newuser = &$member_handler->createUser();
             if ( isset( $user_viewemail ) ) {
                 $newuser->setVar( 'user_viewemail', $user_viewemail );
             }
             $newuser->setVar( 'name', isset( $name ) ? $name : '' );
             $newuser->setVar( 'login', $login );
               $newuser->setVar( 'uname', $user['uname'] );
               $newuser->setVar( 'email', $user['email'] );
               $newuser->setVar( 'user_coppa_agree', $user_coppa_agree );
               $newuser->setVar( 'user_coppa_dob', $user_coppa_dob );
               if ( isset( $url ) && $url != '' ) {
                   $newuser->setVar( 'url', formatURL( $url ) );
               }
               $newuser->setVar( 'user_avatar', '' );
               $actkey = substr( md5( uniqid( mt_rand(), 1 ) ), 0, 8 );
               $newuser->setVar( 'actkey', $actkey );
               $newuser->setVar( 'pass', md5( $pass ) );
               $newuser->setVar( 'timezone_offset', $timezone_offset );
               $newuser->setVar( 'user_regdate', time() );
               $newuser->setVar( 'uorder', $zariliaConfig['com_order'] );
               $newuser->setVar( 'umode', $zariliaConfig['com_mode'] );
               $newuser->setVar( 'user_mailok', $user_mailok );
   
               $newuser->setVar( 'ipaddress', $ip_address );
               if ( $zariliaConfigUser['activation_type'] == 1 ) {
                   $newuser->setVar( 'level', 1 );
               }
               $rez = $member_handler->insertUser( $newuser ) ;
               if ( !$rez ) {
                   echo _US_REGISTERNG;
                   include 'footer.php';
                   exit();
               }
   
               $newid = $newuser->getVar( 'uid' );
               if ( !$member_handler->addUserToGroup( XOOPS_GROUP_USERS, $newid ) ) {
                   echo _US_REGISTERNG;
                   include 'footer.php';
                   exit();
               }
               if ( $zariliaConfigUser['activation_type'] == 1 ) {
                  
                   exit();
               }
   
               if ( $zariliaConfigUser['activation_type'] == 0 ) {
     
               } elseif ( $zariliaConfigUser['activation_type'] == 2 ) {
               
               }

*/
?>