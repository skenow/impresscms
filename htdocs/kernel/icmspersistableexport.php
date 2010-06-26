<?php
class IcmsPersistableExport extends icms_ipf_export_Handler{
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_export_Handler', 'This will be removed in version 1.4');
	}
}
class IcmsExportRenderer extends icms_ipf_export_Renderer {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_export_Renderer', 'This will be removed in version 1.4');
	}
}
?>