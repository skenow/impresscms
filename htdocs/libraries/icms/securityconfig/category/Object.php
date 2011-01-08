<?php
/**
 * Manage security configuration categories
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @category	ICMS
 * @package		SecurityConfig
 * @subpackage	Category
 * @author		Vaughan Montgomery
 * @version		SVN: $Id: Object.php 19999 2010-08-24 23:05:10Z skenow $
 */

defined('ICMS_ROOT_PATH') or die("ImpressCMS root path not defined");

/**
 * A category of configs
 *
 * @author		Vaughan Montgomery <vaughan@impresscms.org>
 * @category	ICMS
 * @package     SecurityConfig
 * @subpackage	Category
 */
class icms_securityconfig_category_Object extends icms_core_Object {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->initVar('sec_cat_id', XOBJ_DTYPE_INT, null);
		$this->initVar('sec_cat_name', XOBJ_DTYPE_OTHER, null);
		$this->initVar('sec_cat_order', XOBJ_DTYPE_INT, 0);
	}
}