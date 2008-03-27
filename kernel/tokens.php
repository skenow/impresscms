<?php
// $Id: tokens.php,v 1.1 2007/03/16 02:39:12 catzwolf Exp $
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

class ZariliaTokens extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaTokens() {
        $this->zariliaObject();
        $this->initVar( 'security_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'security_title', XOBJ_DTYPE_TXTBOX, null, false, 60 );
        $this->initVar( 'security_login', XOBJ_DTYPE_TXTBOX, null, false, 10 );
        $this->initVar( 'security_password', XOBJ_DTYPE_TXTBOX, null, false, 10 );
        $this->initVar( 'security_sessionid', XOBJ_DTYPE_TXTBOX, null, false, 32 );
        $this->initVar( 'security_ip', XOBJ_DTYPE_TXTBOX, null, false, 10 );
        $this->initVar( 'security_date', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'security_user_agent', XOBJ_DTYPE_TXTBOX, null, true, 255 );
        $this->initVar( 'security_remote_addr', XOBJ_DTYPE_TXTBOX, null, true, 20 );
        $this->initVar( 'security_http_referer', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'security_request_uri', XOBJ_DTYPE_TXTBOX, null, true, 255 );
    }
}

/**
 * ZariliaTokensHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: tokens.php,v 1.1 2007/03/16 02:39:12 catzwolf Exp $
 * @access public
 */
class ZariliaTokensHandler extends ZariliaPersistableObjectHandler {
    var $secs = 1; // Number or secounds between a request
    var $keep_secs = 600; // Number of secounds to keep the user registered
    /**
     * ZariliaTokensHandler::ZariliaTokensHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaTokensHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'security', 'ZariliaTokens', 'security_id', 'security_title' );
    }

    function addLog( $title = '', $login = '', $pass = '' ) {
        global $zariliaAddon, $zariliaOption;
        $token = $this->create();
        $ipaddress = $this->_get_ip_list();
        // $stat_array['stat_user_agent'] = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? $_SERVER['HTTP_USER_AGENT'] : "";
        // $stat_array['stat_remote_addr'] = ( isset( $_SERVER['REMOTE_ADDR'] ) ) ? $_SERVER['REMOTE_ADDR'] : "";
        // $stat_array['stat_http_referer'] = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? $_SERVER['HTTP_REFERER'] : "";
        // $stat_array['stat_request_uri'] = ( isset( $_SERVER['REQUEST_URI'] ) ) ? $_SERVER['REQUEST_URI'] : "";
        // $stat_array['stat_request_addon'] = ( is_object( $zariliaAddon ) ) ? $zariliaAddon->getVar( 'name' ) : $zariliaOption['pagetype'];
        $token->setVar( 'security_title', $title );
        $token->setVar( 'security_login', $login );
        $token->setVar( 'security_rport', $_SERVER['REMOTE_PORT'] );
        $token->setVar( 'security_rmethod', $_SERVER['REQUEST_METHOD'] );
        // $token->setVar( 'security_password', $pass );
        $token->setVar( 'security_sessionid', session_id() );
        $token->setVar( 'security_ip', $ipaddress[0] );
        $token->setVar( 'security_date', $_SERVER['REQUEST_TIME'] );
        $token->setVar( 'security_user_agent', $_SERVER['HTTP_USER_AGENT'] );
        $token->setVar( 'security_remote_addr', @$stat_array['stat_remote_addr'] );
        $token->setVar( 'stat_http_referer', @$stat_array['stat_http_referer'] );
        $token->setVar( 'stat_request_uri', @$stat_array['stat_request_uri'] );
        $this->insert( $token );
    }

    function _get_ip_list() {
        $tmp = array();
        if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) && strpos( $_SERVER['HTTP_X_FORWARDED_FOR'], ',' ) ) {
            $tmp += explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
        } elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $tmp[] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        $tmp[] = $_SERVER['REMOTE_ADDR'];
        return $tmp;
    }

    /**
     * ZariliaSectionHandler::getSectionObj()
     *
     * @param array $nav
     * @param mixed $security_id
     * @return
     */
    function getSecurityObj( $nav = null ) {
        $criteria = new CriteriaCompo();
        // if ( $nav['security_display'] != 3 ) {
        // $criteria->add( new Criteria( 'security_display', $nav['security_display'] ) );
        // }
        $object['count'] = $this->getCount( $criteria, false );
        if ( !empty( $nav ) ) {
            $criteria->setSort( $nav['sort'] );
            $criteria->setOrder( $nav['order'] );
            $criteria->setStart( $nav['start'] );
            $criteria->setLimit( $nav['limit'] );
        }
        $object['list'] = $this->getObjects( $criteria, false );
        return $object;
    }
}

?>