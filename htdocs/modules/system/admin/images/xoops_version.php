<?php
// $Id: xoops_version.php 8483 2009-04-05 12:06:11Z icmsunderdog $
/**
* Administration of images, versionfile
*
* @copyright	http://www.xoops.org/ The XOOPS Project
* @copyright	XOOPS_copyrights.txt
* @copyright	http://www.impresscms.org/ The ImpressCMS Project
* @license	LICENSE.txt
* @package	Administration
* @since	XOOPS
* @author	http://www.xoops.org The XOOPS Project
* @author	modified by UnderDog <underdog@impresscms.org>
* @version	$Id: xoops_version.php 8483 2009-04-05 12:06:11Z icmsunderdog $
*/

$modversion = array(
	'name' => _MD_AM_IMAGES,
	'version' => "",
	'description' => _MD_AM_IMAGES_DSC,
	'author' => "",
	'credits' => "The ImpressCMS Project",
	'help' => "images.html",
	'license' => "GPL see LICENSE",
	'official' => 1,
	'image' => "images.gif",
	'hasAdmin' => 1,
	'adminpath' => "admin.php?fct=images",
	'category' => XOOPS_SYSTEM_IMAGE,
	'group' => _MD_AM_GROUPS_MEDIA);

