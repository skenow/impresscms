<?php
/**
 *
 * ImpressCMS Autoloader
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @package		core
 * @since		1.3
 * @author		Marc-André Lanciault (aka marcan) <mal@inboxintl.com>
 * @version		$Id: common.php 19427 2010-06-16 03:31:22Z skenow $
 */

/**
 * Debug feature of autoloader
 * to be removed before release
 */
define('ICMS_AUTOLOADER', false);

function icms_autoload( $class ) {
	if (ICMS_AUTOLOADER) echo 'class = ' . $class;
	$file = str_replace( '_', DIRECTORY_SEPARATOR, $class );
	if ( file_exists( $path = ICMS_ROOT_PATH . "/class/$file.php" ) ) {
		include_once $path;
	}
}

function icms_autoload_register() {
	static $reg = false;
	if ( !$reg ) {
		spl_autoload_register( "icms_autoload" );
		$reg = true;
	}
}

icms_autoload_register();


