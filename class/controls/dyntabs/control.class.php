<?php
// $Id: dyntabs.php,v 1.1 2007/03/16 02:40:28 catzwolf Exp $
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
require_once ZAR_ROOT_PATH.'/class/controls/dyncontent/control.class.php';
include_once ZAR_ROOT_PATH . '/class/class.menubar.php';

/**
 * DHTML/Ajax Dynamic Content control
 * 
 * @package kernel
 * @subpackage ajax
 */
class ZariliaControl_DynTabs 
	extends ZariliaControl {

	 /**
	 * Constructor
	 */
	function ZariliaControl_DynTabs ($tabs, $selected=0, $name = null) {	
		$values = array_values($tabs);
		$tk = new ZariliaControl_DynContent($values[$selected]);
		unset($values);
		$n2 = $tk->GetParam('id');
		$tabbar = new ZariliaTabMenu( $selected, true );
		$tb = array();
		foreach ($tabs as $k => $v) {
			$tb[$k] = "javascript:xajax_ZariliaControlHandler(\"$n2\", \"DynContent\", \"ZariliaControl_DynContent_Handler\", true, \"0:$v\");";
		}
		$tabbar->addTabArray($tb);
		unset($tb);
		$code = "<div>".$tabbar->renderStart()."</div><div>".$tk->render(true)."</div>";
		//$code = "<table border=\"0\" width=\"100%\"><tr><td>".$tabbar->renderStart()."</td></tr><tr><td>".$tk->render()."</td></tr></table>";
		$this->ZariliaControl('DynTabs',$name, $code, false);
	}

}

?>