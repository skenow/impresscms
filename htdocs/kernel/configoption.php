<?php
if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");
/**
 * @deprecated
 * @todo		Remove in version 1.4
 */
class XoopsConfigOption extends icms_config_option_Object {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_config_option_Object', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}
}
/**
 * @deprecated
 * @todo		Remove in version 1.4
 */
class XoopsConfigOptionHandler extends icms_config_option_Handler {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_config_option_Handler', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}
}