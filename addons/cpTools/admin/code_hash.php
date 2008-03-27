<?php
// $Id: code_hash.php,v 1.1 2007/03/16 02:34:43 catzwolf Exp $
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

/*$path = '%s';
$file = '%s';
$dir = '%s';*/

$xpath = "$path/$file";
$mpath = substr($xpath,strlen($dir));
echo $xpath;
$contents = file_get_contents($xpath);

$hash['sha1'] = sha1($contents);
$hash['md5'] = md5($contents);
$hash['crc32'] = crc32($contents);
$size = filesize($xpath);

$handle = fopen(ZAR_CACHE_PATH."/hash.xml", "a");
fwrite($handle,"\t<fileinfo filename=\"$mpath\" size=\"$size\">\r\n");
foreach ($hash as $type => $value) {
	fwrite($handle,"\t\t<hash type=\"$type\">\r\n");
	fwrite($handle,"\t\t\t$value\r\n");
	fwrite($handle,"\t\t</hash>\r\n");
}
fwrite($handle,"\t</fileinfo>\r\n");
fclose($handle);

?>