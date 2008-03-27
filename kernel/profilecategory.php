<?php
// $Id: profilecategory.php,v 1.2 2007/05/05 11:12:12 catzwolf Exp $
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
 * ZariliaProfileCategory
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: profilecategory.php,v 1.2 2007/05/05 11:12:12 catzwolf Exp $
 * @access public
 **/
class ZariliaProfileCategory extends ZariliaObject {
    /**
     * Constructor
     */
    function ZariliaProfileCategory() {
        $this->ZariliaObject();
        $this->initVar( 'profilecat_id', XOBJ_DTYPE_INT, null );
        $this->initVar( 'profilecat_name', XOBJ_DTYPE_OTHER, null );
        $this->initVar( 'profilecat_order', XOBJ_DTYPE_INT, 0 );
        $this->initVar( 'profilecat_desc', XOBJ_DTYPE_TXTAREA, null, false, null );
        $this->initVar( 'profilecat_display', XOBJ_DTYPE_INT, 1, false );
    }

    /**
     * ZariliaProfileCategory::formEdit()
     *
     * @return
     */
    function formEdit() {
        require_once ZAR_ROOT_PATH . '/kernel/kernel_forms/profilecategory.php';
        return $form;
    }
}

/**
 * ZariliaProfileCategoryHandler
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: profilecategory.php,v 1.2 2007/05/05 11:12:12 catzwolf Exp $
 * @access public
 **/
class ZariliaProfileCategoryHandler extends ZariliaPersistableObjectHandler {

    /**
    * Constructor
    *
    **/
	function ZariliaProfileCategoryHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'profilecategory', 'zariliaprofilecategory', 'profilecat_id', 'profilecat_name', 'profilecat_read' );
    }

    /**
     * ZariliaProfileCategoryHandler::getProfileObj()
     *
     * @param array $nav
     * @param mixed $profilecat_id
     * @return
     */
    function getProfileObj( $nav = array(), $profilecat_id = 0 ) {
        $criteria = new CriteriaCompo();
        if ( $profilecat_id > 0 ) {
            $criteria->add( new Criteria( 'profilecat_id', intval( $profilecat_id ) ) );
        }
        $object['count'] = $this->getCount( $criteria, false );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $object['list'] = $this->getObjects( $criteria, false );
        return $object;
    }

    /**
     * ZariliaProfileCategoryHandler::getList()
     *
     * @param integer $groups
     * @param string $perm
     * @param string $value
     * @return
     */
/*    function &getList( $groups = 0, $perm = 'profilecat_read', $value = 'profilecat_name' ) {
        global $zariliaUser;

		$criteria = new CriteriaCompo();

        if ( $groups > 0 ) {
            $criteriaTray = new CriteriaCompo();
            $_groups = ( is_object( $zariliaUser ) ) ? $zariliaUser->getGroups() : ZAR_GROUP_ANONYMOUS;
            foreach ( $_groups as $gid ) {
                $criteriaTray->add( new Criteria( 'profilecat_read', $gid ), 'OR' );
            }
            $criteria->add( $criteriaTray );
            if ( $perm == 'imgcat_read' ) {
                $criteria->add( new Criteria( 'gperm_name', $perm ) );
                $criteria->add( new Criteria( 'gperm_modid', 1 ) );
            }
        }

        $categories = &$this->getObjects( $criteria, true );
        $ret = array();
        foreach ( array_keys( $categories ) as $i ) {
            $ret[$i] = $categories[$i]->getVar( $value );
        }
        return $ret;
    }*/
}

?>