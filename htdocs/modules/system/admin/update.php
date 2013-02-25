<?php
/**
 * ImpressCMS Core Updater
 *
 * @copyright	The ImpressCMS Project http://www.impresscms.org/
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @category	ICMS
 * @package		Administration
 * @subpackage	Update
 * @since		2.0
 */

/**
 * Downloads the remote file locally.
 * @param $remote_file
 * @return bool
 * @throws Exception
*/



/* set get and post filters before including admin_header, if not strings */
$filter_post = array();

$filter_get = array();

/* set default values for variables. $op and $fct are handled in the header */

/** common header for the admin functions */
//include "admin_header.php";




icms_cp_header();
//$icmsVersionUpdate = new icms_core_fetchpackage();
echo "this is the update checker";
//echo "the local folder is " . $dlcache_folder;


icms_cp_footer();
