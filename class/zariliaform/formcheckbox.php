<?php
// $Id: formcheckbox.php,v 1.2 2007/04/22 07:21:38 catzwolf Exp $
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
 * One or more Checkbox(es)
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormCheckBox extends ZariliaFormElement {
    /**
     * Availlable options
     *
     * @var array
     * @access private
     */
    var $_options = array();

    /**
     * pre-selected values in array
     *
     * @var array
     * @access private
     */
    var $_value = array();

    /**
     * Int
     *
     * @var int
     * @access private
     */
    var $_column = array();

    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param mixed $value Either one value as a string or an array of them.
     */
    function ZariliaFormCheckBox( $caption, $name, $value = null, $columns = 3, $show_button = false ) {
        $this->setCaption( $caption );
        $this->setName( $name );
        $this->setColumn( $columns );
        if ( isset( $value ) ) {
            $this->setValue( $value );
        }
        $this->show_button = $show_button;
    }

    /**
     * ZariliaFormCheckBox::getValue()
     *
     * @return
     */
    function getColumn() {
        return $this->_column;
    }

    /**
     * ZariliaFormCheckBox::setColumn()
     *
     * @param  $value
     * @return
     */
    function setColumn( $value ) {
        $this->_column = intval( $value );
    }

    /**
     * Get the "value"
     *
     * @return array
     */
    function getValue() {
        return $this->_value;
    }

    /**
     * Set the "value"
     *
     * @param array $
     */
    function setValue( $value ) {
        $this->_value = array();
        if ( is_array( $value ) ) {
            foreach ( $value as $v ) {
                $this->_value[] = $v;
            }
        } else {
            $this->_value[] = $value;
        }
    }

    /**
     * Add an option
     *
     * @param string $value
     * @param string $name
     */
    function addOption( $value, $name = "" ) {
        if ( $name != "" ) {
            $this->_options[$value] = $name;
        } else {
            $this->_options[$value] = $value;
        }
    }

    /**
     * Add multiple Options at once
     *
     * @param array $options Associative array of value->name pairs
     */
    function addOptionArray( $options ) {
        if ( is_array( $options ) ) {
            foreach ( $options as $k => $v ) {
                $this->addOption( $k, $v );
            }
        }
    }

    /**
     * Get an array with all the options
     *
     * @return array Associative array of value->name pairs
     */
    function getOptions() {
        return $this->_options;
    }

    /**
     * prepare HTML for output
     *
     * @return string
     */
    function render() {
        $ret = "";
        $this->setFormType( 'checkbox' );
        if ( count( $this->getOptions() ) > 1 && substr( $this->getName(), -2, 2 ) != "[]" ) {
            $newname = $this->getName() . "[]";
            $this->setName( $newname );
        }
        $i = 0;
        $ret .= '<table cellpadding="0" cellspacing="0"><tr>';
        foreach ( $this->getOptions() as $value => $name ) {
            $i++;
            $ret .= "<td>";
            $ret .= "<input type='checkbox' name='" . $this->getName() . "' value='" . $value . "'";
            if ( count( $this->getValue() ) > 0 && in_array( $value, $this->getValue() ) ) {
                $ret .= " checked='checked'";
            }
            $ret .= $this->getExtra() . " id=\"".$this->getName()."_id\" /> <label for=\"".$this->getName()."_id\">" . $name . "</label>\n";
            $ret .= "</td>";
            if ( $i > 1 && ( $i == $this->getColumn() ) ) {
                $ret .= '</tr><tr>';
                $i = 0;
            }
        }
        $ret .= '</tr></table>';
        if ( $this->show_button ) {
            $ret .= '<br /><input name="button" type="button" style="width: 100px;" onClick="this.value=zariliaCheckAllElementsButton(this,\'' . $this->getName() . '\')" value="Check All">';
        }
        return $ret;
    }
}

?>