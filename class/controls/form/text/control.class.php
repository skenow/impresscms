<?php

// Loading base class
require_once ZAR_ROOT_PATH.'/class/controls/base/control.class.php';

class ZariliaControl_FormField_Text
	extends ZariliaControl_FormField {
	
	var $name, $value, $title;

	function ZariliaControl_FormField_Text($name, $value='', $title='') {
		$this->ZariliaControl_FormField($name, $value, $title);
	}

	function render() {
		$this->_value = '<input type="text" name="'.$this->name.'" value="'.htmlentities($this->value,ENT_COMPAT,'UTF-8').'" />';
		return parent::render();
	}

}

?>