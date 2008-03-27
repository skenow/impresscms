<?php
// $Id: zariliasecurity.php,v 1.1 2007/03/16 02:38:59 catzwolf Exp $
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

/*
 * Class for managing security aspects such as checking referers, applying tokens and checking global variables for contamination
 *
 * @package        kernel
 * @subpackage    core
 *
 * @author        Jan Pedersen     
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */

class ZariliaSecurity
{
    var $errors = array();
    /**
     * Constructor
     */
    function ZariliaSecurity()
    {
    }

    /**
     * Check if there is a valid token in $_REQUEST['ZAR_TOKEN_REQUEST'] - can be expanded for more wide use, later (Mith)
     *
     * @param bool $clearIfValid whether to clear the token after validation
     * @return bool
     */
    function check( $clearIfValid = true, $token = false )
    {
        return $this -> validateToken( $token, $clearIfValid );
    }

    /**
     * Create a token in the user's session
     *
     * @param int $timeout time in seconds the token should be valid
     * @return string token value
     */
    function createToken( $timeout = 0 )
    {
        $this -> garbageCollection();
        if ( $timeout == 0 )
        {
            $timeout = intval( $GLOBALS['zariliaConfig']['session_expire'] ) * 60; //session_expire is in minutes, we need seconds
            if ( $timeout == 0 || $timeout > 1000 ) // if timeout is still zero - or a very large value
            {
                $timeout = 60 * 60; //set timeout to 60 minutes
            }
        }
        $token_id = md5( uniqid( rand(), true ) );
        // save token data on the server
        if ( !isset( $_SESSION['ZAR_TOKEN_SESSION'] ) )
        {
            $_SESSION['ZAR_TOKEN_SESSION'] = array();
        }
        $expire_time = time() + intval( $timeout );
        $token_data = array( 'id' => $token_id, 'expire' => $expire_time );
        array_push( $_SESSION['ZAR_TOKEN_SESSION'], $token_data );
        return md5( $token_id . $_SERVER['HTTP_USER_AGENT'] . ZAR_DB_PREFIX );
    }

    /**
     * Check if a token is valid. If no token is specified, $_REQUEST['ZAR_TOKEN_REQUEST'] is checked
     *
     * @param string $token token to validate
     * @param bool $clearIfValid whether to clear the token value if valid
     * @return bool
     */
    function validateToken( $token = false, $clearIfValid = true )
    {
        global $zariliaLogger;
        if ( file_exists( ZAR_ROOT_PATH . "/language/" . $GLOBALS['zariliaConfig']['language'] . "/error.php" ) )
        {
            include_once ZAR_ROOT_PATH . "/language/" . $GLOBALS['zariliaConfig']['language'] . "/error.php";
        }
        else
        {
            include_once ZAR_ROOT_PATH . "/language/english/error.php";
        }
        $token = ( $token === false ) ? @$_REQUEST['ZAR_TOKEN_REQUEST'] : $token;
        if ( empty( $token ) || empty( $_SESSION['ZAR_TOKEN_SESSION'] ) )
        {
            $zariliaLogger -> addExtra( 'Token Validation', _ER_SEC_NOTOKENFOUND );
            $this -> setErrors( _ER_SEC_NOTOKENFOUND );
            return false;
        }
        $validFound = false;
        $token_data = &$_SESSION['ZAR_TOKEN_SESSION'];
        foreach ( array_keys( $token_data ) as $i )
        {
            if ( $token === md5( $token_data[$i]['id'] . $_SERVER['HTTP_USER_AGENT'] . ZAR_DB_PREFIX ) )
            {
                if ( $this -> filterToken( $token_data[$i] ) )
                {
                    if ( $clearIfValid )
                    {
                        // token should be valid once, so clear it once validated
                        unset( $token_data[$i] );
                    }
                    $zariliaLogger -> addExtra( 'Token Validation', 'Valid Token Found' );
                    $validFound = true;
                }
                else
                {
                    $str = _ER_SEC_TOKENEXPIRED;
                    $this -> setErrors( $str );
                    $zariliaLogger -> addExtra( 'Token Validation', $str );
                }
            }
        }
        if ( !$validFound )
        {
            $zariliaLogger -> addExtra( 'Token Validation', _ER_SEC_NOTOKENFOUND );
            $this -> setErrors( _ER_SEC_NOTOKENFOUND );
        }
        $this -> garbageCollection();
        return $validFound;
    }

    /**
     * Clear all token values from user's session
     */
    function clearTokens()
    {
        $_SESSION['ZAR_TOKEN_SESSION'] = array();
    }

    /**
     * Check whether a token value is expired or not
     *
     * @param string $token
     * @return bool
     */
    function filterToken( $token )
    {
        return ( !empty( $token['expire'] ) && $token['expire'] >= time() );
    }

    /**
     * Perform garbage collection, clearing expired tokens
     *
     * @return void
     */
    function garbageCollection()
    {
        if ( isset( $_SESSION['ZAR_TOKEN_SESSION'] ) && count( $_SESSION['ZAR_TOKEN_SESSION'] ) > 0 )
        {
            $_SESSION['ZAR_TOKEN_SESSION'] = array_filter( $_SESSION['ZAR_TOKEN_SESSION'], array( $this, 'filterToken' ) );
        }
    }
    /**
     * Check the user agent's HTTP REFERER against ZAR_URL
     *
     * @param int $docheck 0 to not check the referer (used with XML-RPC), 1 to actively check it
     * @return bool
     */
    function checkReferer( $docheck = 1 )
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

    /**
     * Check superglobals for contamination
     *
     * @return void
     */
    function checkSuperglobals()
    {
        foreach ( array( 'GLOBALS', '_SESSION', 'HTTP_SESSION_VARS', '_GET', 'HTTP_GET_VARS', '_POST', 'HTTP_POST_VARS', '_COOKIE', 'HTTP_COOKIE_VARS', '_REQUEST', '_SERVER', 'HTTP_SERVER_VARS', '_ENV', 'HTTP_ENV_VARS', '_FILES', 'HTTP_POST_FILES', 'zariliaDB', 'zariliaUser', 'zariliaUserId', 'zariliaUserGroups', 'zariliaUserIsAdmin', 'zariliaConfig', 'zariliaOption', 'zariliaAddon', 'zariliaAddonConfig', 'zariliaRequestUri' ) as $bad_global )
        {
            if ( isset( $_REQUEST[$bad_global] ) )
            {
                header( 'Location: ' . ZAR_URL . '/' );
                exit();
            }
        }
    }

    /**
     * Check if visitor's IP address is banned
     * Should be changed to return bool and let the action be up to the calling script
     *
     * @return void
     */
    function checkBadips()
    {
        global $zariliaConfig;
        if ( $zariliaConfig['enable_badips'] == 1 && isset( $_SERVER['REMOTE_ADDR'] ) && $_SERVER['REMOTE_ADDR'] != '' )
        {
            foreach ( $zariliaConfig['bad_ips'] as $bi )
            {
                if ( !empty( $bi ) && preg_match( "/" . $bi . "/", $_SERVER['REMOTE_ADDR'] ) )
                {
                    exit();
                }
            }
        }
        unset( $bi );
        unset( $bad_ips );
        unset( $zariliaConfig['badips'] );
    }

    /**
     * Get the HTML code for a ZariliaFormHiddenToken object - used in forms that do not use ZariliaForm elements
     *
     * @return string
     */
    function getTokenHTML()
    {
        require_once( ZAR_ROOT_PATH . "/class/zariliaformloader.php" );
        $token = new ZariliaFormHiddenToken();
        return $token -> render();
    }

    /**
     * Add an error
     *
     * @param string $error
     */
    function setErrors( $error )
    {
        $this -> errors[] = trim( $error );
    }

    /**
     * Get generated errors
     *
     * @param bool $ashtml Format using HTML?
     * @return array |string    Array of array messages OR HTML string
     */
    function &getErrors( $ashtml = false )
    {
        if ( !$ashtml )
        {
            return $this -> errors;
        }
        else
        {
            $ret = '';
            if ( count( $this -> errors ) > 0 )
            {
                foreach ( $this -> errors as $error )
                {
                    $ret .= $error . '<br />';
                }
            }
            return $ret;
        }
    }

	function &getEncryptionInstace() {
		static $instance = null;
		if (!$instance) {
			global $cpConfig;
			$class = str_replace(' ','_',$cpConfig['security']['encryption']);
			require ZAR_FRAMEWORK_PATH.'/encryption/'.strtolower($class).'.class.php';
			$class = 'ZariliaEncryption_'.$class;
			$instance = new $class();
		}
		return $instance;
	}

	function execEncryptionFunc($name) {
		global $cpConfig;
		$obj = &$this->getEncryptionInstace();
		switch ($name) {
			case 'encrypt':
				return $obj->$name(func_get_arg(1), $cpConfig['security']['passkey']);
			break;
			case 'decrypt':
				return $obj->$name(func_get_arg(1), $cpConfig['security']['passkey']);
			break;
			default:
				$args = func_get_args();
				unset($args[0]);
				$args = array_values($args);
				return eval("return $obj-\>$name(".var_export($args, true).');');
			break;
		}
		return null;
	}

}

?>