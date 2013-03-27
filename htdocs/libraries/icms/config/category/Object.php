<?php
/**
 * Manage configuration categories
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @category	ICMS
 * @package		Config
 * @subpackage	Category
 * @author		Kazumi Ono (aka onokazo)
 * @version		SVN: $Id$
 */

defined('ICMS_ROOT_PATH') or die("ImpressCMS root path not defined");

/**
 * A category of configs
 *
 * @author	Kazumi Ono	<onokazu@xoops.org>
 * @category	ICMS
 * @package     Config
 * @subpackage	Category
 */
class icms_config_category_Object extends icms_ipf_Object {
	/**
	 * Constructor
	 *
	 */
	public function __construct(&$handler, $data = array()) {		
		$this->initVar('confcat_id', XOBJ_DTYPE_INT, null);
		$this->initVar('confcat_name', XOBJ_DTYPE_OTHER, null);
		$this->initVar('confcat_order', XOBJ_DTYPE_INT, 0);
                
                parent::__construct($handler, $data);
	}
}

