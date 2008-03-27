<?php
// $Id: exec_convmod.php,v 1.1 2007/03/16 02:34:43 catzwolf Exp $
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

//header('Connection: keep-alive;');
//header('Keep-Alive: 30');

function conv_all_dirs(&$tk, $dir, $type, $ndir) {
	if ($handle = opendir($dir)) {
		while (false !== ($file = readdir($handle))) {
			 $task = sprintf('$file = \'%s\'; $source = \'%s\'; $dest = \'%s\';', $file, $dir, $ndir);
			 if (is_dir("$dir/$file")) {
				 if (ZAR_ROOT_PATH."/templates_c" ==  "$dir/$file") continue;
				 if (ZAR_CACHE_PATH ==  "$dir/$file") continue;
				 if ($file == 'CVS') continue;
				 if ($file[0] == '.') continue;
				 $tk->AddTask($task.'include ZAR_ROOT_PATH.\'/addons/cpTools/admin/code_mkdir.php\';', "Creating folder '$ndir/$file'...");
//				 echo "mkdir('$ndir/$file');". "<br>Creating folder '$ndir/$file'..."."<p></p>";
				 conv_all_dirs($tk, "$dir/$file", $type, "$ndir/$file");
			 } else {
	 			 if (ZAR_CACHE_PATH."/hash.xml" == "$dir/$file") continue;
//				 echo $task.'require ZAR_ROOT_PATH.\'/addons/cpTools/admin/convmod_'.$type.'.php\';'. "<br>Converting file '$dir/$file'..."."<p></p>";
				 $tk->AddTask($task.'include ZAR_ROOT_PATH.\'/addons/cpTools/admin/convmod_'.$type.'.php\';', "Converting file '$ndir/$file'...");
			 }
		}
	}
}

$addon = zarilia_cleanRequestVars( $_POST, 'addon', '' );
$type = zarilia_cleanRequestVars( $_POST, 'type', '' );
$tdir = ZAR_ROOT_PATH.'/addons/'.$addon;
$ndir = $tdir.'_old';
if (file_exists($ndir)) {
	$dir_id = 2;
	while(file_exists($ndir.$dir_id)) {
		$dir_id++;
	}
	$ndir = "$ndir$dir_id";
}

$tk = new ZariliaControl_CPSetup( 'pbar', true, $src='db:', $onFinish = "window.location='".ZAR_URL."/addons/cpTools/admin/index.php';");

//rename("$tdir","$ndir");
//mkdir("$tdir");
///echo "rename('$tdir','$ndir');". "<br>Renaming folder of addon..."."<p></p>";
//echo "mkdir('$tdir');". "<br>Creating folder for new addon..."."<p></p>";

rename($tdir,$ndir);
mkdir($tdir);


//$tk->AddTask("if (!file_exists('$ndir')) rename('$tdir','$ndir');", "Renaming folder of addon...");
//$tk->AddTask("if (!file_exists('$tdir')) {mkdir('$tdir'); sleep(10);", "Creating folder for new addon...");
conv_all_dirs($tk, $ndir, $type, $tdir);
echo $tk->render();

?>