<?php

// Loading base class
require_once ZAR_ROOT_PATH.'/class/controls/base/control.class.php';

class ZariliaControl_FormField_Label
	extends ZariliaControl_FormField {
	
	function ZariliaControl_FormField_Label($title) {
		$this->ZariliaControl_FormField(null, null, $title);
	}

/*	function render() {
		return parent::render();
	}*/

}

?>