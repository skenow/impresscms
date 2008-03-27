<?php
// $Id: cpsetup.php,v 1.2 2007/04/12 14:15:22 catzwolf Exp $
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

/**
 * DHTML/Ajax control bar
 *
 * @package kernel
 * @subpackage ajax
 */
class ZariliaControl_CPSetup
	extends ZariliaControl {

	var $_tmpEvents = array();
	var $_tmpEventsPath = '';

	 /**
	 * Constructor
	 */
	function ZariliaControl_CPSetup ($name, $showConsole=false, $src='db:', $onFinish = '') {
		global $zariliaEvents;
		$vbar = new ZariliaControl_CPSetup_ProgressBar();
		$l = strpos($src, ':')+1;
		$source['type'] = strtolower(substr($src, 0, $l-1));
		$source['location'] = substr($src, $l);
		unset($l);
		$code = '<table border="0"><tr><td valign="middle" align="center"><b>Progress: </b></td><td>'.$vbar->renderProgressBar($name, 0)."</td></tr></table><div id=\"".$name."_desc\" style=\"display: ".((!$showConsole)?'block':'none')."\">Starting...</div>";
	    $code .= "<div id=\"".$name."_console_all\" style=\"position: relative; text-align: left; display: ".(($showConsole)?'block':'none')."; width: 98%; height: 200px; overflow: auto; border-style: solid; \"><div style=\"position: absolute; width: 100%; height: 50px; text-align: left;\" id=\"".$name."_console\"></div></div>";
		switch($source['type']) {
			case 'file':
				$count = count(file($source['location']));
				$this->SetVar('count',$count);
				unset($count);
				$this->ZariliaControl('CPSetup',$name,  $code, false);
			break;
			default:
				$this->_tmpEventsPath = ZAR_CACHE_PATH.'/event_commands_'.time().'.php';
				$source['location'] = $this->_tmpEventsPath;
/*				$this->SetVar('count',0);
				if (!is_object($zariliaEvents)) {
					$zariliaEvents = &zarilia_gethandler( 'events' );
				}
//				$zariliaEvents->deleteEvents();*/
				$this->ZariliaControl('CPSetup',$name,  $code, true);
			break;
		}
		$this->SetVar('source',$source);
		$this->SetVar('value',0);
		$this->SetVar('onfinish',"$onFinish");
		$function = $this->GenerateFunctionName();
		$this->RegisterFunction($function,array('count','value','source','onfinish'));
//		$this->SetEventHandler('onmouseover', $function);
		$this->AddJS($this->GetRJS($function));
	}

     /**
     * Add task to controls task list
     *
     * @param string $code		php source code to be executed on event
	 * @param string $stepdesc  step description
	 * @param string $error		message witch will be displayed on error
	 * @param int $errortype		selects error type
	 * @return boolean				event was added or not
     */
	function AddTask($code, $stepdesc="", $error="",  $errortype = E_WARNING ) {
		global $zariliaEvents, $zariliaUser;
		$done = "";
		if (!(is_object($zariliaUser))) return false;
		$this->_tmpEvents[] = array($code, $stepdesc, $error, $errortype);
/*        $events = new ZariliaEvents();
		$events->setVar('type',XEVENT_DTYPE_ONNEXT);
		$events->setVar('uid',$zariliaUser->getVar('uid'));
		$events->setVar('code',$code);
		$events->setVar('comment', urlencode(serialize(array("desc"=>$stepdesc,"error"=>$error,"done"=>$done,"errortype"=>$errortype))));
		$zariliaEvents->insert($events,true);
		$this->SetVar('count',$this->GetVar('count')+1);*/
		return true;
	}

	function render() {
		if ($this->_tmpEventsPath!='') {
			$this->_tmpEvents[] = array('', '', '', '');
			file_put_contents($this->_tmpEventsPath, '<?php return '.var_export($this->_tmpEvents, true).'; ?>');
			$this->SetVar('count',count($this->_tmpEvents)); 
		}
		return parent::render();
	}

	/**
	* Reads file nad creates tasks from it
	*
	* @param string $file
	*/
	function readfile($file) {
		foreach($this->_parse_data(file_get_contents($file)) as $value) {
			$this->AddTask($value['code'],
									 isset($value['xml']['params']['step'])?$value['xml']['params']['step']:'',
									 isset($value['xml']['params']['error'])?$value['xml']['params']['error']:'',
								 	 isset($value['xml']['params']['done'])?$value['xml']['params']['done']:'',
							  	  	 isset($value['xml']['params']['errortype'])?$value['xml']['params']['errortype']:E_WARNING);
		}
	}

	function _parse_tag($tag) {
		$rez = array();
		$tag = trim($tag);
		if (substr($tag,0,1)!='<') return false;
		if (substr($tag,-1)!='>') return false;
		$tag = trim(substr($tag,1,-1));
		$rez['close']=(substr($tag,0,1)=="/")?true:false;
		$rez['small']=(substr($tag,-1)=="/")?true:false;
		if ($rez['small']&&$rez['close']) {
			$tag = trim(substr($tag,1,-1));
		} elseif ($rez['small']) {
			$tag = trim(substr($tag,0,-1));
		} elseif ($rez['close']) {
			$tag = trim(substr($tag,1));
		}
		$rez['params'] = array();
		$i = strpos($tag, " ");
		if (!$i) {
			$rez['tag'] = $tag;
			return $rez;
		}
		$rez['tag'] = substr($tag,0,$i);
		$tag = substr($tag, $i+1);
		$tmp = split("([=\"*])",$tag);
		$k = count($tmp);
		for ($i=0;$i<$k;$i=$i+3)
			if ($i+2<$k)
				$rez['params'][trim($tmp[$i])] = $tmp[$i+2];
		return $rez;
	}

	function _parse_data($code) {
		$parts = split('<(\?|\?php)', $code);
		foreach ($parts as $key => $value) {
			if (trim($value)=="") {
				unset ($parts[$key]);
				continue;
			}
			$i = strpos($value, "?".">");
			if ($i<0) {
				unset ($parts[$key]);
			} else {
				$parts[$key] = trim(substr($value, 1, $i-1));
			}
		}
		$xcomment = "//|\!@~!~@!/|".rand(0,700);
		$parts = explode($xcomment, ereg_replace("((\/\/|\#)[ *]<)","$xcomment<",implode("\n",$parts)));
		foreach ($parts as $key => $value) {
			if (substr($value, 0, 1)!="<") {
				unset($parts[$key]);
				continue;
			}
			$i = strpos($value, ">");
			if ($i<0) {
				unset($parts[$key]);
			} else {
				$parts[$key] = array("xml"=>$this->_parse_tag(trim(substr($value, 0, ++$i)))
														,"code"=>trim(substr($value, $i)));
				if ($parts[$key]['xml']['close']) unset($parts[$key]);
			}
		}
		return $parts;
	}

}

?>