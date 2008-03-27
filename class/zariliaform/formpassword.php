<?php
// $Id: formpassword.php,v 1.4 2007/05/09 14:14:27 catzwolf Exp $
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
 * A password field
 *
 * @author John Neill
 * @author Raimondas Rimkevicius
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 * @subpackage form
 */
class ZariliaFormPassword extends ZariliaFormElement {
    /**
     * Size of the field.
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
     * Initial content of the field.
     *
     * @var string
     * @access private
     */
    var $_value;
    var $_formname;

    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name "name" attribute
     * @param int $size Size of the field
     * @param int $maxlength Maximum length of the text
     * @param int $value Initial value of the field.
     * 								<b>Warning:</b> this is readable in cleartext in the page's source!
     */
    function ZariliaFormPassword( $caption, $name, $size, $maxlength, $value = '', $addver = 0, $formname = '' ) {
        $this->setCaption( $caption );
        $this->setName( $name );
        $this->setValue( $value );
        $this->_size = intval( $size );
        $this->_maxlength = intval( $maxlength );
        $this->_addver = ( intval( $addver ) != 1 ) ? 0 : 1;
        $this->_formname = $formname;
    }

    /**
     * Get the field size
     *
     * @return int
     */
    function getSize() {
        return $this->_size;
    }

    /**
     * Get the max length
     *
     * @return int
     */
    function getMaxlength() {
        return $this->_maxlength;
    }

    /**
     * Get the initial value
     *
     * @return string
     */
    function getValue() {
        return $this->_value;
    }

    /**
     * Set the initial value
     *
     * @patam $value	string
     */
    function setValue( $value ) {
        $this->_value = $value;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    function render() {
        $this->setFormType( 'password' );
		$config_handler = &zarilia_gethandler( 'config' );
        $passConfig = &$config_handler->getConfigsByCat( 2 );
        $ret = '';
        $formname = $this->_formname;
		$ret .= '<script type="text/javascript" src="' . ZAR_URL . '/include/javascript/passwordquality.js"></script>';
        $ret .= '<script type="text/javascript">
			var qualityName1 = "' . _MD_AM_PASSLEVEL1 . '";
			var qualityName2 = "' . _MD_AM_PASSLEVEL2 . '";
			var qualityName3 = "' . _MD_AM_PASSLEVEL3 . '";
			var qualityName4 = "' . _MD_AM_PASSLEVEL4 . '";
			var qualityName5 = "' . _MD_AM_PASSLEVEL5 . '";
			var qualityName6 = "' . _MD_AM_PASSLEVEL6 . '";
			var passField = "' . $this->getName() . '";
			var tipo = "0";
			var tipo1 = "0";
			var minpass = "' . $passConfig['minpass'] . '";
			var pass_level = "' . $passConfig['pass_level'] . '";
			</script>';
        $ret .= '<input type="hidden" name="regex" value="[^0-9]">';
        $ret .= '<input type="hidden" name="regex3" value="([0-9])\1+">';
        $ret .= '<input type="hidden" name="regex1" value="[0-9a-zA-Z]">';
        $ret .= '<input type="hidden" name="regex4" value="(\W)\1+">';
        $ret .= '<input type="hidden" name="regex2" value="[^A-Z]">';
        $ret .= '<input type="hidden" name="regex5" value="([A-Z])\1+">';
        $ret .= "<input type='password' name='" . $this->getName() . "' id='" . $this->getName() . "' size='" . $this->getSize() . "' maxlength='" . $this->getMaxlength() . "' value='" . $this->getValue() . "'" . $this->getExtra() . " />";
        if ( $this->_addver == true ) {
            $ret .= "&nbsp;<input type='password' name='" . $this->getName() . "2' id='v" . $this->getName() . "' size='v" . $this->getSize() . "' maxlength='" . $this->getMaxlength() . "' value='" . $this->getValue() . "'" . $this->getExtra() . " />";
        }
        $ret .= "<script language='javascript' src='" . ZAR_URL . "/include/javascript/percent_bar.js'></script>";
        return $ret;
    }
}

?>