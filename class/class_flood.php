<?php
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
/**
 * flood_protection
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: class_flood.php,v 1.2 2007/03/30 22:05:45 catzwolf Exp $
 * @access public
 */
class flood_protection {
    var $secs = 20/* $count */;
    var $keep_secs = 60;
    var $useflat;
    var $hardstop;

    /**
     * Constructor
     */
    function flood_protection() {
        $this->db = &ZariliaDatabaseFactory::getDatabaseConnection();
        $this->table = $this->db->prefix( 'flood' );
        $this->_ip = getip();
    }

    /**
     * flood_protection::_register_user()
     *
     * @param mixed $this ->_ip
     * @return
     */
    function _register_user() {
        $sql = sprintf( 'INSERT INTO %s ( ip, time ) VALUES ( %s, %u )', $this->table, $this->db->qstr( $this->_ip ), time() );
        if ( !$result = $this->db->Execute( $sql ) ) {
            return false;
        }
        return true;
    }

    /**
     * flood_protection::_find_user()
     *
     * @param mixed $this ->_ip
     * @return
     */
    function _find_user() {
        $result = $this->db->Execute( 'SELECT time FROM ' . $this->table . ' WHERE ip=' . $this->db->qstr( $this->_ip ) . ' LIMIT 1' );
        $count = $this->db->getRowsNum( $result );
        if ( $count > 0 ) {
            return true;
        }
        return false;
    }

    /**
     * flood_protection::_user_flooding()
     *
     * @param mixed $this ->_ip
     * @return
     */
    function _user_flooding() {
        $sql = 'SELECT time FROM ' . $this->table . ' WHERE ip=' . $this->db->qstr( $this->_ip ) . ' AND time >=' . ( time() - $this->secs ) . ' LIMIT 1';
        $result = $this->db->Execute( $sql );
        $count = $this->db->getRowsNum( $result );
        if ( $count > 0 ) {
            return true;
        }
        return false;
    }

    /**
     * flood_protection::_update_user()
     *
     * @param mixed $this ->_ip
     * @return
     */
    function _update_user() {
        $sql = sprintf( "UPDATE %s SET time = " . time() . " WHERE ip = %u", $this->table, $this->db->qstr( $this->_ip ) );
        if ( !$result = $this->db->Execute( $sql ) ) {
            return false;
        }
        return true;
    }

    /**
     * flood_protection::_remove_users()
     *
     * @return
     */
    function _remove_users() {
        $this->db->Execute( sprintf( "DELETE FROM %s WHERE time <= %u", $this->table, time() - $this->keep_secs ) );
    }

    function _check_request_file() {
        $zariliaUserId = ( isset( $_SESSION['zariliaUserId'] ) && intval( $_SESSION['zariliaUserId'] ) > 0 ) ? $_SESSION['zariliaUserId'] : 0;
        $iptime = ( $zariliaUserId > 0 ) ? 20 : 10;
        $ipmaxvisit = ( $zariliaUserId > 0 ) ? 20 : 10;
        $ippenalty = 60; // Seconds before visitor is allowed back
        $iplogdir = ZAR_ROOT_PATH . "/logs/";
        $iplogfile = "iplog.dat";
        $ipfile = substr( md5( $this->_ip ), -2 );
        $oldtime = 0;
        $time = time();

        if ( file_exists( $iplogdir . $ipfile ) ) {
            $oldtime = filemtime( $iplogdir . $ipfile );
        }
        if ( $oldtime < $time ) {
            $oldtime = $time;
        }
        $newtime = $oldtime + $iptime;
        // echo $newtime;
        if ( $newtime >= ( $time + $iptime * $ipmaxvisit ) ) {
            touch( $iplogdir . $ipfile, $time + $iptime * ( $ipmaxvisit-1 ) + $ippenalty );
            $oldref = ( isset( $_SERVER['HTTP_REFERER'] ) ) ?$_SERVER['HTTP_REFERER'] : '';
            header( "HTTP/1.0 503 Service Temporarily Unavailable" );
            header( "Connection: close" );
            header( "Content-Type: text/html" );
            echo "<html><body bgcolor=#999999 text=#ffffff link=#ffff00>
					<font face='Verdana, Arial'><p><b>
					<h1>Temporary Access Denial</h1>Too many quick page views by your IP address (more than " . $ipmaxvisit . " visits within " . $iptime . " seconds).</b>
					";
            echo "<br />Please wait " . $ippenalty . " seconds and reload.</p></font></body></html>";
            touch( $iplogdir . $iplogfile ); //create if not existing
            $fp = fopen( $iplogdir . $iplogfile, "a" );
            $yourdomain = $_SERVER['HTTP_HOST'];
            if ( $fp ) {
                $useragent = "<unknown user agent>";
                if ( isset( $_SERVER["HTTP_USER_AGENT"] ) )
                    $useragent = $_SERVER["HTTP_USER_AGENT"];
                fputs( $fp, $_SERVER["REMOTE_ADDR"] . " " . date( "d/m/Y H:i:s" ) . " " . $useragent . "\n" );
                fclose( $fp );
                $yourdomain = $_SERVER['HTTP_HOST'];
                // the symbol before @mail means 'supress errors' so you wont see errors on the page if email fails.
                if ( $_SESSION['reportedflood'] < 1 && ( $newtime < $time + $iptime + $iptime * $ipmaxvisit ) )
                    @mail( 'flood_alert@' . $yourdomain, 'site flooded by ' . $_SESSION['zariliaUserId'] . ' '
                         . $_SERVER['REMOTE_ADDR'], 'http://' . $yourdomain . ' rapid website access flood occured and ban for IP ' . $_SERVER['REMOTE_ADDR'] . ' at http://' . $yourdomain . $_SERVER['REQUEST_URI'] . ' from ' . $oldref . ' agent ' . $_SERVER['HTTP_USER_AGENT'] . ' '
                         . $_SESSION['zariliaUserId'] . ' ' . $_SESSION['zariliaUserId'], "From: " . $yourdomain . "\n" );
                $_SESSION['reportedflood'] = 1;
            }
            exit();
        } else {
            $_SESSION['reportedflood'] = 0;
        }
        // echo( "loaded " . $_SESSION['zariliaUserId'] . $iplogdir . $iplogfile . $ipfile . $newtime );
        touch( $iplogdir . $ipfile, $newtime ); //this just updates the IP file access date or creates a new file if it doesn't exist in /iplog
    }

    /*public functions here*/
    // check to see if the user is flooding
    function check_request( $type = '' ) {
        switch ( $type ) {
            case 'database':
                if ( $this->_find_user() ) {
                    $return = $this->_user_flooding();
                    $this->_update_user();
                    $this->_remove_users();
                    return $return;
                } else {
                    $this->_register_user();
                    $this->_remove_users();
                    return false;
                }
                break;
            case 'file':
            default:
                $this->_check_request_file();
                break;
        } // switch
    }
}

function flatfile() {
}

?>