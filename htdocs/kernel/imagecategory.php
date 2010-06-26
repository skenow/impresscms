<?php
if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

class XoopsImagecategory extends icms_image_category_Object {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_image_category_Object', 'This will be removed in version 1.4');
	}
}

class XoopsImagecategoryHandler extends icms_image_category_Handler {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_image_category_Handler', 'This will be removed in version 1.4');
	}
}
?>