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

function ZariliaControl_FormField_List_Validator($name, $value, &$objResponse) {
	if (!strcmp(intval($value).'',$value.'')) {
		$objResponse->call('zcFormField_Error', $name, 'Bad data (trying to hack?)');
		return false;
	}
	return true;
}

?>