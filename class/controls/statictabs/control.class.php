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

// Loading base class
require_once ZAR_CONTROLS_PATH.'/base/control.class.php';
include_once ZAR_ROOT_PATH . '/class/class.menubar.php';

/**
 * DHTML/Ajax Dynamic Content control
 * 
 * @package kernel
 * @subpackage ajax
 */
class ZariliaControl_StaticTabs 
	extends ZariliaControl {

	 /**
	 * Constructor
	 */
	function ZariliaControl_StaticTabs ($tabs, $name) {	
		global $zariliaOption, $zariliaTpl;
		$values = array_values($tabs);
		$selected = (isset($_COOKIE["StaticTab-$name"])?intval($_COOKIE["StaticTab-$name"]):0);
		$tabbar = new ZariliaTabMenu( $selected, true );
		$tb = array();
		$i=0;
		foreach ($tabs as $k => $v) {
			$tb[$k] = "javascript:staticTabsSelect(\"$name\", $i);";
			$this->setVar("tab$i", rawurlencode($v));
			$i++;
		}
		$tabbar->addTabArray($tb);
		unset($tb);
		if (!isset($zariliaOption['staticTabsSelectorLoaded'])) {
			$zariliaTpl->addScript(ZAR_ROOT_PATH.'/controls/statictabs/functions.js');
			$zariliaOption['staticTabsSelectorLoaded'] = true;
		}
		$code = '<div>'.$tabbar->renderStart(true)."</div><div id=\"".$name."_content\">".$values[$selected].'</div>';
		$this->ZariliaControl('StaticTabs',$name, $code, false);
	}

}

?>