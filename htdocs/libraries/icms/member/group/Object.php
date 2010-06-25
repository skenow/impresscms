<?php
/**
 * Manage groups and memberships
 *
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @package		core
 * @subpackage	member
 * @since		XOOPS
 * @author		Kazumi Ono (aka onokazo)
 * @author		http://www.xoops.org The XOOPS Project
 * @version		$Id: group.php 19586 2010-06-24 11:48:14Z malanciault $
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**
 * a group of users
 *
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @author Kazumi Ono <onokazu@xoops.org>
 * @package kernel
 */
class icms_member_group_Object extends icms_core_Object
{
	/**
	 * constructor
	 */
	function icms_member_group_Object()
	{
		$this->icms_core_Object();
		$this->initVar('groupid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 100);
		$this->initVar('description', XOBJ_DTYPE_TXTAREA, null, false);
		$this->initVar('group_type', XOBJ_DTYPE_OTHER, null, false);
	}
}