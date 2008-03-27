<?php 
// $Id: auth_ldap.php,v 1.1 2007/03/16 02:40:44 catzwolf Exp $
// auth_ldap.php - LDAP authentification class
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
 * 
 * @package kernel
 * @subpackage auth
 * @author Pierre-Eric MENUET <pemphp@free.fr> 
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaAuthLdap extends ZariliaAuth {
    var $ldap_server;
    var $ldap_port = '389';
    var $ldap_version = '3';
    var $ldap_base_dn;
    var $ldap_uid_asdn;
    var $ldap_uid_attr;
    var $ldap_mail_attr;
    var $ldap_name_attr;
    var $ldap_surname_attr;
    var $ldap_givenname_attr;
    var $ldap_manager_dn;
    var $ldap_manager_pass;

    /**
     * Authentication Service constructor
     */
    function ZariliaAuthLdap ( $dao ) {
        $this->_dao = $dao; 
        // The config handler object allows us to look at the configuration options that are stored in the database
        $config_handler = &zarilia_gethandler( 'config' );
        $config = &$config_handler->getConfigsByCat( ZAR_CONF_AUTH );
        $confcount = count( $config );
        foreach ( $config as $key => $val ) {
            $this->$key = $val;
        } 
    } 

    /**
     * Authenticate  user again LDAP directory (Bind)
     *     2 options : 
     * 		Authenticate directly with uname in the DN
     * 		Authenticate with manager, search the dn
     * 
     * @param string $uname Username
     * @param string $pwd Password
     * @return bool 
     */
    function authenticate( $uname, $pwd = null ) {
        $user = false;
        if ( !extension_loaded( 'ldap' ) ) {
            $this->setErrors( 0, 'ldap extension not loaded' );
            return $user;
        } 
        $ds = ldap_connect( $this->ldap_server, $this->ldap_port );
        if ( $ds ) {
            @ldap_set_option( $ds, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_version ); 
            // If the uid is not in the DN we proceed to a search
            // The uid is not always in the dn
            if ( !$this->ldap_uid_asdn ) {
                // Bind with the manager
                if ( !ldap_bind( $ds, $this->ldap_manager_dn, stripslashes( $this->ldap_manager_pass ) ) ) {
                    $this->setErrors( ldap_errno( $ds ), $this->ldap_manager_dn );
                    return $user;
                } 
                $sr = ldap_search( $ds, $this->ldap_base_dn, $this->ldap_uid_attr . "=" . $uname, Array( $this->ldap_mail_attr, $this->ldap_name_attr, $this->ldap_surname_attr, $this->ldap_givenname_attr ) );
                $info = ldap_get_entries( $ds, $sr );
                if ( $info["count"] > 0 ) {
                    $userDN = $info[0]['dn'];
                } 
            } else {
                $userDN = $this->ldap_uid_attr . "=" . $uname . "," . $this->ldap_base_dn;
            } 
            // We bind as user
            $ldapbind = ldap_bind( $ds, $userDN, stripslashes( $pwd ) );
            if ( $ldapbind ) {
                $member_handler = &zarilia_gethandler( 'member' );
                $user = &$member_handler->loginUser( $uname, $pwd );
                if ( !is_object( $user ) ) {
                    return false;
                } 
                // return true;
                return is_object( $user );
            } 
            if ( !$user || !is_object( $user ) ) {
                $this->setErrors( ldap_errno( $ds ), $userDN );
            } 
            @ldap_close( $ds );
        } else {
            $this->setErrors( 0, "Could not connect to LDAP server." );
        } 
        return $user;
    } 
} // end class

?>