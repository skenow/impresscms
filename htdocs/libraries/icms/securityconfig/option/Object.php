<?php
/**
 * Manage security configuration options
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @category	ICMS
 * @package		SecurityConfig
 * @subpackage	Option
 * @author		Vaughan montgomery
 * @version		SVN: $Id: Object.php 19775 2010-07-11 18:54:25Z malanciault $
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**
 * A Security Config-Option
 *
 * @author	Vaughan montgomery <vaughan@impresscms.org>
 * @category	ICMS
 * @package     SecurityConfig
 * @subpackage	Option
 */
class icms_securityconfig_option_Object extends icms_core_Object {
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->initVar('sec_op_id', XOBJ_DTYPE_INT, null);
		$this->initVar('sec_op_name', XOBJ_DTYPE_TXTBOX, null, true, 255);
		$this->initVar('sec_op_value', XOBJ_DTYPE_TXTBOX, null, true, 255);
		$this->initVar('sec_id', XOBJ_DTYPE_INT, 0);
	}
}