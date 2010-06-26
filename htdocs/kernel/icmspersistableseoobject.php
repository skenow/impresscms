<?php
if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");
class IcmsPersistableSeoObject extends icms_ipf_seo_Object {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_seo_Object', 'This will be removed in version 1.4');
	}
}

?>