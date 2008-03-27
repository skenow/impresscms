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

function ZariliaControl_Block_Update() {
	$objResponse = new xajaxResponse();
	if (!isset($_SESSION['blocks'])) return $objResponse;
	foreach ($_SESSION['blocks'] as $name => $content) {
		$objResponse->assign('block_'.$name, "innerHTML", $content);
		unset($_SESSION['blocks'][$name]);
		return $objResponse;
	}
    return $objResponse;
}

?>