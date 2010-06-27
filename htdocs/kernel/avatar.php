<?php
defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

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
		$this->_deprecated = icms_deprecated('icms_avatar_Handler', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}
}