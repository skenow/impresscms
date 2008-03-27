<?php
// $Id: formselectdirlist.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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

/**
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */

/**
 * Parent
 */
include_once ZAR_ROOT_PATH . "/class/zariliaform/formselect.php";
// RMV-NOTIFY
/**
 * A select field with a choice of available users
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormSelectDirList extends ZariliaFormSelect {
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param bool $include_anon Include user "anonymous"?
     * @param mixed $value Pre-selected value (or array of them).
     * @param int $size Number or rows. "1" makes a drop-down-list.
     * @param bool $multiple Allow multiple selections?
     */
    function ZariliaFormSelectDirList( $caption, $name, $value = null, $size = 1, $multiple = false, $dirname = null, $prefix = '', $type = array() ) {
        $this->ZariliaFormSelect( $caption, $name, $value, $size, $multiple );
        $filelist = $this->getFileListAsArray( $dirname, $prefix, $type );
        $this->addOptionArray( $filelist );
    }

    /*
	*  gets list of all files in a directory
	*/
    function getFileListAsArray2( $dirname, $prefix = '', $type = array() ) {
        $filelist = array();
        if ( substr( $dirname, -1 ) == '/' ) {
            $dirname = substr( $dirname, 0, -1 );
        }
        $string = "";
        foreach( $type as $types ) {
            $string = "\.$types|";
        }
        if ( is_dir( $dirname ) && $handle = opendir( $dirname ) ) {
            while ( false !== ( $file = readdir( $handle ) ) ) {
                if ( !preg_match( "/^[\.]{1,2}$/", $file ) && preg_match( "/(\.mp3|\.jpg|\.png|\.bmp)$/i", $file ) && is_file( $dirname . '/' . $file ) ) {
                    $file = $prefix . $file;
                    $filelist[$file] = $file;
                }
            }
            closedir( $handle );
            asort( $filelist );
            reset( $filelist );
        }
        return $filelist;
    }

    function getFileListAsArray( $dirname, $prefix = "", $type = array() ) {
        $string = "";
        foreach( $type as $types ) {
            $string = "\.$types|";
        }

        $filelist = array();
        if ( $handle = opendir( $dirname ) ) {
            while ( false !== ( $file = readdir( $handle ) ) ) {
                if ( !preg_match( "/^[\.]{1,2}$/", $file ) && preg_match( "/($string)$/i", $file ) ) {
                    $file = $prefix . $file;
                    $filelist[$file] = $file;
                }
            }
            closedir( $handle );
            asort( $filelist );
            reset( $filelist );
        }
        return $filelist;
    }
}

?>
