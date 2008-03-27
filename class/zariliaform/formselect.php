<?php
// $Id: formselect.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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
 * A select field
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormSelect extends ZariliaFormElement {
    /**
     * Options
     *
     * @var array
     * @access private
     */
    var $_options = array();

    /**
     * Allow multiple selections?
     *
     * @var bool
     * @access private
     */
    var $_multiple = false;

    /**
     * Number of rows. "1" makes a dropdown list.
     *
     * @var int
     * @access private
     */
    var $_size;

    /**
     * Pre-selcted values
     *
     * @var array
     * @access private
     */
    var $_value = array();

    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name "name" attribute
     * @param mixed $value Pre-selected value (or array of them).
     * @param int $size Number or rows. "1" makes a drop-down-list
     * @param bool $multiple Allow multiple selections?
     */
    function ZariliaFormSelect( $caption, $name, $value = null, $size = 1, $multiple = false )
    {
		$this -> setCaption( $caption );
        $this -> setName( $name );
        $this -> _multiple = $multiple;
		$this->setMultiChange();
        $this -> _size = intval( $size );
        if ( isset( $value ) ) {
            $this -> setValue( $value );
        }
    }

    /**
     * Are multiple selections allowed?
     *
     * @return bool
     */
    function isMultiple()
    {
        return $this -> _multiple;
    }

    /**
     * Get the size
     *
     * @return int
     */
    function getSize()
    {
        return $this -> _size;
    }

    /**
     * Get an array of pre-selected values
     *
     * @return array
     */
    function getValue()
    {
        return $this -> _value;
    }

    /**
     * Set pre-selected values
     *
     * @param  $value mixed
     */
    function setValue( $value )
    {
        if ( is_array( $value ) ) {
            foreach ( $value as $v ) {
                $this -> _value[] = $v;
            }
        } else {
            $this -> _value[] = $value;
        }
    }

    /**
     * Add an option
     *
     * @param string $value "value" attribute
     * @param string $name "name" attribute
     */
    function addOption( $value, $name = "" )
    {
        if ( $name != "" ) {
            $this -> _options[$value] = $name;
        } else {
            $this -> _options[$value] = $value;
        }
    }

    /**
     * Add multiple options
     *
     * @param array $options Associative array of value->name pairs
     */
    function addOptionArray( $options, $multi = true )
    {
		if ( is_array( $options ) ) {
            if ( $multi == true ) {
                foreach ( $options as $k => $v ) {
                    $this -> addOption( $k, $v );
                }
            } else {
                foreach ( $options as $k ) {
					$this -> addOption( $k, $k );
                }
            }
        }
    }

    /**
     * Get all options
     *
     * @return array Associative array of value->name pairs
     */
    function getOptions()
    {
        return $this -> _options;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    function render()
    {
		$ret = "<select size='" . $this -> getSize() . "'" . $this -> getExtra() . "";
        if ( $this -> isMultiple() != false ) {
            $ret .= " name='" . $this -> getName() . "[]' id='" . $this -> getName() . "[]' multiple='multiple'>\n";
        } else {
            $ret .= " name='" . $this -> getName() . "' id='" . $this -> getName() . "'>\n";
        }
        foreach ( $this -> getOptions() as $value => $name ) {
            $ret .= "<option value='" . htmlspecialchars( $value, ENT_QUOTES ) . "'";
			if ( count( $this -> getValue() ) > 0 && in_array( $value, $this -> getValue() ) ) {
                $ret .= " selected='selected'";
            }
            $ret .= ">" . $name . "</option>\n";
        }
        $ret .= "</select>";
        return $ret;
    }
}

?>