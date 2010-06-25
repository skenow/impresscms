<?php
if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

class XoopsConfigItem extends icms_config_item_Object {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_config_item_Object', 'This will be removed in version 1.4');
	}
}

class XoopsConfigItemHandler extends icms_config_item_Handler {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_config_item_Handler', 'This will be removed in version 1.4');
	}
}