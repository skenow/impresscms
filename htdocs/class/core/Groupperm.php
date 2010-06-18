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
 * These permissions are managed through a {@link core_GrouppermHandler} object
 *
 * @package     kernel
 * @subpackage	member
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class core_Groupperm extends core_Object
{
	/**
	 * Constructor
	 *
	 */
	function core_Groupperm()
	{
		$this->core_Object();
		$this->initVar('gperm_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('gperm_groupid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('gperm_itemid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('gperm_modid', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('gperm_name', XOBJ_DTYPE_OTHER, null, false);
	}
}
?>