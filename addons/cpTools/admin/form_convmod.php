<?php
// $Id: form_convmod.php,v 1.1 2007/03/16 02:34:43 catzwolf Exp $
// ------------------------------------------------------------------------ //
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

$form = new ZariliaThemeForm( 'Convert addon to Zarilia', 'convmod', ZAR_URL . '/addons/cpTools/admin/index.php' );
$addons = new ZariliaFormSelect( 'Select addon to convert:', 'addon', null, 10 );
$convertors = new ZariliaFormSelect( 'Select input addon type:', 'type', null, 1 );
$dir = ZAR_ROOT_PATH . '/addons/';
if ( $handle = opendir( $dir ) ) {
    while ( false !== ( $file = readdir( $handle ) ) ) {
        if ( $file[0] == '.' ) continue;
        if ( $file == 'CVS' ) continue;
        if ( $file == 'SVN' ) continue;
        if ( $file == 'cpTools' ) continue;
        if ( $file == 'system' ) continue;
        if ( is_dir( "$dir/$file" ) ) {
            $addons->addOption( $file, $file );
        }
    }
}
$dir = ZAR_ROOT_PATH . '/addons/cpTools/admin/';
if ( $handle = opendir( $dir ) ) {
    while ( false !== ( $file = readdir( $handle ) ) ) {
        if ( is_file( "$dir/$file" ) && ( substr( $file, 0, 7 ) == 'convmod' ) ) {
            $file = substr( $file, 8, -4 );
            $convertors->addOption( $file, $file );
        }
    }
}
$form->addElement( $addons, true );
$form->addElement( $convertors, true );
$form->addElement( new ZariliaFormHidden( 'op', 'exec_command' ) );
$form->addElement( new ZariliaFormHidden( 'command', 'convmod' ) );
$form->addElement( new ZariliaFormButton( '', 'submit', 'Convert', 'submit' ) );
$form->display();

?>