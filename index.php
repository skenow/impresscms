<?php
/**
 * Site index aka home page.
 * redirects to installation, if ImpressCMS is not installed yet
 * 
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License(GPL)
 * @package		core
 * @author	    Sina Asghari(aka stranger) <pesian_stranger@users.sourceforge.net>
 * @version		AVN: $Id: index.php 11072 2011-03-14 15:52:14Z m0nty_ $
 **/

/** Need the mainfile */
include "mainfile.php";

$member_handler = icms::handler('icms_member');
$group = $member_handler->getUserBestGroup((@is_object(icms::$user) ? icms::$user->getVar('uid') : 0));

/* HACK FOR RESPONSES */
if (isset($_REQUEST['module']) && isset($_REQUEST['action'])) {    
    $file = ICMS_MODULES_PATH . '/' . $_REQUEST['module'] . '/actions/' . $_REQUEST['action'] . '.php';    
    if (file_exists($file)) {
        
        icms::$xoopsDB->allowWebChanges = true;
        icms::$db->allowWebChanges = true;
        if (!(isset($_REQUEST['logging_enabled']) && $_REQUEST['logging_enabled']))
            icms::$logger->disableRendering();
        
        include_once $file;
        
        $response = icms_collection_Response::getInstance();
        $rt = $_REQUEST;
        foreach (array('logging_enabled', 'show_headers', 'return_format', 'module', 'action') as $key)
           if (isset($rt[$key]))
              unset($rt[$key]);
                
        $response->addModuleAction($_REQUEST['action'], $rt);
        
        if (isset($_REQUEST['logging_enabled']) && (strtolower($_REQUEST['logging_enabled']) == 'yes')) {
            $logger = icms_core_Logger::instance();
            $log_data = array();
            foreach (array('queries', 'extra', 'errors', 'deprecated') as $name)
                $log_data[$name] = $logger->$name;
            $response->add('system_log', $log_data);
        }
        if (isset($_REQUEST['show_headers']) && (strtolower($_REQUEST['show_headers']) == 'yes')) {
            $response->add('system_headers', getallheaders());
            $response->add('$_GET', $_GET);
            $response->add('$_POST', $_POST);
            $response->add('$_FILES', $_FILES);
            $response->add('$_SERVER', $_SERVER);
        }
        $ret_type = isset($_REQUEST['return_format'])?intval($_REQUEST['return_format']):icms_collection_Response::FORMAT_JSON;
        header('Content-Type: text/plain');
        echo $response->render($ret_type);
        die();  
    }        
}
/* ------------------ */

// moved from include/common.php file
if ($icmsConfig['closesite'] == 1) {
    include ICMS_INCLUDE_PATH . '/site-closed.php';
}

$icmsConfig['startpage'] = $icmsConfig['startpage'][$group];

if (isset($icmsConfig['startpage']) && $icmsConfig['startpage'] != "" && $icmsConfig['startpage'] != "--") {
	$arr = explode('-', $icmsConfig['startpage']);
	if (count($arr) > 1) {
		$page_handler = icms::handler('icms_data_page');
		$page = $page_handler->get($arr[1]);
		if (is_object($page)) {
			$url =(substr($page->getVar('page_url'), 0, 7) == 'http://')
				? $page->getVar('page_url') : ICMS_URL . '/' . $page->getVar('page_url');
			header('Location: ' . $url);
		} else {
			$icmsConfig['startpage'] = '--';
			$xoopsOption['show_cblock'] = 1;
			/** Included to start page rendering */
			include "header.php";
			/** Included to complete page rendering */
			include "footer.php";
		}
	} else {
		header('Location: ' . ICMS_MODULES_URL . '/' . $icmsConfig['startpage'] . '/');
	}
	exit();
} else {
	$xoopsOption['show_cblock'] = 1;
	/** Included to start page rendering */
	include "header.php";
	/** Included to complete page rendering */
	include "footer.php";
}
