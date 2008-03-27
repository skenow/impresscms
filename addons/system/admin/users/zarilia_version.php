<?php
// $Id: zarilia_version.php,v 1.1 2007/03/16 02:37:04 catzwolf Exp $
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
/* System Information*/
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
    'category' => ZAR_SYSTEM_USER
    );


#$addonversion['name'] = _MD_AM_USER;
#$addonversion['description'] = _MD_AM_USER_DSC;
#$addonversion['author'] = _MD_AM_ADMIN_AUTHOR;
#$addonversion['license'] = "GPL see LICENSE";
#$addonversion['image'] = "user_admin.png";
#
#/*developer Information*/
#$addonversion['lead'] = "John Neill, Raimondas Rimkevicius";
#$addonversion['contributors'] = "Zarilia";
#$addonversion['credits'] = _MD_AM_ADMIN_CREDITS;
#$addonversion['website_url'] = "http://zarilia.com";
#$addonversion['website_name'] = "Zarilia";
#$addonversion['email'] = "webmaster@zarilia.com";
#$addonversion['version'] = "1.5";
#$addonversion['status'] = "Alpha";
#$addonversion['releasedate'] = "";
#$addonversion['disclaimer'] = "";
#/**
# */
#$addonversion['demo_site_url'] = "";
#$addonversion['demo_site_name'] = "";
#$addonversion['support_site_url'] = "";
#$addonversion['support_site_name'] = "";
#$addonversion['submit_bug_url'] = "";
#$addonversion['submit_bug_name'] = "";
#$addonversion['submit_feature_url'] = "";
#$addonversion['submit_feature_name'] = "";
#
#/**/
#$addonversion['official'] = 1;
#$addonversion['system'] = 1;
#$addonversion['hasAdmin'] = 0;
#$addonversion['adminpath'] = "index.php?fct=users";
#$addonversion['category'] = ZAR_SYSTEM_USER;
$addonversion['menu']['users'][] = array( 'url' => ZAR_URL . "/addons/system/index.php?fct=users", 'title' => _MD_AM_USER_MENU, 'class' => 'useradmin' );
$addonversion['menu']['users'][] = array( 'url' => ZAR_URL . "/addons/system/index.php?fct=users&amp;op=edituser", 'title' => _MD_AM_USERNEW_MENU, 'class' => 'useradd' );
?>