<?php

// Loading base class
require_once ZAR_ROOT_PATH.'/class/controls/base/control.class.php';

class ZariliaControl_FormField_Editor
	extends ZariliaControl_FormField {

	var $editor_configs = array();
	var $OnFailure = '';
	var $noHTML = false;
	var $noColspan = 0;
	var $editorType = null;

	function ZariliaControl_FormField_Editor($name, $value='', $title='') {
		global $zariliaConfig;
		$this->ZariliaControl_FormField($name, $value, $title);
        $this->editor_configs['rows']	= $zariliaConfig['rows']; //25; // default value = 5
        $this->editor_configs['cols']	= $zariliaConfig['cols']; // 60; // default value = 50
        $this->editor_configs['width']	= $zariliaConfig['width'] . "%"; // '100%'; // default value = 100%
        $this->editor_configs['height'] = $zariliaConfig['height'] . "px"; 
		$this->editorType = $zariliaConfig['admin_default'];
	}

	function render() {
//        $this->ZariliaFormTextArea( $this->title, $editor_configs["name"] );
        $editor_handler = &zarilia_gethandler( "editor" );
        $editor = &$editor_handler->get( $this->editorType, $this->editor_configs, $this->noHTML, $this->OnFailure );
		$editor->value = $this->value;
		$editor->_value = $this->value;
		$editor->name = $this->name;
		$editor->setName($this->name);
//		$editor->setName();
//        $this->setNocolspan( intval( $this->noColspan ) );

	//	$this->_value = '<textarea name="'.$this->name.'" cols="'.$zariliaConfig['cols'].'" rows="'.$zariliaConfig['rows'].'" style="width: '.$zariliaConfig['width'].'px; height: '.$zariliaConfig['height'].'px;">'.$this->value.'</textarea>';
		$this->_value = $editor->render();
		return parent::render();
	}

}

?>