<?php
// $Id: formradioyn.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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
include_once ZAR_ROOT_PATH."/class/zariliaform/formradio.php";

/**
 * Yes/No radio buttons.
 * 
 * A pair of radio buttons labelled _YES and _NO with values 1 and 0
 * 
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormRadioYN extends ZariliaFormRadio
{
	/**
	 * Constructor
	 * 
	 * @param	string	$caption
	 * @param	string	$name
	 * @param	string	$value		Pre-selected value, can be "0" (No) or "1" (Yes)
	 * @param	string	$yes		String for "Yes"
	 * @param	string	$no			String for "No"
	 */
	function ZariliaFormRadioYN($caption, $name, $value=null, $yes=_YES, $no=_NO)
	{
		$this->ZariliaFormRadio($caption, $name, $value);
		$this->addOption(1, $yes);
		$this->addOption(0, $no);
	}
}
?>