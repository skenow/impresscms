<?php
// $Id: class.register.php,v 1.1 2007/03/16 02:38:58 catzwolf Exp $
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
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * ZariliaRegister
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: class.register.php,v 1.1 2007/03/16 02:38:58 catzwolf Exp $
 * @access public
 **/
class ZariliaRegister {
    var $error = array();
    var $_var;
    var $_max_length;
    var $_bad_emails;

    function Register()
    {
        $this->_max_length = $zariliaConfigUser['maxuname'];
    }

    function setVar( $var = '' )
    {
        if ( empty( $var ) ) {
            return false;
        }
        $this->_var = $var;
    }

    function check_name( $message = '' )
    {
        if ( preg_match( $this->setVar_restriction(), $this->_var ) ) {
            $this->setVar_Error( sprintf( $message, $this->_max_length ) );
        }
    }

    function check_image( $message = '' )
    {
        if ( empty( $this->_var ) ) {
            $this->setVar_Error( "Please enter the image verification shown" );
            return false;
        }
        if ( ( isset( $login ) ) && ( $login == $uname ) ) {
            $this->setVar_Error( $message );
        }
    }

    function check_image_ver( $pass = '', $vpass )
    {
        $pass = strtolower( $pass );
        $vpass = strtolower( $vpass );

        if ( !isset( $pass ) || $pass == '' || !isset( $vpass ) || $vpass == '' ) {
            $this->setVar_Error( "Please enter the image verification shown" );
        }
        if ( ( isset( $pass ) ) && ( $pass != $vpass ) ) {
            $this->setVar_Error( "Verification did not match" );
        } elseif ( ( $pass != '' ) && ( strlen( $pass ) < 5 ) ) {
            $this->setVar_Error( sprintf( "Verification to short to be correct", 5 ) );
        }
        return true;
    }

    function check_length()
    {
        global $zariliaConfigUser;

        if ( strlen( $this->_var ) > $zariliaConfigUser['maxuname'] ) {
            $this->setVar_Error( sprintf( _US_NICKNAMETOOLONG, $zariliaConfigUser['maxuname'] ) );
        }
        if ( strlen( $this->_var ) < $zariliaConfigUser['minuname'] ) {
            $this->setVar_Error( sprintf( _US_NICKNAMETOOSHORT, $zariliaConfigUser['minuname'] ) );
        }
    }

    function check_sameName( $login = '', $uname, $message = '' )
    {
        if ( ( isset( $login ) ) && ( $login == $uname ) ) {
            $this->setVar_Error( $message );
        }
    }

    function check_spaces( $message = '' )
    {
        if ( strrpos( $this->_var, ' ' ) > 0 ) {
            $this->setVar_Error( $message );
        }
    }

    function check_email( $message = '', $antispam = false )
    {
        if ( !preg_match( "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $this->_var ) ) {
            $this->setVar_Error( $message );
        }
    }

    function check_badEmails( $message = '' )
    {
        global $zariliaConfigUser;
        if ( in_array( $this->_var, $zariliaConfigUser['bad_emails'] ) ) {
            $this->setVar_Error( $message );
        }
    }

    function check_badNames( $message = '' )
    {
        global $zariliaConfigUser;
        if ( in_array( $this->_var, $zariliaConfigUser['bad_unames'] ) ) {
            $this->setVar_Error( $message );
        }
    }

    function check_badIP( $message = '' )
    {
        global $zariliaConfigUser;
        if ( in_array( $this->_var, $zariliaConfigUser['bad_ips'] ) ) {
            $this->setVar_Error( $message );
        }
    }

    function check_disclaimer( $message = '', $agree_disc = 0 )
    {
        global $zariliaConfigUser;

        if ( $zariliaConfigUser['reg_dispdsclmr'] != 0 && $zariliaConfigUser['reg_disclaimer'] != '' ) {
            if ( $agree_disc != 1 ) {
                $this->setVar_Error( $message );
            }
        }
    }

    function setVar_restriction()
    {
        global $zariliaConfigUser;
        switch ( $zariliaConfigUser['uname_test_level'] ) {
            case 0:
                // strict
                $restriction = '/[^a-zA-Z0-9\_\-]/';
                break;
            case 1:
                // medium
                $restriction = '/[^a-zA-Z0-9\_\-\<\>\,\.\$\%\#\@\!\\\'\"]/';
                break;
            case 2:
                // loose
                $restriction = '/[\000-\040]/';
                break;
        }
        return $restriction;
    }

    function check_Count( $table = 'users', $vari = '', $message = '' )
    {
        global $zariliaDB;
        $count = 0;
        if ( in_array( $vari, array( 'email', 'uname' , 'login', 'ipaddress' ) ) ) {
            $sql = "SELECT COUNT(*) FROM " . $zariliaDB->prefix( 'users' ) . " where $vari=" . $zariliaDB->qstr( $this->_var );
            $result = $zariliaDB->Execute( $sql );
            list( $count ) = $zariliaDB->fetchRow( $result );
        }
        if ( $count > 0 ) {
            $this->setVar_Error( $message );
        }
    }

    function check_Password( $pass = '', $vpass )
    {
        global $zariliaConfigUser;

        if ( !isset( $pass ) || $pass == '' || !isset( $vpass ) || $vpass == '' ) {
            $this->setVar_Error( _US_ENTERPWD );
        }
        if ( ( isset( $pass ) ) && ( $pass != $vpass ) ) {
            $this->setVar_Error( _US_PASSNOTSAME );
        } elseif ( ( $pass != '' ) && ( strlen( $pass ) < $zariliaConfigUser['minpass'] ) ) {
            $this->setVar_Error( sprintf( _US_PWDTOOSHORT, $zariliaConfigUser['minpass'] ) );
        }
    }

    function setVar_Error( $_set_error )
    {
        $this->error[] = $_set_error;
    }

    function getVar_Error()
    {
        return $this->error;
    }
}

?>