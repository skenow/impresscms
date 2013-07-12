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
		$installPath = dirname(dirname(__FILE__)) . '/';
		$siteRootPath = dirname(dirname(dirname(__FILE__))) . '/';
		$installTrustPath = $installPath . 'trustpath/';
		$targetTrustPath = $this->_suggestTrustPath($siteRootPath);

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
	 *
	 * @param	string	$installRoot	directory path to current site
	 */
	private function _suggestTrustPath($installRoot) {
		$randName = substr( md5( time() ), 0, 15);

		/* 1st, check outside the document root */
		if (is_writable(dirname($_SERVER['DOCUMENT_ROOT']))) return dirname($_SERVER['DOCUMENT_ROOT']) . '/' . $randName . '/';
		/* Next, check the document root */
		if (is_writable($_SERVER['DOCUMENT_ROOT'])) return $_SERVER['DOCUMENT_ROOT'] . '/' . $randName . '/';
		/* Next, check 1 level above the install root */
		if (is_writable(dirname($installRoot))) return dirname($installRoot) . '/' . $randName . '/';
		/* Finally, check the install root path */
		if (is_writable($installRoot)) return $installRoot . $randName . '/';

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
		if (!$phpOK) {
			$messages[] = sprintf(_PHP_VERSION_FAIL, phpversion(), $required);
		}
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
	 * @param	string	$basepath		root path for the installation
	 * @param	array	$phpSettings	PHP settings from the server
	 * @return	array	$messages		an array of messages if errors, an empty array if successful
	 */
	public function checkFilePaths(array $paths, $basepath, array $phpSettings) {
		$messages = array();
		$mask = umask();
		$openRestrictions = $phpSettings['open_basedir'];

		foreach ($paths as $path){
			if (!is_writable($basepath . $path)) {
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
	 * @param	string	$dbtype			DB engine - currently only MySQL is supported
	 * @param	string	$dbserver		name of the db server
	 * @param	string	$dbuser			authorized db user
	 * @param	string	$dbpassword		password for the db user
	 * @param	string	$dbname			name of the database
	 * @param	string	$dbpersist		should the connection to the database be persistant, 0 for no, 1 for yes
	 * @param	string	$dbcharset		the default characterset for the database
	 * @param	string	$dbcollation	the collation of the characterset
	 * @param	string	$dbprefix		db table prefix to use for this installation
	 * @param	string	$sitesalt		site encryption salt
	 * @return	array	$messages	an array of messages if errors, an empty array if successful
	 */
	public function writeSecureData($installTrustPath, $dbtype, $dbserver, $dbuser, $dbpassword, $dbname, $dbpersist, $dbcharset, $dbcollation, $dbprefix, $sitesalt = '') {

		$messages = array();

		if ($dbtype == '') $dbtype = 'mysql';
		if ($dbcharset == '') $dbcharset = 'utf8';
		if ($dbcollation == '') $dbcollation = 'utf8_general_ci';
		if ($dbpersist == '') $dbpersist = '0';
			
		if ($sitesalt == '') $sitesalt = hash('sha1', time() . $installTrustPath);
		$defines = array(
				'ICMS_DB_TYPE' => $dbtype,
				'ICMS_DB_HOST' => $dbserver,
				'ICMS_DB_USER' => $dbuser,
				'ICMS_DB_PASS' => $dbpassword,
				'ICMS_DB_NAME' => $dbname,
				'ICMS_DB_PCONNECT' => $dbpersist,
				'ICMS_DB_PREFIX' => $dbprefix,
				'ICMS_DB_CHARSET' => $dbcharset,
				'ICMS_DB_COLLATION' => $dbcollation,
				'ICMS_DB_SALT' => $sitesalt,
		);
		$legacy = array(
				'XOOPS_DB_TYPE' => ICMS_DB_TYPE,
				'XOOPS_DB_HOST' => ICMS_DB_HOST,
				'XOOPS_DB_USER' => ICMS_DB_USER,
				'XOOPS_DB_PASS' => ICMS_DB_PASS,
				'XOOPS_DB_NAME' => ICMS_DB_NAME,
				'XOOPS_DB_PCONNECT' => ICMS_DB_PCONNECT,
				'XOOPS_DB_PREFIX' => ICMS_DB_PREFIX,
				'XOOPS_DB_CHARSET' => ICMS_DB_CHARSET,
				'XOOPS_DB_COLLATION' => ICMS_DB_COLLATION,
				'XOOPS_DB_SALT' => ICMS_DB_SALT,
		);

		$contents = '';
		foreach ($defines as $key => $val) {
			$contents .= "define('" . $key . "', '" . $val . "');" . PHP_EOL;
		}
		$contents .= PHP_EOL . "// - Legacy db constants " . PHP_EOL;
		foreach ($legacy as $key => $val) {
			$contents .= "define('" . $key . "', " . $val . ");" . PHP_EOL;
		}
		$result = icms_core_Filesystem::writeFile($contents, 'sdata', 'php', $installTrustPath, FALSE);
		if (!result) $messages[] = "Unable to write secure data";

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
		$moveTrustPath = icms_core_Filesystem::copyRecursive($installTrustPath, $targetTrustPath);
		$moveTrustPath = is_readable($targetTrustPath);
		if ($moveTrustPath) {
			if (!icms_core_Filesystem::deleteRecursive($installTrustPath)) {
				$messages[] = "Unable to remove the installation trust path";
			}
		} else {
			$messages[] = "Unable to create the trustpath";
		}
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

		$messages = array();
		$header = array(
				"<?php",
				"/**",
				" * Required site configuration information",
				" *",
				" * Be careful if you are changing data in this file.",
				" *",
				" * @copyright	http://www.impresscms.org/ The ImpressCMS Project",
				" * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)",
				" * @package		Core",
				" */",
		);
		$defines = array(
				'ICMS_URL' => ICMS_URL,
				'ICMS_ROOT_PATH' => 'dirname(__FILE__)',
				'ICMS_TRUST_PATH' => substr($trustpath, 0, -1),
				'ICMS_SDATA' => $sdata,
				'ICMS_GROUP_ADMIN' => '1',
				'ICMS_GROUP_USERS' => '2',
				'ICMS_GROUP_ANONYMOUS' => '3',
		);

		$includes = array(
				"require ICMS_TRUST_PATH . '/' . ICMS_SDATA;",
				"if (!isset(\$xoopsOption['nocommon']) && ICMS_ROOT_PATH != '') {",
				"	include ICMS_ROOT_PATH . '/include/common.php';",
				"}",
		);

		$contents = '';
		foreach ($header as $line) {
			$contents .= $line . PHP_EOL;
		}
		foreach ($defines as $key => $val) {
			if ($key == 'ICMS_ROOT_PATH') {
				$contents .= "define('" . $key . "', " . $val . ");" . PHP_EOL;
			} else {
				$contents .= "define('" . $key . "', '" . $val . "');" . PHP_EOL;
			}
		}
		foreach ($includes as $include) {
			$contents .= $include . PHP_EOL;
		}

		$result = icms_core_Filesystem::writeFile($contents, 'mainfile', 'php', $sitepath, FALSE);
		if (!$result) $messages[] = "Unable to write mainfile";

		$chmodMainfile = icms_core_Filesystem::chmod($sitepath . 'mainfile.php', 0444);
		if (!$chmodMainfile) $messages[] = "Unable to write protect mainfile";

		return $messages;
	}
}
