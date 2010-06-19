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
 * @version		$Id: configcategory.php 19450 2010-06-18 14:15:29Z malanciault $
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**
 * A category of configs
 *
 * @author	Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 *
 * @package     kernel
 * @subpackage	config
 */
class core_Configcategory extends core_Object
{
	/**
	 * Constructor
	 *
	 */
	function core_Configcategory()
	{
		$this->core_Object();
		$this->initVar('confcat_id', XOBJ_DTYPE_INT, null);
		$this->initVar('confcat_name', XOBJ_DTYPE_OTHER, null);
		$this->initVar('confcat_order', XOBJ_DTYPE_INT, 0);
	}
}
?>