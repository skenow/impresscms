<?php
// $Id: formradio.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//

/**
 * 
 * 
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
/**
 * A Group of radiobuttons
 * 
 * @author	Kazumi Ono	
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * 
 * @package		kernel
 * @subpackage	form
 */
class ZariliaFormRadio extends ZariliaFormElement {

	/**
     * Array of Options
	 * @var	array	
	 * @access	private
	 */
	var $_options = array();

	/**
     * Pre-selected value
	 * @var	string	
	 * @access	private
	 */
	var $_value;

	/**
	 * Constructor
	 * 
	 * @param	string	$caption	Caption
	 * @param	string	$name		"name" attribute
	 * @param	string	$value		Pre-selected value
	 */
	function ZariliaFormRadio($caption, $name, $value = null){
		$this->setCaption($caption);
		$this->setMultiChange();
		$this->setName($name);
		if (isset($value)) {
			$this->setValue($value);
		}
	}

	/**
	 * Get the pre-selected value
	 * 
	 * @return	string
	 */
	function getValue(){
		return $this->_value;
	}

	/**
	 * Set the pre-selected value
	 * 
	 * @param	$value	string
	 */
	function setValue($value){
		$this->_value = $value;
	}

	/**
	 * Add an option
	 * 
	 * @param	string	$value	"value" attribute - This gets submitted as form-data.
	 * @param	string	$name	"name" attribute - This is displayed. If empty, we use the "value" instead.
	 */
	function addOption($value, $name=""){
		if ( $name != "" ) {
			$this->_options[$value] = $name;
		} else {
			$this->_options[$value] = $value;
		}
	}

	/**
	 * Adds multiple options
	 * 
	 * @param	array	$options	Associative array of value->name pairs.
	 */
	function addOptionArray($options){
		if ( is_array($options) ) {
			foreach ( $options as $k=>$v ) {
				$this->addOption($k, $v);
			}
		}
	}

	/**
	 * Gets the options
	 * 
	 * @return	array	Associative array of value->name pairs.
	 */
	function getOptions(){
		return $this->_options;
	}

	/**
	 * Prepare HTML for output
	 * 
	 * @return	string	HTML
	 */
	function render(){
		$ret = "";
		$obn = $this->getName();
		foreach ( $this->getOptions() as $value => $name ) {
			$ret .= "<input type='radio' name='$obn' value='".$value."'";
			$selected = $this->getValue();
			if ( isset($selected) && ($value == $selected) ) {
				$ret .= " checked='checked'";
			}
			$ret .= $this->getExtra()." />".$name."\n";
		}
		return $ret;
	}
}
?>