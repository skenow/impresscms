<?php
/**
 * Manage of template files
 *
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license	LICENSE.txt
 * @package	core
 * @since	XOOPS
 * @author	http://www.xoops.org The XOOPS Project
 * @author	modified by UnderDog <underdog@impresscms.org>
 * @version	$Id: tplfile.php 19459 2010-06-18 14:58:18Z malanciault $
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**
 * @package kernel
 * @copyright copyright &copy; 2000 XOOPS.org
 */

/**
 * Base class for all templates
 *
 * @author Kazumi Ono (AKA onokazu)
 * @copyright copyright &copy; 2000 XOOPS.org
 * @package kernel
 **/
class core_Tplfile extends core_Object
{

	/**
	 * constructor
	 */
	function core_Tplfile()
	{
		$this->core_Object();
		$this->initVar('tpl_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('tpl_refid', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('tpl_tplset', XOBJ_DTYPE_OTHER, null, false);
		$this->initVar('tpl_file', XOBJ_DTYPE_TXTBOX, null, true, 100);
		$this->initVar('tpl_desc', XOBJ_DTYPE_TXTBOX, null, false, 100);
		$this->initVar('tpl_lastmodified', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('tpl_lastimported', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('tpl_module', XOBJ_DTYPE_OTHER, null, false);
		$this->initVar('tpl_type', XOBJ_DTYPE_OTHER, null, false);
		$this->initVar('tpl_source', XOBJ_DTYPE_SOURCE, null, false);
	}

	/**
	 * Gets Template Source
	 */
	function getSource()
	{
		return $this->getVar('tpl_source');
	}

	/**
	 * Gets Last Modified timestamp
	 */
	function getLastModified()
	{
		return $this->getVar('tpl_lastmodified');
	}
}
?>