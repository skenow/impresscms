<?php
// $Id: code_clone.php,v 1.1 2007/03/16 02:34:43 catzwolf Exp $
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

$contents = file_get_contents("$source/$file");

/*function convert_addon($data) {
	global $cname, $newname;
	if ((substr($data, 0, strlen('include')) == 'include')||(substr($data, 0, strlen('include')) == 'require')) {
		return str_replace(array("/$cname", "$cname/", "/$cname/"), array("/$newname", "$newname/", "/$newname/"),$data);
	}
	return prefixqueries($cname, $newname, str_replace($cname, $newname,$data));
}*/

function str_sort($a, $b) {
	if($a == $b) return 0;
	return (strlen($a) > strlen($b) ? -1 : 1);
}

$addonversion = array();
include_once ZAR_ROOT_PATH."/addons/$cname/language/english/addoninfo.php";
include ZAR_ROOT_PATH."/addons/$cname/zarilia_version.php";


function prefixqueries($cname, $newname, $contents) {
     global $addonversion;
	 if (isset($addonversion['tables'])) {
		 $prefix = soundex($newname);
		 usort($addonversion['tables'], "str_sort");
		 foreach ($addonversion['tables'] as $table) {
			 if (!strstr($table, $cname)) {
				 $contents = str_replace($table, $prefix.'_'.$table, $contents);
			 }
		 }
	 }
	 return $contents;
}

if (strlen($file)>3) {
	switch (strtolower(substr($file, -4))) {
		 case '.php':
//			 $contents = str_replace(array("/$cname", "$cname/", "/$cname/"), array("/$newname", "$newname/", "/$newname/"),$data)
//			 include_once ZAR_ROOT_PATH.'/addons/cpTools/admin/code_parse_php.php';
//		 break;
		 case '.sql':
//			 $contents = str_replace($cname, $newname, $contents);// str_replace($cname, $newname, $contents);
			 $contents = str_replace(array("/$cname/", "/$cname", "$cname/", $cname),
									 array("/$newname/", "/$newname", "$newname/", $newname),
									$contents);
			 $contents =  prefixqueries($cname, $newname, $contents);
		 break;
	}
}

$file = str_replace($cname, $newname, $file);
$handle = fopen("$dest/$file", "w");
fwrite($handle, $contents);
fclose($handle);

?>