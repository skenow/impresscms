<?php
// $Id: formcalendar.php,v 1.2 2007/05/05 11:11:39 catzwolf Exp $
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
// Project: The ZARILIA Project                                                //
// ------------------------------------------------------------------------- //
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * See File: class/calendar/calendar.php | (c) dynarch.com 2004
 *      Distributed as part of "The Coolest DHTML Calendar"
 *      under the same terms.
 */

define( 'NEWLINE', "\n" );

class ZariliaFormCalendar extends ZariliaFormElement {
    var $calendar_lib_path;

    var $calendar_file;
    var $calendar_lang_file;
    var $calendar_setup_file;
    var $calendar_theme_file;
    var $calendar_theme_url;
    var $calendar_options = array();
    var $calendar_field_attributes = array();

    /**
     * Constuctor
     *
     * @param string $caption caption
     * @param string $name name
     * @param string $value initial content
     * @param array $extra_options Extra options - see class/calendar/calendar-setup.js for more info on possible parameters
     */
    function ZariliaFormCalendar( $caption, $name, $initial_value = 0, $calendar_options = array(), $calendar_field_attributes = array() ) {
        $stripped = true; //perhaps usable?
        $this->setCaption( $caption );
        $this->setName( $name );
        if ( !$initial_value ) {
            $initial_value = time();
        }

        $this->set_option( 'date', $initial_value );
        $this->set_option( 'ifFormat', '%m/%d/%Y %H:%M' );
        $this->set_option( 'daFormat', '%m/%d/%Y %H:%M' );
        $this->set_option( 'firstDay', 1 ); // show Monday first
        $this->set_option( 'showOthers', true );
        foreach ( $calendar_options as $name => $value ) {
            $this->set_option( $name, $value );
        }

        foreach ( $calendar_field_attributes as $name => $value ) {
            $this->set_field_attribute( $name, $value );
        }

        if ( $stripped ) {
            $this->calendar_file = 'calendar_stripped.js';
            $this->calendar_setup_file = 'calendar-setup_stripped.js';
        } else {
            $this->calendar_file = 'calendar.js';
            $this->calendar_setup_file = 'calendar-setup.js';
        }
        $lang = file_exists( ZAR_ROOT_PATH . '/class/calendar/lang/calendar-' . _LANGCODE . '.js' ) ? _LANGCODE : "en";
        $this->calendar_lang_file = 'lang/calendar-' . $lang . '.js';
        $this->calendar_lib_path = ZAR_URL . "/class/calendar/";
        $this->calendar_theme_file = 'calendar.css';
        $this->calendar_theme_url = ZAR_THEME_URL;
    }

    function set_option( $name, $value ) {
        $this->calendar_options[$name] = $value;
    }

    function set_field_attribute( $name, $value ) {
        $this->calendar_field_attributes[$name] = $value;
    }

    function load_head_files() {
        global $zariliaTpl;

		if ( !file_exists( $this->calendar_theme_url . "css/" . $this->calendar_theme_file ) ) {
            $this->calendar_theme_url = ZAR_THEME_URL . "/default/";
        }
        $zariliaTpl->addCss( $this->calendar_theme_url . "css/" . $this->calendar_theme_file, array( 'media' => 'all' ) );
        $zariliaTpl->addScript( $this->calendar_lib_path . $this->calendar_file );
        $zariliaTpl->addScript( $this->calendar_lib_path . $this->calendar_lang_file );
        $zariliaTpl->addScript( $this->calendar_lib_path . $this->calendar_setup_file );
    }

    function _make_calendar( $other_options = array() ) {
        $js_options = $this->_make_js_hash( array_merge( $this->calendar_options, $other_options ) );
        $code = ( '<script type="text/javascript">Calendar.setup({' . $js_options . '});</script>' );
        return $code;
    }

    function render() {
        $id = $this->_gen_id();
        if ( $id == 1 ) {
            $this->load_head_files();
        }
        $attrstr = $this->_make_html_attr( array_merge( $this->calendar_field_attributes,
                array( 'id' => $this->_get_id( $id ),
                    'type' => 'text',
                    'name' => $this->getName() ) ) );
        $ret = '<input ' . $attrstr . '/>';
        $ret .= '<a href="#" id="' . $this->_trigger_id( $id ) . '">' . '<img align="middle" border="0" src="' . $this->calendar_lib_path . 'img.gif" alt="" /></a>';
        $options = array( 'inputField' => $this->_get_id( $id ), 'button' => $this->_trigger_id( $id ) );
        $ret .= $this->_make_calendar( $options );
        return $ret;
    }
    // / PRIVATE SECTION
    function _get_id( $id ) {
        return 'calendar-field-' . $id;
    }
    function _trigger_id( $id ) {
        return 'calendar-trigger-' . $id;
    }
    function _gen_id() {
        static $idno = 0;
        $idno++;
        return $idno;
    }

    function _make_js_hash( $array ) {
        $jstr = '';
        reset( $array );
        while ( list( $key, $val ) = each( $array ) ) {
            if ( is_bool( $val ) ) {
                $val = $val ? 'true' : 'false';
            } else if ( !is_numeric( $val ) ) {
                $val = '"' . $val . '"';
            }
            if ( $jstr ) {
                $jstr .= ',';
            }
            $jstr .= '"' . $key . '":' . $val;
        }
        return $jstr;
    }

    function _make_html_attr( $array ) {
        $attrstr = '';
        reset( $array );
        while ( list( $key, $val ) = each( $array ) ) {
            $attrstr .= $key . '="' . $val . '" ';
        }
        return $attrstr;
    }
}

?>