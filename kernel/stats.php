<?php
// $Id: stats.php,v 1.1 2007/03/16 02:39:12 catzwolf Exp $
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
 * Zarilia Stats Class
 *
 * @package kernel
 * @author John Neill AKA Catzwolf
 * @copyright (c) 2006 Zarilia
 */
class zariliaStats extends zariliaObject {
    /**
     * constructor
     */
    function zariliaStats() {
        $this->zariliaObject();
        $this->initVar( 'stat_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'stat_date', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'stat_user_agent', XOBJ_DTYPE_TXTBOX, null, true, 255 );
        $this->initVar( 'stat_remote_addr', XOBJ_DTYPE_TXTBOX, null, true, 20 );
        $this->initVar( 'stat_http_referer', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'stat_request_uri', XOBJ_DTYPE_TXTBOX, null, true, 255 );
        $this->initVar( 'stat_request_addon', XOBJ_DTYPE_TXTBOX, null, true, 255 );
        $this->initVar( 'stat_unique', XOBJ_DTYPE_INT, null, false );
    }
}

/**
 * zariliaStatsHandler
 *
 * @package
 * @author Catzwolf
 * @copyright Copyright (c) 2005
 * @version $Id: stats.php,v 1.1 2007/03/16 02:39:12 catzwolf Exp $
 * @access public
 */
class zariliaStatsHandler extends ZariliaPersistableObjectHandler {
    // Get times for the year
    var $summary = array();
    var $time;
    var $hour = 3600;
    var $day = 86400;
    var $week = 604800;
    var $month = 2592000;
    var $year = 31536000;
    var $hour_h = 0;
    var $hour_v = 0;
    var $day_h = 0;
    var $day_v = 0;
    var $week_h = 0;
    var $week_v = 0;
    var $month_h = 0;
    var $month_v = 0;

    /**
     * constructor
     */
    function zariliaStatsHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'stats', 'zariliaStats', 'stat_id', 'stat_date' );
    }

    /**
     * zariliaStatsHandler::getStatPages()
     *
     * @param integer $limit
     * @param integer $start
     * @param string $sort
     * @param string $order
     * @return
     */
    function getStatPages( $limit = 0, $start = 0, $sort = 'stat_id', $order = 'ASC' ) {
        $criteria = new CriteriaCompo();
        $criteria->setSort( $sort );
        $criteria->setOrder( $order );
        $criteria->setStart( $start );
        $criteria->setLimit( $limit );
        // $criteria -> add( new Criteria( 'deptid', '0', '>' ) );
        return $this->getObjects( $criteria, true );
    }

    /**
     * zariliaStatsHandler::getDateStatPages()
     *
     * @param mixed $date
     * @param integer $limit
     * @param integer $start
     * @param string $sort
     * @param string $order
     * @return
     */
    function getDateStatPages( $date, $limit = 0, $start = 0, $sort = 'stat_id', $order = 'ASC' ) {
        $criteria = new CriteriaCompo();
        $criteria->setSort( $sort );
        $criteria->setOrder( $order );
        $criteria->setStart( $start );
        $criteria->setLimit( $limit );
        $criteria->add( new Criteria( 'stat_date', $date, '>' ) );
        return $this->getObjects( $criteria, false );
    }

    /**
     * zariliaStatsHandler::highestData()
     *
     * @param string $data
     * @return
     */
    function highestData( $data = '' ) {
        if ( !is_array( $data ) ) {
            return intval( $data );
        }

        $ret = '';
        foreach( $data as $k ) {
            if ( intval( $k ) > intval( $ret ) ) {
                $ret = $k;
            }
        }
        return intval( $ret );
    }

    function gatherStats() {
        global $stat_array;

        $_SESSION['hit'] = ( !isset( $_SESSION['hit'] ) ) ? true : false;

        $stat_array['stat_date'] = time();
        $parsed = parse_url( ZAR_URL );
        $request = isset( $parsed['scheme'] ) ? $parsed['scheme'] . '://' : 'http://';
        if ( isset( $parsed['host'] ) ) {
            $request .= isset( $parsed['port'] ) ? $parsed['host'] . ':' . $parsed['port'] : $parsed['host'];
        } else {
            $request .= zarilia_getenv( 'HTTP_HOST' );
        }

		$stat_array['stat_user_agent'] = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? $_SERVER['HTTP_USER_AGENT'] : "";
        $stat_array['stat_remote_addr'] = ( isset( $_SERVER['REMOTE_ADDR'] ) ) ? $_SERVER['REMOTE_ADDR'] : "";
        $stat_array['stat_http_referer'] = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? $_SERVER['HTTP_REFERER'] : "";
        $stat_array['stat_request_uri'] = ( isset( $_SERVER['REQUEST_URI'] ) ) ? $_SERVER['REQUEST_URI'] : "";
        $stat_array['stat_unique'] = ( !$_SESSION['hit'] ) ? 1 : 0;
        $stat_array['stat_request_addon'] = ( is_object( $zariliaAddon ) ) ? $zariliaAddon->getVar( 'name' ) : $zariliaOption['pagetype'];

        if ( $zariliaConfig['my_ip'] != $_SERVER['REMOTE_ADDR'] ) {
            $stats_handler = &zarilia_gethandler( 'stats' );
            $stats_obj = $stats_handler->create();
            $stats_obj->setVars( $stat_array );
            $stats_handler->insert( $stats_obj, false );
        }
    }
}

?>