<?php
// $Id: mainfile.php,v 1.4 2007/04/21 09:40:28 catzwolf Exp $
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
//die();
if ( !defined( "ZAR_MAINFILE_INCLUDED" ) )
{
    define( "ZAR_MAINFILE_INCLUDED", 1 );
	define( 'ZAR_DEFAULT_SITE', 'default');

	require_once 'class/cache/settings.class.php';
	$zariliaSettings = &ZariliaSettings::getInstance();
	global $zariliaSettings;
	$cpConfig = &$zariliaSettings->readAll('site.global');
	if (isset($cpConfig['sites'][$_SERVER['HTTP_HOST']])) {
		$zariliaOption['currentsite'] = $_SERVER['HTTP_HOST'];
		$zariliaOption['siteprefix'] = $cpConfig['sites'][$_SERVER['HTTP_HOST']][1];
	} else {
		$zariliaOption['currentsite'] = ZAR_DEFAULT_SITE;
		$zariliaOption['siteprefix'] = $cpConfig['db']['prefix'];
	}
	$zariliaOption['localconfig'] = 'siteinfo.'.$zariliaOption['currentsite'];
	$zariliaOption['globalconfig'] = 'site.global';	

	$zariliaOption['mainfile'] = str_replace('\\','/',__FILE__);

/*    include_once 'siteinfo.php';
    if ( !isset( $cpConfig['root_path'] ) && !defined( 'ZAR_INSTALL' ) )
    {
        header( 'location: ./install' );
    }*/

//    $zariliaOption['currentsite'] = isset( $cpConfig['sites'][$_SERVER["HTTP_HOST"]] ) ? $_SERVER["HTTP_HOST"]: 'default://';
    // ZARILIA Physical Path
    // Physical path to your main ZARILIA directory WITHOUT trailing slash
    // Example: define('ZAR_ROOT_PATH', '/path/to/zarilia/directory');
    define( 'ZAR_ROOT_PATH', $cpConfig['path']['root'] );
    // ZARILIA Virtual Path (URL)
    // Virtual path to your main ZARILIA directory WITHOUT trailing slash
    // Example: define('ZAR_URL', 'http://url_to_zarilia_directory');
    define( 'ZAR_URL', $zariliaSettings->read( $zariliaOption['localconfig'], 'config', 'url') );

    foreach ( array( 'GLOBALS', '_SESSION', 'HTTP_SESSION_VARS', '_GET', 'HTTP_GET_VARS', '_POST', 'HTTP_POST_VARS', '_COOKIE', 'HTTP_COOKIE_VARS', '_REQUEST', '_SERVER', 'HTTP_SERVER_VARS', '_ENV', 'HTTP_ENV_VARS', '_FILES', 'HTTP_POST_FILES', 'zariliaDB', 'zariliaUser', 'zariliaUserId', 'zariliaUserGroups', 'zariliaUserIsAdmin', 'zariliaConfig', 'zariliaOption', 'zariliaAddon', 'zariliaAddonConfig' ) as $bad_global )
    {
        if ( isset( $_REQUEST[$bad_global] ) )
        {
            header( 'Location: ' . ZAR_URL . '/' );
            exit();
        }
    }

	if (!defined('ZAR_INSTALL')) {
		define( 'ZAR_CHECK_PATH', @$cpConfig['path']['check'] ) ;
	} else {
		define( 'ZAR_CHECK_PATH', false ) ;
	}
    // Protect against external scripts execution if safe mode is not enabled
    if ( ZAR_CHECK_PATH && !@ini_get( 'safe_mode' ) )
    {
        if ( function_exists( 'debug_backtrace' ) )
        {
            $zariliaScriptPath = debug_backtrace();
            if ( !count( $zariliaScriptPath ) )
            {
                die( "ZARILIA path check: this file cannot be requested directly" );
            }
            $zariliaScriptPath = $zariliaScriptPath[0]['file'];
        }
        else
        {
            $zariliaScriptPath = isset( $_SERVER['PATH_TRANSLATED'] ) ? $_SERVER['PATH_TRANSLATED'] : $_SERVER['SCRIPT_FILENAME'];
        }
        if ( DIRECTORY_SEPARATOR != '/' )
        {
            // IIS6 may double the \ chars
            $zariliaScriptPath = str_replace( strpos( $zariliaScriptPath, '\\\\', 2 ) ? '\\\\' : DIRECTORY_SEPARATOR, '/', $zariliaScriptPath );
        }
        if ( strcasecmp( substr( $zariliaScriptPath, 0, strlen( ZAR_ROOT_PATH ) ), str_replace( DIRECTORY_SEPARATOR, '/', ZAR_ROOT_PATH ) ) )
        {
            exit( "Zarilia path check: Script is not inside ZAR_ROOT_PATH and cannot run." );
        }
    }

    // Database
    // Choose the database to be used
    define( 'ZAR_DB_TYPE', $cpConfig['db']['type'] );
    // Table Prefix
    // This prefix will be added to all new tables created to avoid name conflict in the database. If you are unsure, just use the default 'zarilia'.
    define( 'ZAR_DB_PREFIX', $cpConfig['db']['prefix'] );
    // Database Hostname
    // Hostname of the database server. If you are unsure, 'localhost' works in most cases.
    define( 'ZAR_DB_HOST', $cpConfig['db']['host'] );
    // Database Username
    // Your database user account on the host
    define( 'ZAR_DB_USER', $cpConfig['db']['user'] );
    // Database Password
    // Password for your database user account
    define( 'ZAR_DB_PASS', $cpConfig['db']['pass'] );
    // Database Name
    // The name of database on the host. The installer will attempt to create the database if not exist
    define( 'ZAR_DB_NAME', $cpConfig['db']['name'] );
    // Use persistent connection? (Yes=1 No=0)
    // Default is 'Yes'. Choose 'Yes' if you are unsure.
    define( 'ZAR_DB_PCONNECT', $cpConfig['db']['pconnect'] );

    foreach ( $cpConfig['groups'] as $groupID => $groupName )
    {
        define( "ZAR_GROUP_$groupName", $groupID );
    }

    unset( $groupID );
    unset( $groupName );
//    unset( $cpConfig['groups'] );

    ini_set( 'arg_separator.output' , '&amp;' );
    ini_set( 'url_rewriter.tags' , 'a=href,area=href,frame=src,input=src,fieldset=' );

    if ( !isset( $zariliaOption['nocommon'] ) && ZAR_ROOT_PATH != '' )
    {
		include ZAR_ROOT_PATH . "/include/common.php";
    }

    /*
    include ZAR_ROOT_PATH . '/class/class_flood.php';
    $protect = new flood_protection(); //call the class
    if ( $protect->check_request( 'file' ) ) { // check the user
        die( 'Request block, Please wait 1 secounds between requests. This is to stop flooding of our server.' ); //die there flooding
    }
*/
} 

?>