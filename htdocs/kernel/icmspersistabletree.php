<?php
if (!defined("ICMS_ROOT_PATH")) die("ICMS root path not defined");

class IcmsPersistableTree extends icms_ipf_Tree{
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_Tree', 'This will be removed in version 1.4');
	}
}
?>