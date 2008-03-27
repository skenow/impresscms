<?php
// $Id: events.php,v 1.3 2007/04/24 11:36:59 catzwolf Exp $
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
// -------------------------------------------------------------------------//                                              //
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * Events system class
 *
 * @package kernel
 * @subpackage events
 */
class ZariliaEvents
extends ZariliaObject {
    var $need_delete = false;

    /**
     * constructor
     */
    function ZariliaEvents() {
        $this->zariliaObject();
        $this->initVar( "id", XOBJ_DTYPE_INT, null, false );
        $this->initVar( "NextTime", XOBJ_DTYPE_INT, time(), true );
        $this->initVar( "RepeatNum", XOBJ_DTYPE_INT, -1, true );
        $this->initVar( "RepeatInterval", XOBJ_DTYPE_INT, null, true );
        $this->initVar( "RepeatSystem", XOBJ_DTYPE_INT, 0, true );
        $this->initVar( "Code", XOBJ_DTYPE_SOURCE, null, true );
    }

    /**
     * Executes code attached to event
     */
    function doEvent() {
        require_once ZAR_ROOT_PATH . '/class/class.vmachine.php';
        $vm = new vMachine();
        $code = "\$zariliaDB = &ZariliaDatabaseFactory::getDatabaseConnection();
				" . $this->getVar( 'code' );
        $return = $vm->exec( $code, true );
        $count = $this->getVar( 'RepeatNum' );
        if ( $count != 0 ) { // event will be not repeated forever
            $count--;
            if ( $count < 1 ) {
                $count--; // we need to make number lower then 0
            }
            $this->setVar( 'RepeatNum', $count );
        }
        $this->setVar( 'NextTime', strtotime( '+' . $this->getVar( 'RepeatInterval' ) . ' ' . $this->getRepeatSystemAsText() ) );
        return $return;
    }

    /**
     * gets repath var
     */
    function getRepeatSystemAsText() {
        switch ( $this->getVar( 'RepeatSystem' ) ) {
            case 1:
                return 'hour';
            case 2:
                return 'day';
            case 3:
                return 'week';
            case 4:
                return 'month';
            case 5:
                return 'year';
        }
        return 'minute';
    }

    /**
     * ZariliaEvents::formEdit()
     *
     * @param mixed $caption
     * @return
     */
    function formEdit() {
        if ( is_readable( ZAR_ROOT_PATH . '/kernel/kernel_forms/events.php' ) ) {
            require_once ZAR_ROOT_PATH . '/kernel/kernel_forms/events.php';
        }
    }

    /**
     * ZariliaEvents::setDate()
     *
     * @param string $format
     * @return
     */
    function setDate() {
        global $_REQUEST;
        $_date = zarilia_cleanRequestVars( $_REQUEST, 'NextTime', '' );
        if ( isset( $_date['date'] ) && !empty( $_date['date'] ) ) {
            if ( strpos( $_date['time'], '%T' ) === false ) {
                $date = strtotime( $_date['date'] ) + intval( $_date['time'] );
            } else {
                if ( $_date['time'] == 0 ) $_date['time'] = '00:00';
                $date = strtotime( str_replace( '%T', $_date['time'], $_date['date'] ) );
            }
        } else {
            $date = '';
        }
        $this->setVar( 'NextTime', $date );
    }
}

/**
 * Events system handler
 *
 * @package kernel
 * @subpackage events
 */
class ZariliaEventsHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaEventsHandler::ZariliaEventsHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaEventsHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'events', 'zariliaevents', 'id' );
    }

    /**
     * Gets selected type current events for current user
     *
     * @param int $ type
     * @return ZariliaEvents
     */
    function &getEvents() {
        $criteria = new CriteriaCompo();
        $criteria->setSort( 'id' );
        $criteria->setOrder( 'ASC' );
        $criteria->add( new Criteria( 'NextTime', time() , '<' , null, "'%s'" ) );
        $criteria->add( new Criteria( 'RepeatNum', 0 , '>=' , null, "'%s'" ) );
        return $this->getObjects( $criteria, false );
    }

    /**
     * ZariliaEventsHandler::getEventsObj()
     *
     * @param mixed $nav
     * @param mixed $opt
     * @return
     */
    function &getEventsObj( $nav, $opt ) {
        $criteria = new CriteriaCompo();
        $obj['count'] = $this->getCount( $criteria );
		
        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $obj['list'] = $this->getObjects( $criteria, true );
        return $obj;
    }

    /**
     * Executes events
     */
    function doEvents() {
        if (!($events = $this->getEvents())) return;
        foreach ( $events as $event ) {
            $event->doEvent();
            if ( $event->getVar( 'RepeatNum' ) < 0 ) {
                if ( !isset( $criteria ) ) {
                    $criteria = new CriteriaCompo();
                }
                $criteria->add( new Criteria( 'id', $event->getVar( 'id' ) ) );
            }
        }
        if ( isset( $criteria ) ) {
            $this->deleteAll( $criteria );
        }
    }

    /**
     * Gets automated tasks executor (ATasks) object
     */
    function &getATaskObj() {
        return true;
		static $atask = null;
        if ( is_object( $atask ) ) return $atask;

        global $zariliaConfig;
        include_once ZAR_ROOT_PATH . '/class/atasks/' . $zariliaConfig['events_system'] . '.php';
        $class = 'AutomatedTasks_' . ucfirst( $zariliaConfig['events_system'] );
        if ( class_exists( $class ) ) {
            $atask = $class();
            return $atask;
        } else {
            return false;
        }
    }
}

?>