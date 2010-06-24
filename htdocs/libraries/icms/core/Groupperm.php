<?php
/**
 * Manage groups and memberships
 *
 * @copyright	The XOOPS Project <http://www.xoops.org/>
 * @copyright	XOOPS_copyrights.txt
 * @copyright	The ImpressCMS Project <http://www.impresscms.org/>
 * @license		LICENSE.txt
 * @since		XOOPS
 *
 * @author		Kazumi Ono (aka onokazo)
 * @author	The XOOPS Project Community <http://www.xoops.org>
 * @author	Gustavo Alejandro Pilla (aka nekro) <nekro@impresscms.org> <gpilla@nube.com.ar>
 *
 * @package	core
 * @subpackage	groupperm
 * @version		$Id: groupperm.php 19450 2010-06-18 14:15:29Z malanciault $
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**
 * A group permission
 *
 * These permissions are managed through a {@link icms_core_GrouppermHandler} object
 *
 * @package     kernel
 * @subpackage	member
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class icms_core_Groupperm extends icms_core_Object {
	/**
	 * Constructor
	 *
	 */
	function __construct() {
		$this->icms_core_Object();
		$this->initVar('gperm_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('gperm_groupid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('gperm_itemid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('gperm_modid', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('gperm_name', XOBJ_DTYPE_OTHER, null, false);
	}
}

/**
 * @deprecated	Use icms_core_Groupperm, instead
 * @todo		Remove in version 1.4
 */
class XoopsGroupPerm extends icms_core_Groupperm {
	public function __construct() {
		parent::__construct();
		$this->setErrors = icms_deprecated('icms_core_Groupperm', 'This will be removed in version 1.4');
	}
}
