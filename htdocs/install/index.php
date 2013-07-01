<?php
/**
 * Core installation for ImpressCMS
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) v3
 * @category	ICMS
 * @package		Administration
 * @subpackage	Installation
 * @since		2.0
 */

/* needed from the core -
 * filtering: icms_core_DataFilter
 * file system: icms_core_Filesystem
 * database: icms_db_legacy_Factory
 *
 * Cannot use: icms_core_Debug::vardump - it needs a connection to the db to retrieve smileys (?)
 * Cannot use: many of the icms_core_DataFilter methods - they need info from the database
 * Cannot use: icms::boot() - it requires a connection to the database
 */

/* for developer testing */
$debug = FALSE;
$debugMsgs = array();

/** language constants for installation */ // @todo	allow to change languages for installation
require 'languages/english/install.php';
/** installation functions */
require 'functions.php';
/** installation class */
require 'class/Install.php';
/** current installation requirements */
require 'requirements.inc.php';

$installation = new Install();

$siteRootPath = $installation->paths['siteRootPath'];
$installTrustPath = $installation->paths['installTrustPath'];
$targetTrustPath = $installation->paths['targetTrustPath'];
$siteURI = $installation->paths['siteURI'];

/* define these constants so we can use core classes/methods - no trailing slash (wrong convention) */
define('ICMS_ROOT_PATH', substr($siteRootPath, 0, -1));
define('ICMS_URL', substr($siteURI, 0 , -1));
define('ICMS_TRUST_PATH', substr($installTrustPath, 0 , -1));

/* these aren't really needed, but include/constants.php checks for them :( */
define('ICMS_GROUP_ADMIN', '1');
define('ICMS_GROUP_USERS', '2');
define('ICMS_GROUP_ANONYMOUS', '3');

/** constants for use in the functions and classes */
require $siteRootPath . 'include/constants.php';
/** common core functions */
require $siteRootPath . 'include/functions.php';
/** current version information */
require $siteRootPath . 'include/version.php';
/** setup core autoloader */
require $siteRootPath . 'libraries/icms.php';

/* we can't use icms::boot(), because the database doesn't exist, yet */
icms::setup();
/* do error trapping - not active now, during development. Will need to encorporate */
//icms::loadService('logger', array("icms_core_Logger", 'instance'));

/*
 * Variables in Will's prototype
	'site_name' => 'str',
	'site_slogan' => 'str',
	'site_language' => 'str',
	'site_admin_email' => 'str',
	'site_admin_display' => 'str',
	'site_admin_uname' => 'str',
	'site_admin_pass' => 'str',
	'site_admin_pass_confirm' => 'str',
	'site_url' => 'str',
	'site_path' => 'str',
	'site_trust' => 'str',
	'site_db' => 'str',
	'site_db_host' => 'str',
	'site_db_name' => 'str',
	'site_db_user' => 'str',
	'site_db_pass' => 'str',
	'site_db_persist' => 'str',
	'site_pw_salt_key' => 'str',
	'site_db_charset' => 'str',
	'site_db_collation' => 'str',
*/

/* set default values for variables */
$fct = $op = "";

/* filter the user input - defining all the inputs will add empty strings as values if not present */
$filter_get = array(
		'op' => 'str',
);

$filter_post = array(
		'op' => 'str',
);

if (!empty($_GET)) {
	$clean_GET = icms_core_DataFilter::checkVarArray($_GET, $filter_get, FALSE);
	extract($clean_GET);
}

if (!empty($_POST)) {
	$clean_POST = icms_core_DataFilter::checkVarArray($_POST, $filter_post, FALSE);
	extract($clean_POST);
}

$valid_op = array(NULL, '', 'go', 'reload', 'finish');

switch ($op) {
	case 'reload' :
	default:
		
		/* do we need to reload the page? */
		$reload = FALSE;
		
		/* Welcome */
		//icms_core_Message::result("Welcome!", TRUE);
		
		/* Check requirements */
		$phpVersionOK = $installation->checkPHPVersion($requirements['phpversion']);
		
		/* check the required extensions against available extensions */
		$extensionsOK = $installation->checkPHPExtensions($requirements['phpextensions']);
		
		/* check the required settings against the available settings */
		$phpSettings = ini_get_all();

		$systemOK = $installation->checkPHPSettings($requirements['phpsettings'], $phpSettings);
		$systemOK = array_merge($phpVersionOK, $extensionsOK, $systemOK);
			
		if (count($systemOK) > 0) {
			// show errors and reload
			$reload = TRUE;
			$debugMsgs[] = icms_core_Message::warning($systemOK, _SERVER_REQUIREMENTS_NOT_MET, FALSE);
			unset($systemOK, $phpVersionOK, $extensionsOK);
		} elseif ($debug) {
			$debugMsgs[] = icms_core_Message::result("System Requirements - OK", '', FALSE);
		}
		
		/* Additional server/system information - file system */
		$pathsOK = $installation->checkFilePaths($requirements['paths'], $siteRootPath, $phpSettings);
		
		if (count($pathsOK) > 0) {
			//show errors and reload
			$reload = TRUE;
			$debugMsgs[] = icms_core_Message::warning($pathsOK, "These paths need to be writable", FALSE);
			unset($pathsOK);
		} elseif ($debug) {
			$debugMsgs[] = icms_core_Message::result("Paths are writable", '', FALSE);
		}
		
		/* trustpath availability (User input? Existing site profile?)
		 * a few things we need to consider:
		 * 	Installation via WebPI - it needs to have parameterized values - no random file names, db prefixes, etc.
		 * 	IIS does not allow paths outside the webroot. Access is controlled differently
		 * 	We have no good way of updating code after installation that has been placed in the trust path (protector module)
		 * So, should we create a fixed location for trust path on installation, then move it?
		 */
		$trustPathOK = is_writable($installTrustPath);
		
		if (!$trustPathOK) {
			//show errors and reload
			$reload = TRUE;
			$debugMsgs[] = icms_core_Message::warning($installTrustPath, "The trust path is not accessible", FALSE);
			unset($trustPathOK);
		} elseif ($debug) {
			$debugMsgs[] = icms_core_Message::result("Trust path is ready", '', FALSE);
		}
		
		/* are you installing the latest version of ImpressCMS? */
		$versionCheck = $installation->checkICMSVersion();
		
		if (count($versionCheck) > 0) {
			// show errors and reload
			$reload = TRUE;
			$debugMsgs[] = icms_core_Message::warning($versionCheck, "Version recommendation", FALSE);
			unset($versionCheck);
		} elseif ($debug) {
			$debugMsgs[] = icms_core_Message::result("You are installing the latest version of ImpressCMS", '', FALSE);
		}
		
		if ($reload) {
			// show reload button index.php?op=reload
			echo "<button type='button'><a href='index.php?op=reload'>Try again</a></button>";
		} else {
		/*  show form, provide some smart defaults - will need POST vars (or GET or both) */
			$site_db_host = isset($site_db_host) ? $site_db_host : 'localhost';
			$site_db_user = isset($site_db_user) ? $site_db_user : '';
			$site_db_pass = isset($site_db_pass) ? $site_db_pass : '';
			$site_db_name = isset($site_db_name) ? $site_db_name : '';
			$site_db_prefix = isset($site_db_prefix) ? $site_db_prefix : icms_core_Password::createSalt(7);

		/* Advanced: set db persistant connection, character set and collation */
			$site_db_persist = isset($site_db_persist) ? $site_db_persist : FALSE;
			$site_db_charset = isset($site_db_charset) ? $site_db_charset : 'utf8';
			$site_db_collation = isset($site_db_collation) ? $site_db_collation : '';
			
		/* set this, if not provided (reinstall) */
			$site_pw_salt_key = isset($site_pw_salt_key) ? $site_pw_salt_key : icms_core_Password::createSalt();
			
			$targetTrustPath = isset($site_trust) ? $site_trust : $targetTrustPath;
			
		/* include the page layout for the form */
			require 'tpl/install.php';
			
		}
		break;
	
	case 'go':
		/* From user input - database credentials: dbserver, dbname, dbuser, dbpassword, table prefix
		 * Advanced: character set, collation
		 */
		
		$dbready = $installation->checkDB($site_db_host, $site_db_user, $site_db_pass, $site_db_name);
		
		if (count($dbready) > 0) {
			// show errors and reload
			$reload = TRUE;
			$debugMsgs[] = icms_core_Message::warning($dbready, "Database problems", FALSE);
			unset($dbready);
		}
		
		/* Save credentials and path info to trustpath/sdata.php */
			$sdataOK = $installation->writeSecureData($installTrustPath, $site_db_host, $site_db_user, $site_db_pass, $site_db_name, $site_db_prefix, $site_pw_salt_key = '');
		
		/* Relocate & rename trustpath/sdata.php */
			$secureData = 'trustpath/sdata.php';
			$targetSecureData = hash('sha1', time() . $siteURI) . ".php";
			$renameSecureData = icms_core_Filesystem::copyRecursive($secureData, $installTrustPath . $targetSecureData);
			if ($renameSecureData) icms_core_Filesystem::deleteFile($secureData);
			$moveTrustPath = $installation->moveTrustPath($installTrustPath, $site_trust);
		
		/* Save sites/mainfile.php */
			$mainfileResults = $installation->writeMainfile($site_path, $installTrustPath, $targetSecureData);
		
		if (count($mainfileResults) > 0) {
			// show errors and reload
			$reload = TRUE;
			$debugMsgs[] = icms_core_Message::warning($mainfileResults, "Couldn't save configuration", FALSE);
			unset($mainfileResults);
		}
		
		/* User input - site details: administrator login (reinstall existing site profile?)
		 * Minimum: email address/password. Create password?
		 * Optional: site name, slogan, general preferences...
		 */
		
		/* could be done with javascript and would be unnecessary, here */
		$messages = array();
		if (count($messages) > 0) {
			// show errors and reload
			$reload = TRUE;
			$debugMsgs[] = icms_core_Message::warning($messages, "Your password entries don't match", FALSE);
			unset($messages);
		}
		
		/* Populate database (install system module). Reinstall existing site profile?
		 * Considerations
		 *  - the db structure isn't the only thing added during install
		 *  - data records are added (blocks, groups, positions, configuration settings, for example)
		 *  - there are installation-specific variables, like db name and table prefix to deal with
		 *  - some of the data is language-specific
		 */
		
		
		$messages = array();
		if (count($messages) > 0) {
			// show errors and reload
			$reload = TRUE;
			$debugMsgs[] = icms_core_Message::warning($messages, "Errors", FALSE);
		} else {
			$debugMsgs[] = icms_core_Message::result("You're good to go!", '', FALSE);
		}
		break;
	
	case 'finish' :
	
		break;
	
}

/* End */
