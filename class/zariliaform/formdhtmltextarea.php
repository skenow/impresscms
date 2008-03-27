<?php
// $Id: formdhtmltextarea.php,v 1.2 2007/05/05 11:11:39 catzwolf Exp $
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
 * base class
 */
include_once ZAR_ROOT_PATH . "/class/zariliaform/formtextarea.php";
// Make sure you have included /include/zariliacodes.php, otherwise DHTML will not work properly!
/**
 * A textarea with zariliaish formatting and smilie buttons
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 * @subpackage form
 */
class ZariliaFormDhtmlTextArea extends ZariliaFormTextArea {
    /**
     * Hidden text
     *
     * @var string
     * @access private
     */
    var $_hiddenText;

    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name "name" attribute
     * @param string $value Initial text
     * @param int $rows Number of rows
     * @param int $cols Number of columns
     * @param string $hiddentext Hidden Text
     */
    function ZariliaFormDhtmlTextArea( $caption, $name, $value, $rows = 5, $cols = 50, $hiddentext = "zariliaHiddenText" ) {
        $this->ZariliaFormTextArea( $caption, $name, $value, $rows, $cols );
        $this->_hiddenText = $hiddentext;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    function render() {
        $ret = "<a name='moresmiley'></a><img onmouseover='style.cursor=\"hand\"' src='" . ZAR_URL . "/images/zariliaeditor/url.gif' alt='url' onclick='zariliaCodeUrl(\"" . $this->getName() . "\", \"" . htmlspecialchars( _ENTERURL, ENT_QUOTES ) . "\", \"" . htmlspecialchars( _ENTERWEBTITLE, ENT_QUOTES ) . "\");' />&nbsp;<img onmouseover='style.cursor=\"hand\"' src='" . ZAR_URL . "/images/zariliaeditor/email.gif' alt='email' onclick='javascript:zariliaCodeEmail(\"" . $this->getName() . "\", \"" . htmlspecialchars( _ENTEREMAIL, ENT_QUOTES ) . "\");' />&nbsp;<img onclick='javascript:zariliaCodeImg(\"" . $this->getName() . "\", \"" . htmlspecialchars( _ENTERIMGURL, ENT_QUOTES ) . "\", \"" . htmlspecialchars( _ENTERIMGPOS, ENT_QUOTES ) . "\", \"" . htmlspecialchars( _IMGPOSRORL, ENT_QUOTES ) . "\", \"" . htmlspecialchars( _ERRORIMGPOS, ENT_QUOTES ) . "\");' onmouseover='style.cursor=\"hand\"' src='" . ZAR_URL . "/images/zariliaeditor/imgsrc.gif' alt='imgsrc' />&nbsp;<img onmouseover='style.cursor=\"hand\"' onclick='javascript:openWithSelfMain(\"" . ZAR_URL . "/mediamanager.php?target=" . $this->getName() . "\",\"imgmanager\",
		700,430);' src='" . ZAR_URL . "/images/zariliaeditor/image.gif' alt='image' />&nbsp;<img src='" . ZAR_URL . "/images/zariliaeditor/code.gif' onmouseover='style.cursor=\"hand\"' alt='code' onclick='javascript:zariliaCodeCode(\"" . $this->getName() . "\", \"" . htmlspecialchars( _ENTERCODE, ENT_QUOTES ) . "\");' />&nbsp;<img onclick='javascript:zariliaCodeQuote(\"" . $this->getName() . "\", \"" . htmlspecialchars( _ENTERQUOTE, ENT_QUOTES ) . "\");' onmouseover='style.cursor=\"hand\"' src='" . ZAR_URL . "/images/zariliaeditor/quote.gif' alt='quote' /><br />\n";
        $sizearray = array( "xx-small", "x-small", "small", "medium", "large", "x-large", "xx-large" );
        $ret .= "<select id='" . $this->getName() . "Size' onchange='setVisible(\"" . $this->_hiddenText . "\");setElementSize(\"" . $this->_hiddenText . "\",this.options[this.selectedIndex].value);'>\n";
        $ret .= "<option value='SIZE'>" . _SIZE . "</option>\n";
        foreach ( $sizearray as $size ) {
            $ret .= "<option value='$size'>$size</option>\n";
        }
        $ret .= "</select>\n";
        $fontarray = array( "Arial", "Courier", "Georgia", "Helvetica", "Impact", "Verdana" );
        $ret .= "<select id='" . $this->getName() . "Font' onchange='setVisible(\"" . $this->_hiddenText . "\");setElementFont(\"" . $this->_hiddenText . "\",this.options[this.selectedIndex].value);'>\n";
        $ret .= "<option value='FONT'>" . _FONT . "</option>\n";
        foreach ( $fontarray as $font ) {
            $ret .= "<option value='$font'>$font</option>\n";
        }
        $ret .= "</select>\n";
        $colorarray = array( "00", "33", "66", "99", "CC", "FF" );
        $ret .= "<select id='" . $this->getName() . "Color' onchange='setVisible(\"" . $this->_hiddenText . "\");setElementColor(\"" . $this->_hiddenText . "\",this.options[this.selectedIndex].value);'>\n";
        $ret .= "<option value='COLOR'>" . _COLOR . "</option>\n";
        foreach ( $colorarray as $color1 ) {
            foreach ( $colorarray as $color2 ) {
                foreach ( $colorarray as $color3 ) {
                    $ret .= "<option value='" . $color1 . $color2 . $color3 . "' style='background-color:#" . $color1 . $color2 . $color3 . ";color:#" . $color1 . $color2 . $color3 . ";'>#" . $color1 . $color2 . $color3 . "</option>\n";
                }
            }
        }
        $ret .= "</select><span id='" . $this->_hiddenText . "'>" . _EXAMPLE . "</span>\n";
        $ret .= "<br /><br />\n";
        $ret .= "<img onclick='javascript:setVisible(\"" . $this->_hiddenText . "\");makeBold(\"" . $this->_hiddenText . "\");' onmouseover='style.cursor=\"hand\"' src='" . ZAR_URL . "/images/zariliaeditor/bold.gif' alt='bold' />&nbsp;<img onclick='javascript:setVisible(\"" . $this->_hiddenText . "\");makeItalic(\"" . $this->_hiddenText . "\");' onmouseover='style.cursor=\"hand\"' src='" . ZAR_URL . "/images/zariliaeditor/italic.gif' alt='italic' />&nbsp;<img onclick='javascript:setVisible(\"" . $this->_hiddenText . "\");makeUnderline(\"" . $this->_hiddenText . "\");' onmouseover='style.cursor=\"hand\"' src='" . ZAR_URL . "/images/zariliaeditor/underline.gif' alt='underline' />&nbsp;<img onclick='javascript:setVisible(\"" . $this->_hiddenText . "\");makeLineThrough(\"" . $this->_hiddenText . "\");' src='" . ZAR_URL . "/images/zariliaeditor/linethrough.gif' alt='linethrough' onmouseover='style.cursor=\"hand\"' />&nbsp;&nbsp;<input type='text' id='" . $this->getName() . "Addtext' size='20' />&nbsp;<input type='button' onclick='zariliaCodeText(\"" . $this->getName() . "\", \"" . $this->_hiddenText . "\", \"" . htmlspecialchars( _ENTERTEXTBOX, ENT_QUOTES ) . "\")' class='formbutton' value='" . _ADD . "' /><br /><br /><textarea id='" . $this->getName() . "' name='" . $this->getName() . "' onselect=\"zariliaSavePosition('" . $this->getName() . "');\" onclick=\"zariliaSavePosition('" . $this->getName() . "');\" onkeyup=\"zariliaSavePosition('" . $this->getName() . "');\" cols='" . $this->getCols() . "' rows='" . $this->getRows() . "'" . $this->getExtra() . ">" . $this->getValue() . "</textarea><br />\n";
        $ret .= $this->_renderSmileys();
        return $ret;
    }

    /**
     * prepare HTML for output of the smiley list.
     *
     * @return string HTML
     */
    function _renderSmileys() {
		static $smiles;

		//$myts = MyTextSanitizer::getInstance();
		//$smiles = $myts->getSmileys();
        $ret = '';
        if ( empty( $smiles ) ) {
            $db = &ZariliaDatabaseFactory::getdatabaseconnection();
            if ( $result = $db->Execute( 'SELECT * FROM ' . $db->prefix( 'smiles' ) . ' WHERE display=1' ) ) {
                while ( $smiles = $result->FetchRow() ) {
                    $ret .= "<img onclick='zariliaCodeSmilie(\"" . $this->getName() . "\", \" " . $smiles['code'] . " \");' onmouseover='style.cursor=\"pointer\"' src='" . ZAR_UPLOAD_URL . "/" . htmlspecialchars( $smiles['smile_url'], ENT_QUOTES ) . "' alt='' />";
                }
            }
        } else {
            $count = count( $smiles );
            for ( $i = 0; $i < $count; $i++ ) {
                if ( $smiles[$i]['display'] == 1 ) {
                    $ret .= "<img onclick='zariliaCodeSmilie(\"" . $this->getName() . "\", \" " . $smiles[$i]['code'] . " \");' onmouseover='style.cursor=\"pointer\"' src='" . ZAR_UPLOAD_URL . "/" . htmlSpecialChars( $smiles['smile_url'], ENT_QUOTES ) . "' border='0' alt='' />";
                }
            }
        }
        $ret .= "&nbsp;[<a href='#moresmiley' onclick='javascript:openWithSelfMain(\"" . ZAR_URL . "/misc.php?type=smilies&amp;target=" . $this->getName() . "\",\"smilies\",300,475);'>" . _MORE . "</a>]";
		return $ret;
    }
}

?>
