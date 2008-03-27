<?php
// $Id: function.zarilia_link.php,v 1.1 2007/03/16 02:42:07 catzwolf Exp $
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

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     zarilia_link
 * Version:  1.0
 * Author:	 Skalpa Keo 
 * Purpose:  format URL for linking to specific Zarilia page
 * Input:    addon	= addon to link to (optional, default to current addon)
 *           page	= page to link to (optional, default to current page)
 *           params	= query string parameters (optional, default to empty)
 *					ex: urlparm1=,urlparm2,urlparm3=val3, etc.....
 *						urlparm3 value will be set to val3
 *						urlparm2 value will keep current one (no = sign)
 *						urlparm1 value will be set to empty ( = sign, but nothing after)
 *
 *			I.e: The template called by 'index.php?cid=5' calls this function with
 *				{zarilia_link page="viewcat.php" urlvars="cid,orderby=titleA"}>
 *			Then the generated URL will be:
 *				ZAR_URL/addons/ADDONNAME/viewcat.php?cid=5&orderby=titleA
 * -------------------------------------------------------------
 */

function smarty_function_zarilia_link($params, $smarty) {
	$urlstr='';
	if (isset($params['urlvars'])) {
		$szvars=explode( '&', $params['urlvars'] );
		$vars=array();
		// Split the string making an array from the ('name','value') pairs
		foreach ($szvars as $szvar) {
			$pos=strpos($szvar,'=');
			if ( $pos != false ) {			// If a value is specified, use it
				$vars[] = array( 'name' => substr($szvar,0,$pos), 'value' => substr($szvar,$pos+1) );
			} else {						// Otherwise use current one (if any)
				if ( isset($_POST[$szvar]) ) {
					$vars[] = array( 'name' => $szvar, 'value' => $_POST[$szvar] );
				} elseif ( isset($_GET[$szvar]) ) {
					$vars[] = array( 'name' => $szvar, 'value' => $_GET[$szvar] );
				}
			}
		}
		// Now reconstruct query string from specified variables
		foreach ($vars as $var) {
			$urlstr = "$urlstr&{$var['name']}={$var['value']}";
		}
		if ( strlen($urlstr) > 0 ) {
			$urlstr = '?' . substr( $urlstr, 1 );
		}
	}

	// Get default addon/page from current ones if necessary
	$addon='';
	$page='';
	if ( !isset($params['addon']) ) {
		if ( isset($GLOBALS['zariliaAddon']) && is_object($GLOBALS['zariliaAddon']) ) {
			$addon = $GLOBALS['zariliaAddon']->getVar('dirname');
		}
	} else {
		$addon = $params['addon'];
	}
	if ( !isset($params['page']) ) {
		$cur = zarilia_getenv('PHP_SELF');
		$page = substr( $cur, strrpos( $cur, '/' ) + 1 );
	} else {
		$page = $params['page'];
	}
	// Now, return entire link URL :-)
	if ( empty($addon) ) {
		echo ZAR_URL . "/$page" . $urlstr;
	} else {
		echo ZAR_URL . "/addons/$addon/$page" . $urlstr;
	}
}

?>