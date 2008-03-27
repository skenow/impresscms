<?php
// $Id: statictabs.php,v 1.1 2007/03/16 02:40:28 catzwolf Exp $
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

require_once ZAR_ROOT_PATH.'/class/controls/dyncontent/functions.inc.php';

function ZariliaControl_DynContent_Handler($name, $url) {
	global $zariliaOption, $zariliaUser;
	$zariliaOption['dyncontent'] = true;
	$url = str_replace(
		array('%root%','%system_dir%','%url%','%sa_dir%'),
		array(ZAR_ROOT_PATH,ZAR_ROOT_PATH.'/addons/system/index.php',ZAR_URL,ZAR_ROOT_PATH.'/addons/system/admin/'),
		$url);
	$s = strpos($url, '?');
	$zariliaOption['query'] = array();
	if ($s>-1) {
		$lpart = substr($url, 0, $s);
		$rpart = substr($url, $s+1);
		$s = strpos($rpart, '#');
		if ($s>-1) {
			$mpart = substr($rpart, 0, $s);
			$rpart = substr($rpart, $s+1);
			$url = $lpart.'#'.$rpart;
		} else {
			$mpart = $rpart;
			$rpart = '';
			$url = $lpart;
		}
		$qrs = explode('&', $mpart);
		foreach ($qrs as $v) {
			$qr2 = explode('=', $v);
			$zariliaOption['query'][$qr2[0]] = $qr2[1];
		}
		unset($qrs);
	}
	

	if (file_exists($url)) {
			
//		echo $url;
		ob_start();
		$contents = include($url);
		echo $contents;
		$contents = ob_get_contents();
		ob_end_clean();
//		$contents = ob_get_contents();
	} else {
//		echo $url;
		$contents = file_get_contents($url);
	}
	$objResponse = new xajaxResponse();
	global $crm;
	$objResponse->assign($name, "innerHTML", $contents);
    return $objResponse;
}

?>