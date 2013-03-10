<?php
/**
 *
 * @copyright
 * @license
 * @category
 * @package
 * @subpackage
 * @since
 */

/**
 *
 * @author
 *
 */
class Install {

	private $_isWindows;
	public $paths;

	public function __construct() {
		$this->_buildPaths();
	}
	
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
		$targetTrustPath = dirname($siteRootPath);
		
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
	
	private function _suggestTrustPath() {
		
	}
	static public function checkPHPVersion($required){
		$messages = array();
		$phpOK = version_compare(phpversion(), $required, '>=');
		if ($phpOK) return;
		$messages[] = sprintf(_PHP_VERSION_FAIL, phpversion(), $required);
		return $messages;
	}

	static public function checkPHPExtensions(array $phprequirements) {
		$messages = array();
		$availableExtenstions = get_loaded_extensions();
		foreach ($phprequirements as $requirement) {
			if (!in_array($requirement, $availableExtenstions)) {
				$messages[] = sprintf(_INSTALL_REQUIRED_EXTENSION_FAILURE, $requirement);
			}
		}
		return $messages;
	}

	static public function checkPHPSettings(array $settings, array $phpSettings) {
		$messages = array();
		foreach ($settings as $setting => $value) {
			if (!compare_values($phpSettings[$setting]['local_value'], $value[0], $value[1])) {
				$messages[] = sprintf(_INSTALL_REQUIRED_SETTING_FAILURE, $setting . $value['1'] . $value['0']);
			}
		}
		return $messages;
	}

	static public function checkFilePaths(array $paths, array $phpSettings) {
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

	static public function checkICMSVersion() {
		$messages = array();
		$versionChecker = icms_core_Versionchecker::getInstance();

		if (!$versionChecker->check()) {
			$messages[] = sprintf(_INSTALL_NEWER_VERSION, $versionChecker->installed_version_name, $versionChecker->latest_version_name);
		}

		return $messages;
	}

	static public function checkDB($dbserver, $dbuser, $dbpassword, $dbname) {
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

	static public function writeSecureData($installTrustPath, $dbserver, $dbuser, $dbpassword, $dbname, $dbprefix, $sitesalt = '') {

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

	static public function moveTrustPath($installTrustPath, $targetTrustPath) {
		$messages = array();
		return $messages;
	}

	static public function writeMainfile($trustpath, $sdata) {

		$contents = "define('ICMS_URL', '". ICMS_URL . "');" . PHP_EOL;
		$contents .= "define('ICMS_ROOT_PATH', dirname(dirname(__FILE__)));" . PHP_EOL;
		$contents .= "define('ICMS_TRUST_PATH', '" . substr($trustpath, 0, -1) . "');" . PHP_EOL;
		$contents .= "define('ICMS_SDATA', '$sdata');" . PHP_EOL;
		$contents .= "require ICMS_TRUST_PATH . '/' . ICMS_SDATA;" . PHP_EOL;

		$messages = icms_core_Filesystem::writeFile($contents, 'mainfile', 'php', $trustpath, FALSE);
		return $messages;
	}
}
