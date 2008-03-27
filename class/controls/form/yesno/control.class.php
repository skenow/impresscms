<?php

// Loading base class
require_once ZAR_ROOT_PATH.'/class/controls/base/control.class.php';

class ZariliaControl_FormField_Yesno
	extends ZariliaControl_FormField {
	
	var $name, $value, $title;

	function ZariliaControl_FormField_Yesno($name, $value='', $title='') {
		$this->ZariliaControl_FormField($name, $value, $title);
	}

	function render() {
		$this->_value  = '<a href="javascript:;" tabindex="0" onclick="this.innerHTML=(document.getElementById(\''.$this->getName().'_field\').value=(this.innerHTML==\'Yes\')?0:1)?\'Yes\':\'No\'; this.focus();">';
		$this->_value .= ($this->value)?'Yes':'No';			
		$this->_value .= '</a><input type="hidden" name="'.$this->name.'" id="'.$this->getName().'_field" value="'.$this->value.'" />';
		return parent::render();
	}

}

?>