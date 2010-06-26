<?php
if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

class IcmsPersistableRow extends icms_ipf_view_Row {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_view_Row', 'This will be removed in version 1.4');
	}
}

class IcmsPersistableSingleView extends icms_ipf_view_Object{

	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_view_Object', 'This will be removed in version 1.4');
	}
}
?>