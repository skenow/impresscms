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
/*
 * gets list of name of directories inside a directory
 */

function getWithSubDirsDirList( $dirname ) {
	$rez = array($bname = basename($dirname));
	$items = getDirList($dirname, true);
	foreach($items as $dir) {
		$rez[] = $bname.'/'.$dir;
		$items = getFileList( $dirname.'/'.$dir);
		foreach ($items as $dfile) {
			$rez[] = $bname.'/'.$dir.'/'.$dfile;
		}
	}
	return $rez;
}

function getDirList( $dirname, $whithSubdirs = false ) {
    $dirlist = array();
    if ( is_dir( $dirname ) && $handle = opendir( $dirname ) ) {
        while ( false !== ( $file = readdir( $handle ) ) ) {
            if ( !preg_match( "/^[.]{1,2}$/", $file ) ) {
                if ( substr( $file,0,1 ) != '.' && is_dir( $dirname . $file ) ) {
                    $dirlist[$file] = $file;
                }
            }
        }
        closedir( $handle );
        asort( $dirlist );
        reset( $dirlist );
    }
	if ($whithSubdirs) {
		$rez = array();
		foreach($dirlist as $dir) {
			$items = getDirList( $dirname.'/'.$dir, true);
			foreach ($items as $ddir) {
				$rez[] = $dir.'/'.$ddir;
			}
		}
		$dirlist = array_merge($dirlist, $rez);
	}
    return $dirlist;
}

function getFileList( $dirname, $prefix = "" ) {
    $filelist = array();
    if ( substr( $dirname, -1 ) == '/' ) {
        $dirname = substr( $dirname, 0, -1 );
    }
    if ( is_dir( $dirname ) && $handle = opendir( $dirname ) ) {
        while ( false !== ( $file = readdir( $handle ) ) ) {
            if ( !preg_match( "/^[\.]{1,2}$/", $file ) && is_file( $dirname . '/' . $file ) ) {
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

/**
 * b_back()
 *
 * @param mixed $option
 * @return
 */
function b_back( $option = null ) {
    if ( !defined( '_INSTALL_B_BACKBUTTON' ) ) {
        define( '_INSTALL_B_BACKBUTTON', 'Back' );
    }
    if ( !isset( $option ) || !is_array( $option ) ) return '';
    $content = '';
    if ( isset( $option[0] ) && $option[0] != '' ) {
        $content .= "<input type='button' class=\"mainbutton\" id=\"b_back\" value='" . _INSTALL_B_BACKBUTTON . "' onclick=\"location='index.php?op=" . htmlspecialchars( $option[0] ) . "'\" /> ";
    } else {
        $content .= "<input type='button' class=\"mainbutton\" id=\"b_back\" value='" . _INSTALL_B_BACKBUTTON . "' onclick=\"javascript:history.back();\" /> ";
    }
    if ( isset( $option[1] ) && $option[1] != '' ) {
        $content .= "<b><< " . htmlspecialchars( $option[1] ) . "</b>";
    }
    return $content;
}

/**
 * b_reload()
 *
 * @param string $option
 * @return
 */
function b_reload( $option ) {
    if ( !defined( '_INSTALL_B_RELOAD' ) ) {
        define( '_INSTALL_B_RELOAD', 'Reload' );
    }
    if ( $option == true ) {
        return '<input class=\"mainbutton\" type="button" value="' . _INSTALL_B_RELOAD . '" onclick="location.reload();" /> ';
    }
}

/**
 * b_next()
 *
 * @param mixed $option
 * @return
 */
function b_next( $option = null ) {
    if ( !defined( '_INSTALL_B_BFORWARD' ) ) {
        define( '_INSTALL_B_BFORWARD', 'Forward' );
    }

    if ( !isset( $option ) || !is_array( $option ) ) return '';
    $content = '';
    if ( isset( $option[1] ) && $option[1] != '' ) {
        $content .= '<b>' . htmlspecialchars( $option[1] ) . '</b> &gt;&gt; ';
    }
    $content .= '<input type="hidden" name="op" value="' . htmlspecialchars( $option[0] ) . '" />';
    $content .= '<input type="submit" class=\"mainbutton\" name="submit" value="' . _INSTALL_B_BFORWARD . '"/>';
    return $content;
}

function b_restart( $dontshow = true ) {
    if ( $dontshow == false ) {
    	return '';
    }
    if ( !defined( '_INSTALL_B_RESTART' ) ) {
        define( '_INSTALL_B_RESTART', 'Restart' );
    }

    return "<input type='button' class=\"mainbutton\" id=\"b_reload\" value='" . _INSTALL_B_RESTART . "' onclick=\"location='index.php?debug=restart'\" /> ";
}

?>