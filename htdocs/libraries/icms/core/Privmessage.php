<?php
/**
 * Manage of private messages
 *
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license	LICENSE.txt
 * @package	core
 * @since	XOOPS
 * @author	http://www.xoops.org The XOOPS Project
 * @author	modified by UnderDog <underdog@impresscms.org>
 * @version	$Id: privmessage.php 19450 2010-06-18 14:15:29Z malanciault $
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**
 * @package     kernel
 *
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */

/**
 * A handler for Private Messages
 *
 * @package		kernel
 *
 * @author		Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 The XOOPS Project (http://www.xoops.org)
 *
 * @version		$Revision: 1102 $ - $Date: 2007-10-18 22:55:52 -0400 (jeu., 18 oct. 2007) $
 */
class icms_core_Privmessage extends icms_core_Object
{

	/**
	 * constructor
	 **/
	function icms_core_Privmessage()
	{
		$this->icms_core_Object();
		$this->initVar('msg_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('msg_image', XOBJ_DTYPE_OTHER, 'icon1.gif', false, 100);
		$this->initVar('subject', XOBJ_DTYPE_TXTBOX, null, true, 255);
		$this->initVar('from_userid', XOBJ_DTYPE_INT, null, true);
		$this->initVar('to_userid', XOBJ_DTYPE_INT, null, true);
		$this->initVar('msg_time', XOBJ_DTYPE_OTHER, null, false);
		$this->initVar('msg_text', XOBJ_DTYPE_TXTAREA, null, true);
		$this->initVar('read_msg', XOBJ_DTYPE_INT, 0, false);
	}
}
?>