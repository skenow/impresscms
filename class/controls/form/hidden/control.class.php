<?php

// Loading base class
require_once ZAR_ROOT_PATH.'/class/controls/base/control.class.php';

class ZariliaControl_FormField_Hidden
	extends ZariliaControl_FormField {
	
	var $name, $value, $title;

	function ZariliaControl_FormField_Hidden($name, $value) {
		$this->ZariliaControl_FormField($name, $value, null);
	}

	function render() {
//		return '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'" />';
		$this->_value = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'" />';
		return parent::render();
	}

}

?>