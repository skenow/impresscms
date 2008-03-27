<?php
// $Id: textclock.php,v 1.1 2007/03/16 02:40:28 catzwolf Exp $
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

// Loading base class
require_once ZAR_ROOT_PATH.'/class/controls/base/control.class.php';

/**
 * DHTML/Ajax Text Clock
 * 
 * @package kernel
 * @subpackage ajax
 */
class ZariliaControl_Block 
	extends ZariliaControl {

	 /**
	 * Constructor
	 */
	function ZariliaControl_Block($content='', $useComet = false, $name = null) {
		global $zariliaOption;
		if ($name !== null) $name = 'block_'.$name;
		$this->ZariliaControl('Block',$name, $content, true);
		if (!$this->isSysFlag('ajax_blocks_started')) {
			$function = $this->GenerateFunctionName('Update');
			$this->RegisterFunction($function);
			$this->AddTimer($function, 5000);
			$_SESSION['blocks'] = array();
			$this->setSysFlag('ajax_blocks_started');
		}
	}

}
?>