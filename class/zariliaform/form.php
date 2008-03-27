<?php
// $Id: form.php,v 1.6 2007/05/09 14:14:27 catzwolf Exp $
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
// public abstruct
/**
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */

/**
 * Abstract base class for forms
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 * @subpackage form
 */
class ZariliaForm {
    /**
     * *#@+
     *
     * @access private
     */
    /**
     * "op" attribute for the html form
     *
     * @var string
     */
    var $_action;

    /**
     * "method" attribute for the form.
     *
     * @var string
     */
    var $_method;

    /**
     * "name" attribute of the form
     *
     * @var string
     */
    var $_name;

    /**
     * title for the form
     *
     * @var string
     */
    var $_title;

    /**
     * array of {@link ZariliaFormElement} objects
     *
     * @var array
     */
    var $_elements = array();

    /**
     * extra information for the <form> tag
     *
     * @var string
     */
    var $_extra;

    /**
     * form input is in different languages
     * once
     *
     * @var boolean
     */
    var $_multilanguage = false;

    /**
     * required elements
     *
     * @var array
     */
    var $_required = array();

    /**
     * *#@-
     */

    /**
     * constructor
     *
     * @param string $title title of the form
     * @param string $name "name" attribute for the <form> tag
     * @param string $op "op" attribute for the <form> tag
     * @param string $method "method" attribute for the <form> tag
     */
    function ZariliaForm( $title, $name, $op, $method = "post" )
    {
        $this->_title = $title;
        $this->_name = $name != "" ? $name : "zarilia_form";
        $this->_action = $op;
        $this->_method = $method;
    }

    /**
     * return the title of the form
     *
     * @return string
     */
    function getTitle()
    {
        return $this->_title;
    }

    /**
     * get the "name" attribute for the <form> tag
     *
     * @return string
     */
    function getName()
    {
        return $this->_name;
    }

    /**
     * get the "op" attribute for the <form> tag
     *
     * @return string
     */
    function getAction()
    {
        return $this->_action;
    }

    /**
     * get the "method" attribute for the <form> tag
     *
     * @return string
     */
    function getMethod()
    {
        return $this->_method;
    }

    /**
     * Add an element to the form
     *
     * @param object $ &$formElement    reference to a {@link ZariliaFormElement}
     * @param bool $required is this a "required" element?
     */
    function addElement( &$formElement, $required = false )
    {
        if ( is_string( $formElement ) ) {
            $this->_elements[] = $formElement;
        } elseif ( is_subclass_of( $formElement, 'zariliaformelement' ) ) {
            $this->_elements[] = &$formElement;
            if ( $required ) {
                if ( !$formElement->isContainer() ) {
                    $this->_required[] = &$formElement;
                } else {
                    $required_elements = &$formElement->getRequired();
                    $count = count( $required_elements );
                    for ( $i = 0 ; $i < $count; $i++ ) {
                        $this->_required[] = &$required_elements[$i];
                    }
                }
            }
        }
    }

    /**
     * get an array of forms elements
     *
     * @param bool $ get elements recursively?
     * @return array array of {@link ZariliaFormElement}s
     */
    function &getElements( $recurse = false )
    {
        if ( !$recurse ) {
            return $this->_elements;
        } else {
            $ret = array();
            $count = count( $this->_elements );
            for ( $i = 0; $i < $count; $i++ ) {
                if ( !$this->_elements[$i]->isContainer() ) {
                    $ret[] = &$this->_elements[$i];
                } else {
                    $elements = &$this->_elements[$i]->getElements( true );
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
     * get an array of "name" attributes of form elements
     *
     * @return array array of form element names
     */
    function getElementNames()
    {
        $ret = array();
        $elements = &$this->getElements( true );
        $count = count( $elements );
        for ( $i = 0; $i < $count; $i++ ) {
            $ret[] = $elements[$i]->getName();
        }
        return $ret;
    }

    /**
     * get a reference to a {@link ZariliaFormElement} object by its "name"
     *
     * @param string $name "name" attribute assigned to a {@link ZariliaFormElement}
     * @return object reference to a {@link ZariliaFormElement}, false if not found
     */
    function &getElementByName( $name )
    {
        $elements = &$this->getElements( true );
        $count = count( $elements );
        for ( $i = 0; $i < $count; $i++ ) {
            if ( $name == $elements[$i]->getName() ) {
                return $elements[$i];
            }
        }
        $ret = false;
        return $ret;
    }

    /**
     * Sets the "value" attribute of a form element
     *
     * @param string $name the "name" attribute of a form element
     * @param string $value the "value" attribute of a form element
     */
    function setElementValue( $name, $value )
    {
        $ele = &$this->getElementByName( $name );
        if ( is_object( $ele ) && method_exists( $ele, 'setValue' ) ) {
            $ele->setValue( $value );
        }
    }

    /**
     * Sets the "value" attribute of form elements in a batch
     *
     * @param array $values array of name/value pairs to be assigned to form elements
     */
    function setElementValues( $values )
    {
        if ( is_array( $values ) && !empty( $values ) ) {
            // will not use getElementByName() for performance..
            $elements = &$this->getElements( true );
            $count = count( $elements );
            for ( $i = 0; $i < $count; $i++ ) {
                $name = $elements[$i]->getName();
                if ( $name && isset( $values[$name] ) && method_exists( $elements[$i], 'setValue' ) ) {
                    $elements[$i]->setValue( $value );
                }
            }
        }
    }

    /**
     * Gets the "value" attribute of a form element
     *
     * @param string $name the "name" attribute of a form element
     * @return string the "value" attribute assigned to a form element, null if not set
     */
    function &getElementValue( $name )
    {
        $ele = &$this->getElementByName( $name );
        $ret = ( is_object( $ele ) && method_exists( $ele, 'getValue' ) ) ? $ele->getValue( $value ) : '';
        return $ret;
    }

    /**
     * gets the "value" attribute of all form elements
     *
     * @return array array of name/value pairs assigned to form elements
     */
    function &getElementValues()
    {
        // will not use getElementByName() for performance..
        $elements = &$this->getElements( true );
        $count = count( $elements );
        $values = array();
        for ( $i = 0; $i < $count; $i++ ) {
            $name = $elements[$i]->getName();
            if ( $name && method_exists( $elements[$i], 'getValue' ) ) {
                $values[$name] = &$elements[$i]->getValue();
            }
        }
        return $values;
    }

    /**
     * set the extra attributes for the <form> tag
     *
     * @param string $extra extra attributes for the <form> tag
     */
    function setExtra( $extra )
    {
        $this->_extra = " " . $extra;
    }

    /**
     * get the extra attributes for the <form> tag
     *
     * @return string
     */
    function &getExtra()
    {
        if ( isset( $this->_extra ) ) {
            return $this->_extra;
        }
        $this->_extra = "";
        return $this->_extra;
    }

    /**
     * make an element "required"
     *
     * @param object $ &$formElement    reference to a {@link ZariliaFormElement}
     */
    function setRequired( &$formElement )
    {
        $this->_required[] = &$formElement;
    }

    /**
     * get an array of "required" form elements
     *
     * @return array array of {@link ZariliaFormElement}s
     */
    function &getRequired()
    {
        return $this->_required;
    }

    /**
     * insert a break in the form
     *
     * This method is abstract. It must be overwritten in the child classes.
     *
     * @param string $extra extra information for the break
     * @abstract
     */
    function insertBreak( $extra = null )
    {
    }

    /**
     * insert a break in the form
     *
     * This method is abstract. It must be overwritten in the child classes.
     *
     * @param string $extra extra information for the break
     * @abstract
     */
    function insertSplit( $extra = null )
    {
    }

    /**
     * returns renderered form
     *
     * This method is abstract. It must be overwritten in the child classes.
     *
     * @abstract
     */
    function render()
    {
    }

    /**
     * displays rendered form
     */
    function display()
    {
        echo $this->render();
    }

    /**
     * sets that form input is not multilanguage
     */
    function notMultiLanguage()
    {
        $this->_multilanguage = false;
    }

    /**
     * Renders the Javascript function needed for client-side for validation
     *
     * @param boolean $withtags Include the < javascript > tags in the returned string
     */
    function renderValidationJS( $withtags = true )
    {
        $js = "";
        if ( $withtags ) {
            $js .= "<script type='text/javascript'>\n<!--//\n";
        }
        $formname = $this->getName();
        $required = &$this->getRequired();
        $reqcount = count( $required );
        $js .= "function zariliaFormValidate_{$formname}() { myform = window.document.$formname;\n";
        if ( $reqcount < 1 ) return '';
        for ( $i = 0; $i < $reqcount; $i++ ) {
            $eltname = $required[$i]->getName();
            $eltcaption = $required[$i]->getCaption();
            $eltmsg = empty( $eltcaption ) ? $eltname : $eltcaption;
            $eltmsg = str_replace( array( ':', '?', '%' ), '', $eltmsg );
            $eltmsg = sprintf( _FORM_ENTER, $eltmsg );
            switch ( $required[$i]->getFormType() ) {
                case 'checkbox':
                    $js .= "if ( !myform.{$eltname}.checked ) { window.alert(\"{$eltmsg}\"); myform.{$eltname}.focus(); return false; }\n";
                    break;
                default:
                    $js .= "if ( myform.{$eltname}.value == \"\" ) { window.alert(\"{$eltmsg}\"); myform.{$eltname}.focus(); return false; }\n";
                    break;
            } // switch
        }
        $js .= "return true;\n}\n";
        if ( $withtags ) {
            $js .= "//--></script>";
        }
        return $js;
    }

    /**
     * assign to smarty form template instead of displaying directly
     *
     * @param object $ &$tpl    reference to a {@link Smarty} object
     * @see Smarty
     */
    function assign( &$tpl )
    {
        $i = 0;
        foreach ( $this->getElements() as $ele ) {
            if ( !is_object( $ele ) ) {
                $elements[$n]['split'] = $ele;
            } else {
                $n = ( $ele->getName() != "" ) ? $ele->getName() : $i;
                $elements[$n]['name'] = $ele->getName();
                $elements[$n]['caption'] = $ele->getCaption();
                $elements[$n]['body'] = $ele->render();
                $elements[$n]['hidden'] = $ele->isHidden();
                $elements[$n]['required'] = $ele->getRequired();
                if ( $ele->getDescription() != '' ) {
                    $elements[$n]['description'] = $ele->getDescription();
                }
            }
            $i++;
        }
        $js = $this->renderValidationJS( false );
        $tpl->assign( $this->getName(), array( 'title' => $this->getTitle(), 'name' => $this->getName(), 'op' => $this->getAction(), 'method' => $this->getMethod(), 'extra' => 'onsubmit="return zariliaFormValidate_' . $this->getName() . '();"' . $this->getExtra(), 'javascript' => $js, 'elements' => $elements ) );
    }
}

?>