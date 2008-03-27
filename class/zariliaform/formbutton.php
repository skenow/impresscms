<?php
// $Id: formbutton.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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
 * A button
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 * @subpackage form
 */
class ZariliaFormButton extends ZariliaFormElement
{
    /**
     * Value
     *
     * @var string
     * @access private
     */
    var $_value;

    /**
     * Type of the button. This could be either "button", "submit", or "reset"
     *
     * @var string
     * @access private
     */
    var $_type;

    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name
     * @param string $value
     * @param string $type Type of the button.
     * This could be either "button", "submit", or "reset"
     */
    function ZariliaFormButton( $caption, $name, $value = "", $type = "button", $onclick = "" )
    {
        $this -> setCaption( $caption );
        $this -> setName( $name );
        $this -> _type = $type;
        $this -> setValue( $value );
        if ( $onclick )
        {
            $this -> setExtra( $onclick );
        }
        else if ( $name == 'cancel' )
        {
            $this -> setExtra( 'onClick="history.go(-1);return true;"' );
        } else {
			$this -> setExtra( '' );
		}
    }

    /**
     * Get the initial value
     *
     * @return string
     */
    function getValue()
    {
        return $this -> _value;
    }

    /**
     * Set the initial value
     *
     * @return string
     */
    function setValue( $value )
    {
        $this -> _value = $value;
    }

    /**
     * Get the type
     *
     * @return string
     */
    function getType()
    {
        return $this -> _type;
    }

    /**
     * prepare HTML for output
     *
     * @return string
     */
    function render()
    {
        return "<input type='" . $this -> getType() . "' class='formbutton'  name='" . $this -> getName() . "'  id='" . $this -> getName() . "' value='" . $this -> getValue() . "'" . $this -> getExtra() . " />";
    }

	function renderButtons(){
		$ret = '<tr><td>
			<input type=\'button\' class=\'formbutton\'  name=\'cancel\'  id=\'cancel\' value=\'Cancel\' onClick=\'history.go(-1);return true;\' />
			<input type=\'reset\' class=\'formbutton\'  name=\'reset\'  id=\'reset\' value=\'Reset\'  />
			<input type=\'submit\' class=\'formbutton\'  name=\'submit\'  id=\'submit\' value=\'Submit\'  /></td></tr>';
		}
}

?>