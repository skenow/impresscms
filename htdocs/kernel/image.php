<?php
if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

class XoopsImage extends icms_image_Object {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_image_Object', 'This will be removed in version 1.4');
	}
}

class XoopsImageHandler extends icms_image_Handler {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_image_Handler', 'This will be removed in version 1.4');
	}
}

?>