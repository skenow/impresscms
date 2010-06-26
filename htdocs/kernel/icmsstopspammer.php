<?php
class IcmsStopSpammer extends imcs_core_StopSpammer{
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('imcs_core_StopSpammer', 'This will be removed in version 1.4');
	}
}

?>