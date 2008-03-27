<?php
// $Id: handlerregistry.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
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
 * A registry for holding references to {@link ZariliaObjectHandler} classes
 *
 * @package kernel
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */

class ZariliaHandlerRegistry {
    /**
     * holds references to handler class objects
     *
     * @var array
     * @access private
     */
    var $_handlers = array();

    /**
     * get a reference to the only instance of this class
     *
     * if the class has not been instantiated yet, this will also take
     * care of that
     *
     * @static
     * @staticvar object  The only instance of this class
     * @return object Reference to the only instance of this class
     */
    function &instance() {
        static $instance;
        if ( !isset( $instance ) ) {
            $instance = new ZariliaHandlerRegistry();
        }
        return $instance;
    }

    /**
     * Register a handler class object
     *
     * @param string $name Short name of a handler class
     * @param object $ &$handler {@link ZariliaObjectHandler} class object
     */
    function setHandler( $name, &$handler ) {
        $this->_handlers['kernel'][$name] = &$handler;
    }

    /**
     * Get a registered handler class object
     *
     * @param string $name Short name of a handler class
     * @return object {@link ZariliaObjectHandler}, FALSE if not registered
     */
    function &getHandler( $name ) {
        if ( !isset( $this->_handlers['kernel'][$name] ) ) {
            return false;
        }
        return $this->_handlers['kernel'][$name];
    }

    /**
     * Unregister a handler class object
     *
     * @param string $name Short name of a handler class
     */
    function unsetHandler( $name ) {
        unset( $this->_handlers['kernel'][$name] );
    }

    /**
     * Register a handler class object for a addon
     *
     * @param string $addon Directory name of a addon
     * @param string $name Short name of a handler class
     * @param object $ &$handler {@link ZariliaObjectHandler} class object
     */
    function setAddonHandler( $addon, $name, &$handler ) {
        $this->_handlers['addon'][$addon][$name] = &$handler;
    }

    /**
     * Get a registered handler class object for a addon
     *
     * @param string $addon Directory name of a addon
     * @param string $name Short name of a handler class
     * @return object {@link ZariliaObjectHandler}, FALSE if not registered
     */
    function &getAddonHandler( $addon, $name ) {
        if ( !isset( $this->_handlers['addon'][$addon][$name] ) ) {
            return false;
        }
        return $this->_handlers['addon'][$addon][$name];
    }

    /**
     * Unregister a handler class object for a addon
     *
     * @param string $addon Directory name of a addon
     * @param string $name Short name of a handler class
     */
    function unsetAddonHandler( $addon, $name ) {
        unset( $this->_handlers['addon'][$addon][$name] );
    }
}

?>