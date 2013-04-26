<?php
/**
 * Logic and rendering for adminstration of modules
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @package		System
 * @subpackage	Modules
 * @author		Sina Asghari (aka stranger) <pesian_stranger@users.sourceforge.net>
 * @version		SVN: $Id$
 */

if (!is_object(icms::$user) || !is_object($icmsModule) || !icms::$user->isAdmin($icmsModule->getVar('mid'))) {
	exit("Access Denied");
}

/**
 * Logic and rendering for listing modules
 * @return NULL	Assigns content to the template
 */
function xoops_module_list() {
	global $icmsAdminTpl, $icmsConfig;

	$icmsAdminTpl->assign('lang_madmin', _MD_AM_MODADMIN);
	$icmsAdminTpl->assign('lang_module', _MD_AM_MODULE);
	$icmsAdminTpl->assign('lang_version', _MD_AM_VERSION);
	$icmsAdminTpl->assign('lang_modstatus', _MD_AM_MODULES_STATUS);
	$icmsAdminTpl->assign('lang_lastup', _MD_AM_LASTUP);
	$icmsAdminTpl->assign('lang_active', _MD_AM_ACTIVE);
	$icmsAdminTpl->assign('lang_order', _MD_AM_ORDER);
	$icmsAdminTpl->assign('lang_order0', _MD_AM_ORDER0);
	$icmsAdminTpl->assign('lang_action', _AM_ACTION);
	$icmsAdminTpl->assign('lang_modulename', _MD_AM_MODULES_MODULENAME);
	$icmsAdminTpl->assign('lang_moduletitle', _MD_AM_MODULES_MODULETITLE);
	$icmsAdminTpl->assign('lang_info', _INFO);
	$icmsAdminTpl->assign('lang_update', _MD_AM_UPDATE);
	$icmsAdminTpl->assign('lang_unistall', _MD_AM_UNINSTALL);
	$icmsAdminTpl->assign('lang_support', _MD_AM_MODULES_SUPPORT);
	$icmsAdminTpl->assign('lang_submit', _MD_AM_SUBMIT);
	$icmsAdminTpl->assign('lang_install', _MD_AM_INSTALL);
	$icmsAdminTpl->assign('lang_installed', _MD_AM_INSTALLED);
	$icmsAdminTpl->assign('lang_noninstall', _MD_AM_NONINSTALL);

	$module_handler = icms::handler('icms_module');
	$installed_mods = $module_handler->getObjects();
	$listed_mods = array();
	foreach ($installed_mods as $module) {
		$module->registerClassPath(FALSE);
		$module->getInfo();
		$mod = array(
			'mid' => $module->getVar('mid'),
			'dirname' => $module->getVar('dirname'),
			'name' => $module->getInfo('name'),
			'title' => $module->getVar('name'),
			'image' => $module->getInfo('image'),
			'adminindex' => $module->getInfo('adminindex'),
			'hasadmin' => $module->getVar('hasadmin'),
			'hasmain' => $module->getVar('hasmain'),
			'isactive' => $module->getVar('isactive'),
			'version' => icms_conv_nr2local(round($module -> getVar('version') / 100, 2)),
			'status' => ($module->getInfo('status')) ? $module->getInfo('status') : '&nbsp;',
			'last_update' => ($module->getVar('last_update') != 0) ? formatTimestamp($module->getVar('last_update'), 'm') : '&nbsp;',
			'weight' => $module->getVar('weight'),
			'support_site_url' => $module->getInfo('support_site_url'),
		);
		$icmsAdminTpl->append('modules', $mod);
		$listed_mods[] = $module->getVar('dirname');
	}

	$dirlist = icms_module_Handler::getAvailable();
	$uninstalled = array_diff($dirlist, $listed_mods);
	foreach ($uninstalled as $file) {
		clearstatcache();
		$file = trim($file);
			$module =& $module_handler->create();
			if (!$module->loadInfo($file, FALSE)) {
				continue;
			}
			$mod = array(
				'dirname' => $module->getInfo('dirname'),
				'name' => $module->getInfo('name'),
				'image' => $module->getInfo('image'),
				'version' => icms_conv_nr2local(round($module->getInfo('version'), 2)),
				'status' => $module->getInfo('status'),
			);
			$icmsAdminTpl->append('avmodules', $mod);
			unset($module);
	}

	return $icmsAdminTpl->fetch('db:admin/modules/system_adm_modules.html');
}

/**
 * Logic for updating a module
 *
 * @todo	add installation_notify(), only if the version of the module changes
 *
 * @param 	str $dirname
 * @return	str	Result messages from the module update
 */
function icms_module_update($dirname) {

}

