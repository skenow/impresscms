<?php
// $Id: zarilia_version.php,v 1.1 2007/03/16 02:36:45 catzwolf Exp $
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
$addonversion['name'] = _MD_AM_PREF;
$addonversion['description'] = _MD_AM_PREF_DSC;
$addonversion['author'] = _MD_AM_ADMIN_AUTHOR;
$addonversion['license'] = "GPL see LICENSE";
$addonversion['image'] = "settings.png";

/*developer Information*/
$addonversion['lead'] = "John Neill, Raimondas Rimkevicius";
$addonversion['contributors'] = "Zarilia";
$addonversion['credits'] = _MD_AM_ADMIN_CREDITS;
$addonversion['website_url'] = "http://zarilia.com";
$addonversion['website_name'] = "Zarilia";
$addonversion['email'] = "webmaster@zarilia.com";
$addonversion['version'] = "1.4";
$addonversion['status'] = "Alpha";
$addonversion['releasedate'] = "";
$addonversion['disclaimer'] = "";
/**
 */
$addonversion['demo_site_url'] = "";
$addonversion['demo_site_name'] = "";
$addonversion['support_site_url'] = "";
$addonversion['support_site_name'] = "";
$addonversion['submit_bug_url'] = "";
$addonversion['submit_bug_name'] = "";
$addonversion['submit_feature_url'] = "";
$addonversion['submit_feature_name'] = "";
/**/
$addonversion['official'] = 1;
$addonversion['system'] = 1;
$addonversion['hasAdmin'] = 1;
$addonversion['adminpath'] = "index.php?fct=preferences";
$addonversion['category'] = ZAR_SYSTEM_PREF;

$confcat_handler = &zarilia_gethandler( 'configcategory' );
if ($menu_config = &$confcat_handler->getCatConfigs( true )) {
	foreach ( $menu_config as $v ) {
		$lang_dsc = '';
		$addonversion['menu']['config'][] = array( 'url' => ZAR_URL . "/addons/system/index.php?fct=preferences&amp;op=show&amp;confcat_id=" . $v->getVar( 'confcat_id' ),
		    'title' => zarilia_constants( $v->getVar( 'confcat_name' ), '', '' ),
			'description' => $lang_dsc,
	        'class' => 'configs'
		    );
	    unset( $lang_dsc );
	}
}

?>