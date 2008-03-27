<?php
// $Id: formfile.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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
 * A file upload field
 *
 * @author	Kazumi Ono	
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 *
 * @package		kernel
 * @subpackage	form
 */
class ZariliaFormFile extends ZariliaFormElement {

	/**
     * Maximum size for an uploaded file
	 * @var	int
	 * @access	private
	 */
	var $_maxFileSize;

	/**
	 * Constructor
	 *
	 * @param	string	$caption		Caption
	 * @param	string	$name			"name" attribute
	 * @param	int		$maxfilesize	Maximum size for an uploaded file
	 */
	function ZariliaFormFile($caption, $name, $maxfilesize = null){
		$this->setCaption($caption);
		$this->setName($name);
		$this->setMultiChange();
		$this->_maxFileSize = intval($maxfilesize);
	}

	 /**
     * Files boxes is allways not multilanguages
     *
     * @return bool
     */
    function isMultiple()
    {
        return false;
    }

	/**
	 * Get the maximum filesize
	 *
	 * @return	int
	 */
	function getMaxFileSize(){
		return $this->_maxFileSize;
	}

	/**
	 * prepare HTML for output
	 *
	 * @return	string	HTML
	 */
	function render(){
		return "<input type='hidden' name='MAX_FILE_SIZE' value='".$this->getMaxFileSize()."' />
			<input type='file' name='".$this->getName()."' id='".$this->getName()."'".$this->getExtra()." />
			<input type='hidden' name='zarilia_upload_file[]' id='zarilia_upload_file[]' value='".$this->getName()."' />";
	}
}
?>