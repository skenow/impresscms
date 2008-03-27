<?php
// $Id: liveupdate.php,v 1.1 2007/03/16 02:40:28 catzwolf Exp $
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
class ZariliaControl_LiveUpdate
	extends ZariliaControl {

	 /**
	 * Constructor
	 */
	function ZariliaControl_LiveUpdate ($update_script, $update_function, $params, $name=null, $interval=5000) {
		global $zariliaOption, $zariliaTpl;
		if (!isset($zariliaOption['liveUpdateIsLoaded'])) {
			$_SESSION['liveUpdate'] = array();
			$zariliaOption['liveUpdateInterval'] = $interval*10;
			$zariliaTpl->addExecBeforeOutput("\$this->headerAdd(ZariliaControl_LiveUpdate::GenerateJS());");
		} else {
			if ($zariliaOption['liveUpdateInterval']>$interval) {
				$zariliaOption['liveUpdateInterval'] = $interval;
			}
		}
		$interval = 100000;
		$params2 = serialize($params);
		if (isset($_SESSION['liveUpdate'][$update_script])) {
			if (!isset($_SESSION['liveUpdate'][$update_script][$update_function])) {
				$_SESSION['liveUpdate'][$update_script][$update_function] = array();
				$_SESSION['liveUpdate'][$update_script][$update_function][$params2] = array('interval' => $interval, 'time'=>time(), 'content'=>eval("return $update_function(\$params);"), 'areas'=>array());
			} else {
				if (!isset($_SESSION['liveUpdate'][$update_script][$update_function][$params2])) {
					$_SESSION['liveUpdate'][$update_script][$update_function][$params2] = array('interval' => $interval, 'time'=>time(), 'content'=>eval("return $update_function(\$params);"), 'areas'=>array());
				}
			}
		} else {
			require_once $update_script;
			$_SESSION['liveUpdate'][$update_script] = array();
			$_SESSION['liveUpdate'][$update_script][$update_function] = array();
			$_SESSION['liveUpdate'][$update_script][$update_function][$params2] = array('interval' => $interval, 'time'=>time(), 'content'=>eval("return $update_function(\$params);"), 'areas'=>array());
		}
		$this->ZariliaControl('LiveUpdate',$name, $_SESSION['liveUpdate'][$update_script][$update_function][$params2]['content'], true);
		$_SESSION['liveUpdate'][$update_script][$update_function][$params2]['areas'][] = $this->_params['id'];
		if (!isset($zariliaOption['liveUpdateIsLoaded'])) {
			$function = $this->GenerateFunctionName();
			$this->RegisterFunction($function);
			$zariliaOption['liveUpdateIsLoaded'] = true;
			$zariliaOption['liveUpdateFunction'] = $this->GetRJS( $function );
		}
	}


	static function GenerateJS() {
		global $zariliaOption;
		$crlf = "\n\r";
		$ret  = '<script type="text/javascript">'.$crlf;
		$ret .= "	var liveUpdateTimer = setInterval(\"" . $zariliaOption['liveUpdateFunction'] . "\"," . $zariliaOption['liveUpdateInterval'] . ");" .$crlf;
		$ret .= '	window.liveUpdate_Update = function(data, content) {'.$crlf;
		$ret .=	'		return;for(var x in data) {'.$crlf;
		$ret .= '			if (!data[x]) continue;'.$crlf;
		$ret .=	'			document.getElementById(data[x]).innerHTML = content;'.$crlf;
		$ret .=	'		}';
		$ret .= '	};'.$crlf;
		$ret .= '</script>'.$crlf;
		return $ret;
	}

}
?>