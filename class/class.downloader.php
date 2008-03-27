<?php
// $Id: class.downloader.php,v 1.1 2007/03/16 02:38:58 catzwolf Exp $
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
 * Sends non HTML files through a http socket
 *
 * @package kernel
 * @subpackage core
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaDownloader {
    /**
     * *#@+
     * file information
     */
    var $_filepath;
    var $_filename;
    var $_downname;
    var $_mimetype;
    var $_ext;
    /**
     * *#@-
     */

    /**
     * Constructor
     */
    function ZariliaDownloader( $filepath, $filename )
    {
        // EMPTY
    }

    /**
     * Send the HTTP header
     *
     * @param string $filename
     * @access private
     */
    function _header( $filename )
    {
        if ( function_exists( 'mb_http_output' ) ) {
            mb_http_output( 'pass' );
        }
        header( 'Content-Type: ' . $this -> mimetype );
        if ( preg_match( "/MSIE ([0-9]\.[0-9]{1,2})/", $_SERVER['HTTP_USER_AGENT'] ) ) {
            header( 'Content-Disposition: inline; filename="' . $filename . '"' );
            header( 'Expires: 0' );
            header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header( 'Pragma: public' );
        } else {
            header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
            header( 'Expires: 0' );
            header( 'Pragma: no-cache' );
        }
    }

    /**
     * ZariliaDownloader::addFile()
     *
     * @param string $filepath
     * @param string $newfilename
     */
    function addFile( $filepath, $newfilename = null )
    {
        // EMPTY
    }

    /**
     * ZariliaDownloader::download()
     *
     * @param string $name
     * @param boolean $gzip
     */
    function download( $name, $gzip = true )
    {
        // EMPTY
    }
}

?>