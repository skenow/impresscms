<?php

class IcmsModuleAbout {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_About', 'This will be removed in version 1.4');
	}
}

?>