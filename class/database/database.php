<?php
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
// if ( $_SERVER['REQUEST_METHOD'] != 'POST' || !zarilia_refcheck( ZAR_DB_CHKREF ) ) {
// define( 'ZAR_DB_PROXY', 1 );
// }
if ( !defined( 'ZARILIA_DATABASE_INCLUDED' ) ) {
    define( 'ZARILIA_DATABASE_INCLUDED', 1 );

    /**
     * zariliadatabase
     *
     * @package
     * @author John
     * @copyright Copyright (c) 2007
     * @version $Id: database.php,v 1.1 2007/03/31 03:57:55 catzwolf Exp $
     * @access public
     */
    class zariliadatabase {
        var $prefix = '';
        var $logger;

        /**
         * zariliadatabase::zariliadatabase()
         */
        function zariliadatabase() {
            // dummy
        }

        /**
         * ZariliaDatabaseFactory::setPrefix()
         *
         * @param mixed $value
         * @return
         */
        function setPrefix( $value ) {
            $this->prefix = $value;
        }

        /**
         * zariliadatabase::setLogger()
         *
         * @param mixed $logger
         * @return
         */
        function setLogger( &$logger ) {
            $this->logger = &$logger;
        }

        /**
         * ZariliaDatabaseFactory::prefix()
         *
         * @param string $tablename
         * @return
         */
        function prefix( $tablename = '' ) {
            return ( $tablename ) ? $this->prefix . "_$tablename" : $this->prefix;
        }

        /**
         * zariliadatabase::displayError()
         *
         * @param string $reason
         * @return
         */
        function displayError( $reason = '' ) {
            echo "Lets give the user a better idea why they have no connection";
            exit();
        }
    }
}

?>