<?php
// $Id: config.php,v 1.1 2007/03/16 02:39:10 catzwolf Exp $
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

require_once ZAR_ROOT_PATH . '/kernel/configoption.php';
require_once ZAR_ROOT_PATH . '/kernel/configitem.php';



/**
 * ZARILIA configuration handling class.
 * This class acts as an interface for handling general configurations of ZARILIA
 * and its addons.
 *
 * @author Kazumi Ono <webmaster@myweb.ne.jp>
 * @todo Tests that need to be made:
 *                - error handling
 * @access public
 */

class ZariliaConfigHandler {
    /**
     * holds reference to config item handler(DAO) class
     *
     * @var object
     * @access private
     */
    var $_cHandler;

    /**
     * holds reference to config option handler(DAO) class
     *
     * @var object
     * @access private
     */
    var $_oHandler;

    /**
     * holds an array of cached references to config value arrays,
     *        indexed on addon id and category id
     *
     * @var array
     * @access private
     */
    var $_cachedConfigs = array();

    /**
     * Constructor
     *
     * @param object $ &$db    reference to database object
     */
    function ZariliaConfigHandler( &$db ) {
        $this->_cHandler = new ZariliaConfigItemHandler( $db );
        $this->_oHandler = new ZariliaConfigOptionHandler( $db );
    }

    /**
     * Create a config
     *
     * @see ZariliaConfigItem
     * @return object reference to the new {@link ZariliaConfigItem}
     */
    function &createConfig() {
        return $this->_cHandler->create();
    }

    /**
     * Get a config
     *
     * @param int $id ID of the config
     * @param bool $withoptions load the config's options now?
     * @return object reference to the {@link ZariliaConfig}
     */
    function &getConfig( $id, $withoptions = false ) {
        $config = &$this->_cHandler->get( $id );
        if ( $withoptions == true ) {
            $config->setConfOptions( $this->getConfigOptions( new Criteria( 'conf_id', $id ) ) );
        }
        return $config;
    }

    /**
     * insert a new config in the database
     *
     * @param object $ &$config    reference to the {@link ZariliaConfigItem}
     */
    function insertConfig( &$config ) {
        if ( !$this->_cHandler->insert( $config ) ) {
            return false;
        }
        $options = &$config->getConfOptions();
        $count = count( $options );
        $conf_id = $config->getVar( 'conf_id' );
        for ( $i = 0; $i < $count; $i++ ) {
            $options[$i]->setVar( 'conf_id', $conf_id );
            if ( !$this->_oHandler->insert( $options[$i] ) ) {
                echo $options[$i]->getErrors();
            }
        }
        if ( !empty( $this->_cachedConfigs[$config->getVar( 'conf_modid' )][$config->getVar( 'conf_catid' )] ) ) {
            unset ( $this->_cachedConfigs[$config->getVar( 'conf_modid' )][$config->getVar( 'conf_catid' )] );
        }
        return true;
    }

    /**
     * Delete a config from the database
     *
     * @param object $ &$config    reference to a {@link ZariliaConfigItem}
     */
    function deleteConfig( &$config ) {
        if ( !$this->_cHandler->delete( $config ) ) {
            return false;
        }
        $options = &$config->getConfOptions();
        $count = count( $options );
        if ( $count == 0 ) {
            $options = &$this->getConfigOptions( new Criteria( 'conf_id', $config->getVar( 'conf_id' ) ) );
            $count = count( $options );
        }
        if ( is_array( $options ) && $count > 0 ) {
            for ( $i = 0; $i < $count; $i++ ) {
                $this->_oHandler->delete( $options[$i] );
            }
        }
        if ( !empty( $this->_cachedConfigs[$config->getVar( 'conf_modid' )][$config->getVar( 'conf_catid' )] ) ) {
            unset ( $this->_cachedConfigs[$config->getVar( 'conf_modid' )][$config->getVar( 'conf_catid' )] );
        }
        return true;
    }

    /**
     * get one or more Configs
     *
     * @param object $criteria {@link CriteriaElement}
     * @param bool $id_as_key Use the configs' ID as keys?
     * @param bool $with_options get the options now?
     * @return array Array of {@link ZariliaConfigItem} objects
     */
    function getConfigs( $criteria = null, $id_as_key = false, $with_options = false ) {
        return $this->_cHandler->getObjects( $criteria, $id_as_key );
    }

    /**
     * Count some configs
     *
     * @param object $criteria {@link CriteriaElement}
     */
    function getConfigCount( $criteria = null ) {
        return $this->_cHandler->getCount( $criteria );
    }

    /**
     * Get configs from a certain category
     *
     * @param int $category ID of a category
     * @param int $addon ID of a addon
     * @return array array of {@link ZariliaConfig}s
     */
    function &getConfigsByCat( $category, $addon = 0, $conf_sectid = 1 ) {
        static $_cachedConfigs;

        if ( !empty( $_cachedConfigs[$addon][$category] ) ) {
            return $_cachedConfigs[$addon][$category];
        } else {
            $ret = array();
            $criteria = new CriteriaCompo( new Criteria( 'conf_modid', intval( $addon ) ) );
            if ( is_array( $category ) && count( $category ) > 0 ) {
                $criteria->add( new Criteria( 'conf_catid', "(" . implode( ',', $category ) . ")", "IN" ) );
                $category = $category[0];
            } else {
                $criteria->add( new Criteria( 'conf_catid', intval( $category ) ) );
            }
            if ( empty( $conf_sectid ) ) {
                $criteria->add( new Criteria( 'conf_sectid', $conf_sectid ) );
            }
            $configs = $this->getConfigs( $criteria, true );
            if ( is_array( $configs ) ) {
                foreach ( array_keys( $configs ) as $i ) {
                    $ret[$configs[$i]->getVar( 'conf_name' )] = $configs[$i]->getConfValueForOutput();
                }
            }
            $_cachedConfigs[$addon][$category] = &$ret;
            return $ret;
        }
    }

    /**
     * Make a new {@link ZariliaConfigOption}
     *
     * @return object {@link ZariliaConfigOption}
     */
    function &createConfigOption() {
        return $this->_oHandler->create();
    }

    /**
     * Get a {@link ZariliaConfigOption}
     *
     * @param int $id ID of the config option
     * @return object {@link ZariliaConfigOption}
     */
    function &getConfigOption( $id ) {
        return $this->_oHandler->get( $id );
    }

    /**
     * Get one or more {@link ZariliaConfigOption}s
     *
     * @param object $criteria {@link CriteriaElement}
     * @param bool $id_as_key Use IDs as keys in the array?
     * @return array Array of {@link ZariliaConfigOption}s
     */
    function getConfigOptions( $criteria = null, $id_as_key = false ) {
        return $this->_oHandler->getObjects( $criteria, $id_as_key );
    }

    /**
     * Count some {@link ZariliaConfigOption}s
     *
     * @param object $criteria {@link CriteriaElement}
     * @return int Count of {@link ZariliaConfigOption}s matching $criteria
     */
    function getConfigOptionsCount( $criteria = null ) {
        return $this->_oHandler->getCount( $criteria );
    }

    /**
     * Get a list of configs
     *
     * @param int $conf_modid ID of the addons
     * @param int $conf_catid ID of the category
     * @return array Associative array of name=>value pairs.
     */
    function &getConfigList( $conf_modid, $conf_catid = 0, $conf_sectid = 1 ) {
        if ( !empty( $this->_cachedConfigs[$conf_modid][$conf_catid] ) ) {
            return $this->_cachedConfigs[$conf_modid][$conf_catid];
        } else {
            $criteria = new CriteriaCompo( new Criteria( 'conf_modid', $conf_modid ) );
            if ( empty( $conf_catid ) ) {
                $criteria->add( new Criteria( 'conf_catid', $conf_catid ) );
            }
            if ( empty( $conf_sectid ) ) {
                $criteria->add( new Criteria( 'conf_sectid', $conf_sectid ) );
            }
            $configs = &$this->_cHandler->getObjects( $criteria );
            $confcount = count( $configs );
            $ret = array();
            for ( $i = 0; $i < $confcount; $i++ ) {
                $ret[$configs[$i]->getVar( 'conf_name' )] = $configs[$i]->getConfValueForOutput();
            }
            $this->_cachedConfigs[$conf_modid][$conf_catid] = &$ret;
            return $ret;
        }
    }
}

?>