<?php
// $Id: formtext.php,v 1.1 2007/03/16 02:41:02 catzwolf Exp $
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
 * A simple text field
 * 
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormText extends ZariliaFormElement {

	/**
     * Size
	 * @var	int 
     * @access	private
	 */
	var $_size;

	/**
     * Maximum length of the text
	 * @var	int 
	 * @access	private
	 */
	var $_maxlength;

	/**
     * Initial text
	 * @var	string  
	 * @access	private
	 */
	var $_value;

	/**
	 * Constructor
	 * 
	 * @param	string	$caption	Caption
	 * @param	string	$name       "name" attribute
	 * @param	int		$size	    Size
	 * @param	int		$maxlength	Maximum length of text
     * @param	string  $value      Initial text
	 */
	function ZariliaFormText($caption, $name, $size, $maxlength, $value=""){
		$this->setCaption($caption);
		$this->setName($name);
		$this->_size = intval($size);
		$this->_maxlength = intval($maxlength);
		$this->setValue($value);
	}

	/**
	 * Get size
	 * 
     * @return	int
	 */
	function getSize(){
		return $this->_size;
	}

	/**
	 * Get maximum text length
	 * 
     * @return	int
	 */
	function getMaxlength(){
		return $this->_maxlength;
	}

	/**
	 * Get initial text value
	 * 
     * @return  string
	 */
	function getValue(){
		return $this->_value;
	}

	/**
	 * Set initial text value
	 * 
     * @param	$value  string
	 */
	function setValue($value){
		$this->_value = $value;
	}

	/**
	 * Prepare HTML for output
	 * 
     * @return	string  HTML
	 */
	function render(){
		return "<input type='text' name='".$this->getName()."' id='".$this->getName()."' size='".$this->getSize()."' maxlength='".$this->getMaxlength()."' value='".$this->getValue()."'".$this->getExtra()." />";
	}
}
?>