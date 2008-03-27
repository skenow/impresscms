<?php
// $Id: exec_clone.php,v 1.1 2007/03/16 02:34:43 catzwolf Exp $
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
$addon = zarilia_cleanRequestVars( $_POST, 'addon', '' );
$newname = zarilia_cleanRequestVars( $_POST, 'newname', '' );

/*if (!file_exists(ZAR_ROOT_PATH.'/addons/'.$addon)) {
	echo 'Error: Addons doesn\'t exists';
	return;
}
if ($addon == $newname) {
	echo 'Error: Addons and cloned addon can\'t have the same name.';
	return;
}
if (file_exists(ZAR_ROOT_PATH.'/addons/'.$newname)) {
	echo 'Error: New addon name is same as existing addon.';
	return;
}*/

header( 'Connection: keep-alive;' );
header( 'Keep-Alive: 30' );

function clone_all_dirs( &$tk, $dir, $ndir, $newname, $cname )
{
    if ( $handle = opendir( $dir ) ) {
        while ( false !== ( $file = readdir( $handle ) ) ) {
            $task = sprintf( '$file = \'%s\'; $source = \'%s\'; $dest = \'%s\'; $newname =\'%s\'; $cname=\'%s\'; ', $file, $dir, $ndir, $newname, $cname );
            if ( is_dir( "$dir/$file" ) ) {
                if ( ZAR_ROOT_PATH . "/templates_c" == "$dir/$file" ) continue;
                if ( ZAR_CACHE_PATH == "$dir/$file" ) continue;
                if ( $file == 'CVS' ) continue;
                if ( $file[0] == '.' ) continue;
                $tk->AddTask( $task . 'include ZAR_ROOT_PATH.\'/addons/cpTools/admin/code_mkdir.php\';', "Creating folder '$ndir/$file'..." );
                clone_all_dirs( $tk, "$dir/$file", "$ndir/$file", $newname, $cname );
            } else {
                $tk->AddTask( $task . 'include ZAR_ROOT_PATH.\'/addons/cpTools/admin/code_clone.php\';', "Cloning file '$ndir/$file'..." );
            }
        }
    }
}

$dir = ZAR_ROOT_PATH . '/addons/' . $addon;
$tdir = ZAR_ROOT_PATH . '/addons/' . $newname;

$tk = new ZariliaControl_CPSetup( 'pbar', true, $src = 'db:', $onFinish = "/*window.location='" . ZAR_URL . "/addons/cpTools/admin/index.php';*/" );

$tk->AddTask( "if (!file_exists('$tdir')) mkdir('$tdir'); ", "Creating folder for cloned addon..." );
clone_all_dirs( $tk, $dir, $tdir, $newname, $addon );

echo $tk->render();

?>