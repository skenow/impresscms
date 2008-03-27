<?php
/*
 * Smarty plugin
 * ------------------------------------------------------------- 
 * File:     resource.db.php
 * Type:     resource
 * Name:     db
 * Purpose:  Fetches templates from a database
 * -------------------------------------------------------------
 */
function smarty_resource_db_source($tpl_name, &$tpl_source, &$smarty)
{
    $tplfile_handler =& zarilia_gethandler('tplfile');
	$tplobj =& $tplfile_handler->find($GLOBALS['zariliaConfig']['template_set'], null, null, null, $tpl_name, true);
	if (count($tplobj) > 0) {
		if (false != $smarty->zarilia_canUpdateFromFile()) {
			$conf_theme = isset($GLOBALS['zariliaConfig']['theme_set']) ? $GLOBALS['zariliaConfig']['theme_set'] : 'default';
			if ($conf_theme != 'default') {
				switch ($tplobj[0]->getVar('tpl_type')) {
					case 'addon':
						$filepath = ZAR_THEME_PATH.'/'.$conf_theme.'/templates/'.$tplobj[0]->getVar('tpl_addon').'/'.$tpl_name;
						break;
					case 'block':
						$filepath = ZAR_THEME_PATH.'/'.$conf_theme.'/templates/'.$tplobj[0]->getVar('tpl_addon').'/blocks/'.$tpl_name;
						break;
					default:
						$filepath = "";
						break;
				}
			} else {
				switch ($tplobj[0]->getVar('tpl_type')) {
					case 'addon':
						$filepath = ZAR_ROOT_PATH.'/addons/'.$tplobj[0]->getVar('tpl_addon').'/templates/'.$tpl_name;
						break;
					case 'block':
						$filepath = ZAR_ROOT_PATH.'/addons/'.$tplobj[0]->getVar('tpl_addon').'/templates/blocks/'.$tpl_name;
						break;
					default:
						$filepath = "";
						break;
				}
			}
			if ($filepath != "" && file_exists($filepath)) {
				$file_modified = filemtime($filepath);
				if ($file_modified > $tplobj[0]->getVar('tpl_lastmodified')) {
					if (false != $fp = fopen($filepath, 'r')) {
						$filesource = fread($fp, filesize($filepath));
    					fclose($fp);
						$tplobj[0]->setVar('tpl_source', $filesource, true);
						$tplobj[0]->setVar('tpl_lastmodified', time());
						$tplobj[0]->setVar('tpl_lastimported', time());
    					$tplfile_handler->forceUpdate($tplobj[0]);
						$tpl_source = $filesource;
        				return true;
					}
				}
			} 
		}
        $tpl_source = $tplobj[0]->getVar('tpl_source');
        return true;
    } else {
		trigger_error('Can\'t fetch template from db:'.$tpl_name, E_USER_ERROR);
		return false;
	}
}

function smarty_resource_db_timestamp($tpl_name, &$tpl_timestamp, &$smarty)
{
    $tplfile_handler =& zarilia_gethandler('tplfile');
    $tplobj =& $tplfile_handler->find($GLOBALS['zariliaConfig']['template_set'], null, null, null, $tpl_name, false);
	if (count($tplobj) > 0) {
		if (false != $smarty->zarilia_canUpdateFromFile()) {
			$conf_theme = isset($GLOBALS['zariliaConfig']['theme_set']) ? $GLOBALS['zariliaConfig']['theme_set'] : 'default';
			if ($conf_theme != 'default') {
				switch ($tplobj[0]->getVar('tpl_type')) {
					case 'addon':
						$filepath = ZAR_THEME_PATH.'/'.$conf_theme.'/templates/'.$tplobj[0]->getVar('tpl_addon').'/'.$tpl_name;
						break;
					case 'block':
						$filepath = ZAR_THEME_PATH.'/'.$conf_theme.'/templates/'.$tplobj[0]->getVar('tpl_addon').'/blocks/'.$tpl_name;
						break;
					default:
						$filepath = "";
						break;
				}
			} else {
				switch ($tplobj[0]->getVar('tpl_type')) {
					case 'addon':
						$filepath = ZAR_ROOT_PATH.'/addons/'.$tplobj[0]->getVar('tpl_addon').'/templates/'.$tpl_name;
						break;
					case 'block':
						$filepath = ZAR_ROOT_PATH.'/addons/'.$tplobj[0]->getVar('tpl_addon').'/templates/blocks/'.$tpl_name;
						break;
					default:
						$filepath = "";
						break;
				}
			}
			if ($filepath != "" && file_exists($filepath)) {
				$file_modified = filemtime($filepath);
				if ($file_modified > $tplobj[0]->getVar('tpl_lastmodified')) {
					$tpl_timestamp = $file_modified;
					return true;
				}
			}
		}
        $tpl_timestamp = $tplobj[0]->getVar('tpl_lastmodified');
        return true;
    } else {
		return false;
	}
}

function smarty_resource_db_secure($tpl_name, &$smarty)
{
    // assume all templates are secure
    return true;
}

function smarty_resource_db_trusted($tpl_name, &$smarty)
{
    // not used for templates
}
?>