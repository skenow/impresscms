<?php
defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

class IcmsPersistableMemberHandler extends icms_ipf_member_Handler{
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_member_Handler', 'This will be removed in version 1.4');
	}
}

?>