<?php
// $Id: auth.php,v 1.2 2007/03/30 22:05:44 catzwolf Exp $
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

/**
 *
 * @package kernel
 * @subpackage auth
 * @author Pierre-Eric MENUET <pemphp@free.fr>
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaAuth {
    var $_dao;

    /**
     * Authentication Service constructor
     */
    function ZariliaAuth ( &$dao ) {
        $this->_dao = $dao;
    }

    /**
     *
     * @abstract need to be write in the dervied class
     */
    function authenticate() {
        $authenticated = false;
        return $authenticated;
    }
}

?>