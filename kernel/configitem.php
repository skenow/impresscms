<?php
// $Id: configitem.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
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
 * *#@+
 * Config type
 */
define( 'ZAR_CONF', 1 );
define( 'ZAR_CONF_USER', 2 );
define( 'ZAR_CONF_METAFOOTER', 3 );
define( 'ZAR_CONF_CENSOR', 4 );
define( 'ZAR_CONF_SEARCH', 5 );
define( 'ZAR_CONF_MAILER', 6 );
define( 'ZAR_CONF_PMESSAGES', 7 );
define( 'ZAR_CONF_MEDIA', 8 );
define( 'ZAR_CONF_AGE', 9 );
define( 'ZAR_CONF_MIMETYPES', 10 );
define( 'ZAR_CONF_COPPA', 11 );
define( 'ZAR_CONF_PROFILES', 12 );
define( 'ZAR_CONF_LOCALE', 13 );
define( 'ZAR_CONF_AUTH', 14 );
define( 'ZAR_CONF_SERVER', 15 );
define( 'ZAR_CONF_EVENTS', 16 );

/**
 *
 * @package kernel
 * @author John Neill AKA Catzwolf
 * @copyright (c) 2006 Zarilia
 */
class ZariliaConfigItem extends ZariliaObject {
    /**
     * Config options
     *
     * @var array
     * @access private
     */
    var $_confOptions = array();

    /**
     * Constructor
     */
    function ZariliaConfigItem()
    {
        $this->initVar( 'conf_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'conf_modid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'conf_catid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'conf_sectid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'conf_name', XOBJ_DTYPE_OTHER );
        $this->initVar( 'conf_title', XOBJ_DTYPE_TXTBOX );
        $this->initVar( 'conf_value', XOBJ_DTYPE_TXTAREA );
        $this->initVar( 'conf_desc', XOBJ_DTYPE_OTHER );
        $this->initVar( 'conf_formtype', XOBJ_DTYPE_OTHER );
        $this->initVar( 'conf_valuetype', XOBJ_DTYPE_OTHER );
        $this->initVar( 'conf_order', XOBJ_DTYPE_INT );
        $this->initVar( 'conf_required', XOBJ_DTYPE_INT );
		$this->initVar( 'conf_source', XOBJ_DTYPE_TXTAREA );
    }

    /**
     * Get a config value in a format ready for output
     *
     * @return string
     */
    function getConfValueForOutput()
    {
        switch ( $this->getVar( 'conf_valuetype' ) ) {
            case 'int':
                $value = intval( $this->getVar( 'conf_value', 'N' ) );
                break;
            case 'array':
                 $value = unserialize( $this->getVar( 'conf_value', 'N' ) );
                break;
            case 'float':
                $value = $this->getVar( 'conf_value', 'N' );
                $value = ( float )$value;
                break;
            case 'textarea':
                $value = $this->getVar( 'conf_value', 'S' );
                break;
            default:
                $value = $this->getVar( 'conf_value', 'N' );
                break;
        }
        return $value;
    }

    /**
     * Set a config value
     *
     * @param mixed $ &$value Value
     * @param bool $force_slash
     */
    function setConfValueForInput( &$value, $force_slash = false )
    {
        switch ( $this->getVar( 'conf_valuetype' ) ) {
            case 'array':
                if ( !is_array( $value ) ) {
                    $value = explode( '|', trim( $value ) );
                }
                $this->setVar( 'conf_value', serialize( $value ), $force_slash );
                break;
            case 'text':
                $this->setVar( 'conf_value', trim( $value ), $force_slash );
                break;
            default:
                $this->setVar( 'conf_value', $value, $force_slash );
                break;
        }
    }

    /**
     * Assign one or more {@link ZariliaConfigItemOption}s
     *
     * @param mixed $option either a {@link ZariliaConfigItemOption} object or an array of them
     */
    function setConfOptions( $option )
    {
        if ( is_array( $option ) ) {
            $count = count( $option );
            for ( $i = 0; $i < $count; $i++ ) {
                $this->setConfOptions( $option[$i] );
            }
        } else {
            if ( is_object( $option ) ) {
                $this->_confOptions[] = &$option;
            }
        }
    }

    /**
     * Get the {@link ZariliaConfigItemOption}s of this Config
     *
     * @return array array of {@link ZariliaConfigItemOption}
     */
    function &getConfOptions()
    {
        $value = $this->_confOptions;
        return $value;
    }
}

/**
 * ZariliaConfigItemHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: configitem.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
 * @access public
 */
class ZariliaConfigItemHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaAgeHandler
     *
     * @param  $db
     * @return
     */
    function ZariliaConfigItemHandler( &$db )
    {
        $this->ZariliaPersistableObjectHandler( $db, 'config', 'zariliaconfigitem', 'conf_id', 'conf_name' );
    }
}

?>