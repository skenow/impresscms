<?php
// $Id: dyncontent.php,v 1.1 2007/03/16 02:40:28 catzwolf Exp $
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
require_once ZAR_CONTROLS_PATH.'/base/control.class.php';

/**
 * DHTML/Ajax Dynamic Content control
 *
 * @package kernel
 * @subpackage ajax
 */
class ZariliaControl_DynContent
	extends ZariliaControl {

	 /**
	 * Constructor
	 */
	function ZariliaControl_DynContent ($url, $name = null) {
		$this->ZariliaControl('DynContent',$name, "Loading...", true);
		$this->SetVar('url',$url);
		$function = $this->GenerateFunctionName();
		$this->RegisterFunction($function, array('url'));
		$this->AddJS($this->GetRJS($function));
	}

}


?>