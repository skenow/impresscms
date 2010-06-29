<?php
/**
 * Manage avatars for users
 *
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @package		core
 * @subpackage	avatar
 * @since		XOOPS
 * @author		Kazumi Ono (aka onokazo)
 * @author		http://www.xoops.org The XOOPS Project
 * @version		$Id: avatar.php 19569 2010-06-24 00:53:57Z malanciault $
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**
 * Avatar class
 * @package avatar
 *
 */
class icms_core_avatar_Object extends icms_core_Object
{
	/** @var integer */
	var $_userCount;

	/**
	 * Constructor for avatar class, initializing all the properties of the class object
	 *
	 */
	function icms_core_avatar_Object()
	{
		parent::__construct();
		$this->initVar('avatar_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('avatar_file', XOBJ_DTYPE_OTHER, null, false, 30);
		$this->initVar('avatar_name', XOBJ_DTYPE_TXTBOX, null, true, 100);
		$this->initVar('avatar_mimetype', XOBJ_DTYPE_OTHER, null, false);
		$this->initVar('avatar_created', XOBJ_DTYPE_INT, null, false);
		$this->initVar('avatar_display', XOBJ_DTYPE_INT, 1, false);
		$this->initVar('avatar_weight', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('avatar_type', XOBJ_DTYPE_OTHER, 0, false);
	}

	/**
	 * Sets the value for the number of users
	 * @param integer $value
	 *
	 */
	function setUserCount($value)
	{
		$this->_userCount = (int) ($value);
	}

	/**
	 * Gets the value for the number of users
	 * @return integer
	 */
	function getUserCount()
	{
		return $this->_userCount;
	}
}
?>