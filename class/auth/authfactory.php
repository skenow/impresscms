<?php
// $Id: authfactory.php,v 1.1 2007/03/16 02:40:44 catzwolf Exp $
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
 * ZariliaAuthFactory
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: authfactory.php,v 1.1 2007/03/16 02:40:44 catzwolf Exp $
 * @access public
 */
class ZariliaAuthFactory {
    /**
     * ZariliaAuthFactory::ZariliaAuthFactory()
     */
    function ZariliaAuthFactory() {
        /*Empty Contructor*/
    }

    /**
     * ZariliaAuthFactory::getAuthConnection()
     *
     * @param string $auth_method
     * @return
     */
    function &getAuthConnection( $auth_method = 'zarilia' ) {
        $type = ( $auth_method == 'zarilia' ) ? 'zarilia' : 'ldap';

        require_once ZAR_ROOT_PATH . '/class/auth/auth.php';
        switch ( strval( $type ) ) {
            case 'zarilia':
            default:
                $zarilia_auth_method = 'zarilia';
                $dao = &$GLOBALS['zariliaDB'];
                break;

            case 'ldap':
                $config_handler = &zarilia_gethandler( 'config' );
                $criteria = new CriteriaCompo();
                $criteria->add( new Criteria( 'conf_name', 'auth_method' ) );
                $config = &$config_handler->getConfigs( $criteria );
                if ( !$config ) { // If there is a config error, we use zarilia
                    $zarilia_auth_method = 'zarilia';
                } else {
                    $zarilia_auth_method = $config[0]->getVar( 'conf_value' );
                }
                $dao = null;
                break;
        } // switch
        require_once ZAR_ROOT_PATH . '/class/auth/auth_' . $zarilia_auth_method . '.php';

        $class = 'ZariliaAuth' . ucfirst( $zarilia_auth_method );
        $auth_instance = new $class( $dao );
        return $auth_instance;
        unset( $auth_instance );
    }
}

?>