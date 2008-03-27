<?php
// $Id: formgenpassword.php,v 1.3 2007/05/05 11:11:39 catzwolf Exp $
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
 * A simple text field
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormGenPassword extends ZariliaFormElement {
    /**
     * Autoupdate
     *
     * @var int
     * @access private
     */
    var $_autoupdate;

    /**
     * Size
     *
     * @var int
     * @access private
     */
    var $_size;

    /**
     * Maximum length of the text
     *
     * @var int
     * @access private
     */
    var $_maxlength;

    /**
     * Initial text
     *
     * @var string
     * @access private
     */
    var $_value;

    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name "name" attribute
     * @param int $size Size
     * @param int $maxlength Maximum length of text
     * @param string $value Initial text
     */
    function ZariliaFormGenPassword( $caption, $name, $size, $maxlength, $value = "", $form = 'userinfo', $autoupdate = true )
    {
        global $zariliaTpl, $zariliaOption;
        if ( is_object( $zariliaTpl ) ) {
            if ( !isset( $zariliaOption['pass.gen_loaded'] ) ) {
                $zariliaTpl->addScript( ZAR_URL . '/class/zariliaform/scripts/generate.js' );
                $zariliaOption['pass.gen_loaded'] = true;
            }
        } 
        $this->setCaption( $caption );
        $this->setName( $name );
        $this->setValue( $value );
        $this->_size = intval( $size );
        $this->_maxlength = intval( $maxlength );
        $this->_form = $form;
        $this->_autoupdate = $autoupdate;
    }

    /**
     * Get size
     *
     * @return int
     */
    function getSize()
    {
        return $this->_size;
    }

    /**
     * Get maximum text length
     *
     * @return int
     */
    function getMaxlength()
    {
        return $this->_maxlength;
    }

    /**
     * Get initial text value
     *
     * @return string
     */
    function getValue()
    {
        return $this->_value;
    }

    /**
     * Set initial text value
     *
     * @param  $value string
     */
    function setValue( $value )
    {
        $this->_value = $value;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    function render()
    {
        $rez = "<select name='type'>
				 <option selected>" . _US_PG_TYPE . "</option>
				 <option>" . _US_PG_UPPERCASE . "</option>
				 <option>" . _US_PG_LOWERCASE . "</option>
				 <option>" . _US_PG_MIXEDCASE . "</option>
				</select>
				<select name='length'>
				 <option value='0'>" . _US_PG_LENGTH . "</option>";
        for( $i = 1;$i < ( $this->_maxlength ); ++$i ) {
            if ( $i == intval( $this->_maxlength / 2 ) ) {
                $rez .= "<option value='$i' selected='selected'>" . sprintf( _US_PG_CHARACTERS, $i ) . "</options>";
            } else {
                $rez .= "<option value='$i'>" . sprintf( _US_PG_CHARACTERS, $i ) . "</options>";
            }
        }
        $rez .= "</select><br />
		 <input type='hidden' name='autoupdate' value='" . ( ( $this->_autoupdate ) ? 'true' : 'false' ) . "'>
		 <input type='text' name='password' size='20' readonly='readonly'><br />
		 <input type='button' name='Generate Password'  id='Generate Password' value='"._US_CREATEPASSWORD."' onClick='generate(this.form, true);' />";
        return $rez;
    }
}

?>