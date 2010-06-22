<?php
/**
 * Manage configuration categories
 *
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @package		core
 * @subpackage	config
 * @since		XOOPS
 * @author		Kazumi Ono (aka onokazo)
 * @author		http://www.xoops.org The XOOPS Project
 * @version		$Id: Configcategory.php 19514 2010-06-21 22:50:14Z skenow $
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**
 * A category of configs
 *
 * @author	Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 *
 * @package     core
 * @subpackage	config
 */
class icms_core_Configcategory extends core_Object {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->initVar('confcat_id', XOBJ_DTYPE_INT, null);
		$this->initVar('confcat_name', XOBJ_DTYPE_OTHER, null);
		$this->initVar('confcat_order', XOBJ_DTYPE_INT, 0);
	}
}

/**
 * @deprecated	Use icms_core_Configcategory, instead
 * @todo		Remove in version 1.4 - all instances have been removed from the core
 *
 */
class XoopsConfigCategory extends icms_core_Configcategory {
	public function __construct() {
		parent::__construct();
		$this->setErrors(icms_deprecated('icms_core_Configcategory', 'This will be removed in version 1.4'));
	}

}
