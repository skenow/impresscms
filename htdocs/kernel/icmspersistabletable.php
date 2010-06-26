<?php
if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

class IcmsPersistableColumn extends icms_ipf_view_Column {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_view_Column', 'This will be removed in version 1.4');
	}
}

class IcmsPersistableTable extends icms_ipf_view_Table {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_view_Table', 'This will be removed in version 1.4');
	}
}

?>