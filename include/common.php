<?php
// $Id: common.php,v 1.5 2007/05/05 11:12:10 catzwolf Exp $
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
defined( "ZAR_MAINFILE_INCLUDED" ) or trigger_error( 'Mainfile not included', E_USER_WARNING );
error_reporting( E_ALL );
set_magic_quotes_runtime( 0 );

global $zariliaSecurity, $zariliaLogger, $zariliaUser;

/**
 * Instantiate security object
 * Check super globals
 */
require_once ZAR_ROOT_PATH . "/class/zariliasecurity.php";
$zariliaSecurity = new ZariliaSecurity();
$zariliaSecurity->checkSuperglobals();

/*Do checks to see if direcotries that require 777 are 777*/
$dir_array = array( 'data' => '2000' );
$error = array();
foreach( $dir_array as $k => $v ) {
    if ( !is_writable( ZAR_ROOT_PATH . "/$k" ) ) {
        if ( @chmod( $k, '0777' ) === false ) {
            $error[] = "<b>Warning:</b> Error: $v";
        }
    }
}
if ( count( $error ) > 0 ) {
    echo "<h3>Startup Errors</h4><p>If you are the webmaster of this website then please visit http://support.zarilia.com regarding the following errors:</p>";
    foreach( $error as $errors ) {
        echo "<div>{$errors}</div>";
    }
    die();
}

/*
    if ( is_dir( ZAR_ROOT_PATH . "/install/" ) ) {
        include_once ZAR_ROOT_PATH."/header.php";
		$error = sprintf( _WARNINSTALL2, ZAR_ROOT_PATH . '/install/' );
        $image = zarilia_img_show( 'important', $error, 'middle', 'png', 'addons/system/images/icons' );
        echo "<div style='text-align: center;'>" . zarilia_error( $error, '', $image, false ) . "</div>";
        include_once ZAR_ROOT_PATH."/footer.php";
        exit();
    }

    if ( is_writable( ZAR_ROOT_PATH . "/siteinfo.php" ) ) {
        include_once ZAR_ROOT_PATH."/header.php";
        $error = sprintf( _WARNINWRITEABLE, ZAR_ROOT_PATH . '/siteinfo.php' );
        $image = zarilia_img_show( 'important', $error, 'middle', 'png', 'addons/system/images/icons' );
        echo "<div style='text-align: center;'>" . zarilia_error( $error, '', $image, false ) . "</div>";
        include_once ZAR_ROOT_PATH."/footer.php";
        exit();
    }
*/

/**
 */
include_once ZAR_ROOT_PATH . '/class/logger.php';
$zariliaLogger = &ZariliaLogger::instance();
$zariliaErrorHandler = &$zariliaLogger;
$zariliaLogger->startTime();
$zariliaLogger->startTime( 'Zarilia Boot Time' );
// require_once ZAR_ROOT_PATH . '/class/class_errorhandler.php';
// $zariliaError = new zariliaErrorHandler();
/**
 * ############## Include defines file ##############
 */
require_once ZAR_ROOT_PATH . '/include/defines.php';

/**
 * ############## Include common functions file ##############
 */
require_once ZAR_ROOT_PATH . '/include/functions.php';
/**
 * #################### Connect to DB ##################
 */
//require_once ZAR_ROOT_PATH . '/class/database/databasefactory.php';
//$zariliaDB = &ZariliaDatabaseFactory::getDatabaseConnection();
require_once ZAR_ROOT_PATH . '/class/adodb_lite/adodb-errorhandler.inc.php';
require_once ZAR_ROOT_PATH . '/class/adodb_lite/adodb.inc.php';
$zariliaDB = ADONewConnection(ZAR_DB_TYPE);
if ( !($result = $zariliaDB->Connect(ZAR_DB_HOST, ZAR_DB_USER, ZAR_DB_PASS, ZAR_DB_NAME)) ) {
    trigger_error( "Database error: could not connect (".$zariliaDB->ErrorMsg().")", E_USER_ERROR );
}
// $GLOBALS['zariliaLogger']->sysRender( E_USER_ERROR, _MA_AD_RUSUREINSFAILED, __FILE__, __LINE__ );
/**
 * ################# Include required files ##############
 */
require_once ZAR_ROOT_PATH . '/kernel/object.php';
require_once ZAR_ROOT_PATH . '/class/criteria.php';

/**
 * #################### Include text sanitizer ##################
 */
include_once ZAR_ROOT_PATH . "/class/class.textsanitizer.php";

/**
 * ################# Load Config Settings ##############
 */
$config_handler = &zarilia_gethandler( 'config' );
$zariliaConfig = &$config_handler->getConfigsByCat( array( ZAR_CONF, ZAR_CONF_LOCALE, ZAR_CONF_SERVER, ZAR_CONF_EVENTS ) );
$zariliaLogger->setDebugmode();

/**
 * #################### Check bad IP settings ##################
 */
$zariliaSecurity->checkBadips();
/**
 * ################ Include version info file ################
 */
include_once ZAR_ROOT_PATH . '/include/version.php';

/**
 * ################ Host abstraction layer ################
 */
if ( !isset( $_SERVER['PATH_TRANSLATED'] ) && isset( $_SERVER['SCRIPT_FILENAME'] ) ) {
    $_SERVER['PATH_TRANSLATED'] = &$_SERVER['SCRIPT_FILENAME']; // For Apache CGI
} elseif ( isset( $_SERVER['PATH_TRANSLATED'] ) && !isset( $_SERVER['SCRIPT_FILENAME'] ) ) {
    $_SERVER['SCRIPT_FILENAME'] = &$_SERVER['PATH_TRANSLATED']; // For IIS/2K now I think :-(
}
if ( empty( $_SERVER[ 'REQUEST_URI' ] ) ) {
    // Under some configs, IIS makes SCRIPT_NAME point to php.exe :-(
    if ( !( $_SERVER[ 'REQUEST_URI' ] = @$_SERVER['PHP_SELF'] ) ) {
        $_SERVER[ 'REQUEST_URI' ] = $_SERVER['SCRIPT_NAME'];
    }
    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $_SERVER[ 'REQUEST_URI' ] .= '?' . $_SERVER[ 'QUERY_STRING' ];
    }
}
if ( !strpos( $_SERVER['REQUEST_URI'], ".php" ) ) {
    if ( strpos( $_SERVER['REQUEST_URI'], "?" ) ) {
        $_SERVER['REQUEST_URI'] = preg_replace( "/(\/\?)/U", "/index.php?", $_SERVER['REQUEST_URI'] );
    } else {
        $_SERVER['REQUEST_URI'] .= "index.php";
    }
}
$zariliaConfig['theme_set'] = is_dir( ZAR_ROOT_PATH . '/themes/' . $zariliaConfig['theme_set'] ) ? $zariliaConfig['theme_set'] : 'default';

/**
 * ################ Login a user with a valid session ################
 */
$zariliaUserIsAdmin = false;
$member_handler = &zarilia_gethandler( 'member' );
$sess_handler = &zarilia_gethandler( 'session' );
if ( $zariliaConfig['use_ssl'] && isset( $_POST[$zariliaConfig['sslpost_name']] ) && $_POST[$zariliaConfig['sslpost_name']] != '' ) {
    session_id( $_POST[$zariliaConfig['sslpost_name']] );
} elseif ( $zariliaConfig['use_mysession'] && $zariliaConfig['session_name'] != '' ) {
    if ( isset( $_COOKIE[$zariliaConfig['session_name']] ) ) {
        session_id( $_COOKIE[$zariliaConfig['session_name']] );
    }
    if ( function_exists( 'session_cache_expire' ) ) {
        session_cache_expire( $zariliaConfig['session_expire'] );
    }
    @ini_set( 'session.gc_maxlifetime', $zariliaConfig['session_expire'] * 60 );
}
session_set_save_handler( array( &$sess_handler, 'open' ), array( &$sess_handler, 'close' ), array( &$sess_handler, 'read' ), array( &$sess_handler, 'write' ), array( &$sess_handler, 'destroy' ), array( &$sess_handler, 'gc' ) );

/*Start a session*/
session_start();

require ZAR_ROOT_PATH . '/class/auth/authfactory.php';
$zariliaAuth = &ZariliaAuthFactory::getAuthConnection();
$zariliaAuth->doCheck();
$zariliaUser = $zariliaAuth->doSession();
if ( is_object( $zariliaUser ) && $zariliaUser->isAdmin() ) {
    $zariliaUserIsAdmin = $zariliaUser->isAdmin();
}
/**
 */
$zariliaLogger->setDebugmode();

$online_handler = &zarilia_gethandler( 'online' );
$online_handler->write();

/**
 * #################### Include site-wide lang file ##################
 */
if ( is_object( $zariliaUser ) && $zariliaUser->getVar( 'theme' ) ) {
    $zariliaConfig['theme_set'] = $zariliaUser->getVar( 'theme' );
}
// define( 'ZAR_THEME_URL', ZAR_URL . '/themes' );
// define( 'ZAR_THEME_PATH', ZAR_ROOT_PATH . '/themes' );
// define( 'ZAR_CSS_URL', ZAR_URL . '/themes/' . $zariliaConfig['theme_set'] . '/css/style.css' );
require_once ZAR_ROOT_PATH . '/include/api.php';

if ($zariliaConfig['language'] !='') {
	$zariliaConfig['language'] = is_dir( ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] ) ? $zariliaConfig['language'] : 'english';
} else {
	$zariliaConfig['language'] = 'english';
}
if ( is_object( $zariliaUser ) && $zariliaUser->getVar( 'user_language' ) != 'english' ) {
    if ( $zariliaUser->getVar( 'user_language' ) ) {
        $zariliaConfig['language'] = $zariliaUser->getVar( 'user_language' );
    }
}
require_once ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/local.php';
require_once ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/global.php';

if ( isset( $zariliaOption['pagetype'] ) && false === strpos( $zariliaOption['pagetype'], '.' ) && $zariliaOption['pagetype'] != 'system' ) {
    if ( is_readable( ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/' . $zariliaOption['pagetype'] . '.php' ) ) {
        require ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/' . $zariliaOption['pagetype'] . '.php';
    }
}
$zariliaAuth->doClosedSiteCheck();

/**
 * ################ Include ZAR_USE_MULTIBYTES ################
 */
// %%%%%		LANGUAGE SPECIFIC SETTINGS   %%%%%
define( '_CHARSET', $zariliaConfig['charset'] );
define( '_LANGCODE', $zariliaConfig['language'] );
define( 'ZAR_USE_MULTIBYTES', $zariliaConfig['multibyte'] );
/**
 * ################ Include page-specific lang file ################
 */

/**
 * ############### Events system ###################
 */
include_once ZAR_ROOT_PATH . '/include/atasks.php';

/**
 * #################### Error reporting settings ##################
 */
$zariliaErrorHandler->setDebugmode( true );
/**
 * ################ get the theme ################
 */
if ( !empty( $_POST['zarilia_theme_select'] ) && in_array( $_POST['zarilia_theme_select'], $zariliaConfig['theme_set_allowed'] ) ) {
    $zariliaConfig['theme_set'] = $_POST['zarilia_theme_select'];
    $_SESSION['zariliaUserTheme'] = $_POST['zarilia_theme_select'];
} elseif ( !empty( $_SESSION['zariliaUserTheme'] ) && in_array( $_SESSION['zariliaUserTheme'], $zariliaConfig['theme_set_allowed'] ) ) {
    $zariliaConfig['theme_set'] = $_SESSION['zariliaUserTheme'];
}

/**
 * ################ Include Addons details ################
 */
$addon_handler = &zarilia_gethandler( 'addon' );
$addonperm_handler = &zarilia_gethandler( 'groupperm' );
$zariliaAddon = $addon_handler->loadAddon();
if ( is_object( $zariliaAddon ) && $zariliaAddon->getVar( 'isactive' ) ) {
    if ( $zariliaAddon->getVar( 'dirname' ) != 'system' && !$zariliaAddon->checkAccess() ) {
        redirect_header( ZAR_URL, 2, _NOPERM );
        exit();
    } else {
        if ( $zariliaAddon->getVar( 'hasconfig' ) == 1 || $zariliaAddon->getVar( 'hascomments' ) == 1 || $zariliaAddon->getVar( 'hasnotification' ) == 1 ) {
            $zariliaAddonConfig = &$config_handler->getConfigsByCat( 0, $zariliaAddon->getVar( 'mid' ) );
        }
        if ( !isset( $zariliaOption['pagetype'] ) ) {
            $zariliaOption['pagetype'] = 'main';
        }
        if ( isset( $zariliaOption['pagetype'] ) && $zariliaOption['pagetype'] != 'system' ) {
            $zariliaAddon->loadLanguage( $zariliaOption['pagetype'] );
        }
        $zariliaUserIsAdmin = $zariliaUser ? $zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) : false;
    }
} else {
    redirect_header( ZAR_URL, 2, _NOPERM );
    exit();
}
$zariliaLogger->stopTime( 'Zarilia Boot Time' );
$zariliaLogger->startTime( 'Addon Load Time' );
$zariliaLogger->startGatherStats();

?>