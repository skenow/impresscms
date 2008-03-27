<?php
// $Id: user.php,v 1.1 2007/03/16 02:40:54 catzwolf Exp $
// auth.php - defines abstract authentification wrapper class
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

if ( file_exists( ZAR_ROOT_PATH . "/language/" . $GLOBALS['zariliaConfig']['language'] . "/error.php" ) ) {
    include_once ZAR_ROOT_PATH . "/language/" . $GLOBALS['zariliaConfig']['language'] . "/error.php";
} else {
    include_once ZAR_ROOT_PATH . "/language/english/error.php";
}

/**
 * ZariliaUser
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: user.php,v 1.1 2007/03/16 02:40:54 catzwolf Exp $
 * @access public
 **/
class ZariliaUser {
    var $_dao;

    var $_errors;
    /**
     * Authentication Service constructor
     */
    function ZariliaUser ( &$dao ) {
        $this->_dao = $dao;
    }

    /**
     * add an error
     *
     * @param string $value error to add
     * @access public
     */
    function setErrors( $err_no, $err_str ) {
        $this->_errors[$err_no] = trim( $err_str );
    }

    /**
     * return the errors for this object as an array
     *
     * @return array an array of errors
     * @access public
     */
    function getErrors() {
        return $this->_errors;
    }

    /**
     * return the errors for this object as html
     *
     * @return string html listing the errors
     * @access public
     */
    function getHtmlErrors() {
        $ret = '<h4>' . _ERRORS . '</h4>';
        $ret = '<br>';
        if ( !empty( $this->_errors ) ) {
            foreach ( $this->_errors as $errno => $errstr ) {
                $msg = ( function_exists( "ldap_err2str" ) ? ldap_err2str ( $errno ) : '' );
                $ret .= $msg . ' <br> ' . $errstr . '<br />';
            }
        } else {
            $ret .= _NONE . '<br />';
        }
        return $ret;
    }
}

?>