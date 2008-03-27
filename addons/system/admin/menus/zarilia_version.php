<?php
// $Id: zarilia_version.php,v 1.2 2007/03/30 22:04:42 catzwolf Exp $
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
$addonversion = array( 'name' => _MA_INFO_NAME,
    'description' => _MA_INFO_DESCRIPTION,
    'author' => _MA_INFO_AUTHOR,
    'license' => _MA_INFO_LICENSE,
    'image' => _MA_INFO_IMAGE,
    'lead' => _MA_INFO_LEAD,
    'contributors' => _MA_INFO_CONTRIBUTORS,
    'credits' => _MA_INFO_CREDITS,
    'website_url' => _MA_INFO_WEBSITE_URL,
    'website_name' => _MA_INFO_WEBSITE_NAME,
    'email' => _MA_INFO_EMAIL,
    'version' => _MA_INFO_VERSION,
    'status' => _MA_INFO_STATUS,
    'releasedate' => _MA_INFO_RELEASEDATE,
    'disclaimer' => _MA_INFO_DISCLAIMER,
    'demo_site_url' => _MA_INFO_DEMO_SITE_URL,
    'demo_site_name' => _MA_INFO_DEMO_SITE_NAME,
    'support_site_url' => _MA_INFO_SUPPORT_SITE_URL,
    'support_site_name' => _MA_INFO_SUPPORT_SITE_NAME,
    'submit_bug_url' => _MA_INFO_SUBMIT_BUG_URL,
    'submit_bug_name' => _MA_INFO_SUBMIT_BUG_NAME,
    'submit_feature_url' => _MA_INFO_SUBMIT_FEATURE_URL,
    'submit_feature_name' => _MA_INFO_SUBMIT_FEATURE_NAME,
    'official' => _MA_INFO_OFFICIAL,
    'system' => _MA_INFO_SYSTEM,
    'hasAdmin' => _MA_INFO_HASADMIN,
    'adminpath' => _MA_INFO_PATH,
    'category' => ZAR_SYSTEM_MENUS
    );

$addonversion['menu']['menu'][] =
	array(
		'url' => ZAR_URL . "/addons/system/index.php?fct=menus",
		'title' => _MD_AM_MENUS_MENU,
		'class' => 'menus'
	);

$addonversion['menu']['menu'][] =
	array(
		'url' => ZAR_URL . "/addons/system/index.php?fct=menus&op=menus&menu_type=mainmenu",
		'title' => _MD_AM_MENUS_MAINMENU,
		'class' => 'menus',
		'group' => 'menu'
	);

$addonversion['menu']['menu'][] =
	array(
		'url' => ZAR_URL . "/addons/system/index.php?fct=menus&op=menus&menu_type=usermenu",
		'title' => _MD_AM_MENUS_USERMENU,
		'class' => 'menus',
		'group' => 'menu'
	);

$addonversion['menu']['menu'][] =
	array(
		'url' => ZAR_URL . "/addons/system/index.php?fct=menus&op=menus&menu_type=topmenu",
		'title' => _MD_AM_MENUS_TOPMENU,
		'class' => 'menus',
		'group' => 'menu'
	);

$addonversion['menu']['menu'][] =
	array(
		'url' => ZAR_URL . "/addons/system/index.php?fct=menus&op=menus&menu_type=footermenu",
		'title' => _MD_AM_MENUS_FOOTERMENU,
		'class' => 'menus',
		'group' => 'menu'
	);

?>