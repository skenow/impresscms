<?php
/**
 * A static class for file system functions
 *
 * Using a static class instead of a include file with global functions, along with
 * autoloading of classes, reduces the memory usage and only includes files when needed.
 *
 * @category	Core
 * @package		Filesystem
 * @author		Steve Kenow <skenow@impresscms.org>
 * @copyright	(c) 2007-2008 The ImpressCMS Project - www.impresscms.org
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		SVN: $Id$
 * @since		1.3
 */

/**
 * Perform filesystem actions
 */
class core_Filesystem {

	/* Since all the methods are static, there is no __construct necessary	 */

	/**
	 *
	 * Change the permission of a file or folder
	 * Replaces icms_chmod()
	 *
	 * @author	Newbb2	developement team
	 * @param	string	$target	target file or folder
	 * @param	int		$mode	permission
	 * @return	bool	Returns true on success, false on failure
	 */
	public static function chmod($target, $mode = 0777) {
		return @chmod($target, $mode);
	}

	/**
	 *
	 * Safely create a folder and any folders in between
	 * Replaces icms_mkdir()
	 *
	 * @param string	$target		path to the folder to be created
	 * @param integer	$mode		permissions to set on the folder. This is affected by umask in effect
	 * @param string	$base		root location for the folder, ICMS_ROOT_PATH or ICMS_TRUST_PATH, for example
	 * @param array		$metachars	Characters to exclude from a valid path name
	 * @return boolean True if folder is created, False if it is not
	 */
	public static function mkdir($target, $mode = 0777, $base = ICMS_ROOT_PATH, $metachars = array()) {

		if( is_dir( $target )) return TRUE;
		if ( !isset($metachars) ) {
			$metachars = array('[', '?', '"', '.', '<', '>', '|', ' ', ':' );
		}

		$base = preg_replace ( '/[\\|\/]/', DIRECTORY_SEPARATOR, $base);
		$target = preg_replace ( '/[\\|\/]/', DIRECTORY_SEPARATOR, $target);
		if ($base !== '') {
			$target = str_ireplace($base . DIRECTORY_SEPARATOR, '', $target);
			$target = $base . DIRECTORY_SEPARATOR . str_replace( $metachars , '_', $target );
		} else {
			$target = str_replace($metachars , '_', $target);
		}
		if ( mkdir($target, $mode, TRUE) ) {
			// create an index.html file in this directory
			if ($fh = @fopen($target.'/index.html', 'w')) {
				fwrite($fh, '<script>history.go(-1);</script>');
				@fclose($fh);
			}

			if ( substr(decoct(fileperms($target)), 2) != $mode ) {
				chmod($target, $mode);
			}
		}
		return is_dir($target);
	}

	/**
	 *
	 * Removes the content of a folder.
	 * Replaces icms_clean_folders()
	 *
	 * @author	Steve Kenow (aka skenow) <skenow@impresscms.org>
	 * @author	modified by Vaughan <vaughan@impresscms.org>
	 * @author	modified by Sina Asghari (aka stranger) <pesian_stranger@users.sourceforge.net>
	 * @param	string	$dir	The folder path to cleaned. Must be an array like: array('templates_c' => ICMS_ROOT_PATH."/templates_c/");
	 * @param	bool  $remove_admin_cache	  True to remove admin cache, if required.
	 */
	public static function cleanFolders($dir, $remove_admin_cache = FALSE) {
		global $icmsConfig;
		foreach ($dir as $d) {
			$dd = opendir($d);
			while($file = readdir($dd)) {
				$files_array = $remove_admin_cache
						? ($file != 'index.html' && $file != 'php.ini' && $file != '.htaccess'
							&& $file != '.svn')
						: ($file != 'index.html' && $file != 'php.ini' && $file != '.htaccess'
							&& $file != '.svn' && $file != 'adminmenu_' . $icmsConfig['language'] . '.php');
				if (is_file($d.$file) && $files_array) {
					unlink($d.$file);
				}
			}
			closedir($dd);
		}
		return true;
	}

	/**
	 *
	 * Clean up all writable folders
	 * Replaces icms_cleaning_write_folders()
	 *
	 */
	public static function cleanWriteFolders() {
		return self::cleanFolders(
			array(
				'templates_c' => ICMS_ROOT_PATH . '/templates_c/',
				'cache' => ICMS_ROOT_PATH . '/cache/',
			)
		);
	}

	/**
	 *
	 * Copy a file, or a folder and its contents
	 * Replaces icms_copyr()
	 *
	 * @author	Aidan Lister <aidan@php.net>
	 * @param	string	$source	The source
	 * @param	string	$dest	The destination
	 * @return	boolean	Returns true on success, false on failure
	 */
	public static function copyRecursive($source, $dest) {
		// Simple copy for a file
		if (is_file($source)) {return copy($source, $dest);}

		// Make destination directory
		if (!is_dir($dest)) {
			self::mkdir($dest, 0777, '');
		}

		// Loop through the folder
		$dir = dir($source);
		while(false !== $entry = $dir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {continue;}
			// Deep copy directories
			if (is_dir("$source/$entry") && ($dest !== "$source/$entry")) {
				self::copyRecursive("$source/$entry", "$dest/$entry");
			} else {
				copy("$source/$entry", "$dest/$entry");
			}
		}
		// Clean up
		$dir->close();
		return true;
	}

	/**
	 *
	 * Deletes a file
	 * Replaces icms_deleteFile()
	 *
	 * @param string $dirname path of the file
	 * @return	The unlinked dirname
	 */
	public static function deleteFile($dirname) {
		// Simple delete for a file
		if (is_file($dirname)) {
			return unlink($dirname);
		}
	}

	/**
	 *
	 * Copy a file, or a folder and its contents from a website to your host
	 * Replaces icms_stream_copy()
	 *
	 * @author	Sina Asghari <stranger@impresscms.org>
	 * @author	nensa at zeec dot biz
	 * @param	string	$src	The source
	 * @param	string 	$dest	  The destination
	 * @return 	boolean	Returns stream_copy_to_stream($src, $dest) on success, false on failure
	 */
	public static function copyStream($src, $dest) {
		$len = false;
		if(@ini_get('allow_url_fopen')){
			$fsrc = fopen($src, 'r');
			$fdest = fopen($dest, 'w+');
			$len = stream_copy_to_stream($fsrc, $fdest);
			fclose($fsrc);
			fclose($fdest);
		}
		return $len;
	}

	/**
	 *
	 * Recursively delete a directory
	 * Replaces icms_unlinkRecursive()
	 *
	 * @param string $dir Directory name
	 * @param bool $deleteRootToo Delete specified top-level directory as well
	 */
	public static function deleteRecursive($dir, $deleteRootToo=true) {
		if (!$dh = @opendir($dir)) {
			return;
		}
		while (false !== ($obj = readdir($dh))) {
			if ($obj == '.' || $obj == '..') {
				continue;
			}

			if (!@unlink($dir . '/' . $obj)) {
				self::deleteRecursive($dir . '/' . $obj, true);
			}
		}

		closedir($dh);

		if ($deleteRootToo) {
			@rmdir($dir);
		}

		return;
	}

}