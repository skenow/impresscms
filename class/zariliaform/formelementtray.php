<?php
// $Id: formelementtray.php,v 1.2 2007/04/22 07:21:38 catzwolf Exp $
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
 * A group of form elements
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 * @subpackage form
 */
class ZariliaFormElementTray extends ZariliaFormElement {
    /**
     * array of form element objects
     *
     * @var array
     * @access private
     */
    var $_elements = array();

    /**
     * required elements
     *
     * @var array
     */
    var $_required = array();

    /**
     * HTML to seperate the elements
     *
     * @var string
     * @access private
     */
    var $_delimeter;

    /**
     * whether to display captions
     *
     * @var bool
     * @access private
     */
    var $_showElementsCaptions;

    /**
     * constructor
     *
     * @param string $caption Caption for the group.
     * @param string $delimiter HTML to separate the elements
     */
    function ZariliaFormElementTray( $caption, $delimeter = "&nbsp;", $name = "", $showCaptions = true )
    {
        $this -> setName( $name );
        $this -> setCaption( $caption );
        $this -> _delimeter = $delimeter;
		$this -> _showElementsCaptions = $showCaptions;
    }

    /**
     * Is this element a container of other elements?
     *
     * @return bool true
     */
    function isContainer()
    {
        return true;
    }

    /**
     * Add an element to the group
     *
     * @param object $ &$element    {@link ZariliaFormElement} to add
     */
    function addElement( &$formElement, $required = false )
    {
        $this -> _elements[] = &$formElement;
        if ( $required ) {
            if ( !$formElement -> isContainer() ) {
                $this -> _required[] = &$formElement;
            } else {
                $required_elements = &$formElement -> getElements( true );
                $count = count( $required_elements );
                for ( $i = 0 ; $i < $count; $i++ ) {
                    $this -> _required[] = &$required_elements[$i];
                }
            }
        }
    }

    /**
     * get an array of "required" form elements
     *
     * @return array array of {@link ZariliaFormElement}s
     */
    function &getRequired()
    {
        return $this -> _required;
    }

    /**
     * Get an array of the elements in this group
     *
     * @param bool $recurse get elements recursively?
     * @return array Array of {@link ZariliaFormElement} objects.
     */
    function &getElements( $recurse = false )
    {
        if ( !$recurse ) {
            return $this -> _elements;
        } else {
            $ret = array();
            $count = count( $this -> _elements );
            for ( $i = 0; $i < $count; $i++ ) {
                if ( !$this -> _elements[$i] -> isContainer() ) {
                    $ret[] = &$this -> _elements[$i];
                } else {
                    $elements = &$this -> _elements[$i] -> getElements( true );
                    $count2 = count( $elements );
                    for ( $j = 0; $j < $count2; $j++ ) {
                        $ret[] = &$elements[$j];
                    }
                    unset( $elements );
                }
            }
            return $ret;
        }
    }

    /**
     * Get the delimiter of this group
     *
     * @return string The delimiter
     */
    function getDelimeter()
    {
        return $this -> _delimeter;
    }

    /**
     * prepare HTML to output this group
     *
     * @return string HTML output
     */
    function render()
    {
		$count = 0;
        $ret = '<div id="'.$this -> getName().'">';
        foreach ( $this -> getElements() as $ele ) {
            if ( $count > 0 ) {
                $ret .= $this -> getDelimeter();
            }
            if ( $this -> _showElementsCaptions && $ele -> getCaption() != '' ) {
                $ret .= $ele -> getCaption();
            }
            $ret .= $ele -> render() . "\n";
            if ( !$ele -> isHidden() ) {
                $count++;
            }
        }
		$ret .= '</div>';
        return $ret;
    }
}

?>