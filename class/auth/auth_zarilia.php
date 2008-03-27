<?php
// $Id: auth_zarilia.php,v 1.4 2007/05/05 11:11:24 catzwolf Exp $
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
 *
 * @package kernel
 * @subpackage auth
 * @author Pierre-Eric MENUET <pemphp@free.fr>
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
/**
 * ZariliaAuthZarilia
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: auth_zarilia.php,v 1.4 2007/05/05 11:11:24 catzwolf Exp $
 * @access public
 */
/**
 * ZariliaAuthZarilia
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: auth_zarilia.php,v 1.4 2007/05/05 11:11:24 catzwolf Exp $
 * @access public
 */
class ZariliaAuthZarilia extends ZariliaAuth {
    var $_user;
    var $_login;
    var $_pass;
    var $_md5pass = true;
    var $_rememberme;
    var $_persconnect;
    var $_ipaddress;
	var $_loginanon = 0;

    /**
     * Authentication Service constructor
     */
    function ZariliaAuthZarilia ( &$dao ) {
        $this->_dao = $dao;
    }

    function setLogin( $value = '' ) {
        $this->_login = $value;
        $this->_doType = ( strstr( $value , '@' ) ) ? 1 : 0;
    }

    function setPass( $value = '' ) {
        $this->_pass = $value;
    }

    function setMD5( $value = false ) {
        $this->_md5pass = md5( $value );
    }

    function setImageVer( $value = '' ) {
        $this->_imagever = $value;
    }

    function setVerification( $value = '' ) {
        $this->_verification = $value;
    }

    function setRememberMe( $value = 0 ) {
        $this->_rememberme = ( intval( $value ) != 0 ) ? 1 : 0 ;
    }

    function setLoginAnon( $value = 0 ) {
        $this->_loginanon = ( intval( $value ) != 0 ) ? 1 : 0 ;
    }
    /**
     * ZariliaAuthZarilia::authenticate()
     *
     * @param mixed $uname
     * @param mixed $pwd
     * @return
     */
    function doLogin() {
        global $zariliaConfig;
        if ( $_SERVER['REQUEST_METHOD'] <> 'POST' ) {
            die( 'You can only reach this page by posting from the html form' );
        }
		
		if ((strlen(trim($this->_login))<1)  && (strlen(trim($this->_pass))<1)) {
            $security_handler = &zarilia_gethandler( 'tokens' );
            $security_handler->addLog( $title = 'Failed Login Attempts', $this->_login, $pass = '' );
            return '';
		}

        $criteria = new CriteriaCompo();
        if ( $this->_doType ) {
            $criteria->add( new Criteria( 'email', $this->_login ) );
        } else {
            $criteria->add( new Criteria( 'login', $this->_login ) );
        }
//        if ( $this->_md5pass == true ) {
            $criteria->add( new Criteria( 'pass', $GLOBALS['zariliaSecurity']->execEncryptionFunc('encrypt', $this->_pass ) ) );
//        } else {
//            $criteria->add( new Criteria( 'pass', $this->_pass ) );
//        }
        $criteria->add ( new Criteria( 'level', 0, '!=' ), 'AND' );
        $criteria->add ( new Criteria( 'level', 6, '!=' ), 'AND' );
        $user_handler = &zarilia_gethandler( 'user' );
        $user = &$user_handler->getObjects( $criteria, false );
        if ( is_object( $user[0] ) && $this->_verimage() ) {
            $_SESSION = array();
            $_SESSION['zariliaUserId'] = $user[0]->getVar( 'uid' );
            $_SESSION['zariliaUserGroups'] = $user[0]->getGroups();
            $_SESSION['zariliaUserCookie'] = ( $user[0]->getVar( 'user_cookie' ) ) ? $user[0]->getVar( 'user_cookie' ) : md5( session_id() );
            $_SESSION['zariliaLogonanon'] = $this->_loginanon; //( $user[0]->getVar( 'user_anon' ) ) ? $user[0]->getVar( 'user_anon' ) : intval( $this->_loginanon );
            $this->_setSession( $user[0], $this->_rememberme, true );
            $this->_doLoginMaintenance( $user[0] );
            // return $user[0];
            if ( is_object( $user[0] ) ) {
				redirect_header( $this->doRedirect(), 1, sprintf( _US_LOGGINGU, $user[0]->getVar( 'uname' ) ) );
            } else {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _US_INCORRECTLOGIN );
                return false;
            }
        } else {
            // sleep( 3 );
            /**
             */
            $security_handler = &zarilia_gethandler( 'tokens' );
            $security_handler->addLog( $title = 'Failed Login Attempts', $this->_login, $pass = '' );
            return '';
        }
    }

    function _verimage() {
        global $zariliaConfigUser, $config_handler;
        $zariliaConfigUser = &$config_handler->getConfigsByCat( ZAR_CONF_USER );
        if ( isset( $zariliaConfigUser['showimagever'] ) && $zariliaConfigUser['showimagever'] == 1 ) {
            unset( $zariliaConfigUser );
            if ( !$this->_verification && !$this->_imagever || ( $this->_verification != $this->_imagever ) ) {
                return false;
            }
        }
        unset( $zariliaConfigUser );
        return true;
    }

    function _setSession( &$user, $remember, $init = true ) {
        global $zariliaConfig, $member_handler;
        if ( $zariliaConfig['use_mysession'] && $zariliaConfig['session_name'] != '' ) {
            setcookie( $zariliaConfig['session_name'], session_id(), time() + ( 60 * $zariliaConfig['session_expire'] ), '/', '', 0 );
        }
        if ( $remember ) {
            $cookie = $this->updateCookie( $user->getVar( 'user_cookie' ), true );
        }
        /*Theme session for user*/
        $user_theme = $user->getVar( 'theme' );
        if ( in_array( $user_theme, $zariliaConfig['theme_set_allowed'] ) ) {
            $_SESSION['zariliaUserTheme'] = $user_theme;
        }

        if ( $init == true ) {
            $sql = "UPDATE " . $this->_dao->prefix( 'users' ) . " SET user_cookie='" . session_id() . "', last_login = " . time() . ", user_anon = " . intval($_SESSION['zariliaLogonanon']) . " WHERE uid = " . $user->getVar( 'uid' );
            $this->_dao->Execute( $sql );
            //$user->setVar( 'user_cookie', session_id() );
            //$user->setVar( 'last_login', time() );
            //$member_handler->insertUser( $user, true );
        }
    }

    function _doLoginMaintenance( &$user ) {
        $notification_handler = &zarilia_gethandler( 'notification' );
        $notification_handler->doLoginMaintenance( $user->getVar( 'uid' ) );
        unset( $notification_handler );
    }

    /* do session login from here */
    function doSession() {
        if ( isset( $_SESSION['zariliaUserId'] ) && !empty( $_SESSION['zariliaUserId'] ) ) {
            $zariliaUser = $this->_checkSession();
        } elseif ( isset( $_COOKIE['zariliaUserLogin'] ) && $_COOKIE['zariliaUserLogin'] ) {
            $zariliaUser = $this->_checkRemembered();
        } else {
			//$user_handler = &zarilia_gethandler( 'user' );
            //$zariliaUser = new ZariliaUser();
			$zariliaUser = null;
        }
        return $zariliaUser;
    }

    function _checkRemembered() {
        $string = stripslashes( $_COOKIE['zariliaUserLogin'] );
        list( $zariliaUserId, $sessionid ) = unserialize( $string );
        if ( !$zariliaUserId or !$sessionid ) {
            return false;
        }
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'uid', "$zariliaUserId" ) );
        $criteria->add( new Criteria( 'user_cookie', "$sessionid" ) );
        $user_handler = &zarilia_gethandler( 'user' );
        $user = &$user_handler->getObjects( $criteria, false );
        if ( $user ) {
            $this->_setSession( $user[0], 1, true );
            $this->_doLoginMaintenance( $user[0] );
            return $user[0];
        }
    }

    function _checkSession( $cookie = '' ) {
        global $zariliaConfig, $member_handler;
        if ( !empty( $_SESSION['zariliaUserId'] ) ) {
            $criteria = new CriteriaCompo();
            $criteria->add( new Criteria( 'uid', $_SESSION['zariliaUserId'] ) );
            if ( $cookie ) {
                $criteria->add( new Criteria( 'user_cookie', session_id() ) );
            }
            $user_handler = &zarilia_gethandler( 'user' );
            $user = &$user_handler->getObjects( $criteria, false );
            if ( !is_object( $user[0] ) ) {
                $user = '';
                $_SESSION = array();
            } else {
                $this->_setSession( $user[0], $this->_rememberme, false );
                $user[0]->setGroups( $_SESSION['zariliaUserGroups'] );
                if ( !$user[0]->getVar( 'editor' ) ) {
                    $user[0]->setVar( 'editor', $zariliaConfig['user_default'] );
                }
                // update who's online
            }
            return $user[0];
        }
    }

    /**
     * ZariliaAuthZarilia::doCheck()
     *
     * @return
     */
    function doCheck() {
        global $zariliaConfig;
        if ( $zariliaConfig['use_mysession'] && $zariliaConfig['session_name'] != '' && !isset( $_COOKIE[$zariliaConfig['session_name']] ) && !empty( $_SESSION['zariliaUserId'] ) ) {
            unset( $_SESSION['zariliaUserId'] );
        }
    }

    /**
     * ZariliaAuthZarilia::doClosedSiteCheck()
     *
     * @return
     */
    function doClosedSiteCheck() {
        global $zariliaUser, $zariliaConfig;

        $allowed = false;
        if ( $zariliaConfig['closesite'] == 1 ) {
            if ( is_object( $zariliaUser ) && array_intersect( $zariliaUser->getGroups(), $zariliaConfig['debug_mode_okgrp'] ) ) {
                return true;
            } elseif ( !empty( $_POST['zarilia_login'] ) ) {
                $this->setLogin( $_POST['uname'] );
                $this->setPass( $_POST['pass'] );
                $this->doLogin();
            }
            if ( !$allowed ) {
                include_once ZAR_ROOT_PATH . '/class/template.php';
                $zariliaTpl = new ZariliaTpl();
                $zariliaTpl->assign(
                    array( 'lang_login' => _LOGIN,
                        'lang_username' => _USERNAME,
                        'lang_password' => _PASSWORD,
                        'lang_siteclosemsg' => $zariliaConfig['closesite_text']
                        )
                    );
                $zariliaTpl->display( $zariliaConfig['theme_set'] . '/addons/system/system_siteclosed.html' );
                exit();
            }
            unset( $allowed, $group );
        }
    }

    /**
     * ZariliaAuthZarilia::updateCookie()
     *
     * @param mixed $cookie
     * @param mixed $save
     * @return
     */
    function updateCookie( $cookie, $save ) {
        $_SESSION['zariliaUserCookie'] = $cookie;
        if ( $save == true ) {
            $cookie_array = array( $_SESSION['zariliaUserId'], $cookie );
            setcookie( 'zariliaUserLogin', serialize( $cookie_array ), time() + 31104000, '/' );
        }
        return $cookie;
    }

    /**
     * ZariliaAuthZarilia::dologout()
     *
     * @return
     */
    function dologout() {
        global $zariliaUser, $zariliaConfig, $member_handler;
        $_SESSION = array();
        session_destroy();
        if ( $zariliaConfig['use_mysession'] && $zariliaConfig['session_name'] != '' ) {
            setcookie( $zariliaConfig['session_name'], '', time() - 3600, '/', '', 0 );
        }
        setcookie( 'zariliaUserLogin', '', time() - 3600, '/', '', 0 );
        if ( is_object( $zariliaUser ) ) {
            $online_handler = &zarilia_gethandler( 'online' );
            $online_handler->destroy( $zariliaUser->getVar( 'uid' ) );
            $zariliaUser->setVar( 'user_anon', 0 );
            $member_handler->insertUser( $zariliaUser );
        }
    }

    /**
     * ZariliaAuthZarilia::doRedirect()
     *
     * @return
     */
    function doRedirect() {
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
            $url = ZAR_URL;
        }
        return $url;
    }
}

?>