<?php
/**
 * Administration of security preferences, main file
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license	LICENSE.txt
 * @package	Administration
 * @since	1.4
 * @author	Vaughan Montgomery <vaughan@impresscms.org>
 * @version	$Id: main.php 20671 2011-01-08 16:14:49Z m0nty_ $
 */

if (! is_object(icms::$user) 
	|| ! is_object($icmsModule) 
	|| ! icms::$user->isAdmin($icmsModule->getVar('mid'))
	) {
	exit("Access Denied");
} else {
	if (isset($_POST)) {
		foreach ($_POST as $k => $v) {
			${$k} = $v;
		}
	}
	$icmsAdminTpl = new icms_view_Tpl();
	$op =(isset($_GET['op'])) ? trim(filter_input(INPUT_GET, 'op'))
		: ((isset($_POST['op'])) ? trim(filter_input(INPUT_POST, 'op')) : 'list');

	if (isset($_GET['sec_cat_id'])) {
		$sec_cat_id = (int) $_GET['sec_cat_id'];
	}

	if ($op == 'list') {
		/**
		 * Allow easely change the order of Preferences.
		 * $order = 1; Alphabetically order;
		 * $order = 0; Weight order;
		 *
		 * @todo: Create a preference option to set this value and improve the way to change the order.
		 */
		$order = 1;
		$sec_cat_handler = icms::handler('icms_securityconfig_category');
		$sec_cats = $sec_cat_handler->getObjects();
		$catcount = count($sec_cats);
		$sccats = array();
		$i = 0;
		foreach ($sec_cats as $sec_cat) {
			$sccats [$i] ['id'] = $sec_cat->getVar('sec_cat_id');
			$sccats [$i] ['name'] = constant($sec_cat->getVar('sec_cat_name'));
			$column [] = constant($sec_cat->getVar('sec_cat_name'));
			$i ++;
		}
		if ($order == 1) {
			array_multisort($column, SORT_ASC, $sccats);
		}

		icms_cp_header();
		echo '<div class="CPbigTitle" style="background-image: url('
			. ICMS_URL . '/modules/system/admin/security/images/security_big.png)">'
			. _MD_AM_SITE_SEC_PREF . '</div><br /><ul>';
		foreach ($sccats as $sec_cat) {
			echo '<li>' . $sec_cat ['name']	.
				' [<a href="admin.php?fct=security&amp;op=show&amp;sec_cat_id='
				. $sec_cat ['id'] . '">' . _EDIT . '</a>]</li>';
		}
		echo '</ul>';
		icms_cp_footer();
		exit();
	}

	if ($op == 'show') {
		if (empty($sec_cat_id)) {
			$sec_cat_id = 1;
		}
		$sec_cat_handler = icms::handler('icms_securityconfig_category');
		$sec_cat = & $sec_cat_handler->get($sec_cat_id);
		if (! is_object($sec_cat)) {
			redirect_header('admin.php?fct=security', 1);
		}
		global $icmsSecurityConfigUser;
		$form = new icms_form_Theme(constant($sec_cat->getVar('sec_cat_name')), 'sec_form', 'admin.php?fct=security', 'post', true);
		$sconfig_handler = icms::handler('icms_securityconfig');
		$criteria = new icms_db_criteria_Compo();
		$criteria->add(new icms_db_criteria_Item('sec_modid', 0));
		$criteria->add(new icms_db_criteria_Item('sec_catid', $sec_cat_id));
		$sconfig = $sconfig_handler->getConfigs($criteria);
		$seccount = count($sconfig);
		for ($i = 0; $i < $seccount; $i ++) {
			$title =(!defined($sconfig[$i]->getVar('sec_desc')) || constant($sconfig[$i]->getVar('sec_desc')) == '')
				? constant($sconfig[$i]->getVar('sec_title')) : constant($sconfig[$i]->getVar('sec_title'))
				. '<img class="helptip" src="./images/view_off.png" alt="Vew help text" /><span class="helptext">'
				. constant($sconfig[$i]->getVar('sec_desc')) . '</span>';
			switch ($sconfig[$i]->getVar('sec_formtype')) {
				case 'textsarea' :
					if ($sconfig[$i]->getVar('sec_valuetype') == 'array') {
						// this is exceptional.. only when value type is array, need a smarter way for this
						$ele = ($sconfig[$i]->getVar('sec_value') != '')
							? new icms_form_elements_Textarea($title, $sconfig[$i]->getVar('sec_name'),
								icms_core_DataFilter::htmlSpecialChars(implode('|', $sconfig[$i]->getConfValueForOutput())), 5, 50)
							: new icms_form_elements_Textarea($title, $sconfig[$i]->getVar('sec_name'), '', 5, 50);
					} else {
						$ele = new icms_form_elements_Textarea($title, $sconfig[$i]->getVar('sec_name'),
							icms_core_DataFilter::htmlSpecialChars($sconfig[$i]->getConfValueForOutput()));
					}
					break;
					
				case 'textarea' :
					if ($sconfig[$i]->getVar('sec_valuetype') == 'array') {
						// this is exceptional.. only when value type is array, need a smarter way for this
						$ele = ($sconfig[$i]->getVar('sec_value') != '')
							? new icms_form_elements_Textarea($title, $sconfig[$i]->getVar('sec_name'),
								icms_core_DataFilter::htmlSpecialChars(implode('|', $sconfig[$i]->getConfValueForOutput())), 5, 50)
							: new icms_form_elements_Textarea($title, $sconfig[$i]->getVar('sec_name'), '', 5, 50);
					} else {
						$ele = new icms_form_elements_Dhtmltextarea($title, $sconfig[$i]->getVar('sec_name'),
							icms_core_DataFilter::htmlSpecialChars($sconfig[$i]->getConfValueForOutput()));
					}
					break;
					
				case 'autotasksystem':
					$handler = icms_getModuleHandler('autotasks', 'system');
					$options = &$handler->getSystemHandlersList(true);
					$ele = new icms_form_elements_Select($title, $sconfig[$i]->getVar('sec_name'),
						$sconfig[$i]->getConfValueForOutput(), 1, false);
					foreach ($options as $option) {
						$ele->addOption($option, $option);
					}
					unset($handler, $options, $option);
					break;
					
				case 'select' :
					$ele = new icms_form_elements_Select($title, $sconfig[$i]->getVar('sec_name'),
						$sconfig[$i]->getConfValueForOutput());
					$options = $sconfig_handler->getConfigOptions(new icms_db_criteria_Item('sec_id',
						$sconfig[$i]->getVar('sec_id')));
					$opcount = count($options);
					for ($j = 0; $j < $opcount; $j ++) {
						$optval = defined($options[$j]->getVar('sec_op_value'))
							? constant($options[$j]->getVar('sec_op_value')) : $options[$j]->getVar('sec_op_value');
						$optkey = defined($options[$j]->getVar('sec_op_name'))
							? constant($options[$j]->getVar('sec_op_name')) : $options[$j]->getVar('sec_op_name');
						$ele->addOption($optval, $optkey);
					}
					break;
					
				case 'select_multi' :
					$ele = new icms_form_elements_Select($title, $sconfig[$i]->getVar('sec_name'),
						$sconfig[$i]->getConfValueForOutput(), 5, true);
					$options = $sconfig_handler->getConfigOptions(new icms_db_criteria_Item('sec_id',
						$sconfig[$i]->getVar('sec_id')));
					$opcount = count($options);
					for ($j = 0; $j < $opcount; $j ++) {
						$optval = defined($options[$j]->getVar('sec_op_value'))
							? constant($options[$j]->getVar('sec_op_value'))
							: $options[$j]->getVar('sec_op_value');
						$optkey = defined($options[$j]->getVar('sec_op_name'))
							? constant($options[$j]->getVar('sec_op_name'))
							: $options[$j]->getVar('sec_op_name');
						$ele->addOption($optval, $optkey);
					}
					break;
					
				case 'yesno' :
					$ele = new icms_form_elements_Radioyn($title, $sconfig[$i]->getVar('sec_name'),
						$sconfig[$i]->getConfValueForOutput(), _YES, _NO);
					break;
					
				case 'editor' :
				case 'editor_source' :
					$type = explode('_', $sconfig[$i]->getVar('sec_formtype'));
					$ele = new icms_form_elements_Select($title, $sconfig[$i]->getVar('sec_name'),
						$sconfig[$i]->getConfValueForOutput());
					$type = array_pop($type);
					if ($type == 'editor') {
						$type = '';
					}
					$dirlist = icms_plugins_EditorHandler::getListByType($type);
					if (!empty($dirlist)) {
						asort($dirlist);
						$ele->addOptionArray($dirlist);
					}
					unset($type);
					break;

				case 'editor_multi' :
					$ele = new icms_form_elements_Select($title, $sconfig[$i]->getVar('sec_name'),
						$sconfig[$i]->getConfValueForOutput(), 5, true);
					$dirlist = icms_plugins_EditorHandler::getListByType();
					if (!empty($dirlist)) {
						asort($dirlist);
						$ele->addOptionArray($dirlist);
					}
					break;
						
				case 'select_plugin' :
					$ele = new icms_form_elements_Select($title, $sconfig[$i]->getVar('sec_name'),
						$sconfig[$i]->getConfValueForOutput(), 8, true);
					$dirlist = icms_core_Filesystem::getDirList(ICMS_ROOT_PATH.'/plugins/textsanitizer/');
					if (!empty($dirlist)) {
						asort($dirlist);
						$ele->addOptionArray($dirlist);
					}
					break;
					
				case 'group' :
					$ele = new icms_form_elements_select_Group($title, $sconfig[$i]->getVar('sec_name'), true,
						$sconfig[$i]->getConfValueForOutput(), 1, false);
					break;
					
				case 'group_multi' :
					$ele = new icms_form_elements_select_Group($title, $sconfig[$i]->getVar('sec_name'), true,
						$sconfig[$i]->getConfValueForOutput(), 5, true);
					break;
					
				// RMV-NOTIFY - added 'user' and 'user_multi'
				case 'user' :
					$ele = new icms_form_elements_select_User($title, $sconfig[$i]->getVar('sec_name'), false,
						$sconfig[$i]->getConfValueForOutput(), 1, false);
					break;
					
				case 'user_multi' :
					$ele = new icms_form_elements_select_User($title, $sconfig[$i]->getVar('sec_name'), false,
						$sconfig[$i]->getConfValueForOutput(), 5, true);
					break;
					
				case 'password' :
					$ele = new icms_form_elements_Password($title, $sconfig[$i]->getVar('sec_name'), 50, 255,
						icms_core_DataFilter::htmlSpecialChars($sconfig[$i]->getConfValueForOutput()), false,
							($icmsSecurityConfigUser['pass_level'] ? 'password_adv' : ''));
					break;
					
				case 'hidden' :
					$ele = new icms_form_elements_Hidden($sconfig[$i]->getVar('sec_name'),
						icms_core_DataFilter::htmlSpecialChars($sconfig[$i]->getConfValueForOutput()));
					break;
					
				case 'textbox' :
				default :
					$ele = new icms_form_elements_Text($title, $sconfig[$i]->getVar('sec_name'), 50, 255,
						icms_core_DataFilter::htmlspecialchars($sconfig[$i]->getConfValueForOutput()));
					break;
			}
			$hidden = new icms_form_elements_Hidden('sec_ids[]', $sconfig[$i]->getVar('sec_id'));
			$form->addElement($ele);
			$form->addElement($hidden);
			unset($ele, $hidden);
		}
		$form->addElement(new icms_form_elements_Hidden('op', 'save'));
		$form->addElement(new icms_form_elements_Button('', 'button', _GO, 'submit'));
		icms_cp_header();
		echo '<div class="CPbigTitle" style="background-image: url('
			. ICMS_URL . '/modules/system/admin/security/images/security_big.png)"><a href="admin.php?fct=security">'
			. _MD_AM_SEC_PREFMAIN . '</a>&nbsp;<span style="font-weight:bold;">&raquo;&raquo;</span>&nbsp;'
			. constant($sec_cat->getVar('sec_cat_name')) . '<br /><br /></div><br />';
		$form->display();
		icms_cp_footer();
		exit();
	}

	if ($op == 'showmod') {
		$sconfig_handler = icms::handler('icms_securityconfig');
		$mod = isset($_GET ['mod']) ? (int) $_GET ['mod'] : 0;
		if (empty($mod)) {
			header('Location: admin.php?fct=security');
			exit();
		}
		$sconfig = $sconfig_handler->getConfigs(new icms_db_criteria_Item('sec_modid', $mod));
		$count = count($sconfig);
		if ($count < 1) {
			redirect_header('admin.php?fct=security', 1);
		}
		$form = new icms_form_Theme(_MD_AM_MODCONFIG, 'sec_form', 'admin.php?fct=security', 'post', true);
		$module_handler = icms::handler('icms_module');
		$module = & $module_handler->get($mod);
		icms_loadLanguageFile($module->getVar('dirname'), 'modinfo');
		// if has comments feature, need comment lang file
		if ($module->getVar('hascomments') == 1) {
			icms_loadLanguageFile('core', 'comment');
		}
		// RMV-NOTIFY
		// if has notification feature, need notification lang file
		if ($module->getVar('hasnotification') == 1) {
			icms_loadLanguageFile('core', 'notification');
		}

		$modname = $module->getVar('name');
		if ($module->getInfo('adminindex')) {
			$form->addElement(new icms_form_elements_Hidden('redirect',
				ICMS_URL . '/modules/' . $module->getVar('dirname') . '/' . $module->getInfo('adminindex')));
		}
		for ($i = 0; $i < $count; $i ++) {
			$title =(!defined($sconfig[$i]->getVar('sec_desc')) || constant($sconfig[$i]->getVar('sec_desc')) == '')
				? constant($sconfig[$i]->getVar('sec_title'))
				: constant($sconfig[$i]->getVar('sec_title'))
					. '<img class="helptip" src="./images/view_off.png" alt="Vew help text" /><span class="helptext">'
					. constant($sconfig[$i]->getVar('sec_desc'))
					. '</span>';
			switch ($sconfig[$i]->getVar('sec_formtype')) {
				case 'textsarea' :
					if ($sconfig[$i]->getVar('sec_valuetype') == 'array') {
						// this is exceptional.. only when value type is arrayneed a smarter way for this
						$ele = ($sconfig[$i]->getVar('sec_value') != '')
							? new icms_form_elements_Textarea($title, $sconfig[$i]->getVar('sec_name'),
								icms_core_DataFilter::htmlSpecialChars(implode('|', $sconfig[$i]->getConfValueForOutput())), 5, 50)
							: new icms_form_elements_Textarea($title, $sconfig[$i]->getVar('sec_name'), '', 5, 50);
					} else {
						$ele = new icms_form_elements_Textarea($title, $sconfig[$i]->getVar('sec_name'),
							icms_core_DataFilter::htmlSpecialChars($sconfig[$i]->getConfValueForOutput()), 5, 50);
					}
					break;
					
				case 'textarea' :
					if ($sconfig[$i]->getVar('sec_valuetype') == 'array') {
						// this is exceptional.. only when value type is arrayneed a smarter way for this
						$ele = ($sconfig[$i]->getVar('sec_value') != '')
							? new icms_form_elements_Textarea($title, $sconfig[$i]->getVar('sec_name'),
								icms_core_DataFilter::htmlSpecialChars(implode('|', $sconfig[$i]->getConfValueForOutput())), 5, 50)
							: new icms_form_elements_Textarea($title, $sconfig[$i]->getVar('sec_name'), '', 5, 50);
					} else {
						$ele = new icms_form_elements_Dhtmltextarea($title, $sconfig[$i]->getVar('sec_name'),
							icms_core_DataFilter::htmlSpecialChars($sconfig[$i]->getConfValueForOutput()), 5, 50);
					}
					break;
					
				case 'select' :
					$ele = new icms_form_elements_Select($title, $sconfig[$i]->getVar('sec_name'),
						$sconfig[$i]->getConfValueForOutput());

					$options = & $sconfig_handler->getConfigOptions(new icms_db_criteria_Item('sec_id',
						$sconfig[$i]->getVar('sec_id')));
					$opcount = count($options);
					for ($j = 0; $j < $opcount; $j ++) {
						$optval = defined($options[$j]->getVar('sec_op_value'))
							? constant($options[$j]->getVar('sec_op_value'))
							: $options[$j]->getVar('sec_op_value');
						$optkey = defined($options[$j]->getVar('sec_op_name'))
							? constant($options[$j]->getVar('sec_op_name'))
							: $options[$j]->getVar('sec_op_name');
						$ele->addOption($optval, $optkey);
					}
					break;
					
				case 'select_multi' :
					$ele = new icms_form_elements_Select($title, $sconfig[$i]->getVar('sec_name'),
						$sconfig[$i]->getConfValueForOutput(), 5, true);
					$options = & $sconfig_handler->getConfigOptions(new icms_db_criteria_Item('sec_id',
						$sconfig[$i]->getVar('sec_id')));
					$opcount = count($options);
					for ($j = 0; $j < $opcount; $j ++) {
						$optval = defined($options[$j]->getVar('sec_op_value'))
							? constant($options[$j]->getVar('sec_op_value'))
							: $options[$j]->getVar('sec_op_value');
						$optkey = defined($options[$j]->getVar('sec_op_name'))
							? constant($options[$j]->getVar('sec_op_name'))
							: $options[$j]->getVar('sec_op_name');
						$ele->addOption($optval, $optkey);
					}
					break;
					
				case 'yesno' :
					$ele = new icms_form_elements_Radioyn($title, $sconfig[$i]->getVar('sec_name'),
						$sconfig[$i]->getConfValueForOutput(), _YES, _NO);
					break;
					
				case 'group' :
					$ele = new icms_form_elements_select_Group($title, $sconfig[$i]->getVar('sec_name'), true,
						$sconfig[$i]->getConfValueForOutput(), 1, false);
					break;
					
				case 'group_multi' :
					$ele = new icms_form_elements_select_Group($title, $sconfig[$i]->getVar('sec_name'), true,
						$sconfig[$i]->getConfValueForOutput(), 5, true);
					break;
					
					// RMV-NOTIFY: added 'user' and 'user_multi'
				case 'user' :
					$ele = new icms_form_elements_select_User($title, $sconfig[$i]->getVar('sec_name'), false,
						$sconfig[$i]->getConfValueForOutput(), 1, false);
					break;
					
				case 'user_multi' :
					$ele = new icms_form_elements_select_User($title, $sconfig[$i]->getVar('sec_name'), false,
						$sconfig[$i]->getConfValueForOutput(), 5, true);
					break;
					
				case 'password' :
					$ele = new icms_form_elements_Password($title, $sconfig[$i]->getVar('sec_name'), 50, 255,
						icms_core_DataFilter::htmlSpecialChars($sconfig[$i]->getConfValueForOutput()));
					break;
					
				case 'hidden' :
					$ele = new icms_form_elements_Hidden($sconfig[$i]->getVar('sec_name'),
						icms_core_DataFilter::htmlSpecialChars($sconfig[$i]->getConfValueForOutput()));
					break;
					
				case 'textbox' :
				default :
					$ele = new icms_form_elements_Text($title, $sconfig[$i]->getVar('sec_name'), 50, 255,
						icms_core_DataFilter::htmlSpecialChars($sconfig[$i]->getConfValueForOutput()));
					break;
			}
			$hidden = new icms_form_elements_Hidden('sec_ids[]', $sconfig[$i]->getVar('sec_id'));
			$form->addElement($ele);
			$form->addElement($hidden);
			unset($ele, $hidden);
		}
		$form->addElement(new icms_form_elements_Hidden('op', 'save'));
		$form->addElement(new icms_form_elements_Button('', 'button', _GO, 'submit'));
		icms_cp_header();
		if ($module->getInfo('hasAdmin') == true) {
			$modlink = '<a href="' . ICMS_URL . '/modules/' . $module->getVar('dirname') . '/'
				. $module->getInfo('adminindex') . '">' . $modname . '</a>';
		} else {
			$modlink = $modname;
		}
		$iconbig = $module->getInfo('iconbig');
		if (isset($iconbig) && $iconbig == false) {
			echo '<div class="CPbigTitle" style="background-image: url('
				. ICMS_URL . '/modules/system/admin/security/images/security_big.png);">'
				. $modlink . ' &raquo; ' . _SEC_PREFERENCES . '</div>';

		}
		if (isset($iconbig) && $iconbig == true) {
			echo '<div class="CPbigTitle" style="background-image: url('
				. ICMS_URL . '/modules/' . $module->getVar('dirname') . '/' . $iconbig . ')">'
				. $modlink . ' &raquo; ' . _SEC_PREFERENCES . '</div>';
		}
		$form->display();
		icms_cp_footer();
		exit();
	}

	if ($op == 'save') {
		if (!icms::$security->check()) {
			redirect_header('admin.php?fct=security', 3, implode('<br />', icms::$security->getErrors()));
		}
		$xoopsTpl = new icms_view_Tpl();
		$count = count($sec_ids);
		$encryption_updated = false;
		$purifier_style_updated = false;
		$saved_config_items = array();
		if ($count > 0) {
			for ($i = 0; $i < $count; $i ++) {
				$sconfig = & $sconfig_handler->getConfig($sec_ids [$i]);
				$new_value = & ${$sconfig->getVar('sec_name')};
				$old_value = $sconfig->getVar('sec_value');
				icms::$preload->triggerEvent('savingSystemAdminSecurityPreferencesItem', array(
												(int) $sconfig->getVar('sec_catid'),
												$sconfig->getVar('sec_name'),
												$sconfig->getVar('sec_value')
												));

				if (is_array($new_value) || $new_value != $sconfig->getVar('sec_value')) {
					// if password encryption has been changed
					if (!$encryption_updated && $sconfig->getVar('sec_catid') == ICMS_SEC_CONF_USER
						&& $sconfig->getVar('sec_name') == 'enc_type') {

						if ($sconfig->getVar('closesite') !== 1) {
							$member_handler = icms::handler('icms_member');
							$member_handler->updateUsersByField('pass_expired', 1);
							$encryption_updated = true;
						} else {
							redirect_header('admin.php?fct=security', 2, _MD_AM_UNABLEENCCLOSED);
						}
					}

					if (!$purifier_style_updated 
						&& $sconfig->getVar('sec_catid') == ICMS_SEC_CONF_HTMLFILTER
						&& $sconfig->getVar('sec_name') == 'purifier_Filter_ExtractStyleBlocks') {

						if ($sconfig->getVar('purifier_Filter_ExtractStyleBlocks') == 1) {
							if (!file_exists(ICMS_ROOT_PATH . '/plugins/csstidy/class.csstidy.php')) {
								redirect_header('admin.php?fct=security', 5, _MD_AM_UNABLECSSTIDY);
							}
							$purifier_style_updated = true;
						}
					}

					$sconfig->setConfValueForInput($new_value);
					$sconfig_handler->insertConfig($sconfig);
				}
				unset($new_value);

				if (!isset($saved_config_items[$sconfig->getVar('sec_catid')])) {
					$saved_config_items[$sconfig->getVar('sec_catid')] = array();
				}
				$saved_config_items[$sconfig->getVar('sec_catid')][$sconfig->getVar('sec_name')] = array(
																	$old_value,
																	$sconfig->getVar('sec_value')
																	);

			}
		}

		icms::$preload->triggerEvent('afterSaveSystemAdminSecurityPreferencesItems', $saved_config_items);
		unset($saved_config_items);

		if (! empty($use_mysession) && $icmsConfig ['use_mysession'] == 0 && $session_name != '') {
			setcookie($session_name, session_id(), time() +(60 *(int)($session_expire)), '/', '', 0);
		}

		// Clean cached files, may take long time
		// User register_shutdown_function to keep running after connection closes so that cleaning cached files can be finished
		// Cache management should be performed on a separate page
		register_shutdown_function(array(&$xoopsTpl, 'clear_all_cache'));

		// If language is changed, leave the admin menu file to be regenerated upon next request,
		// otherwise regenerate admin menu file for now
		if (!$lang_updated) {
			// regenerate admin menu file
			register_shutdown_function('xoops_module_write_admin_menu', impresscms_get_adminmenu());
		} else {
			$redirect = ICMS_URL . '/admin.php';
		}

		if (isset($redirect) && $redirect != '') {
			redirect_header($redirect, 2, _MD_AM_DBUPDATED);
		} else {
			redirect_header('admin.php?fct=security', 2, _MD_AM_DBUPDATED);
		}
	}
}

