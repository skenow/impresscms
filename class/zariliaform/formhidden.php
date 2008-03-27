<?php
// $Id: formhidden.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
/**
 * A hidden field
 * 
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormHidden extends ZariliaFormElement {

	/**
     * Value
	 * @var	string	
	 * @access	private
	 */
	var $_value;

	/**
	 * Constructor
	 * 
	 * @param	string	$name	"name" attribute
	 * @param	string	$value	"value" attribute
	 */
	function ZariliaFormHidden($name, $value){
		$this->setName($name);
		$this->setHidden();
		$this->setValue($value);
		$this->setCaption("");
	}

	/**
	 * Get the "value" attribute
	 * 
	 * @return	string
	 */
	function getValue(){
		return $this->_value;
	}

	/**
	 * Sets the "value" attribute
	 * 
	 * @patam  $value	string
	 */
	function setValue($value){
		$this->_value = $value;
	}

	/**
	 * Prepare HTML for output
	 * 
	 * @return	string	HTML
	 */
	function render(){
		return "<input type='hidden' name='".$this->getName()."' id='".$this->getName()."' value='".$this->getValue()."' />";
	}
}
?>