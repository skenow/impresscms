<?php
// $Id: errors.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
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
if ( !class_exists( 'ZariliaObject' ) ) {
    require_once ZAR_ROOT_PATH . '/kernel/object.php';
    require_once ZAR_ROOT_PATH . '/class/criteria.php';
}

/**
 * ZariliaErrors
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: errors.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 */
class ZariliaErrors extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaErrors() {
        $this->zariliaObject();
        $this->initVar( 'errors_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'errors_title', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'errors_description', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'errors_no', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'errors_ip', XOBJ_DTYPE_TXTBOX, null, false, 20 );
        $this->initVar( 'errors_date', XOBJ_DTYPE_LTIME, time(), false );
        $this->initVar( 'errors_report', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'errors_hash', XOBJ_DTYPE_TXTBOX, null, false, 40 );
    }

    /**
     * ZariliaErrors::formView()
     *
     * @return
     */
    function formEdit() {
        echo $this->getVar( 'errors_report', 's' );
        echo "<br />";
    }

    /**
     * ZariliaErrors::getReports()
     *
     * @return
     */
    function getReports() {
        $report = $this->getVar( 'errors_report', 'e' );
        $report = strip_tags( $report );
        $report = zarilia_substr( $report, 0, 60, $trimmarker = '...' );
        return $report;
    }
}

/**
 * ZariliaErrorsHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: errors.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 */
class ZariliaErrorsHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaErrorsHandler::ZariliaErrorsHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaErrorsHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'errors', 'ZariliaErrors', 'errors_id', 'errors_no' );
    }

    function getHashCount( $hash = null ) {
        $criteria = new CriteriaCompo();
        $criteria->add ( new Criteria( 'errors_hash', $hash, '=' ) );
        return $this->getCount( $criteria, false );
    }

    function getErrorsObj( $nav = array(), $pulldate = null ) {
        $criteria = new CriteriaCompo();
        if ( !empty( $pulldate ) ) {
            $addon_date = &$this->getaDate( $pulldate );
            if ( $addon_date['begin'] && $addon_date['end'] ) {
                $criteria->add( new Criteria( 'errors_date', $addon_date['begin'], '>=' ) );
                $criteria->add( new Criteria( 'errors_date', $addon_date['end'], '<=' ) );
            }
        }
        $obj['count'] = $this->getCount( $criteria, false );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $obj['list'] = $this->getObjects( $criteria, false );
        return $obj;
    }

    function getaDate( $exp_value = '', $exp_time = '', $useMonth = 0 ) {
        $_date_arr = array();
        $_date = ( $exp_value ) ? $exp_value : time();
        $d = date( "j", $_date ) ;
        $m = date( "m", $_date ) ;
        $y = date( "Y", $_date ) ;
        if ( $useMonth > 0 ) {
            /**
             * We use +1 for the the previous month and not the next here,
             * if the day var is set to 0 ( You would have thought a neg value would have been correct here but nope!
             * Bloody strange way of doing it if you ask me! :-/ )
             */
            $_date_arr['begin'] = mktime ( 0, 0, 0, $m, 1, $y );
            $_date_arr['end'] = mktime ( 0, 0, 0, $m + 1, 0, $y );
        } else {
            /**
             * 86400 = 1 day, while 86399 = 23 hours and 59 mins and 59 secs
             */
            $_date_arr['begin'] = mktime ( 0, 0, 0, $m, $d, $y );
            $_date_arr['end'] = mktime ( 23, 59, 59, $m, $d, $y );
        }
        return $_date_arr;
    }
}

?>