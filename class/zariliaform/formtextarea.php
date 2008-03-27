<?php
// $Id: formtextarea.php,v 1.1 2007/03/16 02:41:02 catzwolf Exp $
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
 * A textarea
 * 
 * @author Kazumi Ono 
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 * @subpackage form
 */
class ZariliaFormTextArea extends ZariliaFormElement {
    /**
     * number of columns
     * 
     * @var int 
     * @access private 
     */
    var $_cols;

    /**
     * number of rows
     * 
     * @var int 
     * @access private 
     */
    var $_rows;

    /**
     * initial content
     * 
     * @var string 
     * @access private 
     */
    var $_value;

    /**
     * Constuctor
     * 
     * @param string $caption caption
     * @param string $name name
     * @param string $value initial content
     * @param int $rows number of rows
     * @param int $cols number of columns
     */
    function ZariliaFormTextArea( $caption, $name, $value = "", $rows = 5, $cols = 50 )
    {
        $this -> setCaption( $caption );
        $this -> setName( $name );
        $this -> _rows = intval( $rows );
        $this -> _cols = intval( $cols );
        $this -> setValue( $value );
    } 

    /**
     * get number of rows
     * 
     * @return int 
     */
    function getRows()
    {
        return $this -> _rows;
    } 

    /**
     * Get number of columns
     * 
     * @return int 
     */
    function getCols()
    {
        return $this -> _cols;
    } 

    /**
     * Get initial content
     * 
     * @return string 
     */
    function getValue()
    {
        return $this -> _value;
    } 

    /**
     * Set initial content
     * 
     * @param  $value string
     */
    function setValue( $value )
    {
        $this -> _value = $value;
    } 

    /**
     * prepare HTML for output
     * 
     * @return sting HTML
     */
    function render()
    {
        $name = $this -> getName();
        return "<textarea name='" . $name . "' id='" . $name . "' rows='" . $this -> getRows() . "' cols='" . $this -> getCols() . "'" . $this -> getExtra() . ">" .$this -> getValue() . "</textarea>";
    } 
} 

?>