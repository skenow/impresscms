<?php
// $Id: formselectlang.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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
 * lists of values
 */
include_once ZAR_ROOT_PATH."/class/zarilialists.php";
/**
 * parent class
 */
include_once ZAR_ROOT_PATH."/class/zariliaform/formselect.php";

/**
 * A select field with available languages
 * 
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormSelectLang extends ZariliaFormSelect
{
	/**
	 * Constructor
	 * 
	 * @param	string	$caption
	 * @param	string	$name
	 * @param	mixed	$value	Pre-selected value (or array of them).
	 * 							Legal is any name of a ZAR_ROOT_PATH."/language/" subdirectory.
	 * @param	int		$size	Number of rows. "1" makes a drop-down-list.
	 */
	function ZariliaFormSelectLang($caption, $name, $value=null, $size=1, $multi=false)
	{
		$this->ZariliaFormSelect($caption, $name, $value, $size);		
		$this->addOptionArray( ZariliaLists::getLangList(), $multi );
	}
}
?>