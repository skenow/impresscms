<?php

// Loading base class
require_once ZAR_ROOT_PATH.'/class/controls/base/control.class.php';

class ZariliaControl_FormField_Multitext
	extends ZariliaControl_FormField {

	function ZariliaControl_FormField_Multitext($name, &$value, $title='') {
		global $zariliaOption;
		$this->ZariliaControl_FormField($name, unserialize($this->to7bit($value)), $title);
        if ( !isset( $zariliaOption['ZariliaControl_FormField_Multitext'] ) ) {
			$zariliaOption['ZariliaControl_FormField_Multitext'] = true;
			$this->addJS('function zcFormFieldMultiText_add(id, name) {
							var obj = document.getElementById(id);
							var obj2 = document.getElementById(id + "_add");
							var input = document.createElement("input");
							var span = document.createElement("div");
							var a = document.createElement("a");
							var txt = document.createTextNode(" ");
							input.name = name + "[]";
							input.type = "text";
							a.href = "";
							a.onclick = "zcFormFieldMultiText_delete(this); return false;";
							a.innerHTML = "'._DELETE.'";
							span.appendChild(input);
							span.appendChild(txt);
							span.appendChild(a);
							obj.insertBefore(span, obj2);
						 }
						 function zcFormFieldMultiText_delete(obj) {
							 var obj2 = obj.parentNode;
							 obj.parentNode.parentNode.removeChild(obj2);
						 }');
		}
	}

	function to7bit($text,$from_enc='auto') {
		$text = html_entity_decode(mb_convert_encoding($text,'HTML-ENTITIES',$from_enc));
	    return $text;
	}

	function render() {
		$this->_value = '';
		foreach ($this->value as $value) {
			$this->_value .= '<div><input type="text" name="'.$this->name.'[]" value="'.$value.'"> ';
			$this->_value .= '<a href="" onclick="zcFormFieldMultiText_delete(this); return false;">'._DELETE.'</a> </div>';
		}		
		$this->_value .= '<a href="" onclick="zcFormFieldMultiText_add(\''.$this->getName().'\',\''.$this->name.'\'); return false;" id="'.$this->getName().'_add">'._ADD.'</a>';
/*		$this->_value  = '<a href="javascript:;" tabindex="0" onclick="this.innerHTML=(document.getElementById(\''.$this->getName().'\').value=(this.innerHTML==\'Yes\')?0:1)?\'Yes\':\'No\'; this.focus();">';
		$this->_value .= ($this->value)?'Yes':'No';			
		$this->_value .= '</a><input type="hidden" name="'.$this->name.'" value="'.$this->value.'" />';*/
		return parent::render();
	}

}

?>