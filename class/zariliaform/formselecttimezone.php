<?php
// $Id: formselecttimezone.php,v 1.1 2007/03/16 02:41:02 catzwolf Exp $
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
 * base class
 */
include_once ZAR_ROOT_PATH."/class/zariliaform/formselect.php";

/**
 * A select box with timezones
 * 
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormSelectTimezone extends ZariliaFormSelect
{
	/**
	 * Constructor
	 * 
	 * @param	string	$caption
	 * @param	string	$name
	 * @param	mixed	$value	Pre-selected value (or array of them). 
	 * 							Legal values are "-12" to "12" with some ".5"s strewn in ;-)
	 * @param	int		$size	Number of rows. "1" makes a drop-down-box.
	 */
	function ZariliaFormSelectTimezone($caption, $name, $value=null, $size=1)
	{
		$this->ZariliaFormSelect($caption, $name, $value, $size);
		$this->addOptionArray(ZariliaLists::getTimeZoneList());
	}
}
?>