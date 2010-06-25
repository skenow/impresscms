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
 * membership of a user in a group
 *
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * @package kernel
 * @subpackage	member
 */
class icms_member_group_membership_Object extends icms_core_Object
{
	/**
	 * constructor
	 */
	function icms_member_group_membership_Object()
	{
		$this->icms_core_Object();
		$this->initVar('linkid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('groupid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('uid', XOBJ_DTYPE_INT, null, false);
	}
}