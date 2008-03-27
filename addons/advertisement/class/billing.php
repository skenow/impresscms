<?php
// $Id: billing.php,v 1.2 2007/03/30 22:03:30 catzwolf Exp $
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

/**
 * Zarilia Banners
 *
 * @package kernel
 * @author John Neill AKA Catzwolf
 * @copyright (c) 2006 Zarilia
 */
class ZariliaBilling extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaBilling( $id = null ) {
        $this->zariliaObject();
        $this->initVar( 'b_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'b_cid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'b_type', XOBJ_DTYPE_TXTBOX, null, false, 20 );
        $this->initVar( 'b_length', XOBJ_DTYPE_TXTBOX, null, false, 20 );
        $this->initVar( 'b_sizew', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'b_sizeh', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'b_weekly', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'b_monthly', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'b_yearly', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'b_image', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'b_active', XOBJ_DTYPE_INT, null, false );
    }
}

/**
 * ZariliaBannersHandler
 *
 * @package
 * @author Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: billing.php,v 1.2 2007/03/30 22:03:30 catzwolf Exp $
 * @access public
 */
class ZariliaBillingHandler extends ZariliaPersistableObjectHandler {
	/**
     * ZariliaBannersHandler::ZariliaBannersAddsHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaBillingHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'billing', 'ZariliaBilling', 'b_id' );
	}

    /**
     * ZariliaBillingHandler::getBannerAddObj()
     *
     * @param mixed $nav
     * @param mixed $is_active
     * @return
     **/
    function getBannerAddObj( $nav, $is_active = false ) {
        $criteria = new CriteriaCompo();
        if ( $is_active == true ) {
            $criteria->add ( new Criteria( 'add_active', 1, '=' ) );
        }
        $obj['count'] = $this->getCount( $criteria, false );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $obj['list'] = $this->getObjects( $criteria, false );
        return $obj;
    }

}

?>