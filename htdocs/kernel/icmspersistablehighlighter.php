<?php
class IcmsPersistableHighlighter extends icms_ipf_Highlighter{
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_Highlighter', 'This will be removed in version 1.4');
	}
}
?>