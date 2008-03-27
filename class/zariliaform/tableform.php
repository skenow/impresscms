<?php
// $Id: tableform.php,v 1.2 2007/04/22 07:21:38 catzwolf Exp $
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
 * the base class
 */
include_once ZAR_ROOT_PATH."/class/zariliaform/form.php";

/**
 * Form that will output formatted as a HTML table
 *
 * No styles and no JavaScript to check for required fields.
 *
 * @author	Kazumi Ono	
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 *
 * @package     kernel
 * @subpackage  form
 */
class ZariliaTableForm extends ZariliaForm
{

	/**
	 * create HTML to output the form as a table
	 *
     * @return	string
	 */
	function render()
	{
		$ret = $this->getTitle()."\n<form name='".$this->getName()."' id='".$this->getName()."' action='".$this->getAction()."' method='".$this->getMethod()."'".$this->getExtra().">\n<table border='0' width='100%'>\n";
		foreach ( $this->getElements() as $ele ) {
			if ( !$ele->isHidden() ) {
				$ret .= "<tr valign='top' align='left'><td width='40%'>".$ele->getCaption();
				if ($ele->getDescription() != '') {
					$ret .= '<div style="padding-top: 8px;"><span style="font-weight: normal;">'.$ele->getDescription().'</span></div>';
				}
				$ret .= "</td>
				<td>".$ele->render()."</td></tr>";
			} else {
				$ret .= $ele->render()."\n";
			}
		}
		$ret .= "</table>\n";
		$ret .= "</form>\n";
		return $ret;
	}
}
?>