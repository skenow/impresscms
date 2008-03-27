<?php
// $Id: user_register.php,v 1.4 2007/04/22 07:21:37 catzwolf Exp $
// auth_zarilia.php - ZARILIA authentification class
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
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * ZariliaUserRegister
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: user_register.php,v 1.4 2007/04/22 07:21:37 catzwolf Exp $
 * @access public
 */
global $zariliaConfig;

class ZariliaUserRegister extends ZariliaAuth {
    var $_point = 'intro';
    var $_stages = array();
    var $_zariliaConfigUser = array();
    var $_zariliaCoppa = array();
    var $_options = array();
    var $_key = 0;
    // protected//
    var $_start = 0;
    var $_pointer = 'intro';
    var $_hidden = array();
    /**
     * Authentication Service constructor
     */
    function ZariliaUserRegister () {
        if ( empty( $this->_zariliaConfigUser ) ) {
            $this->_zariliaConfigUser = &$GLOBALS['config_handler']->getConfigsByCat( ZAR_CONF_USER );
        }
        if ( !$this->_zariliaConfigUser['allow_register'] ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_NOTICE, _US_NOREGISTER );
            return false;
        }

        $this->_zariliaCoppa = &$GLOBALS['config_handler']->getConfigsByCat( ZAR_CONF_COPPA );
        if ( !$this->_zariliaCoppa['show_coppa'] ) {
            $this->_stages = array( 'intro', 'profile', 'newuser', 'saveuser' );
        } else {
            $this->_stages = array( 'intro', 'coppa', 'profile', 'newuser', 'saveuser' );
        }
    }

    /**
     * ZariliaUserRegister::addOptions()
     *
     * @param mixed $key
     * @param mixed $value
     * @return
     */
    function addOptions( $key = null, $value = null ) {
        if ( !is_array( $key ) ) {
            $this->option[$key] = $value;
        } else {
            foreach( $key as $k => $v ) {
                $this->option[$k] = $v;
            }
        }
    }

    /**
     * ZariliaUserRegister::initialize()
     *
     * @return
     */
    function initialize() {
        $point = zarilia_cleanRequestVars( $_REQUEST, 'point', 'intro', XOBJ_DTYPE_TXTBOX );
        $is_restart = ( in_array( $point, $this->_stages ) && $point != 'restart' ) ? true : false;

        if ( !$_SESSION[session_id()]['formregstart'] || $is_restart == false ) {
            $_SESSION[session_id()]['formregstart'] = 1;
            $this->_pointer = 'intro';
        } else {
            $_SESSION[session_id()]['formregstart'] = 1;
            $this->_pointer = $point;
        }
    }

    /**
     * ZariliaUserRegister::unRequest()
     *
     * @return
     */
    function unRequest() {
        if ( isset( $_REQUEST['submit'] ) ) {
            unset( $_REQUEST['submit'] );
            $this->_hidden = $_REQUEST;
        }
    }

    /**
     * ZariliaUserRegister::sidemenu()
     *
     * @return
     */
    function sidemenu() {
        $content = '<div id="stepbar">';
        foreach( $this->_stages as $k ) {
            $class = ( $k == $this->_pointer ) ? 'step-on': 'step-off';
            $content .= "<div class='$class'>" . constant( strtoupper( '_US_REG' . $k ) ) . "</div>";
        }
        $content .= '</div>';
        $this->option['stepbar'] = $content;
    }

    /**
     * ZariliaUserRegister::getbuttons()
     *
     * @return
     */
    function getbuttons() {
        $this->_key = array_search( $this->_pointer, $this->_stages );
        /* Back Button */
		//echo $this->_pointer;
		
        if ( $this->_pointer != 'intro' ) {
            $new_point = $this->_key-1;
            $this->addOptions( 'b_back', "<input type='button' class=\"mainbutton\" id=\"b_back\" value='" . _US_REG_BACK . "' onclick=\"location='index.php?page_type=register&amp;point=" . htmlspecialchars( $this->_stages[$new_point] ) . "'\" />  " );
        }

        /* Forward Button */
        if ( $this->_pointer != 'saveuser' ) {
            $new_point = $this->_key + 1;
            $content = "<input type='hidden' name='page_type' value='register' />\n";
            $content .= "<input type='hidden' name='point' value='" . htmlspecialchars( $this->_stages[$new_point] ) . "' />\n";
            $content .= "<input type='submit' class=\"mainbutton\" name='submit' id=\"b_next\" value='" . _US_REG_FORWARD . "' />\n";
            $this->addOptions( 'b_next', $content );
        }
        /* Reload Button */
        $this->addOptions( 'b_reload', "<input type='button' class=\"mainbutton\" id=\"b_reload\" value='" . _US_REG_REFRESH . "' onclick=\"location.reload();\" /> " );
        /* Restart Button */
        $this->addOptions( 'b_restart', "<input type='button' class=\"mainbutton\" id=\"b_restart\" value='" . _US_REG_RESTART . "' onclick=\"location='index.php?page_type=register&amp;point=restart'\" /> " );
    }

    function getForm() {
		global $zariliaConfig;
        $myts = &MyTextSanitizer::getInstance();
        require ZAR_ROOT_PATH . '/class/zariliaformloader.php';
        /**
         */
        include ZAR_ROOT_PATH . "/class/user/forms/regform_" . strval( $this->_pointer ) . ".php";
    }

    function isdefault() {
		global $zariliaConfig;
        /**
         */
        $this->initialize();
//		die('1');
        $this->unRequest();
        $this->getForm();
        $this->getbuttons();
        /**
         */
        $this->sidemenu();
        $this->addOptions( 'template_main', 'system_registerform.html' );
        $this->addOptions( 'login', true );
        return $this->option;
    }
}

?>