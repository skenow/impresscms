<?php
// $Id: control.class.php,v 1.1 2007/03/16 02:40:18 catzwolf Exp $
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
// no direct access
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );
/**
 * Ajax enabled controls
 *
 * @package kernel
 * @subpackage ajax
 */
class ZariliaControl {
    var $_params = array();
    var $_js = array();
    var $_style = array();
    var $_value = '';
    var $_type;
    var $_events = array();
    var $_vars = array();
    var $_functions = array();
    var $_prefix = 'ZariliaControl';
    var $_needfkl;

	/**
	 * Creator function
	 * use this if you wanna easy to create control instance
	 * Example: $setup = &ZariliaControl::getInstance('CPSetup');
	 */
	function &getInstance($control_name, $name=null) {
		require_once ZAR_CONTROLS_PATH . '/'.strtolower($control_name).'.php';
		$class = 'ZariliaControl_'.$control_name.'.php';
		$obj = new $class($name);
		unset($class);
		return $obj;
	}

    /**
     * Constructor
     */
    function ZariliaControl ( $type, $name = null, $value = '', $needfullload = false )
    {
        global $zariliaOption, $zariliaTpl, $zariliaAjax;
        if ( !isset( $zariliaOption['ajax_enabled'] ) ) {
            $zariliaOption['ajax_enabled'] = true;
            if ( !isset( $zariliaOption['installing'] ) ) {
                if ( !is_object( $zariliaTpl ) )
					trigger_error( 'Template engine is not initializated', E_USER_ERROR  );
					//die( 'Error: Template engine is not initializated' );
            }
            require_once ZAR_FRAMEWORK_PATH . '/xajax/xajax_core/xajax.inc.php';
            $zariliaAjax = new xajax( ZAR_CONTROLS_URL . '/base/control.func.php' );
            $zariliaAjax->setFlag('debug',isset($_REQUEST['debug']));
			$zariliaAjax->setFlag('statusMessages', isset($_REQUEST['debug']));
			$zariliaAjax->setFlag('decodeUTF8Input', true);
//            $zariliaAjax->errorHandlerOff();
            $zariliaAjax->registerFunction( 'ZariliaControlHandler' );
            if ( !isset( $zariliaOption['installing'] ) ) {
                $zariliaTpl->addExecBeforeOutput( "global \$zariliaAjax; \$this->headerAdd(\$zariliaAjax->getJavascript('".ZAR_FRAMEWORK_URL."/xajax'));");
            } else {
                $zariliaOption['ajaxScript'] = $zariliaAjax->getJavascript( ZAR_FRAMEWORK_URL . '/xajax' );
            }
        }
        $this->_value = $value;
        $this->_type = $type;
        if ( $name == null ) $name = $type . '_' . rand( 0, 7000 );
        $this->_params['id'] = $name;
        $this->_needfkl = $needfullload?'true':'false';
    }


	function setSysFlag($name, $value=true) {
		global $zariliaOption;
		$zariliaOption[$name] = $value;
	}

	function getSysFlag($name) {
		global $zariliaOption;
		return $zariliaOption[$name];
	}
	
	function isSysFlag($name) {
		global $zariliaOption;
		return isset($zariliaOption[$name]);
	}

	function getName() {
		return $this->_params['id'];
	}

    /**
     * Sets variables that will be automatic declared in generated javascript code
     *
     * @param string $var variable name
     * @param variant $value variable value
     */
    function SetVar( $var, $value )
    {
        $this->_vars[$var] = $value;
    }

    /**
     * Gets variables that will be automatic declared in generated javascript code
     *
     * @param string $var variable name
     * @return variant
     */
    function GetVar( $var )
    {
        return $this->_vars[$var];
    }

    /**
     * Registers function ussed to callback
     *
     * @param string $function function name
     * @param array $params array of functions vars names (need first to be declared with SetVar
     */
    function RegisterFunction( $function, $params = array() )
    {
        $this->_functions[$function] = $params;
    }

    /**
     * Sets function for handling selected event
     *
     * @param string $type event type (onmouseover, onmouseout, onclick...)
     * @param string $function function name (function must be registered first)
     * @return bool returns true if event handler was set, false if no
     */
    function SetEventHandler( $type, $function )
    {
        if ( !isset( $this->_functions[$function] ) ) return false;
        $this->_events[$type] = $function;
        return true;
    }

    /**
     * Generate name of function in generated JavaScript code
     *
     * @param string $type type of event
     * @return string
     */
    function GenerateFunctionName( $type = 'Handler' )
    {
        return $this->_prefix . '_' . $this->_type . '_' . $type;
    }

    /**
     * Adds timer (executes selected function in some interval)
     *
     * @param string $function function name (must be registered first)
     * @param string $interval interval
     * @return bool returns true if timer has been added, false if no
     */
    function AddTimer( $function, $interval )
    {
        static $count = 0;
        if ( !isset( $this->_functions[$function] ) ) return false;
        $count++;
        $this->AddJS( "timer$count = setInterval(\"" . $this->GetRJS( $function ) . "\"," . $interval . ");" );
        return true;
    }

    /**
     * Sets object CSS style
     *
     * @param string $item property name (example.: width)
     * @param variant $value property value (example.: 492px)
     */
    function SetStyle( $item, $value )
    {
        $this->_style[$item] = $value;
    }

    /**
     * Gets object CSS style value
     *
     * @param string $item property name (example.: width)
     * @return variant
     */
    function GetStyle( $item )
    {
        return ( ( isset( $this->_style[$item] ) )?$this->_style[$item]:null );
    }

    /**
     * Sets object param
     *
     * @param string $item param name (example.: class)
     * @param variant $value param value (example.: whatclass)
     */
    function SetParam( $item, $value )
    {
        $this->_params[strtolower( $item )] = $value;
    }

    /**
     * Gets object param
     *
     * @param string $item param name (example.: class)
     * @return variant
     */
    function GetParam( $item )
    {
        return ( ( isset( $this->_params[$item] ) )?$this->_params[$item]:null );
    }

    /**
     * Gets real javascript function call
     *
     * @param string $function function name (must be registered first)
     * @return string
     */
    function GetRJS( $function ) {
        if ( !isset( $this->_functions[$function] ) ) return null;
        $name = $this->_params['id'];
        $type = $this->_type;
        $needfkl = $this->_needfkl;
        $temp = "xajax_ZariliaControlHandler('$name','$type','$function',$needfkl";
        foreach ( $this->_functions[$function] as $value ) {
			if (strstr($value,'(')) {
				$temp .= ',' . $value;
			} else {
				$temp .= ',' . $this->_params['id'] . "_" . $value;
			}
		}
        return $temp . ');';
    }

    /**
     * Gets real javascript function call (only first part)
     *
     * @param string $function function name (must be registered first)
     * @return string
     */
    function GetRJSfp( $function ) {
        $name = $this->_params['id'];
        $type = $this->_type;
        $needfkl = $this->_needfkl;
		$function = $this->_prefix . '_' . $this->_type . '_' .$function;
        return "xajax_ZariliaControlHandler('$name','$type','$function',$needfkl";
    }

    /**
     * Generate control HTML/JS code
     *
     * @return string
     */
    function render()
    {
        // ########### Control Generation ################################################
        $temp = '<div';
        if ( !empty( $this->_style ) ) {
            $style = "";
            foreach ( $this->_style as $key => $value )
            $style .= "$key: $value;";
            $this->_params['style'] = $style;
            unset( $style );
        }
        if ( !empty( $this->_events ) )
            foreach ( $this->_events as $key => $function )
            $this->_params[$key] = $this->GetRJS( $function );
        foreach ( $this->_params as $key => $value )
        $temp .= " $key=\"$value\"";
        $temp .= '>' . $this->_value . '</div>' . "\n";
        // ########### JS Vars Code ################################################
        if ( !empty( $this->_vars ) ) {
            $js = "";
            foreach( $this->_vars as $key => $value ) {
                switch ( gettype( $value ) ) {
                    case "NULL":
                        $js .= " var " . $this->_params['id'] . "_$key = null; \n";
                        break;
                    case "boolean":
                        $js .= " var " . $this->_params['id'] . "_$key = " . ( ( $value )?"true":"false" ) . ";\n";
                        break;
                    case "integer":
                    case "double":
                    case "float":
                        $js .= " var " . $this->_params['id'] . "_$key = $value;\n";
                        break;
                    case "string":
                        $js .= " var " . $this->_params['id'] . "_$key = \"0:" . addslashes( $value ) . "\";\n";
                        break;
                    default:
                        $js .= " var " . $this->_params['id'] . "_$key = '1:" . base64_encode( serialize( $value ) ) . "';\n";
                        break;
                }
            }
        }
        // ########### JS Code Generation #########################################
        if ( !empty( $this->_js ) || ( isset( $js ) ) ) {
            if ( !isset( $js ) ) $js = ' ';
            $temp .= "<script type=\"text/javascript\" language=\"javascript\">\n$js";
            foreach( $this->_js as $value )
            $temp .= "$value\n";
            $temp .= "</script>\n";
        }
        return $temp;
    }

    function AddJS( $code )
    {
        $this->_js[] = $code;
    }
}

?>