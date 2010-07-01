<?php
if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

class IcmsPersistableRow extends icms_ipf_view_Row {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_view_Row', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}
}

class IcmsPersistableSingleView extends icms_ipf_view_Single{

	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_view_Single', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}
}
?>