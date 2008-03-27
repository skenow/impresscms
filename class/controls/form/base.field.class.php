<?php

// Loading base class
require_once ZAR_ROOT_PATH.'/class/controls/base/control.class.php';

class ZariliaControl_FormField 
	extends ZariliaControl {
	
	var $name, $value, $title;

	var $beforeSubmit = '';

	function ZariliaControl_FormField($name, $value='', $title='', $truename=null) {
		$this->name = $name;
		$this->value = $value;
		$this->title = $title;
		$this->ZariliaControl('FormField',$truename);
	}

	function &getFieldData() {
		$data = array(array($this->getName(), $this->name, substr(get_class($this),25)));
		return $data;
	}

}

?>