<?php

// Loading base class
require_once ZAR_ROOT_PATH.'/class/controls/base/control.class.php';

class ZariliaControl_FormField_Listfromarray 
	extends ZariliaControl_FormField {
	
	var $array, $multi;

	function ZariliaControl_FormField_Listfromarray($name, $value, $title, &$array, $multi=false) {
		$this->ZariliaControl_FormField($name, $value, $title);
		$this->array = $array;
		$this->multi = $multi;
	}

	function render() {
		if ($this->multi) {
			require_once ZAR_ROOT_PATH.'/class/multilanguage/charsetconvert.class.php';
			$this->_value = '';
			$this->value = charsetConvert::to7bit($this->value);
			$this->value = unserialize($this->value);
			if (!is_array($this->value)) $this->value = array();
			foreach ($this->array as $id => $title) {
				$this->_value .= '<input type="checkbox" name="'.$this->name.'[]" id="'.$this->name.'_'.$id.'_id" value="'.$id.'"'.((in_array($id,$this->value))?' checked="checked"':'').'> <label for="'.$this->name.'_'.$id.'_id">'.$title.'</label> <br />';
			}
		} else {
			$this->_value = '<select name="'.$this->name.'">';
			foreach ($this->array as $id => $title) {
				$this->_value .= '<option value="'.$id.'"'.(($id===$this->value)?' selected="selected"':'').'>'.$title.'</option>';
			}
			$this->_value .= '</option>';
		}		
		return parent::render();
	}

}

?>