<?php
// $Id: index.php,v 1.3 2007/04/12 14:15:51 catzwolf Exp $
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
error_reporting( E_ALL );
include_once './include/passwd.php';
include_once './include/functions.php';
if ( INSTALL_USER != '' || INSTALL_PASSWD != '' ) {
    if ( !isset( $_SERVER['PHP_AUTH_USER'] ) ) {
        header( 'WWW-Authenticate: Basic realm="Zarilia Installer"' );
        header( 'HTTP/1.0 401 Unauthorized' );
        echo 'You can not access this Zarilia installer.';
        exit;
    } else {
        if ( INSTALL_USER != '' && $_SERVER['PHP_AUTH_USER'] != INSTALL_USER ) {
            header( 'HTTP/1.0 401 Unauthorized' );
            echo 'You can not access this Zarilia installer.';
            exit;
        }
        if ( INSTALL_PASSWD != $_SERVER['PHP_AUTH_PW'] ) {
            header( 'HTTP/1.0 401 Unauthorized' );
            echo 'You can not access this Zarilia installer.';
            exit;
        }
    }
}
$zariliaOption['nocommon'] = true;
define( 'ZAR_INSTALL', 1 );
// **//
session_start();

require_once './class/class_installer.php';
$installer = new ZariliaInstall();
$installer->restart();

/**
 * * Session start must be after the restart function or bad things will happen! *
 */
$zariliaOption['InstallPrefix'] = 'zariliasetup-' . md5( $_SERVER['SERVER_NAME'] . '-' . $_SERVER["SERVER_PORT"] . session_id() );
$op = ( isset( $_REQUEST['op'] ) && !empty( $_REQUEST['op'] ) ) ? strval( $_REQUEST['op'] ): 'langselect';

$installer->doChecks( $op );
// Installer Setup//
$installer->setArgs( 'op', $op );
$installer->getLanguage();
$installer->setArgs( 'template', 'install.tpl.php' );
$installer->setArgs( 'install_path', 'http://'.$_SERVER["HTTP_HOST"].':'.$_SERVER["SERVER_PORT"]. $_SERVER['SCRIPT_NAME'] );
//phpinfo();
$installer->setArgs( 'template_path', './templates/' );

// Sequence Setup//
$sequence = new ZariliaInstallSeq();
$sequence->createSteps();

$filename = "class/sequences/seq.$op.php";
if ( file_exists( $filename ) ) {
    include $filename;
} else {
    $installer->render();
}

?>