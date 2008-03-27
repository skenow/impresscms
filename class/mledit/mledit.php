<?php
// $Id: mledit.php,v 1.2 2007/04/22 07:21:34 catzwolf Exp $
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
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

class MultiLanguageEditor {
    var $_ml_content = true;

    function &getStyles()
    {
        $rez = array();
        if ( $handler = opendir( ZAR_ROOT_PATH . '/class/mledit/' ) ) {
            while ( false !== ( $file = readdir( $handler ) ) ) {
                if ( substr( $file, 0, 11 ) == "mledititem_" ) {
                    $rez[substr( $file, 11, -4 )] = ucwords( str_replace( "_", " ", substr( $file, 11, -4 ) ) );
                }
            }
        }
        return $rez;
    }

    function render_simple( $form, $validation = '' )
    {
        $this->_ml_content = false;
        $ret = "<form name='" . $form->getName() . "' id='" . $form->getName() . "' op='' method='" . $form->getMethod() . "'";
        if ( $validation ) $ret .= " onsubmit='return zariliaFormValidate_" . $form->getName() . "();'";
        $ret .= " onsubmit=\"SubmitForm(this,'" . $form->getAction() . "');\"";
        $ret .= " " . $form->getExtra() . ">\n";
        $ret .= $form->render_table( $form->getElements() );
        $ret .= "</form>";
        return $ret;
    }

    function render_multilanguage_elements( &$elements, &$language )
    {
        $this->_ml_content = true;
        $count = 0;
        foreach ( $elements as $key => $ele ) {
            if ( !is_object( $ele ) ) {
                $count++;
                continue;
            }
            if ( $ele->isHidden() ) {
                $count++;
                continue;
            }
            if ( $ele->isContainer() ) {
                $elems = $elements[$key]->getElements();
                $count += $this->render_multilanguage_elements( $elems, $language );
            } else {
                if ( !$ele->getMultiChange() ) {
                    if ( is_callable( array( $ele, "isMultiple" ) ) ) {
                        $elements[$key]->setExtra( "onchange=\"ChangeAllFormsItems(this, '" . $ele->getName() . "'," . $language->getVar( 'lang_id' ) . ");\"" );
                    } else {
                        $elements[$key]->setExtra( "onclick=\"ChangeAllFormsItems(this, '" . $ele->getName() . "'," . $language->getVar( 'lang_id' ) . ");\"" );
                    }
                    $count++;
                }
            }
            $elements[$key]->setName( "zarilia_language[" . $language->getVar( 'lang_id' ) . "][" . $ele->getName() . "]" );
            if ( function_exists( 'translate' ) ) {
                if ( is_callable( array( $ele, "setValue" ) ) && ( !is_array( $ele->getValue() ) ) ) {
                    $elements[$key]->setValue( translate( $ele->getValue(), $language->getVar( 'lang_name' ) ) );
                }
            }
        }
        return $count;
    }

    function render_multilanguage( $form, $languages, $validation = false )
    {
        global $zariliaOption, $zariliaTpl, $zariliaConfig;
        $ret = "<form name='" . $form->getName() . "' id='" . $form->getName() . "' op='javascript:;' method='" . $form->getMethod() . "'";
        $ret .= " onsubmit=\"SubmitForm(this,'" . $form->getAction() . "');\"";
        $ret .= " " . $form->getExtra() . ">\n";
        $i = 0;
        $id = -1;
        foreach( $languages as $language ) {
            $elements = unserialize( serialize( $form->getElements() ) );
            $lang_code = $language->getVar( 'lang_code' );
            $count = $this->render_multilanguage_elements( $elements, $language );
            if ( $count > ( count( $elements )-2 ) ) {
                return $this->render_simple( $form, $validation );
            }
            $ret .= "<div id=\"form-" . $language->getVar( 'lang_id' ) . "-" . $zariliaOption['multilanguage_forms_count'] . "\"";
            if ( $zariliaConfig['language'] != $language->getVar( 'lang_name' ) ) {
                $ret .= " style=\"visibility: false; display: none;\"";
            } else {
                $id = $language->getVar( 'lang_id' );
            }
            $i++;
            $ret .= ">\n";
            $ret .= $form->render_table( $elements );
            $ret .= "</div>\n";
            if ( !isset( $zariliaOption['multilanguage_data'] ) ) $lngs[$language->getVar( 'lang_id' )] = $lang_code;
        }
        $ret .= "<div id=\"form-default-" . $zariliaOption['multilanguage_forms_count'] . "\"";
        $ret .= " style=\"visibility: false; display: none;\"";
        $ret .= ">\n";
        $ret .= $form->render_table( $form->getElements() );
        $ret .= "</div>\n";
        if ( !isset( $zariliaOption['multilanguage_data'] ) ) {
            reset( $languages );
            $language = current( $languages );
            $tmp1 = "<script type=\"text/javascript\">\n";
            $tmp1 .= "   languages_ids = {";
            $b = false;
            foreach ( $lngs as $lid => $lcd ) {
                if ( $b ) $tmp1 .= ",";
                $tmp1 .= '\'' . $lcd . '\': ' . strval( $lid );
                $b = true;
            }
            $tmp1 .= "};\n";
            $tmp1 .= "   currentid = $id;\n</script>";
            $zariliaTpl->addScript( $tmp1 );
            $zariliaOption['multilanguage_data'] = 1;
            unset( $b );
            unset( $lngs );
            unset( $tmp1 );
        }
        $ret .= "</form>\n<br />";
        return $ret;
    }

    function render( $form, $style = null, $validation = false )
    {
        global $zariliaOption, $zariliaTpl;
        if ( $style == null ) {
            global $zariliaConfig;
            if ( isset( $zariliaConfig['multiLanguageEditorStyle'] ) ) {
                $style = $zariliaConfig['multiLanguageEditorStyle'];
                if ( $style == '' ) $style = "Tabs_Top";
            } else {
                $style = "Tabs_Top";
            }
        }
        $language_handler = &zarilia_gethandler( 'language' );
        $languages = $language_handler->getAll();
        if ( count( $languages ) < 1 ) {
            $rez = $this->render_simple( $form, $validation );
        } else {
            $rez = $this->render_multilanguage( $form, $languages, $validation );
        }
        if ( $this->_ml_content ) {
            $class = "mlEditItem_" . $style;
            include_once ZAR_ROOT_PATH . "/class/mledit/" . strtolower( $class ) . ".php";
            $smake = &new $class( $languages, $form, '' );
            $smake->add( $rez );
            unset( $rez );
            return $smake->render();
        }
        return $rez;
    }
}

?>