<?php
// $Id: formcaptcha.php,v 1.2 2007/05/09 14:14:27 catzwolf Exp $
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
class ZariliaFormCaptcha extends ZariliaFormElement {
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
    function ZariliaFormCaptcha( $caption, $name, $size, $maxlength, $value = "" ) {
        $this->setCaption( $caption );
        $this->setName( $name );
        $this->_size = intval( $size );
        $this->_maxlength = intval( $maxlength );
        $this->setValue( $value );
    }

    /**
     * Get size
     *
     * @return int
     */
    function getSize() {
        return $this->_size;
    }

    /**
     * Get maximum text length
     *
     * @return int
     */
    function getMaxlength() {
        return $this->_maxlength;
    }

    /**
     * Get initial text value
     *
     * @return string
     */
    function getValue() {
        return $this->_value;
    }

    /**
     * Set initial text value
     *
     * @param  $value string
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
        require( ZAR_ROOT_PATH . '/class/captcha/php-captcha.inc.php' );
        $aFonts = array( ZAR_ROOT_PATH . '/class/captcha/VeraMoBd.ttf' );
        $oVisualCaptcha = new PhpCaptcha( $aFonts, 150, 40 );
        $oVisualCaptcha->SetFileType( 'gif' );
        $oVisualCaptcha->UseColour( false );
        $oVisualCaptcha->Create( ZAR_CACHE_PATH . '/captchaimage.'.($t = sha1(time())).'.php' );
		return "<img src=\"" . ZAR_CACHE_URL  . "/captchaimage.".$t.".php\" alt=\"Visual CAPTCHA\" /> <br /> <input type=\"text\" name=\"" . $this->getName() . "\" id=\"" . $this->getName() . "\" size=\"" . $this->getSize() . "\" maxlength=\"" . $this->getMaxlength() . "\" value=\"" . $this->getValue() . "\"" . $this->getExtra() . " />";
    }
}

?>