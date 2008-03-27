<?php
// $Id: exec_hash.php,v 1.1 2007/03/16 02:34:43 catzwolf Exp $
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

header( 'Connection: keep-alive;' );
header( 'Keep-Alive: 30' );

function ev( $task )
{
    require_once ZAR_ROOT_PATH . '/class/class.vmachine.php';
    $vm = new vMachine();
    $vm->exec( $task, true );
}

function hash_all_dirs( $dir )
{
    global $tk, $tdir;
    if ( $handle = opendir( $dir ) ) {
        // $taskX = '?'.'>'.file_get_contents('code_hash.php').'<'.'?';
        while ( false !== ( $file = readdir( $handle ) ) ) {
            if ( is_dir( "$dir/$file" ) ) {
                if ( ZAR_ROOT_PATH . "/templates_c" == "$dir/$file" ) continue;
                if ( ZAR_CACHE_PATH == "$dir/$file" ) continue;
                if ( $file == 'CVS' ) continue;
                if ( $file[0] == '.' ) continue;
                hash_all_dirs( "$dir/$file" );
            } else {
                if ( ZAR_CACHE_PATH . "/hash.xml" == "$dir/$file" ) continue;
                $task = sprintf( '$path = \'%s\'; $file = \'%s\'; $dir = \'%s\';', $dir, $file, $tdir );
                // ev($task);
                $tk->AddTask( $task . 'require ZAR_ROOT_PATH.\'/addons/cpTools/admin/code_hash.php\';', "Hashing file '$dir/$file'..." );
            }
        }
    }
}

$file = ZAR_URL . '/download.php?file=' . urlencode( base64_encode( ZAR_URL . '/cache/hash.xml' ) );
$tk = new ZariliaControl_CPSetup( 'pbar', true, $src = 'db:', $onFinish = "window.location = '$file';" );
$tdir = zarilia_cleanRequestVars( $_POST, 'source', '/' );

$tk->AddTask( 'require_once ZAR_ROOT_PATH.\'/addons/cpTools/admin/code_init.php\';', "Begining hashing'..." );
hash_all_dirs( $tdir );
$tk->AddTask( 'require_once ZAR_ROOT_PATH.\'/addons/cpTools/admin/code_finish.php\';', "Finishing hashing'..." );
echo $tk->render();

?>