<?php

if (get_magic_quotes_gpc()) {
    $GLOBALS['clean_func'] = function ($value) {
        return is_array($value)?array_map($GLOBALS['clean_func'], $value):stripslashes($value);
    };
    foreach(array('_GET', '_REQUEST', '_POST') as $name)
        $$name = $GLOBALS['clean_func']($$name);
    unset($GLOBALS['clean_func'], $$name);
}

if (!empty($_SERVER['QUERY_STRING']) && substr($_SERVER['QUERY_STRING'], 0, 5) == 'icms:') {
    $i = strpos($_SERVER['QUERY_STRING'], ':', 6);    
    $data = rawurldecode(substr($_SERVER['QUERY_STRING'], $i + 1));
    $action = intval(substr($_SERVER['QUERY_STRING'], 5, $i - 5));
    switch ($action) {
        case 1:            
            $data = json_decode(gzinflate(base64_decode($data)), true);            
            if (is_array($data)) {
                $_REQUEST = array();
                foreach ($data as $key => $value) {
                    $k_parts = explode('[', $key);
                    $k_count = count($k_parts);
                    if ($k_count < 2) {
                        $_REQUEST[$key] = $value;
                    } else {
                        $key = $k_parts[0];                        
                        if (!isset($_REQUEST[$key]))
                            $_REQUEST[$key] = array();
                        $var = &$_REQUEST[$key];
                        for($i = 1; $i < $k_count; $i++) {
                            $key = substr($k_parts[$i], 0, -1);
                            if (($i + 1) == $k_count) {
                                $var[$key] = $value;
                            } else {
                                if (!isset($var[$key]))
                                    $var[$key] = array();
                                $var = &$var[$key];
                            }
                        }
                    }
                }                
            }            
        break;
    }
}

include "mainfile.php";

$action_handler = icms::handler('icms_action');
if (isset($_REQUEST['icms'])) {
    // Set output format (by default is JSON)    
    if (isset($_REQUEST['icms']['format']))
        $action_handler->output_format = intval($_REQUEST['icms']['format']);
    // Do we need to show headers ?
    if (isset($_REQUEST['icms']['show_headers']) && intval($_REQUEST['icms']['show_headers']))
        $action_handler->includeHeadersInResponse();
}

if ($action_handler->hasActions($_REQUEST)) { 
    if (isset($_REQUEST[$action_handler::PARAM_BASE_CONTROLS])) {
            $base_controls = explode(';', $_REQUEST[$action_handler::PARAM_BASE_CONTROLS]);
            unset($_REQUEST[$action_handler::PARAM_BASE_CONTROLS]);           
    }
    $member_handler = icms::handler('icms_member');
    $group = $member_handler->getUserBestGroup((icms::$user instanceof icms_member_user_Object) ? icms::$user->getVar('uid') : 0);
    $rq = $_REQUEST;
    foreach ($_COOKIE as $key => $value)
        unset($rq[$key]);
    unset($key, $value);
    if (isset($rq['icms']))
        unset($rq['icms']);
    $action_handler->execActionFromArray($rq);
    if (isset($base_controls)) {
         $controls_handler = icms::handler('icms_controls');
         $required = array();         
         $diff = array_diff($controls_handler::$renderedControlTypes, $base_controls);
                          
         if (is_array($diff) && !empty($diff)) {
            foreach ($diff as $m_type) {
                $ctl = $controls_handler->make($m_type);
                foreach ($ctl->getRequiredURLs() as $type => $urls)
                    if (isset($required[$type]))
                        $required[$type] = array_merge($required[$type], $urls);
                    else
                        $required[$type] = $urls;
            }
            unset($m_type, $urls);
         }         
         
		 if (!empty($required))
			$action_handler->response->add('load_files', $required);
         unset($diff, $required);
    }
    unset($base_controls);
} else {
    $action_handler->response->error('Unknown request!');
}

if (isset($_REQUEST['icms']['logging_enabled']) && intval($_REQUEST['icms']['logging_enabled']))
    $action_handler->includeLoggingInfoInResponse();

$action_handler->render();