<?php
// $Id: formlabel.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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
 * A text label
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormLabel extends ZariliaFormElement {
    /**
     * Text
     *
     * @var string
     * @access private
     */
    var $_value;

    /*
	* Modified by Catzwolf
	*/
    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $value Text
     */
    function ZariliaFormLabel( $caption = "", $value = "", $nocolspan = 0 )
    {
        $this -> setCaption( $caption );
        $this -> setNocolspan( $nocolspan );
        $this -> _value = $value;
    }

    /**
     * Get the text
     *
     * @return string
     */
    function getValue()
    {
        return $this -> _value;
    }

    /**
     * Prepare HTML for output
     *
     * @return string
     */
    function render()
    {
        return $this -> getValue();
    }
}

?>