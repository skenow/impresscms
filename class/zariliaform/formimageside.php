<?php
// $Id: formimageside.php,v 1.3 2007/04/22 07:24:52 catzwolf Exp $
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
 * @author John Neill
 * @copyright copyright (c) 2006 Zarilia
 */
/**
 * Parent
 */
include_once ZAR_ROOT_PATH."/class/zariliaform/formselect.php";

/**
 * ZariliaFormImageSide
 *
 * @package
 * @author John Neill
 * @copyright Copyright (c) 2006
 * @version $Id: formimageside.php,v 1.3 2007/04/22 07:24:52 catzwolf Exp $
 * @access public
 */
class ZariliaFormImageSide extends ZariliaFormSelect {
    /*
	* Modified by Catzwolf
	*/
    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $value Text
     */
    function ZariliaFormImageSide( $caption, $name, $value = null, $size = 1 )
    {
        include_once ZAR_ROOT_PATH."/class/zariliaform/formselect.php";
		$this->ZariliaFormSelect( $caption, $name, $value, $size, 0 );
        $this->addOptionArray( array( 'left' => 'Left', 'center' => 'Center', 'right' => 'Right' ) );
    }
}

?>