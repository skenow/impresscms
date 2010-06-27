<?php
defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

class XoopsBlock extends icms_core_Block {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_core_Block', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}
}

class XoopsBlockHandler extends icms_core_BlockHandler {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_core_BlockHandler', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}
}
