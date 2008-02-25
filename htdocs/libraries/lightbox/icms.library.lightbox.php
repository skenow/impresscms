<?php
/**
* Initiating file for the third party library Lightbox
*
* This file is responsible for initiating the Lightbox library within ImpressCMS
*
* @copyright	The ImpressCMS Project http://www.impresscms.org/
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @package		libraries
* @since		1.1
* @author		marcan <therplima@impresscms.org>
* @version		$Id: icms.library.lightbox.php 983 2008-02-24 18:26:45Z therplima $
*/

function icmsLibraryLightbox_BeforeFooter() {
	global $xoTheme;
	
	$xoTheme->addScript(ICMS_LIBRARIES_URL . '/lightbox/js/lightbox.js');
	$js  = 'var fileLoadingImage = "'.ICMS_LIBRARIES_URL.'/lightbox/images/loading.gif";';
	$js .= 'var fileBottomNavCloseImage = "'.ICMS_LIBRARIES_URL.'/lightbox/images/close.gif";';
	$xoTheme->addScript('','',$js);
	$xoTheme->addStylesheet(ICMS_LIBRARIES_URL . '/lightbox/css/lightbox.css');
}

function icmsLibraryLightbox_AdminBeforeFooter() {
	$ret  = '<script type="text/javascript" src="'.ICMS_LIBRARIES_URL.'/lightbox/js/lightbox.js"></script>';
	$ret .= '<script type="text/javascript">var fileLoadingImage = "'.ICMS_LIBRARIES_URL.'/lightbox/images/loading.gif";';
	$ret .= 'var fileBottomNavCloseImage = "'.ICMS_LIBRARIES_URL.'/lightbox/images/close.gif";</script>';
	
	$ret .= '<link rel="stylesheet" type="text/css" media="all" href="'.ICMS_LIBRARIES_URL.'/lightbox/css/lightbox.css" />';
	
	echo $ret;
}
?>