<?php
// $Id$
// auth_ldap.php - LDAP authentification class
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
/**
 * @package     kernel
 * @subpackage  auth
 * @description	Authentification class for standard LDAP Server V2 or V3
 * @author	    Pierre-Eric MENUET	<pemphp@free.fr>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
include_once XOOPS_ROOT_PATH . '/class/auth/auth_provisionning.php';

class XoopsAuthLdap extends XoopsAuth {

    var $ldap_server;
    var $ldap_port = '389';
    var $ldap_version = '3';
    var $ldap_base_dn;
    var $ldap_loginname_asdn;
    var $ldap_loginldap_attr;
    var $ldap_mail_attr;
    var $ldap_name_attr;
    var $ldap_surname_attr;
    var $ldap_givenname_attr;
    var $ldap_manager_dn;
    var $ldap_manager_pass;
    var $_ds;

    /**
	 * Authentication Service constructor
	 */
    function XoopsAuthLdap (&$dao) {
		$this->_dao = $dao;
        //The config handler object allows us to look at the configuration options that are stored in the database
        $config_handler =& xoops_gethandler('config');    
        $config =& $config_handler->getConfigsByCat(XOOPS_CONF_AUTH);
        $confcount = count($config);
        foreach ($config as $key => $val) {
            $this->$key = $val;
        }	
    }


    /**
	 *  Authenticate  user again LDAP directory (Bind)
	 *  2 options : 
	 * 		Authenticate directly with uname in the DN
	 * 		Authenticate with manager, search the dn
	 *
	 * @param string $uname Username
	 * @param string $pwd Password
	 *
	 * @return bool
	 */	
    function authenticate($uname, $pwd = null) {
        $authenticated = false;
        if (!extension_loaded('ldap')) {
            $this->setErrors(0, _AUTH_LDAP_EXTENSION_NOT_LOAD);
            return $authenticated;
        }
        $this->_ds = ldap_connect($this->ldap_server, $this->ldap_port);
        if ($this->_ds) {
            ldap_set_option($this->_ds, LDAP_OPT_PROTOCOL_VERSION, $this->ldap_version);
            // If the uid is not in the DN we proceed to a search
            // The uid is not always in the dn
            $userDN = $this->getUserDN($uname);
            if (!$userDN) return false;
            // We bind as user to test the credentials         
            $authenticated = ldap_bind($this->_ds, $userDN, stripslashes($pwd));
            if ($authenticated) {
            	// We load the Xoops User database
            	return $this->loadXoopsUser($userDN, $uname, $pwd);            	               
            } else $this->setErrors(ldap_errno($this->_ds), ldap_err2str(ldap_errno($this->_ds)) . '(' . $userDN . ')');
        }
        else {
            $this->setErrors(0, _AUTH_LDAP_SERVER_NOT_FOUND);            
        }
        @ldap_close($this->_ds);
        return $authenticated;
    }
    
    
    /**
	 *  Compose the user DN with the configuration.
	 * 
	 *
	 * @return userDN or false
	 */	    
    function getUserDN($uname) {
    	$userDN = false;
	    if (!$this->ldap_loginname_asdn) {
	        // Bind with the manager
	        if (!ldap_bind($this->_ds, $this->ldap_manager_dn, stripslashes($this->ldap_manager_pass))) {
	             $this->setErrors(ldap_errno($this->_ds), ldap_err2str(ldap_errno($this->_ds)) . '(' . $this->ldap_manager_dn . ')');
	             return false;
	        }
			$filter = $this->getFilter($uname);
	        $sr = ldap_search($this->_ds, $this->ldap_base_dn, $filter);
	        $info = ldap_get_entries($this->_ds, $sr);
	        if ($info["count"] > 0) {
	            $userDN = $info[0]['dn'];
	        } else $this->setErrors(0, sprintf(_AUTH_LDAP_USER_NOT_FOUND, $uname, $filter, $this->ldap_base_dn));	        
	    }
	    else {
	        $userDN = $this->ldap_loginldap_attr."=".$uname.",".$this->ldap_base_dn;
	    }
	    return $userDN;
    }


    /**
	 *  Load user from XOOPS Database
	 * 
	 * @return XoopsUser object
	 */	        
	 function getFilter($uname) {
	 	$filter = '';
	 	if ($this->ldap_filter_person != '') {
			$filter = str_replace('@@loginname@@',$uname, $this->ldap_filter_person);	 			
	 	}	
	 	else {
	 		$filter = $this->ldap_loginldap_attr . "=" . $uname;
	 	} 	
	 	return $filter;
	 } 

	          
	function loadXoopsUser($userdn, $uname, $pwd = null) {
		$provisHandler = XoopsAuthProvisionning::getInstance($this);
        $sr = ldap_read($this->_ds, $userdn, '(objectclass=*)');
        $entries = ldap_get_entries($this->_ds, $sr);        
        if ($entries["count"] > 0) {
        	$xoopsUser = $provisHandler->sync($entries[0], $uname, $pwd);
        }
        else $this->setErrors(0, sprintf('loadXoopsUser - ' . _AUTH_LDAP_CANT_READ_ENTRY, $userdn));
		return $xoopsUser;
	}

    
} // end class


?>
