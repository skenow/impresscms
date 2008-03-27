<?php
// $Id: admin_header.php,v 1.1 2007/03/16 02:36:17 catzwolf Exp $
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

include '../../../../mainfile.php';
include ZAR_ROOT_PATH.'/include/cp_functions.php';
if (is_object($zariliaUser)) {
	$addon_handler =& zarilia_gethandler('addon');
	$zariliaAddon =& $addon_handler->getByDirname('system');
	if (!in_array(ZAR_GROUP_ADMIN, $zariliaUser->getGroups())) {
		$sysperm_handler =& zarilia_gethandler('groupperm');
		if (!$sysperm_handler->checkRight('system_admin', ZAR_SYSTEM_COMMENT, $zariliaUser->getGroups())) {
			redirect_header(ZAR_URL.'/', 3, _NOPERM);;
			exit();
		}
	}
} else {
	redirect_header(ZAR_URL.'/', 3, _NOPERM);
	exit();
}
?>