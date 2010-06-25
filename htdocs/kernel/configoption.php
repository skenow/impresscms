<?php
if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

class XoopsConfigOption extends icms_config_option_Object {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_config_option_Object', 'This will be removed in version 1.4');
	}
}

class XoopsConfigOptionHandler extends icms_config_option_Handler {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_config_option_Handler', 'This will be removed in version 1.4');
	}
}