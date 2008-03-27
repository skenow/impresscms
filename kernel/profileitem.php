<?php
// $Id: profileitem.php,v 1.2 2007/05/05 11:12:12 catzwolf Exp $
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
 * ZariliaProfileItem
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: profileitem.php,v 1.2 2007/05/05 11:12:12 catzwolf Exp $
 * @access public
 */
class ZariliaProfileItem extends ZariliaObject
{
    var $_profileOptions = array();

    /**
     * ZariliaProfileItem::ZariliaProfileItem()
     */
    function ZariliaProfileItem()
    {
        $this->initVar( 'profile_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'profile_modid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'profile_catid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'profile_sectid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'profile_name', XOBJ_DTYPE_OTHER );
        $this->initVar( 'profile_title', XOBJ_DTYPE_TXTBOX );
        $this->initVar( 'profile_value', XOBJ_DTYPE_TXTAREA, null, false, null );
        $this->initVar( 'profile_desc', XOBJ_DTYPE_TXTAREA, null, false, null );
        $this->initVar( 'profile_formtype', XOBJ_DTYPE_OTHER );
        $this->initVar( 'profile_valuetype', XOBJ_DTYPE_OTHER );
        $this->initVar( 'profile_order', XOBJ_DTYPE_INT, 0 );
        $this->initVar( 'profile_required', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'profile_display', XOBJ_DTYPE_INT, 1 );
    }

    /**
     * ZariliaProfileItem::getProfValueForOutput()
     *
     * @param string $profile_value
     * @return
     */
    function &getProfValueForOutput( $profile_value = '' )
    {
        switch ( $this->getVar( 'profile_valuetype' ) )
        {
            case 'int':
                $value = intval( $this->getVar( 'profile_value', 'N' ) );
                break;
            case 'array':
                $value = unserialize( $this->getVar( 'profile_value', 'N' ) );
                break;
            case 'float':
                $value = $this->getVar( 'profile_value', 'N' );
                $value = ( float )$value;
                break;
            case 'textarea':
                $value = $this->getVar( 'profile_value', 'N' );
                break;
            default:
                $value = $this->getVar( 'profile_value', 'N' );
                break;
        }
        return $value;
    }

    /**
     * ZariliaProfileItem::getProfValueForminput()
     *
     * @param string $profile_value
     * @return
     */
    function &getProfValueForminput( $profile_value = '' )
    {
        if ( $profile_value )
        {
            $set_value = $profile_value;
        }
        else
        {
            $set_value = $this->getVar( 'profile_value', 'N' );
        }

        switch ( $this->getVar( 'profile_valuetype' ) )
        {
            case 'int':
                $value = intval( $set_value );
                break;
            case 'array':
                $value = unserialize( $set_value );
                break;
            case 'float':
                $value = $set_value;
                $value = ( float )$value;
                break;
            case 'textarea':
                $value = $set_value;
                break;
            default:
                $value = $set_value;
                break;
        }
        return $value;
    }

    /**
     * ZariliaProfileItem::setProfValueForInput()
     *
     * @param mixed $value
     * @param mixed $force_slash
     * @return
     */
    function setProfValueForInput( &$value, $force_slash = false )
    {
        switch ( $this->getVar( 'profile_valuetype' ) )
        {
            case 'array':
                if ( !is_array( $value ) )
                {
                    $value = explode( '|', trim( $value ) );
                }
                $this->setVar( 'profile_value', serialize( $value ), $force_slash );
                break;
            case 'text':
                $this->setVar( 'profile_value', trim( $value ), $force_slash );
                break;
            default:
                $this->setVar( 'profile_value', $value, $force_slash );
                break;
        }
    }

    /**
     * ZariliaProfileItem::setProfOptions()
     *
     * @param mixed $option
     * @return
     */
    function setProfOptions( $option )
    {
        if ( is_array( $option ) )
        {
            for ( $i = 0; $i < count( $option ); $i++ )
            {
                $this->setProfOptions( $option[$i] );
            }
        }
        else
        {
            if ( is_object( $option ) )
            {
                $this->_profileOptions[] = &$option;
            }
        }
    }

    /**
     * ZariliaProfileItem::getProfOptions()
     *
     * @return
     */
    function &getProfOptions()
    {
        $value = $this->_profileOptions;
        return $value;
    }
}

/**
 * ZariliaProfileItemHandler
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: profileitem.php,v 1.2 2007/05/05 11:12:12 catzwolf Exp $
 * @access public
 */
class ZariliaProfileItemHandler extends ZariliaPersistableObjectHandler
{
    /**
     * ZariliaProfileItemHandler::ZariliaProfileItemHandler()
     *
     * @param mixed $db
     */
    function ZariliaProfileItemHandler( &$db )
    {
        $this->ZariliaPersistableObjectHandler( $db, 'profile', 'zariliaprofileitem', 'profile_id' );
    }

    /**
     * ZariliaProfileItemHandler::getProfileObj()
     *
     * @param array $nav
     * @param integer $profile_modid
     * @param mixed $profilecat_id
     * @return
     */
    function getProfileObj( $nav = array(), $profile_modid = 0, $profilecat_id = 0 )
    {
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'profile_modid', 1 ) );
        if ( $profilecat_id )
        {
            $criteria->add( new Criteria( 'profile_catid', $profilecat_id ) );
        }
        $object['count'] = $this->getCount( $criteria, false );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $object['list'] = $this->getObjects( $criteria, false );
        return $object;
    }
}

?>