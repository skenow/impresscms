<?php
/**
 * Installer class for ImpressCMS
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) v3
 * @category	ICMS
 * @package		Administration
 * @subpackage	Installation
 * @since		2.0
 */

/**
 * Installer object
 * @category	ICMS
 * @package		Administration
 * @subpackage	Installation
 * @since		2.0
 */
class Install {

	/** is the install running on Windows OS? */
	private $_isWindows;
	
	/** file paths for the installation */
	public $paths;

	/**
	 * Creates the install object
	 */
	public function __construct() {
		$this->_buildPaths();
	}
	
	/**
	 * Determine the various paths necessary, parsing from the server
	 */
	private function _buildPaths() {
		$this->_isWindows = strtolower(substr(PHP_OS, 0, 3)) === 'win';

		$schema = strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === FALSE ? 'http://' : 'https://';
		$host = $_SERVER['HTTP_HOST'] . '/';
		$script = $_SERVER['SCRIPT_NAME'];
		$query = $_SERVER['QUERY_STRING'];
		$requestURI = $_SERVER['REQUEST_URI'];
				
		/* define these with a trailing slash, as directories should be */
		$installPath = dirname(__FILE__) . '/';
		$siteRootPath = dirname(dirname(__FILE__)) . '/';
		$installTrustPath = $installPath . 'trustpath/';
		$targetTrustPath = dirname($siteRootPath) . '/'; // @todo	implement _suggestTrustPath()
		
		if ($this->_isWindows) {
			$installPath = str_replace('\\', '/', $installPath);
			$siteRootPath = str_replace('\\', '/', $siteRootPath);
			$installTrustPath = str_replace('\\', '/', $installTrustPath);
			$targetTrustPath = str_replace('\\', '/', $targetTrustPath);
		}
		
		$siteURI = $schema . $host . basename($siteRootPath) . '/';
		$this->paths = array(
				'installPath' => $installPath,
				'siteRootPath' => $siteRootPath,
				'installTrustPath' => $installTrustPath,
				'targetTrustPath' => $targetTrustPath,
				'siteURI' => $siteURI,
			);
		return $this;
	}
	
	/**
	 * A method to suggest a trust path for the installation
	 */
	private function _suggestTrustPath() {
		
	}
	
	/**
	 * Check if the PHP version is suitable
	 *
	 * @param	string	$required	the required version of PHP
	 * @return	array	$messages	an array of messages if errors, an empty array if successful
	 */
	public function checkPHPVersion($required){
		$messages = array();
		$phpOK = version_compare(phpversion(), $required, '>=');
		if ($phpOK) return;
		$messages[] = sprintf(_PHP_VERSION_FAIL, phpversion(), $required);
		return $messages;
	}

	/**
	 * Check if the required PHP extensions are available
	 *
	 * @param	array	$phprequirements	required PHP extensions
	 * @return	array	$messages			an array of messages if errors, an empty array if successful
	 */
	public function checkPHPExtensions(array $phprequirements) {
		$messages = array();
		$availableExtenstions = get_loaded_extensions();
		foreach ($phprequirements as $requirement) {
			if (!in_array($requirement, $availableExtenstions)) {
				$messages[] = sprintf(_INSTALL_REQUIRED_EXTENSION_FAILURE, $requirement);
			}
		}
		return $messages;
	}

	/**
	 * Check if the PHP configuration settings meet the requirements
	 *
	 * @param	array	$settings		Required PHP settings
	 * @param	array	$phpSettings	PHP settings from the server
	 * @return	array	$messages		an array of messages if errors, an empty array if successful
	 */
	public function checkPHPSettings(array $settings, array $phpSettings) {
		$messages = array();
		foreach ($settings as $setting => $value) {
			if (!compare_values($phpSettings[$setting]['local_value'], $value[0], $value[1])) {
				$messages[] = sprintf(_INSTALL_REQUIRED_SETTING_FAILURE, $setting . $value['1'] . $value['0']);
			}
		}
		return $messages;
	}

	/**
	 * Check if the necessary file paths are writable
	 *
	 * @param	array	$paths			system paths to check
	 * @param	array	$phpSettings	PHP settings from the server
	 * @return	array	$messages		an array of messages if errors, an empty array if successful
	 */
	public function checkFilePaths(array $paths, array $phpSettings) {
		$messages = array();
		$mask = umask();
		$openRestrictions = $phpSettings['open_basedir'];


		foreach ($paths as $path){
			if (!is_writable($path)) {
				$messages[] = sprintf(_PATH_NOT_WRITABLE, $path);
			}
		}

		return $messages;
	}

	/**
	 * Check if the latest version of ImpressCMS is being installed
	 *
	 * @return	array	$messages	an array of messages if errors, an empty array if successful
	 */
	public function checkICMSVersion() {
		$messages = array();
		$versionChecker = icms_core_Versionchecker::getInstance();

		if (!$versionChecker->check()) {
			$messages[] = sprintf(_INSTALL_NEWER_VERSION, $versionChecker->installed_version_name, $versionChecker->latest_version_name);
		}

		return $messages;
	}

	/**
	 * Check if the database is available
	 *
	 * @param	string	$dbserver	name of the db server
	 * @param	string	$dbuser		authorized db user
	 * @param	string	$dbpassword	password for the db user
	 * @param	string	$dbname		name of the database
	 * @return	array	$messages	an array of messages if errors, an empty array if successful
	 */
	public function checkDB($dbserver, $dbuser, $dbpassword, $dbname) {
		$messages = array();
		$dblink = mysql_connect($dbserver, $dbuser, $dbpassword, TRUE);
		if (!$dblink) {
			$messages[] = sprintf(_DATABASE_CONNECTION_FAILED);
		} elseif (!mysql_select_db($dbname, $dblink)) { // connection successful, check for database
			//the database doesn't exist, try to create it
			$dbcreated = mysql_query("CREATE DATABASE `" . $dbname . "`");
			if (!$dbcreated) {
				$messages[] = sprintf(_DATABASE_UNAVAILABLE, $dbname);
			}
		}
		return $messages;
	}

	/**
	 * Write the secure data into the trust path
	 *
	 * @param	string	$installTrustPath
	 * @param	string	$dbserver	name of the db server
	 * @param	string	$dbuser		authorized db user
	 * @param	string	$dbpassword	password for the db user
	 * @param	string	$dbname		name of the database
	 * @param	string	$dbprefix	db table prefix to use for this installation
	 * @param	string	$sitesalt	site encryption salt
	 * @return	array	$messages	an array of messages if errors, an empty array if successful
	 */
	public function writeSecureData($installTrustPath, $dbserver, $dbuser, $dbpassword, $dbname, $dbprefix, $sitesalt = '') {

		if ($sitesalt == '') $sitesalt = hash('sha1', time() . $installTrustPath);

		$contents = "define('ICMS_DB_HOST', '$dbserver');" . PHP_EOL;
		$contents .= "define('ICMS_DB_USER', '$dbuser');" . PHP_EOL;
		$contents .= "define('ICMS_DB_PASS', '$dbpassword');" . PHP_EOL;
		$contents .= "define('ICMS_DB_NAME', '$dbname');" . PHP_EOL;
		$contents .= "define('ICMS_DB_PREFIX', '$dbprefix');" . PHP_EOL;
		$contents .= "define('ICMS_DB_SALT', '$sitesalt');" . PHP_EOL;

		$messages = icms_core_Filesystem::writeFile($contents, 'sdata', 'php', $installTrustPath, FALSE);
		return $messages;
	}

	/**
	 * Move the trust path from the installation folder to the production folder
	 *
	 * @param	string	$installTrustPath	initial location of the trust path
	 * @param	string	$targetTrustPath	target location for the trust path
	 * @return	array	$messages	an array of messages if errors, an empty array if successful
	 */
	public function moveTrustPath($installTrustPath, $targetTrustPath) {
		$messages = array();
		return $messages;
	}

	/**
	 * Write the contents of the mainfile, so the configuration info can be located
	 *
	 * @param	string	$sitepath	path to site mainfile
	 * @param	string	$trustpath	path for secure data
	 * @param	string	$sdata		filename of the secure data file
	 * @return	array	$messages	an array of messages if errors, an empty array if successful
	 */
	public function writeMainfile($sitepath, $trustpath, $sdata) {

		$contents = "define('ICMS_URL', '". ICMS_URL . "');" . PHP_EOL;
		$contents .= "define('ICMS_ROOT_PATH', dirname(dirname(__FILE__)));" . PHP_EOL;
		$contents .= "define('ICMS_TRUST_PATH', '" . substr($trustpath, 0, -1) . "');" . PHP_EOL;
		$contents .= "define('ICMS_SDATA', '$sdata');" . PHP_EOL;
		$contents .= "require ICMS_TRUST_PATH . '/' . ICMS_SDATA;" . PHP_EOL;

		$messages = icms_core_Filesystem::writeFile($contents, 'mainfile', 'php', $sitepath, FALSE);
		return $messages;
	}
}
