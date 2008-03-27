<?php
// $Id: userfactory.php,v 1.3 2007/05/05 11:11:34 catzwolf Exp $
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
class ZariliaUserFactory {
    /**
     * ZariliaUserFactory::ZariliaUserFactory()
     */
    function ZariliaUserFactory() {
        /*Empty Contructor*/
    }

    /**
     * ZariliaUserFactory::getUserAction()
     *
     * @param string $user_method
     * @return
     */
    function &getUserAction( $user_method = 'user' ) {
		if ( file_exists( ZAR_ROOT_PATH . '/class/user/user_' . $user_method . '.php' ) ) {
            require_once ZAR_ROOT_PATH . '/class/user/user_' . $user_method . '.php';
            $class = 'ZariliaUser' . ucfirst( $user_method );
			$user_instance = new $class();
            return $user_instance;
            unset( $user_instance );
        } else {
            return false;
        }
    }
}

?>