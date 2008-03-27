<?php
// $Id: zarilia_version.php,v 1.1 2007/03/16 02:37:00 catzwolf Exp $
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
    'category' => ZAR_SYSTEM_PROFILES
    );
$addonversion['menu']['users'][] =  array( 'url' => ZAR_URL . "/addons/system/index.php?fct=userprofiles&amp;op=list_category", 'title' => _MD_AM_PROFILES_CAT, 'group'=>$addonversion['name'], 'class'=>'useradmin' );
$addonversion['menu']['users'][] =  array( 'url' => ZAR_URL . "/addons/system/index.php?fct=userprofiles&amp;op=list_profile", 'title' => _MD_AM_PROFILES_LIST, 'group'=>$addonversion['name'], 'class'=>'useradmin' );
$addonversion['menu']['users'][] =  array( 'url' => ZAR_URL . "/addons/system/index.php?fct=userprofiles&amp;op=edit&amp;profile_catid=12&amp;profile_id=0", 'title' => _MD_AM_PROFILES_NEW, 'group'=>$addonversion['name'], 'class'=>'useradd' );
?>