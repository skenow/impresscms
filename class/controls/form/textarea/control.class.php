<?php

// Loading base class
require_once ZAR_ROOT_PATH.'/class/controls/base/control.class.php';

class ZariliaControl_FormField_TextArea
	extends ZariliaControl_FormField {
	
	var $name, $value, $title;

	function ZariliaControl_FormField_TextArea($name, $value='', $title='') {
		$this->ZariliaControl_FormField($name, $value, $title);
	}

	function render() {
		global $zariliaConfig;
		$this->_value = '<textarea name="'.$this->name.'" cols="'.$zariliaConfig['cols'].'" rows="'.$zariliaConfig['rows'].'" style="width: '.$zariliaConfig['width'].'px; height: '.$zariliaConfig['height'].'px;">'.$this->value.'</textarea>';
		return parent::render();
	}

}

?>