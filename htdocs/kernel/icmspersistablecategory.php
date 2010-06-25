<?php

class IcmsPersistableCategory extends icms_ipf_category_Object {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_category_Object', 'This will be removed in version 1.4');
	}
}
/**
 * Provides data access mechanisms to the IcmsPersistableCategory object
 * @copyright 	The ImpressCMS Project http://www.impresscms.org/
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since 		1.1
 */
class IcmsPersistableCategoryHandler extends icms_ipf_category_Handler {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_ipf_category_Handler', 'This will be removed in version 1.4');
	}
}

?>