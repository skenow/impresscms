<?php
// $Id: formselectmatchoption.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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
 * base class
 */
include_once ZAR_ROOT_PATH."/class/zariliaform/formselect.php";

/**
 * A selection box with options for matching search terms.
 * 
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormSelectMatchOption extends ZariliaFormSelect
{
	/**
	 * Constructor
	 * 
	 * @param	string	$caption
	 * @param	string	$name
	 * @param	mixed	$value	Pre-selected value (or array of them). 
	 * 							Legal values are {@link ZAR_MATCH_START}, {@link ZAR_MATCH_END}, 
	 * 							{@link ZAR_MATCH_EQUAL}, and {@link ZAR_MATCH_CONTAIN}
	 * @param	int		$size	Number of rows. "1" makes a drop-down-list
	 */
	function ZariliaFormSelectMatchOption($caption, $name, $value=null, $size=1)
	{
		$this->ZariliaFormSelect($caption, $name, $value, $size, false);
		$this->addOption(ZAR_MATCH_START, _STARTSWITH);
		$this->addOption(ZAR_MATCH_END, _ENDSWITH);
		$this->addOption(ZAR_MATCH_EQUAL, _MATCHES);
		$this->addOption(ZAR_MATCH_CONTAIN, _CONTAINS);
	}
}
?>