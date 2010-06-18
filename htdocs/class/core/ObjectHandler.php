<?php
/**
 * Manage of original Xoops Objects
 *
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license	LICENSE.txt
 * @package	core
 * @since	XOOPS
 * @author	http://www.xoops.org The XOOPS Project
 * @author	modified by UnderDog <underdog@impresscms.org>
 * @version	$Id: object.php 19419 2010-06-13 22:52:12Z skenow $
 */

/**
 * @package kernel
 * @copyright copyright &copy; 2000 XOOPS.org
 */

/**
 * XOOPS object handler class.
 * This class is an abstract class of handler classes that are responsible for providing
 * data access mechanisms to the data source of its corresponsing data objects
 * @package kernel
 * @abstract
 *
 * @author  Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright &copy; 2000 The XOOPS Project
 */
class core_ObjectHandler
{

	/**
	 * holds referenced to {@link XoopsDatabase} class object
	 *
	 * @var object
	 * @see XoopsDatabase
	 * @access protected
	 */
	var $db;

	//
	/**
	* called from child classes only
	*
	* @param object $db reference to the {@link XoopsDatabase} object
	* @access protected
	*/
	function core_ObjectHandler(&$db)
	{
		$this->db =& $db;
	}

	/**
	 * creates a new object
	 *
	 * @abstract
	 */
	function &create()
	{
	}

	/**
	 * gets a value object
	 *
	 * @param int $int_id
	 * @abstract
	 */
	function &get($int_id)
	{
	}

	/**
	 * insert/update object
	 *
	 * @param object $object
	 * @abstract
	 */
	function insert(&$object)
	{
	}

	/**
	 * delete object from database
	 *
	 * @param object $object
	 * @abstract
	 */
	function delete(&$object)
	{
	}

}

?>