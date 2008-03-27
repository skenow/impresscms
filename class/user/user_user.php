<?php
// $Id: user_user.php,v 1.4 2007/04/22 07:21:37 catzwolf Exp $
// auth_zarilia.php - ZARILIA authentification class
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
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * ZariliaUserUser
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: user_user.php,v 1.4 2007/04/22 07:21:37 catzwolf Exp $
 * @access public
 */
class ZariliaUserUser extends ZariliaAuth {
    /**
     * Authentication Service constructor
     */
    function ZariliaUserUser ()
    {

    }

    function logout()
    {
        global $zariliaUser;
        if ( !is_object( $zariliaUser ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_ERROR_NOTLOGIN );
            return false;
        }
        require_once ZAR_ROOT_PATH . '/class/auth/authfactory.php';
        $zariliaAuth = &ZariliaAuthFactory::getAuthConnection();
        $zariliaAuth->dologout();
        redirect_header( ZAR_URL, 1, _US_THANKYOUFORVISIT );
    }

    /**
     * ZariliaUserUser::dologinform()
     *
     * @return
     */
    function loginform()
    {
        global $zariliaUser, $config_handler, $zariliaConfig;
        if ( is_object( $zariliaUser ) ) {
            header( 'Location: ' . ZAR_URL . '/index.php?page_type=userinfo&amp;uid=' . $zariliaUser->getVar( 'uid' ) );
            exit();
        }
        $zarilia_redirect = zarilia_cleanRequestVars( $_REQUEST, 'zarilia_redirect', '', XOBJ_DTYPE_TXTBOX );

        $option['template_main'] = 'system_userform.html';
        if ( isset( $_COOKIE[$zariliaConfig['usercookie']] ) ) {
            $option['usercookie'] = $_COOKIE[$zariliaConfig['usercookie']];
        }
        $option['redirect_page'] = htmlspecialchars( trim( $zarilia_redirect ), ENT_QUOTES );

        $zariliaConfigUser = &$config_handler->getConfigsByCat( ZAR_CONF_USER );
        $allow_register = ( isset( $zariliaConfigUser['allow_register'] ) && $zariliaConfigUser['allow_register'] ) ? true : false;
        $option['allow_register'] = $allow_register;
        return $option;
    }

    function dologin()
    {
        global $zariliaUser;

        if ( is_object( $zariliaUser ) && ($zariliaUser->getVar('uid') !== null) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_ERROR_ALREADYLOGIN );
            return false;
        }

        if ( isset( $_GET['login'] ) || isset( $_GET['pass'] ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_ERROR_CANNOTLOGIN );
            return false;
        }

        $login = zarilia_cleanRequestVars( $_REQUEST, 'login', '', XOBJ_DTYPE_TXTBOX );
        $pass = zarilia_cleanRequestVars( $_REQUEST, 'pass', '', XOBJ_DTYPE_TXTBOX );
        $verification = zarilia_cleanRequestVars( $_REQUEST, 'verification', '', XOBJ_DTYPE_TXTBOX );
        $verification_ver = zarilia_cleanRequestVars( $_REQUEST, 'verification_ver', '', XOBJ_DTYPE_TXTBOX );
        $rememberme = zarilia_cleanRequestVars( $_REQUEST, 'rememberme', '', XOBJ_DTYPE_TXTBOX );
        $logonanon = zarilia_cleanRequestVars( $_REQUEST, 'logonanon', 0, XOBJ_DTYPE_TXTBOX );

        /**
         */
        require_once ZAR_ROOT_PATH . '/class/auth/authfactory.php';
        $zariliaAuth = &ZariliaAuthFactory::getAuthConnection();
        $zariliaAuth->setLogin( $login );
        $zariliaAuth->setPass( $pass );
        $zariliaAuth->setRememberMe( $rememberme );
        $zariliaAuth->setLoginAnon( $logonanon );
        if ( !empty( $verification_ver ) ) {
            $zariliaAuth->setImageVer( $verification_ver );
            $zariliaAuth->setVerification( strtoupper( $verification ) );
        }
        /* login User */
        $user = $zariliaAuth->doLogin();
        if ( is_object( $user ) ) {
            redirect_header( $this->doRedirect(), 2, sprintf( _US_LOGGINGU, $user->getVar( 'uname' ) ) );
        } else {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_INCORRECTLOGIN );
            return false;
        }
    }

    function actv()
    {
        $id = zarilia_cleanRequestVars( $_REQUEST, 'id', 0, XOBJ_DTYPE_INT );
        $actkey = zarilia_cleanRequestVars( $_REQUEST, 'actkey', '', XOBJ_DTYPE_TXTBOX );

        $member_handler = &zarilia_gethandler( 'member' );
        $thisuser = &$member_handler->getUser( $id );

        if ( !is_object( $thisuser ) || empty( $id ) || empty( $actkey ) ) {
            header( 'Location: ' . ZAR_URL );
        }

        if ( $thisuser->getVar( 'actkey' ) != $actkey ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_ACTKEYNOT );
        } else {
            if ( $thisuser->getVar( 'level' ) > 0 ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_NOTICE, _US_ACONTACT );
            } else {
                if ( false != $member_handler->activateUser( $thisuser ) ) {
                    $config_handler = &zarilia_gethandler( 'config' );
                    $zariliaConfigUser = &$config_handler->getConfigsByCat( ZAR_CONF_USER );
                    if ( $zariliaConfigUser['activation_type'] == 2 ) {
                        $zariliaMailer = &getMailer();
                        $zariliaMailer->useMail();
                        $zariliaMailer->setTemplate( 'activated.tpl' );
                        $zariliaMailer->assign( 'SITENAME', $zariliaConfig['sitename'] );
                        $zariliaMailer->assign( 'ADMINMAIL', $zariliaConfig['adminmail'] );
                        $zariliaMailer->assign( 'SITEURL', ZAR_URL . "/" );
                        $zariliaMailer->setToUsers( $thisuser );
                        $zariliaMailer->setFromEmail( $zariliaConfig['adminmail'] );
                        $zariliaMailer->setFromName( $zariliaConfig['sitename'] );
                        $zariliaMailer->setSubject( sprintf( _US_YOURACCOUNT, $zariliaConfig['sitename'] ) );
                        if ( !$zariliaMailer->send() ) {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, sprintf( _US_ACTVMAILOK, $thisuser->getVar( 'uname' ) ) );
                            return false;
                        }
                    } else {
                        redirect_header( 'index.php', 1, _US_ACTLOGIN );
                    }
                } else {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_ACTKEYFAILED );
                }
            }
        }
    }

    function delete()
    {
        global $config_handler, $zariliaUser;

        $zariliaConfigUser = &$config_handler->getConfigsByCat( ZAR_CONF_USER );
        if ( !$zariliaUser || $zariliaConfigUser['self_delete'] != 1 ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_NOPERMISS );
            return false;
        } else {
            $groups = $zariliaUser->getGroups();
            if ( in_array( ZAR_GROUP_ADMIN, $groups ) ) {
                // users in the webmasters group may not be deleted
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_ADMINNO );
                return false;
            }
            $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
            if ( $ok != 1 ) {
                return zarilia_confirm(
                    array( 'page_type' => 'user', 'act' => 'delete', 'ok' => 1 ),
                    'index.php', _US_SURETODEL . '<br/>' . _US_REMOVEINFO, '', '', false, false
                    );
            } else {
                $del_uid = $zariliaUser->getVar( "uid" );
                $member_handler = &zarilia_gethandler( 'member' );
                if ( false != $member_handler->deleteUser( $zariliaUser ) ) {
                    $online_handler = &zarilia_gethandler( 'online' );
                    $online_handler->destroy( $del_uid );
                    zarilia_notification_deletebyuser( $del_uid );
                    redirect_header( ZAR_URL, 1, _US_BEENDELED );
                }
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_NOPERMISS );
                return false;
            }
        }
    }

    function lostpass()
    {
        $option['template_main'] = 'system_lostpass.html';
        return $option;
    }

    function lostconfirm()
    {
        global $zariliaConfig;

        $email = zarilia_cleanRequestVars( $_REQUEST, 'email', '', XOBJ_DTYPE_TXTBOX );

		if ( $email=='' ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_SORRYNOTFOUND );
            return false;
        }


        $member_handler = &zarilia_gethandler( 'member' );
        $getuser = &$member_handler->getUsers( new Criteria( 'email', addslashes($email)  ) );

        if ( !is_array( $getuser ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_SORRYNOTFOUND );
            return false;
        }


        $areyou = substr( $getuser[0]->getVar( "pass" ), 0, 5 );
        $zariliaMailer = &getMailer();
        $zariliaMailer->useMail();
        $zariliaMailer->setTemplate( "lostpass1.tpl" );
        $zariliaMailer->assign( "SITENAME", $zariliaConfig['sitename'] );
        $zariliaMailer->assign( "ADMINMAIL", $zariliaConfig['adminmail'] );
        $zariliaMailer->assign( "SITEURL", ZAR_URL );
        $zariliaMailer->assign( "IP", $_SERVER['REMOTE_ADDR'] );
        $zariliaMailer->assign( "NEWPWD_LINK", ZAR_URL . "/index.php?page_type=user&act=lostupdate&email=" . $email . "&code=" . $areyou );
        $zariliaMailer->setToUsers( $getuser[0] );
        $zariliaMailer->setFromEmail( $zariliaConfig['adminmail'] );
        $zariliaMailer->setFromName( $zariliaConfig['sitename'] );
        $zariliaMailer->setSubject( sprintf( _US_NEWPWDREQ, $zariliaConfig['sitename'] ) );
        if ( !$zariliaMailer->send() ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_MAILERROR );
            return false;
        } else {
            redirect_header( 'index.php', 1, sprintf( _US_CONFMAIL, $getuser[0]->getVar( "uname" ) ) );
        }
    }

    function lostupdate()
    {
        global $zariliaConfig, $zariliaDB;

        $email = zarilia_cleanRequestVars( $_REQUEST, 'email', '', XOBJ_DTYPE_TXTBOX );
        $code = zarilia_cleanRequestVars( $_REQUEST, 'code', '', XOBJ_DTYPE_TXTBOX );

        if ( empty( $email ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_SORRYNOTFOUND );
            return false;
        }

        $member_handler = &zarilia_gethandler( 'member' );
        $getuser = &$member_handler->getUsers( new Criteria( 'email', addslashes( $email ) ) );				
        if ( !is_array( $getuser ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_SORRYNOTFOUND );
            return false;
        }		

        $areyou = substr( $getuser[0]->getVar( "pass" ), 0, 5 );
        if ( $code != '' && $areyou == $code ) {
            $newpass = zarilia_makepass();
            $zariliaMailer = &getMailer();
            $zariliaMailer->useMail();
            $zariliaMailer->setTemplate( "lostpass2.tpl" );
            $zariliaMailer->assign( "SITENAME", $zariliaConfig['sitename'] );
            $zariliaMailer->assign( "ADMINMAIL", $zariliaConfig['adminmail'] );
            $zariliaMailer->assign( "SITEURL", ZAR_URL );
            $zariliaMailer->assign( "IP", $_SERVER['REMOTE_ADDR'] );
            $zariliaMailer->assign( "NEWPWD", $newpass );
            $zariliaMailer->setToUsers( $getuser[0] );
            $zariliaMailer->setFromEmail( $zariliaConfig['adminmail'] );
            $zariliaMailer->setFromName( $zariliaConfig['sitename'] );
            $zariliaMailer->setSubject( sprintf( _US_NEWPWDREQ, ZAR_URL ) );
            if ( !$zariliaMailer->send() ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_MAILERROR );
                return false;
            }
            // Next step: add the new password to the database
            $sql = sprintf( "UPDATE %s SET pass = '%s' WHERE uid = %u", $zariliaDB->prefix( 'users' ), $GLOBALS['zariliaSecurity']->execEncryptionFunc('encrypt', $newpass ), $getuser[0]->getVar( 'uid' ) );
            if ( !$zariliaDB->Execute( $sql ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_MAILPWDNG );
                return false;
            } else {
                redirect_header( "index.php", 1, sprintf( _US_PWDMAILED, $getuser[0]->getVar( "uname" ) ), false );
            }
        } else {
            return $this->lostconfirm();
        }
    }

    function isdefault()
    {
        return $this->loginform();
    }

    function doRedirect()
    {
        if ( !empty( $_POST['zarilia_redirect'] ) && !strpos( $_POST['zarilia_redirect'], 'register' ) ) {
            $_POST['zarilia_redirect'] = trim( $_POST['zarilia_redirect'] );
            $parsed = parse_url( ZAR_URL );
            $url = isset( $parsed['scheme'] ) ? $parsed['scheme'] . '://' : 'http://';
            if ( isset( $parsed['host'] ) ) {
                $url .= $parsed['host'];
                if ( isset( $parsed['port'] ) ) {
                    $url .= ':' . $parsed['port'];
                }
            } else {
                $url .= $_SERVER['HTTP_HOST'];
            }
            if ( @$parsed['path'] ) {
                if ( strncmp( $parsed['path'], $_POST['zarilia_redirect'], strlen( $parsed['path'] ) ) ) {
                    $url .= $parsed['path'];
                }
            }
            $url .= $_POST['zarilia_redirect'];
        } else {
            $url = ZAR_URL . '/index.php';
        }
        return $url;
    }
}

?>