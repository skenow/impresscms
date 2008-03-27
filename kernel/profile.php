<?php
// $Id: profile.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
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

require_once ZAR_ROOT_PATH . '/kernel/profileoption.php';
require_once ZAR_ROOT_PATH . '/kernel/profileitem.php';

/**
 * ZariliaProfileHandler
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: profile.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
 * @access public
 **/
class ZariliaProfileHandler {
    var $_piHandler;
    var $_poHandler;
    var $_cachedProfiles = array();

    /**
     * ZariliaProfileHandler::ZariliaProfileHandler()
     *
     * @param mixed $db
     */
    function ZariliaProfileHandler( &$db )
    {
        $this->_piHandler = new ZariliaProfileItemHandler( $db );
        $this->_poHandler = new ZariliaProfileOptionHandler( $db );
    }

    /**
     * ZariliaProfileHandler::createProfile()
     *
     * @return
     */
    function &createProfile()
    {
        return $this->_piHandler->create();
    }

    /**
     * ZariliaProfileHandler::getProfile()
     *
     * @param mixed $id
     * @param mixed $withoptions
     * @return
     */
    function &getProfile( $id, $withoptions = false )
    {
        $profile = &$this->_piHandler->get( $id );
        if ( $withoptions == true ) {
            $profile->setProfOptions( $this->getProfileOptions( new Criteria( 'profile_id', $id ) ) );
        }
        return $profile;
    }

    /**
     * ZariliaProfileHandler::insertProfile()
     *
     * @param mixed $profile
     * @return
     */
    function insertProfile( &$profile )
    {
        if ( !$this->_piHandler->insert( $profile ) ) {
            return false;
        }
        $options = &$profile->getProfOptions();
        $count = count( $options );
        $profile_id = $profile->getVar( 'profile_id' );
        for ( $i = 0; $i < $count; $i++ ) {
            $options[$i]->setVar( 'profile_id', $profile_id );
            if ( !$this->_poHandler->insert( $options[$i] ) ) {
                // echo $options[$i] -> getErrors();
            }
        }
        if ( !empty( $this->_cachedProfiles[$profile->getVar( 'profile_modid' )][$profile->getVar( 'profile_catid' )] ) ) {
            unset ( $this->_cachedProfiles[$profile->getVar( 'profile_modid' )][$profile->getVar( 'profile_catid' )] );
        }
        return true;
    }

    /**
     * ZariliaProfileHandler::deleteProfile()
     *
     * @param mixed $profile
     * @return
     */
    function deleteProfile( &$profile )
    {
        if ( !$this->_piHandler->delete( $profile ) ) {
            return false;
        }
        $options = &$profile->getProfOptions();
        $count = count( $options );
        if ( $count == 0 ) {
            $options = &$this->getProfileOptions( new Criteria( 'profile_id', $profile->getVar( 'profile_id' ) ) );
            $count = count( $options );
        }
        if ( is_array( $options ) && $count > 0 ) {
            for ( $i = 0; $i < $count; $i++ ) {
                $this->_poHandler->delete( $options[$i] );
            }
        }
        if ( !empty( $this->_cachedProfiles[$profile->getVar( 'profile_modid' )][$profile->getVar( 'profile_catid' )] ) ) {
            unset ( $this->_cachedProfiles[$profile->getVar( 'profile_modid' )][$profile->getVar( 'profile_catid' )] );
        }
        return true;
    }

    /**
     * ZariliaProfileHandler::getProfiles()
     *
     * @param mixed $criteria
     * @param mixed $id_as_key
     * @param mixed $with_options
     * @return
     */
    function &getProfiles( $criteria = null, $id_as_key = false, $with_options = false )
    {
        $ret = $this->_piHandler->getObjects( $criteria, $id_as_key );
        return $ret;
    }

    /**
     * ZariliaProfileHandler::getProfileDetails()
     *
     * @return
     */
    function &getProfileDetails()
    {
        $ret = $this->_piHandler->getProfileDetails( $criteria = null, $id_as_key = false, $as_object = true );
        return $ret;
    }

    /**
     * ZariliaProfileHandler::getProfileCount()
     *
     * @param mixed $criteria
     * @return
     */
    function getProfileCount( $criteria = null )
    {
        return $this->_piHandler->getCount( $criteria );
    }

    /**
     * ZariliaProfileHandler::getProfilesByCat()
     *
     * @param mixed $category
     * @param integer $addon
     * @param mixed $profile_sectid
     * @return
     */
    function &getProfilesByCat( $category, $addon = 0, $profile_sectid = 1 )
    {
        static $_cachedProfiles;
        if ( !empty( $_cachedProfiles[$addon][$category] ) ) {
            return $_cachedProfiles[$addon][$category];
        } else {
            $ret = array();
            $criteria = new CriteriaCompo( new Criteria( 'profile_modid', intval( $addon ) ) );
            if ( !empty( $category ) ) {
                $criteria->add( new Criteria( 'profile_catid', intval( $category ) ) );
            }
            if ( empty( $profile_sectid ) ) {
                $criteria->add( new Criteria( 'profile_sectid', $profile_sectid ) );
            }
            $profiles = &$this->getProfiles( $criteria, true );
            if ( is_array( $profiles ) ) {
                foreach ( array_keys( $profiles ) as $i ) {
                    $ret[$profiles[$i]->getVar( 'profile_name' )] = $profiles[$i]->getProfValueForOutput();
                }
            }
            $_cachedProfiles[$addon][$category] = &$ret;
            return $ret;
        }
    }

    /**
     * ZariliaProfileHandler::createProfileOption()
     *
     * @return
     */
    function &createProfileOption()
    {
        return $this->_poHandler->create();
    }

    /**
     * ZariliaProfileHandler::getProfileOption()
     *
     * @param mixed $id
     * @return
     */
    function &getProfileOption( $id )
    {
        return $this->_poHandler->get( $id );
    }

    /**
     * ZariliaProfileHandler::getProfileOptions()
     *
     * @param mixed $criteria
     * @param mixed $id_as_key
     * @return
     */
    function &getProfileOptions( $criteria = null, $id_as_key = false )
    {
        return $this->_poHandler->getObjects( $criteria, $id_as_key );
    }

    /**
     * ZariliaProfileHandler::getProfileOptionsCount()
     *
     * @param mixed $criteria
     * @return
     **/
    function getProfileOptionsCount( $criteria = null )
    {
        return $this->_poHandler->getCount( $criteria );
    }

    /**
     * ZariliaProfileHandler::getProfileList()
     *
     * @param mixed $profile_modid
     * @param integer $profile_catid
     * @param mixed $profile_sectid
     * @return
     **/
    function &getProfileList( $profile_modid, $profile_catid = 0, $profile_sectid = 1 )
    {
        if ( !empty( $this->_cachedProfiles[$profile_modid][$profile_catid] ) ) {
            return $this->_cachedProfiles[$profile_modid][$profile_catid];
        } else {
            $criteria = new CriteriaCompo( new Criteria( 'profile_modid', $profile_modid ) );
            if ( empty( $profile_catid ) ) {
                $criteria->add( new Criteria( 'profile_catid', $profile_catid ) );
            }
            if ( empty( $profile_sectid ) ) {
                $criteria->add( new Criteria( 'profile_sectid', $profile_sectid ) );
            }
            $profiles = &$this->_piHandler->getObjects( $criteria );
            $profcount = count( $profiles );
            $ret = array();
            for ( $i = 0; $i < $profcount; $i++ ) {
                $ret[$profiles[$i]->getVar( 'profile_name' )] = $profiles[$i]->getProfValueForOutput();
            }
            $this->_cachedProfiles[$profile_modid][$profile_catid] = &$ret;
            return $ret;
        }
    }

    function deleteAll( $criteria = null )
    {
        return $this->_piHandler->deleteAll( $criteria );
    }

    function getProfileObj( $nav, $profile_modid = 1, $profilecat_id )
    {
        return $this->_piHandler->getProfileObj( $nav, $profile_modid, $profilecat_id );
    }

    /**
     * ZariliaProfileHandler::getProfileCount()
     *
     * @param mixed $criteria
     * @return
     */
    function setSubmit()
    {
        return $this->_piHandler->setSubmit( 'fct', 'userprofiles');
    }
}

?>
