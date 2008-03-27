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

require_once ZAR_CONTROLS_PATH.'/base/control.class.php';
require_once ZAR_CONTROLS_PATH .'/cpsetup/progressbar.class.php';

if (!function_exists('file_put_contents')) {
define('FILE_APPEND', 1);
function file_put_contents($n, $d, $flag = false) {
    $mode = ($flag == FILE_APPEND || strtoupper($flag) == 'FILE_APPEND') ? 'a' : 'w';
    $f = @fopen($n, $mode);
    if ($f === false) {
        return 0;
    } else {
        if (is_array($d)) $d = implode($d);
        $bytes_written = fwrite($f, $d);
        fclose($f);
        return $bytes_written;
    }
}
}

function ZariliaControl_CPSetup_Handler($name, $count, $value, $source, $onfinish) {
	global $zariliaEvents, $zariliaOption;
	switch($source['type']) {
		case 'file':
			$data = file($source['location']);
			if (isset($data[$value])) {
				$events = unserialize($data[$value]);
				foreach ($events as $k => $v) $events[$k] = urldecode($v);
			} else {
				//unlink($source['location']);
			}
			unset($data);
			$fullload = 'false';
		break;
		default:
//			$zariliaOption['setupMode'] = true;
			$events = require_once $source['location'];
			$e = array();
			$e['code'] = $events[0][0];
			$e['description'] = $events[0][1];
			$e['error'] = $events[0][2];
			$e['errortype'] = $events[0][3];
			unset($events[0]);
			if (count($events)>0) {
				file_put_contents($source['location'], '<?php return '.var_export(array_values($events), true).'; ?>');
			} else {
				unlink($source['location']);
			}
			$events = &$e;
//			$events = $zariliaEvents->getEvents(XEVENT_DTYPE_ONNEXT);
			$fullload = 'true';
		break;
	}
	$objResponse = new xajaxResponse();
	if (!$events) {
		$objResponse->assign($name, "innerHTML", "Done.");
	} else {
		if (isset($events['description'])) {
			$comment['desc'] = $events['description'];
			require_once ZAR_ROOT_PATH.'/class/class.vmachine.php';
			require_once ZAR_ROOT_PATH.'/class/adodb_lite/adodb.inc.php';
			$vm = new vMachine();
			$events['code'] =	"
										\$db = ADONewConnection(ZAR_DB_TYPE);
										if ( !(\$result = \$db->Connect(ZAR_DB_HOST, ZAR_DB_USER, ZAR_DB_PASS, ZAR_DB_NAME)) ) {
											   trigger_error( \"Database error: could not connect\", E_USER_ERROR );
										}
										\$zariliaOption['setupMode'] = true;
								        ".$events['code'];
			$comment['msg'] = $vm->exec($events['code'], true);
		} else {
			$comment['msg'] = $events[0]->doEvent();
			$comment = @unserialize(urldecode($events[0]->getVar('comment')));
			$zariliaEvents->delete($events[0]);
		}
		$vbar = new ZariliaControl_CPSetup_ProgressBar();
		if ($count-1>0) {
			$value = round(100/($count-1)*$value);
		} else {
			$value = 100;
		}
		$objResponse->assign($name."_bg_text", "innerHTML", "$value%");
		$objResponse->assign($name."_graph_text", "innerHTML", "$value%");
		$rez = $vbar->recalc_width($value)."px";
		$objResponse->assign($name."_graph", "style.width", $rez);
		$objResponse->assign($name."_graph", "style.minwidth", $rez);
		$objResponse->assign($name."_desc", "innerHTML", $comment['desc']);

		if (!isset($comment['msg'])) $comment['msg'] ='';
		if ($comment['msg']!='') {
			$comment['data']  = "<table border=\"0\" width=\"100%\"><tr><td align=\"left\" valign=\"top\" width=\"50\">[".date("H:i:s")."]</td><td align=\"left\" valign=\"top\">".$comment['desc']."</td></tr><tr><td></td><td align=\"left\" valign=\"top\">".$comment['msg']."</td></tr></table>";
		} else {
			$comment['data']  = $comment['desc']."<br />";
		}

		$objResponse->append($name."_console", "innerHTML", $comment['data']);
//		$objResponse->assign($name."_console", "style.top", "1000");
		$objResponse->assign($name."_console", "style.top", "");
		$objResponse->script("  document.getElementById('".$name."_console_all').scrollTop = - document.getElementById('".$name."_console_all').clientHeight + document.getElementById('".$name."_console_all').scrollHeight;");
//		$objResponse->assign($name."_console_all", "scrollTop", "- document.getElementById('".$name."_console_all').clientHeight");
	//	$objResponse->assign($name, "innerHTML",  $value/* $vbar->renderProgressBar($name, $value)."<div class=\"stepDesc\">".$events[0]->getVar('comment')."</div>"*/);
		if ($value<100) {
//	  	    $objResponse->script(' alert("a");');
			$objResponse->script(' '.$name.'_value++;');
			$objResponse->script("xajax_ZariliaControlHandler('$name', 'CPSetup', 'ZariliaControl_CPSetup_Handler',$fullload,".$name."_count,".$name."_value,".$name."_source,".$name."_onfinish );");
//*/
//			$objResponse->script($this->GetRJS('CPSetup'));
		} else {
//			$objResponse->assign($name."_console", "innerHTML", "Done.");
//document.getElementById('b_next').value = 'aa';
			if (trim($onfinish)!='') {
	//			$objResponse->script("alert('$onfinish');");
				$objResponse->script(" $onfinish");
			} else {
				$objResponse->assign($name, "innerHTML", "Done.");
			}
		}
	}
    return $objResponse;
}

?>