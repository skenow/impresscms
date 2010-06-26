<?php
if (!defined("ICMS_ROOT_PATH")) die("ImpressCMS root path not defined");

class IcmsPersistableRegistry extends icms_ipf_Registry {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_Registry', 'This will be removed in version 1.4');
	}
}
?>