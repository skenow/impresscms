<?php
// $Id: code_finish.php,v 1.1 2007/03/16 02:34:43 catzwolf Exp $
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
if ($handle = fopen(ZAR_CACHE_PATH."/hash.xml", "a")) {
	fwrite($handle,"</zarilia>");
	fclose($handle);
}

?>