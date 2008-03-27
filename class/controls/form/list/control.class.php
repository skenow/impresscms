<?php

// Loading base class
require_once ZAR_ROOT_PATH.'/class/controls/base/control.class.php';

class ZariliaControl_FormField_List 
	extends ZariliaControl_FormField {
	
	var $name, $value, $title;

	function ZariliaControl_FormField_List($name, $value='', $title='') {
		$this->ZariliaControl_FormField($name, $value, $title);
	}

	function render() {
		$this->_value = '<input type="text" name="'.$this->name.'" value="'.$this->value.'" />';
		return parent::render();
	}

}

?>