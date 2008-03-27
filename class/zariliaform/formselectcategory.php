<?php
// $Id: formselectcategory.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */

/**
 * Parent
 */
include_once ZAR_ROOT_PATH . "/class/zariliaform/formselect.php";
// RMV-NOTIFY
/**
 * A select field with a choice of available users
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormSelectCategory extends ZariliaFormSelect {
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param bool $include_anon Include user "anonymous"?
     * @param mixed $value Pre-selected value (or array of them).
     * @param int $size Number or rows. "1" makes a drop-down-list.
     * @param bool $multiple Allow multiple selections?
     */
    function ZariliaFormSelectCategory( $caption, $name, $value = null, $size = 1, $multiple = false )
    {
        $this->ZariliaFormSelect( $caption, $name, $value, $size, $multiple );
        $category_handler = &zarilia_gethandler( 'category' );
        $categorys = $category_handler->getList();
        $this->addOptionArray( $categorys );
    }
}

?>
