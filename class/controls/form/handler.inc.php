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

function ZariliaControl_Form_Handler($name, $actionfile, $info) {
	global $_POST, $zariliaUser, $zariliaAddon, $zariliaConfig, $_GET;
	$objResponse = new xajaxResponse();
	$objResponse->call('zcForm_ReturnEffect', $name);
	$count = count($info);
	$error = false;
	$_POST = array();
//	echo nl2br(var_export($info, true));
	for($i=0;$i<$count;$i++) {
		if (file_exists($file = ZAR_CONTROLS_PATH.'/form/'.$info[$i][2].'/handler.inc.php')) {
			require_once $file;
			$funcname = 'ZariliaControl_FormField_'.ucfirst($info[$i][2]).'_Validator';
			$error = $error || (!$funcname($info[$i][0], $info[$i][3], $objResponse));
		}
		if ($error) continue;
		if ($o = mb_strpos($info[$i][1], '[')) {
			$name = '$_POST[\''.mb_substr($info[$i][1],0,$o).'\']'.mb_substr($info[$i][1],$o);
		} else {
			$name = '$_POST[\''.$info[$i][1].'\']';
		}
//		echo "$name = isset(\$info[$i][3])?;<br />";
		eval("$name = isset(\$info[$i][3])?\$info[$i][3]:null;");
//		$_POST[$info[$i][1]] = $info[$i][3];
	}
	if ($error) return $objResponse;
	$_REQUEST = &$_POST;
	 require ZAR_ROOT_PATH.$actionfile;
    return $objResponse;
}

?>