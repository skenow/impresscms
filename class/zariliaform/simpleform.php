<?php
// $Id: simpleform.php,v 1.2 2007/04/22 07:21:38 catzwolf Exp $
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
 * base class
 */
include_once ZAR_ROOT_PATH."/class/zariliaform/form.php";

/**
 * Form that will output as a simple HTML form with minimum formatting
 * 
 * @author	Kazumi Ono	
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * 
 * @package     kernel
 * @subpackage  form
 */
class ZariliaSimpleForm extends ZariliaForm
{
	/**
	 * create HTML to output the form with minimal formatting
	 * 
     * @return	string
	 */
	function render()
	{
		$ret = $this->getTitle()."\n<form name='".$this->getName()."' id='".$this->getName()."' action='".$this->getAction()."' method='".$this->getMethod()."'".$this->getExtra().">\n";
		foreach ( $this->getElements() as $ele ) {
			if ( !$ele->isHidden() ) {
				$ret .= "<b>".$ele->getCaption()."</b><br />".$ele->render()."<br />\n";
			} else {
				$ret .= $ele->render()."\n";
			}
		}
		$ret .= "</form>\n";
		return $ret;
	}
}
?>