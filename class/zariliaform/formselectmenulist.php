<?php
// $Id: formselectmenulist.php,v 1.3 2007/04/22 07:21:38 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
/**
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */

/**
 * Parent
 */
include_once ZAR_ROOT_PATH . "/class/zariliaform/formselect.php";

/**
 * A select field with countries
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormSelectMenu extends ZariliaFormSelect {
    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name "name" attribute
     * @param mixed $value Pre-selected value (or array of them).
     *                                 Legal are all 2-letter country codes (in capitals).
     * @param int $size Number or rows. "1" makes a drop-down-list
     * @param string $handler Handler to use to get the list
     * @param string $addon Dirname of addon - defaults to current addon
     */
    function ZariliaFormSelectMenu( $caption, $name, $value = null, $size = 1, $nullValue = false ) {
        $menus_handler = &zarilia_gethandler( 'menus' );
        $_menus = &$menus_handler->getMenuList();
        $multiple = 0;

        $this->ZariliaFormSelect( $caption, $name, $value, $size, $multiple );
        if ( $nullValue ) {
            $this->addOption( '', '------------------' );
        }
        $this->addOptionArray( $_menus );
    }
}

?>