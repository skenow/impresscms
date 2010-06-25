<?php
if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

class IcmsPersistableController extends icms_ipf_Controller{
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_Controller', 'This will be removed in version 1.4');
	}
}

?>