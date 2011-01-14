<?php
/**
* Administration of security preferences, versionfile
*
* @copyright	http://www.impresscms.org/ The ImpressCMS Project
* @license	LICENSE.txt
* @package	Administration
* @since	1.4
* @author	Vaughan Montgomery <vaughan@impresscms.org>
* @version	$Id: icms_version.php 20671 2011-01-08 16:14:49Z m0nty_ $
*/

$modversion = array(
	'name' => _MD_AM_SEC_PREF,
	'version' => 1.0,
	'description' => "ImpressCMS Security Preferences",
	'author' => "Vaughan montgomery",
	'credits' => "The ImpressCMS Project",
	'help' => "security.html",
	'license' => "GPL see LICENSE",
	'official' => 1,
	'image' => "sec.gif",
	'hasAdmin' => 1,
	'adminpath' => "admin.php?fct=security",
	'category' => ICMS_SYSTEM_SECURITY,
	'group' => _MD_AM_GROUPS_SECURITY);