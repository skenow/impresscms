<?php 
// $Id: configcategory.php,v 1.1 2007/03/16 02:39:10 catzwolf Exp $
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
 * 
 * @author John Neill AKA Catzwolf <catzwolf@zarilia.com> 
 * @copyright copyright (c) 2006 Zarilia
 */
class ZariliaConfigCategory extends ZariliaObject {
    /**
     * Constructor
     */
    function ZariliaConfigCategory() {
        $this->ZariliaObject();
        $this->initVar( 'confcat_id', XOBJ_DTYPE_INT, null );
        $this->initVar( 'confcat_name', XOBJ_DTYPE_OTHER, null );
        $this->initVar( 'confcat_order', XOBJ_DTYPE_INT, 0 );
        $this->initVar( 'confcat_display', XOBJ_DTYPE_INT, 0 );
    } 
} 

/**
 * ZariliaConfigCategoryHandler
 * 
 * This class is responsible for providing data access mechanisms to the data source 
 * of ZARILIA configuration category class objects.
 * 
 * @package 
 * @author John Neill AKA Catzwolf 
 * @copyright Copyright (c) 2006
 * @version $Id: configcategory.php,v 1.1 2007/03/16 02:39:10 catzwolf Exp $
 * @access public 
 */
class ZariliaConfigCategoryHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaConfigCategoryHandler::ZariliaConfigCategoryHandler()
     * 
     * @param  $db 
     * @return 
     */
    function ZariliaConfigCategoryHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'configcategory', 'zariliaconfigcategory', 'confcat_id' );
    } 

    function &getCatConfigs( $showall = false, $limit = 0, $start = 0, $sort = 'confcat_order', $order = 'ASC', $id_as_key = false ) {
        if ( $showall == false ) {
            $criteria = new CriteriaCompo( new Criteria( 'confcat_display', 1 ) );
        } else {
            $criteria = new CriteriaCompo();
        } 
        $criteria -> setSort( $sort );
        $criteria -> setOrder( $order );
        $criteria -> setStart( $start );
        $criteria -> setLimit( $limit );
        $obj = $this->getObjects( $criteria, $id_as_key );
		return $obj;
    } 
} 

?>