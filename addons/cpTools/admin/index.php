<?php
// $Id: index.php,v 1.1 2007/03/16 02:34:43 catzwolf Exp $
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

include_once '../../../include/cp_header.php';

$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'default' );

zarilia_cp_header();
zarilia_admin_menu( _MD_AD_ACTION_BOX, array( "?op=hash" => 'Hash', "?op=convmod" => 'Convert addon' , '?op=clone' => 'Clone addon' ) );
switch ( $op ) {
    case 'exec_command':
        $command = zarilia_cleanRequestVars( $_POST, 'command', 'default' );
        include_once ZAR_CONTROLS_PATH . '/cpsetup/control.class.php';
        $file = "exec_$command.php";
        if ( file_exists( $file ) ) {
            include $file;
        } else {
            echo 'This function in current version not exists';
        }
        break;
    default:
        include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
        $file = "form_$op.php";
        if ( file_exists( $file ) ) {
            include $file;
        } else {
            echo 'This function in current version not exists';
        }
        break;
}

zarilia_cp_footer();

?>
