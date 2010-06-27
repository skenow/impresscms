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
 * @version		$Id$
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

class XoopsAvatar extends icms_avatar_Object {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_avatar_Object', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}
}

class XoopsAvatarHandler extends icms_avatar_Handler {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_avatar_Object', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}
}

?>