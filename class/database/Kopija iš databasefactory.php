<?php
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
/**
 *
 * @version $Id: databasefactory.php,v 1.4 2007/04/22 07:21:33 catzwolf Exp $
 * @copyright 2007
 */
class ZariliaDatabaseFactory {
    var $instance;

    /**
     * ZariliaDatabaseFactory::ZariliaDatabaseFactory()
     */
    function ZariliaDatabaseFactory() {
        $this->instance = null;
    }

    /**
     * ZariliaDatabaseFactory::getDatabaseConnection()
     *
     * @return
     */
    function &getDatabaseConnection() {
        $singleFactory = &getMyStaticFactory();
        if ( !$singleFactory->instance ) {
            /**
             */
            require_once ZAR_ROOT_PATH . '/class/database/' . ZAR_DB_TYPE . 'database.php';
            $class = 'Zarilia' . ucfirst( ZAR_DB_TYPE ) . 'Database';
            if ( !class_exists( $class ) ) {
                trigger_error( "Class: $class does not exist", E_USER_ERROR );
            }
            $singleFactory->instance = &new $class();
            $singleFactory->instance->setPrefix( ZAR_DB_PREFIX );
            $singleFactory->instance->setLogger( ZariliaLogger::instance() );
            if ( !$singleFactory->instance->connect() ) {
                trigger_error( 'Unable to connect to database', E_USER_ERROR );
            }
        }
        return $singleFactory->instance;
    }
}

function &getMyStaticFactory() {
    static $myStaticInstance = null;
    if ( $myStaticInstance == null ) {
        $myStaticInstance = &new ZariliaDatabaseFactory();
    }
    return $myStaticInstance;
}

?>