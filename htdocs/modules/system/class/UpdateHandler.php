<?php
/**
 * ImpressCMS Update Handler
 *
 * @copyright	The ImpressCMS Project http://www.impresscms.org/
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @category	ICMS
 * @package		Administration
 * @subpackage	Update
 */

defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

/* This may be loaded by other modules - and not just through the cpanel */
icms_loadLanguageFile('system', 'update', TRUE);

/**
 * Handler for the system update
 *
 * @category	ICMS
 * @package		Administration
 * @subpackage	Update
 *
 */
class mod_system_UpdateHandler extends icms_ipf_Handler {

	/**
	 * Construct the update handler
	 * @param	$db	the database instance
	 */
	public function __construct(&$db) {
        parent::__construct($db, "update", "id", "name", "desc", "system");

	}
}
