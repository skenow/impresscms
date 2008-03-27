<?php
// $Id: formelement.php,v 1.3 2007/04/22 07:21:38 catzwolf Exp $
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
 * Abstract base class for form elements
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 * @subpackage form
 */
class ZariliaFormElement {
    /**
     * *#@+
     *
     * @access private
     */
    /**
     * "name" attribute of the element
     *
     * @var string
     */
    var $_name;

    /**
     * caption of the element
     *
     * @var string
     */
    var $_caption;

    /**
     * Accesskey for this element
     *
     * @var string
     */
    var $_accesskey = '';

    /**
     * HTML class for this element
     *
     * @var string
     */
    var $_class = '';

    /**
     * hidden?
     *
     * @var bool
     */
    var $_hidden = false;

    /**
     * extra attributes to go in the tag
     *
     * @var string
     */
    var $_extra = "";

    /**
     * required field?
     *
     * @var bool
     */
    var $_required = false;

    /**
     * if multilanguage is turned on does user could enter
     * different values on different languages
     *
     * @var string
     */
    var $_multichange = true;

    /**
     * description of the field
     *
     * @var string
     */
    var $_description = "";
    /**
     * *#@-
     */

    var $_nocolspan = "";
    var $_formtype = "";
    /**
     * constructor
     */
    function ZariliaFormElement()
    {
        exit( "This class cannot be instantiated!" );
    }

    /**
     * Is this element a container of other elements?
     *
     * @return bool false
     */
    function isContainer()
    {
        return false;
    }

    /**
     * set the "name" attribute for the element
     *
     * @param string $name "name" attribute for the element
     */
    function setName( $name )
    {
        $this->_name = trim( $name );
    }

    /**
     * get the "name" attribute for the element
     *
     * @param bool $ encode?
     * @return string "name" attribute
     */
    function getName( $encode = true )
    {
        if ( false != $encode ) {
            return str_replace( "&amp;", "&", str_replace( "'", "&#039;", htmlspecialchars( $this->_name ) ) );
        }
        return $this->_name;
    }

    /**
     * set the "accesskey" attribute for the element
     *
     * @param string $key "accesskey" attribute for the element
     */
    function setAccessKey( $key )
    {
        $this->_accesskey = trim( $key );
    }
    /**
     * get the "accesskey" attribute for the element
     *
     * @return string "accesskey" attribute value
     */
    function getAccessKey()
    {
        return $this->_accesskey;
    }
    /**
     * If the accesskey is found in the specified string, underlines it
     *
     * @param string $str String where to search the accesskey occurence
     * @return string Enhanced string with the 1st occurence of accesskey underlined
     */
    function getAccessString( $str )
    {
        $access = $this->getAccessKey();
        if ( !empty( $access ) && ( false !== ( $pos = strpos( $str, $access ) ) ) ) {
            return substr( $str, 0, $pos ) . '<span style="text-decoration:underline">' . substr( $str, $pos, 1 ) . '</span>' . substr( $str, $pos + 1 );
        }
        return $str;
    }

    /**
     * set the "class" attribute for the element
     *
     * @param string $key "class" attribute for the element
     */
    function setClass( $class )
    {
        $class = trim( $class );
        if ( empty( $class ) ) {
            $this->_class = '';
        } else {
            $this->_class .= ( empty( $this->_class ) ? '' : ' ' ) . $class;
        }
    }
    /**
     * get the "class" attribute for the element
     *
     * @return string "class" attribute value
     */
    function getClass()
    {
        return $this->_class;
    }

    /**
     * set the caption for the element
     *
     * @param string $caption
     */
    function setCaption( $caption )
    {
        $this->_caption = trim( $caption );
    }

    /**
     * get the caption for the element
     *
     * @return string
     */
    function getCaption()
    {
        return $this->_caption;
    }

    /**
     * set the element's description
     *
     * @param string $description
     */
    function setDescription( $description )
    {
        $this->_description = trim( $description );
    }

    /**
     * get the element's description
     *
     * @return string
     */
    function getDescription()
    {
        return $this->_description;
    }

    /**
     * set the element's description
     *
     * @param string $description
     */
    function setRequired( $required = false )
    {
        $this->_required = $required;
    }

    /**
     * get the element's description
     *
     * @return string
     */
    function getRequired()
    {
		return $this->_required;
    }
    /**
     * flag element multichange state
     *
     * @param boolean $value
     */
    function setMultiChange( $value = false )
    {
        $this->_multichange = $value;
    }

    /**
     * gets elements multichange state
     *
     * @return bool
     */
    function getMultiChange()
    {
        return $this->_multichange;
    }

    /**
     * flag the element as "hidden"
     */
    function setHidden()
    {
        $this->_hidden = true;
    }

    /**
     * Find out if an element is "hidden".
     *
     * @return bool
     */
    function isHidden()
    {
        return $this->_hidden;
    }

    /**
     * Add extra attributes to the element.
     *
     * This string will be inserted verbatim and unvalidated in the
     * element's tag. Know what you are doing!
     *
     * @param string $extra
     * @param string $replace If true, passed string will replace current content otherwise it will be appended to it
     * @return string New content of the extra string
     */
    function setExtra( $extra, $replace = false )
    {
        if ( $replace ) {
            $this->_extra = trim( $extra );
        } else {
            $this->_extra .= " " . trim( $extra );
        }
        return $this->_extra;
    }

    /**
     * Get the extra attributes for the element
     *
     * @return string
     */
    function getExtra()
    {
        if ( isset( $this->_extra ) ) {
            return $this->_extra;
        }
    }

    /**
     * set the element's nocolspan
     * Modified by Catzwolf
     *
     * @param string $description
     */
    function setNocolspan( $nocolspan )
    {
        $this->_nocolspan = intval( $nocolspan );
    }

    /**
     * get the element's nocolspan
     * Modified by Catzwolf
     *
     * @return string
     */
    function getFormType()
    {
        return $this->_formtype;
    }

    /**
     * set the element's nocolspan
     * Modified by Catzwolf
     *
     * @param string $description
     */
    function setFormType( $value )
    {
        $this->_formtype = $value;
    }

    /**
     * get the element's nocolspan
     * Modified by Catzwolf
     *
     * @return string
     */
    function getNocolspan()
    {
        return $this->_nocolspan;
    }

    /**
     * Generates output for the element.
     *
     * This method is abstract and must be overwritten by the child classes.
     *
     * @abstract
     */
    function render()
    {
    }
}

?>