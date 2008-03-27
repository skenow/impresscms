<?php
// $Id: code_init.php,v 1.1 2007/03/16 02:34:43 catzwolf Exp $
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
if (file_exists(ZAR_CACHE_PATH."/hash.xml")) {
	unlink(ZAR_CACHE_PATH."/hash.xml");
}

if ($handle = fopen(ZAR_CACHE_PATH."/hash.xml", "w")) {
	fwrite($handle,"<"."?xml version=\"1.0\" ?".">\r\n");
	fwrite($handle,"<zarilia>\r\n");
	fclose($handle);
}

?>