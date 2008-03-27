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
require_once ZAR_ROOT_PATH.'/class/ajax/control.class.php';

/**
 * DHTML/Ajax Text Clock
 * 
 * @package kernel
 * @subpackage ajax
 */
class ZariliaControl_TextClock 
	extends ZariliaControl {

	 /**
	 * Constructor
	 */
	function ZariliaControl_TextClock ($name = null) {
		$this->ZariliaControl('TextClock',$name, date("r", time()));
		$function = $this->GenerateFunctionName();
		$this->RegisterFunction($function);
		$this->AddTimer($function,1000);
	}

}
?>