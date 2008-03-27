<?php
// $Id: object.php,v 1.4 2007/05/05 11:12:12 catzwolf Exp $
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
 * *#@+
 * Zarilia object datatype
 */
define( 'XOBJ_DTYPE_TXTBOX', 1 );
define( 'XOBJ_DTYPE_TXTAREA', 2 );
define( 'XOBJ_DTYPE_INT', 3 );
define( 'XOBJ_DTYPE_URL', 4 );
define( 'XOBJ_DTYPE_EMAIL', 5 );
define( 'XOBJ_DTYPE_ARRAY', 6 );
define( 'XOBJ_DTYPE_OTHER', 7 );
define( 'XOBJ_DTYPE_SOURCE', 8 );
define( 'XOBJ_DTYPE_STIME', 9 );
define( 'XOBJ_DTYPE_MTIME', 10 );
define( 'XOBJ_DTYPE_LTIME', 11 );
define( 'XOBJ_DTYPE_PASSWORD', 12 );
define( 'XOBJ_DTYPE_IMAGE', 13 );

/**
 * Base class for all objects in the Zarilia kernel (and beyond)
 *
 * @author Kazumi Ono (AKA onokazu)
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 */
class ZariliaObject {
    /**
     * holds all variables(properties) of an object
     *
     * @var array
     * @access protected
     */
    var $vars = array();

    /**
     * variables cleaned for store in DB
     *
     * @var array
     * @access protected
     */
    var $cleanVars = array();

    /**
     * is it a newly created object?
     *
     * @var bool
     * @access private
     */
    var $_isNew = false;

    /**
     * has any of the values been modified?
     *
     * @var bool
     * @access private
     */
    var $_isDirty = false;

    /**
     * errors
     *
     * @var array
     * @access private
     */
    var $_errors = array();

    /**
     * additional filters registered dynamically by a child class object
     *
     * @access private
     */
    var $_filters = array();

    /**
     * constructor
     *
     * normally, this is called from child classes only
     *
     * @access public
     */
    function ZariliaObject() {
    }

    /**
     * *#@+
     * used for new/clone objects
     *
     * @access public
     */
    function setNew() {
        $this->_isNew = 1;
    }

    function unsetNew() {
        $this->_isNew = false;
    }

    function isNew() {
        return $this->_isNew;
    }

    /**
     * *#@+
     * mark modified objects as dirty
     *
     * used for modified objects only
     *
     * @access public
     */
    function setDirty() {
        $this->_isDirty = true;
    }

    function unsetDirty() {
        $this->_isDirty = false;
    }

    function isDirty() {
        return $this->_isDirty;
    }

    /**
     * initialize variables for the object
     *
     * @access public
     * @param string $key
     * @param int $data_type set to one of XOBJ_DTYPE_XXX constants (set to XOBJ_DTYPE_OTHER if no data type ckecking nor text sanitizing is required)
     * @param mixed $
     * @param bool $required require html form input?
     * @param int $maxlength for XOBJ_DTYPE_TXTBOX type only
     * @param string $option does this data have any select options?
     */
    function initVar( $key, $data_type, $value = null, $required = false, $maxlength = null, $options = '' ) {
    	if (isset($this->vars[$key])) return false;
        $this->vars[$key] = array( 'value' => $value, 'required' => $required, 'data_type' => $data_type, 'maxlength' => $maxlength, 'changed' => false, 'options' => $options );
        return true;
    }

    /**
     * assign a value to a variable
     *
     * @access private
     * @param string $key name of the variable to assign
     * @param mixed $value value to assign
     */
    function assignVar( $key, $value ) {
        if ( isset( $this->vars[$key] ) ) {
            $this->vars[$key]['value'] = $value;
        } else {
			if (isset($_REQUEST['debug'])) {
				echo $key.'='.$value."<br>";
			}
		}
    }

    /**
     * assign values to multiple variables in a batch
     *
     * @access private
     * @param array $var_array associative array of values to assign
     */
    function assignVars( $var_arr ) {
        foreach ( $var_arr as $key => $value ) {
            $this->assignVar( $key, $value );
        }
    }

    /**
     * assign a value to a variable
     *
     * @access public
     * @param string $key name of the variable to assign
     * @param mixed $value value to assign
     * @param bool $not_gpc
     */
    function setVar( $key, $value, $not_gpc = false ) {
        if ( !empty( $key ) && isset( $value ) && isset( $this->vars[$key] ) ) {
            $this->vars[$key]['value'] = $value;
            $this->vars[$key]['not_gpc'] = $not_gpc;
            $this->vars[$key]['changed'] = true;
            $this->setDirty();
        }
    }

    function unsetVar( $key ) {
        if ( !empty( $key ) && isset( $value ) && isset( $this->vars[$key] ) ) {
            $this->setVar( $key, '' );
        }
    }

    /**
     * assign values to multiple variables in a batch
     *
     * @access private
     * @param array $var_arr associative array of values to assign
     * @param bool $not_gpc
     */
    function setVars( $var_arr, $not_gpc = false ) {
        foreach ( $var_arr as $key => $value ) {
            $this->setVar( $key, $value, $not_gpc );
        }
    }

    /**
     * Assign values to multiple variables in a batch
     *
     * Meant for a CGI context:
     * - prefixed CGI args are considered save
     * - avoids polluting of namespace with CGI args
     *
     * @access private
     * @param array $var_arr associative array of values to assign
     * @param string $pref prefix (only keys starting with the prefix will be set)
     */
    function setFormVars( $var_arr = null, $pref = 'xo_', $not_gpc = false ) {
        $len = strlen( $pref );
        foreach ( $var_arr as $key => $value ) {
            if ( $pref == substr( $key, 0, $len ) ) {
                $this->setVar( substr( $key, $len ), $value, $not_gpc );
            }
        }
    }

    /**
     * returns all variables for the object
     *
     * @access public
     * @return array associative array of key->value pairs
     */
    function &getVars() {
        return $this->vars;
    }

    /**
     * returns a specific variable for the object in a proper format
     *
     * @access public
     * @param string $key key of the object's variable to be returned
     * @param string $format format to use for the output
     * @return mixed formatted value of the variable
     */
    function &getVar( $key, $format = 'show' ) {
        if ( !isset( $this->vars[$key] ) ) {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, "ERROR: Key '$key' does not exist in this object" );
			$null = null;
			return $null;
        }
        $ret = $this->vars[$key]['value'];
        $format = substr( $format, 0, 1 );
        switch ( $this->vars[$key]['data_type'] ) {
            case XOBJ_DTYPE_STIME:
            case XOBJ_DTYPE_MTIME:
            case XOBJ_DTYPE_LTIME:
                switch ( strtolower( $format ) ) {
                    case 's':
                        $var = ($ret) ? formatTimeStamp( $ret ) : '';
                        return $var;
                        break 1;
                    case 'e':
                        $var = intval( $ret );
                        return $var;
                        break 1;
                    case 'n':
                    default:
                        break 1;
                }
                break;

            case XOBJ_DTYPE_TXTBOX:
                switch ( strtolower( $format ) ) {
                    case 's':
                    case 'e':
                        $var = htmlSpecialChars( $ret, ENT_QUOTES );
                        return $var;
                        break 1;

                    case 'p':
                    case 'f':
                        $var = htmlSpecialChars( stripslashes( $ret ), ENT_QUOTES );
                        return $var;
                        break 1;
                    case 'n':
                    default:
                        break 1;
                }
                break;

            case XOBJ_DTYPE_TXTAREA:
                switch ( strtolower( $format ) ) {
                    case 's':
                        $html = !empty( $this->vars['dohtml']['value'] ) ? 1 : 0;
                        $xcode = ( !isset( $this->vars['doxcode']['value'] ) || $this->vars['doxcode']['value'] == 1 ) ? 1 : 0;
                        $smiley = ( !isset( $this->vars['dosmiley']['value'] ) || $this->vars['doxcode']['value'] == 1 ) ? 1 : 0;
                        $image = ( !isset( $this->vars['doimage']['value'] ) || $this->vars['doxcode']['value'] == 1 ) ? 1 : 0;
                        $br = ( !isset( $this->vars['dobr']['value'] ) || $this->vars['dobr']['value'] == 1 ) ? 1 : 0;
                        $ts = &MyTextSanitizer::getInstance();
                        $rez = $ts->displayTarea( $ret, $html, $smiley, $xcode, $image, $br );
                        unset( $ts );
                        return $rez;
                        break 1;

                    case 'e':
                        $var = htmlSpecialChars( $ret, ENT_QUOTES );
                        return $var;
                        break 1;

                    case 'p':
                        $html = !empty( $this->vars['dohtml']['value'] ) ? 1 : 0;
                        $xcode = ( !isset( $this->vars['doxcode']['value'] ) || $this->vars['doxcode']['value'] == 1 ) ? 1 : 0;
                        $smiley = ( !isset( $this->vars['dosmiley']['value'] ) || $this->vars['doxcode']['value'] == 1 ) ? 1 : 0;
                        $image = ( !isset( $this->vars['doimage']['value'] ) || $this->vars['doxcode']['value'] == 1 ) ? 1 : 0;
                        $br = ( !isset( $this->vars['dobr']['value'] ) || $this->vars['dobr']['value'] == 1 ) ? 1 : 0;
                        $ts = &MyTextSanitizer::getInstance();
                        $ret = $ts->previewTarea( $ret, $html, $smiley, $xcode, $image, $br );
                        return $ret;
                        break 1;

                    case 'f':
                        $var = htmlSpecialChars( stripslashes( $ret ), ENT_QUOTES );
                        return $var;
                        break 1;

                    case 'r':
                        $var = htmlSpecialChars( stripslashes( $ret ), ENT_QUOTES );
                        $var = strip_tags( $var );
                        return $var;
                        break 1;

					case 'c':
						global $zariliaUser;
                        if ( $zariliaUser )   {
                            $ret = str_replace( '{X_UID}', $zariliaUser->getVar( 'uid' ), $ret );
							$ret = str_replace( '{X_USERNAME}', $zariliaUser->getVar( 'uname' ), $ret );
                        } else {
                            $ret = str_replace( '{X_UID}', -time(), $ret );
							$ret = str_replace( '{X_USERNAME}', soundex(md5(time())), $ret );
						}
                        $smiley = ( !isset( $this->vars['dosmiley']['value'] ) || $this->vars['doxcode']['value'] == 1 ) ? 1 : 0;
                        $image = ( !isset( $this->vars['doimage']['value'] ) || $this->vars['doxcode']['value'] == 1 ) ? 1 : 0;
                        $br = ( !isset( $this->vars['dobr']['value'] ) || $this->vars['dobr']['value'] == 1 ) ? 1 : 0;
						$ts = &MyTextSanitizer::getInstance();
						$ret = $ts->displayTarea( $ret, 1, $smiley, 0, $image, $br );
                        return $ret;
					break 1;

                    case 'n':
                    default:
                        break 1;
                }
                break;

            case XOBJ_DTYPE_EMAIL:
                switch ( strtolower( $format ) ) {
                    case 's':
                        $var = htmlSpecialChars( stripslashes( $ret ), ENT_QUOTES );
                        return $var;
                        break 1;
                    case 'e':
                        $var = htmlSpecialChars( $ret, ENT_QUOTES );
                        return $var;
                        break 1;
                    case 'c':
                        $ts = &MyTextSanitizer::getInstance();
                        $var = $ts->makeClickable( $ret );
                        return $var;
                        break 1;
                    case 'n':
                    default:
                        break 1;
                }
                $ret = unserialize( $ret );
                break;

            case XOBJ_DTYPE_ARRAY:
                $ret = unserialize( $ret );
                break;

            case XOBJ_DTYPE_SOURCE:
                switch ( strtolower( $format ) ) {
                    case 's':
                        break 1;
                    case 'e':
                        $var = htmlSpecialChars( $ret, ENT_QUOTES );
                        return $var ;
                        break 1;
                    case 'p':
                        $var = stripslashes( $ret );
                        return $var;
                        break 1;
                    case 'f':
                        $var = htmlSpecialChars( stripslashes( $ret ), ENT_QUOTES );
                        return $var;
                        break 1;
                    case 'n':
                    default:
                        break 1;
                }
                break;

            case XOBJ_DTYPE_IMAGE:
                switch ( strtolower( $format ) ) {
                    case 's':
                        $reti = explode( '|', $ret );
                        $ret = ( count( $reti ) > 0 ) ? $reti[0] : $ret ;
                        break 1;
                    case 'e':
                        $ret = htmlSpecialChars( $ret, ENT_QUOTES );
                        return $var;
                        break 1;

                    case 'p':
                    case 'f':
                        $ret = htmlSpecialChars( stripslashes( $var ), ENT_QUOTES );
                        return $var;
                        break 1;
                    case 'n':
                    default:
                        break 1;
                }
                break;

            default:
                if ( $this->vars[$key]['options'] != '' && $ret != '' ) {
                    switch ( strtolower( $format ) ) {
                        case 's':
                            $selected = explode( '|', $ret );
                            $options = explode( '|', $this->vars[$key]['options'] );
                            $i = 1;
                            $ret = array();
                            foreach ( $options as $op ) {
                                if ( in_array( $i, $selected ) ) {
                                    $ret[] = $op;
                                }
                                $i++;
                            }
                            return implode( ', ', $ret );
                        case 'e':
                            $ret = explode( '|', $ret );
                            break 1;
                        default:
                            break 1;
                    }
                }
                break;
        }
        return $ret;
    }

    /**
     * clean values of all variables of the object for storage.
     * also add slashes whereever needed
     *
     * @return bool true if successful
     * @access public
     */
    function cleanVars() {
        foreach ( $this->vars as $k => $v ) {
            $cleanv = $v['value'];
            if ( !$v['changed'] ) {
            } else {
                $cleanv = is_string( $cleanv ) ? trim( $cleanv ) : $cleanv;
                switch ( $v['data_type'] ) {
                    case XOBJ_DTYPE_TXTBOX:
                        if ( $v['required'] && $cleanv != '0' && $cleanv == '' ) {
                            $this->setErrors( "$k @ ".get_class()." is required." );
                            continue;
                        }
                        if ( isset( $v['maxlength'] ) && strlen( $cleanv ) > intval( $v['maxlength'] ) ) {
                            $this->setErrors( "$k must be shorter than " . intval( $v['maxlength'] ) . " characters." );
                            continue;
                        }
                        $ts = &MyTextSanitizer::getInstance();
                        $cleanv = ( !$v['not_gpc'] ) ? stripslashes( $ts->censorString( $cleanv ) ) : $ts->censorString( $cleanv );
                        break;

                    case XOBJ_DTYPE_TXTAREA:
                        if ( $v['required'] && $cleanv != '0' && $cleanv == '' ) {
                            $this->setErrors( "$k is required." );
                            continue;
                        }
                        $ts = &MyTextSanitizer::getInstance();
						$cleanv = ( !$v['not_gpc'] ) ? stripslashes( $ts->censorString( $cleanv ) ) : $ts->censorString( $cleanv );
                        break;

                    case XOBJ_DTYPE_SOURCE:
                        $cleanv = ( !$v['not_gpc'] ) ? stripslashes( $cleanv ) : $cleanv;
                        break;

                    case XOBJ_DTYPE_INT:
                        $cleanv = intval( $cleanv );
                        break;

                    case XOBJ_DTYPE_EMAIL:
                        if ( $v['required'] && $cleanv == '' ) {
                            $this->setErrors( "$k is required." );
                            continue;
                        }
                        if ( $cleanv != '' && !preg_match( "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+([\.][a-z0-9-]+)+$/i", $cleanv ) ) {
                            $this->setErrors( "Invalid Email" );
                            continue;
                        }
                        if ( !$v['not_gpc'] ) {
                            $cleanv = stripslashes( $cleanv );
                        }
                        break;

                    case XOBJ_DTYPE_URL:
                        if ( $v['required'] && $cleanv == '' ) {
                            $this->setErrors( "$k is required." );
                            continue;
                        }

                        if ( $cleanv != '' && !preg_match( "/^http[s]*:\/\//i", $cleanv ) ) {
                            $cleanv = 'http://' . $cleanv;
                        }
                        if ( !$v['not_gpc'] ) {
                            $cleanv = stripslashes( $cleanv );
                        }
                        break;

                    case XOBJ_DTYPE_ARRAY:
                        $cleanv = serialize( $cleanv );
                        break;

                    case XOBJ_DTYPE_IMAGE:
                        $cleani = explode( '|', $cleanv );
                        $cleanv = ( is_array( $cleani ) ) ? $cleani[0] : $cleanv ;
                        break;

                    case XOBJ_DTYPE_STIME:
                    case XOBJ_DTYPE_MTIME:
                    case XOBJ_DTYPE_LTIME:
                        $cleanv = !is_string( $cleanv ) ? intval( $cleanv ) : strtotime( $cleanv );
                        break;
                    default:
                        break;
                }
            }
            $this->cleanVars[$k] = &$cleanv;
            unset( $cleanv );
        }
        if ( count( $this->_errors ) > 0 ) {
            return false;
        }
        $this->unsetDirty();
        return true;
    }
    /**
     * dynamically register additional filter for the object
     *
     * @param string $filtername name of the filter
     * @access public
     */
    function registerFilter( $filtername ) {
        $this->_filters[] = $filtername;
    }
    /**
     * load all additional filters that have been registered to the object
     *
     * @access private
     */
    function _loadFilters() {
        // include_once ZAR_ROOT_PATH.'/class/filters/filter.php';
        // foreach ($this->_filters as $f) {
        // include_once ZAR_ROOT_PATH.'/class/filters/'.strtolower($f).'php';
        // }
    }
    /**
     * create a clone(copy) of the current object
     *
     * @access public
     * @return object clone
     */
    function &zariliaClone() {
        $class = get_class( $this );
        $clone = new $class();
        foreach ( $this->vars as $k => $v ) {
            $clone->assignVar( $k, $v['value'] );
        }
        // need this to notify the handler class that this is a newly created object
        $clone->setNew();
        return $clone;
    }
    /**
     * add an error
     *
     * @param string $value error to add
     * @access public
     */
    function setErrors( $err_str ) {
        $this->_errors[] = trim( $err_str );
    }
    /**
     * return the errors for this object as an array
     *
     * @return array an array of errors
     * @access public
     */
    function &getErrors() {
        return $this->_errors;
    }
    /**
     * return the errors for this object as html
     *
     * @return string html listing the errors
     * @access public
     */
    function getHtmlErrors() {
        // if ( !empty( $this->_errors ) ) {
        // zarilia_error( $this->_errors, 'Errors Notice', '', 1 );
        // } else {
        // zarilia_error( "Error code 99: The system has reported an unknown error.", 'Errors Found', '', 1 );
        // }
        // $GLOBALS['zariliaLogger']->setSysError( 99, $this->_errors, __FILE__, __LINE__ );
        // $GLOBALS['zariliaLogger']->setSysError( $this->_errors );
    }
    /**
     * Returns an array representation of the object
     *
     * @return array
     */
    function toArray() {
        $ret = array();
        $vars = $this->getVars();
        foreach ( array_keys( $vars ) as $i ) {
            $ret[$i] = $this->getVar( $i );
        }
        return $ret;
    }

    function getTextbox( $id = null, $name = null, $size = 25, $max = 255 ) {
        $i = $this->getVar( $id );
        return "<input type='text' name='" . $name . "[$i]' value='" . $this->getVar( $name ) . "' size='$size' maxlength='$max' />";
    }

    function getYesNobox( $id = null, $name = null, $value = null ) {
        $i = $this->getVar( $id );
        $ret = "<input type='radio' name='" . $name . "[" . $i . "]' value='1'";
        $selected = $this->getVar( $name );
        if ( isset( $selected ) && ( 1 == $selected ) ) {
            $ret .= " checked='checked'";
        }
        $ret .= " />" . _YES . "\n";
        $ret .= "<input type='radio' name='" . $name . "[" . $i . "]' value='0'";
        $selected = $this->getVar( $name );
        if ( isset( $selected ) && ( 0 == $selected ) ) {
            $ret .= " checked='checked'";
        }
        $ret .= " />" . _NO . "\n";
        return $ret;
    }

    /**
     * ZariliaObject::getCheckbox()
     *
     * @param mixed $id
     * @return
     */
    function getCheckbox( $id = null ) {
        return '<input type="checkbox" value="1" name="checkbox[' . $this->getVar( $id ) . ']" />';
    }
}

/**
 * ZARILIA object handler class.
 * This class is an abstract class of handler classes that are responsible for providing
 * data access mechanisms to the data source of its corresponsing data objects
 *
 * @package kernel
 * @abstract
 * @author Kazumi Ono
 * @copyright copyright &copy; 2000 The ZARILIA Project
 */
class ZariliaObjectHandler {
    /**
     * holds referenced to {@link ZariliaDatabase} class object
     *
     * @var object
     * @see ZariliaDatabase
     * @access protected
     */
    var $db;

    /**
     * called from child classes only
     *
     * @param object $db reference to the {@link ZariliaDatabase} object
     * @access protected
     */
    function ZariliaObjectHandler( &$db ) {
        $this->db = &$db;
    }

    /**
     * creates a new object
     *
     * @abstract
     */
    function &create() {
    }

    /**
     * gets a value object
     *
     * @param int $int_id
     * @abstract
     */
    function &get( $int_id ) {
    }

    /**
     * insert/update object
     *
     * @param object $object
     * @abstract
     */
    function insert( &$object ) {
    }

    /**
     * delete object from database
     *
     * @param object $object
     * @abstract
     */
    function delete( &$object ) {
    }
}

/**
 * ZariliaPersistableObjectHandler
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: object.php,v 1.4 2007/05/05 11:12:12 catzwolf Exp $
 * @access public
 */
class ZariliaPersistableObjectHandler extends ZariliaObjectHandler {
    /**
     * *#@+
     * Information about the class, the handler is managing
     *
     * @var string
     */
    var $db_table;
    var $obj_class;
    var $key_name;
    var $ckey_name;
    var $identifier_name;
    var $groupName;
    var $isAdmin;
    var $doPermissions;
    var $_errors = array();
    /**
     * Constructor - called from child classes
     *
     * @param object $db {@link ZariliaDatabase} object
     * @param string $db_tablename Name of database table
     * @param string $obj_class Name of Class, this handler is managing
     * @param string $key_name Name of the property, holding the key
     * @return void
     */
    function ZariliaPersistableObjectHandler( &$db, $db_table = '', $obj_class = '', $key_name = '', $identifier_name = false, $group_name = false ) {
        global $zariliaUserIsAdmin, $zariliaUser;

        static $db = null;
		if ($db == null) {
			$db = &ZariliaDatabaseFactory::getDatabaseConnection();
		}
        $this->ZariliaObjectHandler( $db );
        $this->db_table = $db->prefix( $db_table );
        $this->obj_class = $obj_class;
        // **//
        $this->identifierName = ( $identifier_name != false ) ? $identifier_name : '';
        $this->groupName = ( $group_name != false ) ? $group_name : '';
        $this->user_groups = ( is_object( $zariliaUser ) ) ? $zariliaUser->getGroups() : array( 0 => ZAR_GROUP_ANONYMOUS );

        if ( $this->groupName == true && !in_array( 1 , $this->user_groups ) ) {
            $this->doPermissions = 1;
        } else {
            $this->doPermissions = 0;
        }
        $this->ckeyName = ( $this->doPermissions ) ? 'c.' . $key_name : $key_name;
        $this->keyName = $key_name;
    }

    function &getInstance( &$db ) {
        static $instance;
        if ( !isset( $instance ) ) {
            $_class = $this->obj_class . 'Handler';
            $instance = new $_class( $db );
        }
        return $instance;
    }

    function setPermission( $value = true ) {
        $this->doPermissions = $value;
    }
    /**
     * create a new instance
     *
     * @param bool $isNew Flag the new objects as "new"?
     * @return object
     */
    function &create( $isNew = true ) {
		$obj = new $this->obj_class();
        if ( !is_object( $obj ) ) {
            $GLOBALS['zariliaLogger']->setSysError( 101, '', __FILE__, __LINE__ );
            return false;
        } else {
            if ( $isNew == true ) {
				$obj->setNew();
            }
            return $obj;
        }
    }

    function &get( $id, $as_object = true ) {
		$ret = false;
        if ( is_array( $this->keyName ) ) {
            $criteria = new CriteriaCompo();
            for ( $i = 0; $i < count( $this->keyName ); $i++ ) {
                $criteria->add( new Criteria( $this->keyName[$i], intval( $id[$i] ) ) );
            }
        } else {
            $criteria = new Criteria( $this->ckeyName, $id );
        }
        $criteria->setLimit( 1 );
        $obj_array = $this->getObjects( $criteria, false, $as_object );
        if ( !is_array( $obj_array ) || count( $obj_array ) != 1 ) {
            $rez = false;
            return $rez;
        } else {
            $ret = &$obj_array[0];
        }
        return $ret;
    }

    /**
     * retrieve objects from the database
     *
     * @param object $criteria {@link CriteriaElement} conditions to be met
     * @param bool $id_as_key use the ID as key for the array?
     * @param bool $as_object return an array of objects?
     * @return array
     */
    function &getObjects( $criteria = null, $id_as_key = false, $as_object = true, $return_error = false ) {
        $ret = array();
        $limit = $start = 0;
        if ( $this->doPermissions ) {
            $sql = 'SELECT SQL_CACHE DISTINCT c.* FROM ' . $this->db_table . ' c	LEFT JOIN ' . $this->db->prefix( 'group_permission' ) . " l				ON l.gperm_itemid = $this->ckeyName				WHERE ( l.gperm_name = '$this->groupName'				AND l.gperm_groupid IN ( " . implode( ',', $this->user_groups ) . " )				)";
        } else {
            $sql = 'SELECT  * FROM ' . $this->db_table;
        }
        if ( isset( $criteria ) && is_subclass_of( $criteria, 'criteriaelement' ) ) {
            if ( $this->doPermissions ) {
                $sql .= ' AND ' . $criteria->render();
            } else {
                $sql .= ' ' . $criteria->renderWhere();
            }
            if ( $criteria->getSort() != '' ) {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
		if ($limit == 0) {
			$result = $this->db->Execute($sql);
		} else {
			$result = $this->db->SelectLimit($sql, $limit, $start);
		}
        if ( !$result ) {
            $ret = false;
            if ( $return_error != false ) {
   				$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
            }
            return $ret;
        } else {
            $ret = $this->convertResultSet( $result, $id_as_key, $as_object );
			return $ret;
        }
    }

    /**
     * Convert a database resultset to a returnable array
     *
     * @param object $result database resultset
     * @param bool $id_as_key - should NOT be used with joint keys
     * @param bool $as_object
     * @return array
     */
    function convertResultSet( &$result, $id_as_key = false, $as_object = true ) {
//		$ADODB_FETCH_MODE = ;
//		$this->db->SetFetchMode(ADODB_FETCH_ASSOC);
        $ret = array();
        while ( $myrow = $result->FetchRow() ) {
            $obj = &$this->create( false );
            $obj->assignVars( $myrow );
            if ( !$id_as_key ) {
                if ( $as_object ) {
                    $ret[] = &$obj;
                } else {
                    $row = array();
                    $vars = $obj->getVars();
                    foreach ( array_keys( $vars ) as $i ) {
                        $row[$i] = $obj->getVar( $i );
                    }
                    $ret[] = $row;
                }
            } else {
                if ( $as_object ) {
                    $ret[$myrow[$this->keyName]] = &$obj;
                } else {
                    $row = array();
                    $vars = $obj->getVars();
                    foreach ( array_keys( $vars ) as $i ) {
                        $row[$i] = $obj->getVar( $i );
                    }
                    $ret[$myrow[$this->keyName]] = $row;
                }
            }
            unset( $obj );
        }

        return $ret;
    }

    /**
     * ZariliaPersistableObjectHandler::getList()
     *
     * @param mixed $criteria
     * @param integer $limit
     * @param integer $start
     * @param string $querie
     * @param mixed $show
     * @return
     */
    function getList( $criteria = null, $querie = '', $show = null, $doCriteria = true ) {
        $ret = array();
        $limit = $start = 0;
        if ( $this->doPermissions ) {
            if ( $querie ) {
                $query = $querie;
            } else {
                $query = $this->ckeyName;
                if ( !empty( $this->identifierName ) ) {
                    $query .= ', c.' . $this->identifierName;
                }
            }
            $sql = 'SELECT DISTINCT c.* FROM ' . $this->db_table . ' c LEFT JOIN ' . $this->db->prefix( 'group_permission' ) . " l ON l.gperm_itemid = $this->ckeyName WHERE ( l.gperm_name = '$this->groupName' AND l.gperm_groupid IN ( " . implode( ',', $this->user_groups ) . " ))";
        } else {
            if ( $querie ) {
                $query = $querie;
            } else {
                $query = $this->ckeyName;
                if ( !empty( $this->identifierName ) ) {
                    $query .= ', ' . $this->identifierName;
                }
            }
            $sql = 'SELECT ' . $query . ' FROM ' . $this->db_table;
        }

        if ( $doCriteria != false ) {
            $criteria = new CriteriaCompo();
			if ( $criteria == null ) {
                $criteria = new CriteriaCompo();
            }
            if ( $criteria->getSort() == '' ) {
                $criteria->setSort( $this->identifierName );
            }

            if ( isset( $criteria ) && is_subclass_of( $criteria, 'criteriaelement' ) ) {
                if ( $this->doPermissions ) {
					$something = $criteria->render();
					if ($something) $sql .= ' AND ' . $something;
                } else {
                    $sql .= ' ' . $criteria->renderWhere();
                }
                if ( $criteria->getSort() != '' ) {
                    $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
                }
                $limit = $criteria->getLimit();
                $start = $criteria->getStart();
            }
        }

        if ( !$result = $this->db->SelectLimit( $sql, $limit, $start ) ) {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
            return false;
        }
        if ( $show == true ) {
            $ret[0] = "Display All";
        }
        /**
         */
		while ( $myrow = $result->FetchRow() ) {
            $ret[$myrow[$this->keyName]] = empty( $this->identifierName ) ? '' : htmlSpecialChars( $myrow[$this->identifierName], ENT_QUOTES );
        }

        return $ret;
    }

    /**
     * ZariliaPersistableObjectHandler::getCount()
     *
     * @param unknown $criteria
     * @return
     */
    function getCount( $criteria = null ) {
        if ( $this->doPermissions ) {
            $sql = 'SELECT DISTINCT * FROM ' . $this->db_table . ' c LEFT JOIN ' . $this->db->prefix( 'group_permission' ) . " l ON l.gperm_itemid = $this->ckeyName WHERE ( l.gperm_name = '$this->groupName' AND l.gperm_groupid IN ( " . implode( ',', $this->user_groups ) . " ) )";
        } else {
            $sql = 'SELECT * FROM ' . $this->db_table;
        }
        if ( isset( $criteria ) && is_subclass_of( $criteria, 'criteriaelement' ) ) {
            if ( $this->doPermissions ) {
                $sql .= ' AND ' . $criteria->render();
            } else {
                $sql .= ' ' . $criteria->renderWhere();
            }
        }
        if ( !$result = $this->db->Execute( $sql ) ) {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error '.$this->db->ErrorNo().': '. $this->db->ErrorMsg(), __FILE__, __LINE__ );
            return false;
        }
        $count = $result->RecordCount();

        return intval( $count );
    }

    /**
     * ZariliaPersistableObjectHandler::insert()
     *
     * @param mixed $obj
     * @param mixed $checkObject
     * @param mixed $andclause
     * @return
     */
    function insert( &$obj, $checkObject = true, $andclause = null ) {
		global $zariliaConfig;
        if ( $checkObject == true ) {
            if ( !is_object( $obj ) || !is_a( $obj, $this->obj_class ) ) {
				require_once ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/error.php';
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, sprintf( _ER_PAGE_NOT_OBJECT, $this->obj_class ), __FILE__, __LINE__ );
                return false;
            }

            if ( !$obj->isDirty() ) {
				require_once ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/error.php';
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _ER_PAGE_NOT_DIRTY, __FILE__, __LINE__);
                return false;
            }
        }
        if ( !$obj->cleanVars() ) {
            $GLOBALS['zariliaLogger']->setSysError( "Input Error", $obj->getErrors(), __FILE__, __LINE__ );
            return false;
        }
	    if ( $obj->isNew() ) {
            foreach ( $obj->cleanVars as $k => $v ) {
				$cleanvars[$k] = ( $obj->vars[$k]['data_type'] == XOBJ_DTYPE_INT ) ? intval( $v ) : $this->db->qstr( $v );
            }
	        if (!is_array( $this->keyName ) ) {
				$cleanvars[$this->keyName]  = $this->db->GenID($this->db_table.'_seq');
		        $obj->assignVar( $this->keyName, $cleanvars[$this->keyName]  );
	        }
		//	die();
           // unset( $cleanvars[$this->keyName] );
            $sql = "INSERT INTO " . $this->db_table . " (`" . implode( '`, `', array_keys( $cleanvars ) ) . "`) VALUES (" . implode( ',', array_values( $cleanvars ) ) . ")";
        } else {
            unset( $obj->cleanVars[$this->keyName] );
            $sql = "UPDATE " . $this->db_table . " SET";
            foreach ( $obj->cleanVars as $k => $v ) {
                if ( isset( $notfirst ) ) {
                    $sql .= ", ";
                }
                if ( $obj->vars[$k]['data_type'] == XOBJ_DTYPE_INT ) {
                    $sql .= " " . $k . " = " . intval( $v );
                } else {
                    $sql .= " " . $k . " = " . $this->db->qstr( $v );
                }
                $notfirst = true;
            }
            $sql .= " WHERE " . $this->keyName . " = '" . $obj->getVar( $this->keyName ) . "'";
            if ( $andclause ) {
                $sql .= $andclause;
            }
        }
		if (!$this->db->Execute( $sql ) ) {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
            return false;
        }
        return true;
    }

    /**
     * Change a value for objects with a certain criteria
     *
     * @param string $fieldname Name of the field
     * @param string $fieldvalue Value to write
     * @param object $criteria {@link CriteriaElement}
     * @return bool
     */
    function updateAll( $fieldname, $fieldvalue = 0, $criteria = null ) {
        if ( is_array( $fieldname ) && $fieldvalue == 0 ) {
            $set_clause = "";
            foreach ( $fieldname as $key => $value ) {
                if ( isset( $notfirst ) ) {
                    $set_clause .= ", ";
                }
                $set_clause .= is_numeric( $key ) ? " " . $key . " = " . $value : " " . $key . " = " . $this->db->qstr( $value );
                $notfirst = true;
            }
        } else {
            $set_clause = is_numeric( $fieldvalue ) ? $fieldname . ' = ' . $fieldvalue : $fieldname . ' = ' . $this->db->qstr( $fieldvalue );
        }
        $sql = 'UPDATE ' . $this->db_table . ' SET ' . $set_clause;
        if ( isset( $criteria ) && is_subclass_of( $criteria, 'criteriaelement' ) ) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$this->db->Execute($sql)) {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
            return false;
        }

        return true;
    }

    /**
     * delete an object from the database
     *
     * @param object $obj reference to the object to delete
     * @param bool $force
     * @return bool FALSE if failed.
     */
    function delete( &$obj, $return_false = false ) {
        if ( !is_object( $obj ) || !is_a( $obj, $this->obj_class ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, sprintf( _ER_PAGE_NOT_OBJECT, $this->obj_class ), __FILE__, __LINE__ );
            return false;
        }
        if ( is_array( $this->keyName ) ) {
            $clause = array();
            for ( $i = 0; $i < count( $this->keyName ); $i++ ) {
                $clause[] = $this->keyName[$i] . " = " . $obj->getVar( $this->keyName[$i] );
            }
            $whereclause = implode( " AND ", $clause );
        } else {
            $whereclause = $this->keyName . " = " . $obj->getVar( $this->keyName );
        }
        $sql = "DELETE FROM " . $this->db_table . " WHERE " . $whereclause;
        if (!$this->db->Execute($sql)) {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
            return false;
        }
        return true;
    }
    /**
     * ZariliaPersistableObjectHandler::deleteAll()
     *
     * @param mixed $criteria
     * @param mixed $returncount
     * @return
     */
    function deleteAll( $criteria = null, $returncount = false ) {
        if ( isset( $criteria ) && is_subclass_of( $criteria, 'criteriaelement' ) ) {
            $sql = 'DELETE FROM ' . $this->db_table;
            if ( $criteria ) {
                $sql .= ' ' . $criteria->renderWhere();
            }
            if ( !$result = $this->db->Execute( $sql ) ) {
   				$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
                return false;
            } else {
                $rows = $this->db->Affected_Rows();
                return ( $returncount == true ) ? $rows : true;
            }
        }
    }

    /**
     * ZariliaPersistableObjectHandler::doDatabase()
     *
     * @param string $act
     * @return
     */
    function doDatabase( $act = '' ) {
        $act = strip_tags( $act );
        $do_array = array( 'optimize', 'repair', 'truncate', 'analyze' );
        if ( !in_array( $act, $do_array ) ) {
            return false;
        }

        $protected_tables = array( 'users', 'block_addon_link', 'config', 'configcategory', 'configoption', 'groups', 'groups_users_link', 'group_permission', 'addons', 'newblocks', 'profile', 'profilecategory', 'profileoption', 'session', 'tplfile', 'tplset', 'tplsource' );
        if ( in_array( $this->db_table, $protected_tables ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'WARNING: This is a protected table and contents cannot be touched' );
            return false;
        }
        $sql = "$act TABLE " . $this->db_table;
        if ( !$this->db->Execute( $sql ) ) {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
            return false;
        }
        return true;
    }

    /**
     * ZariliaPersistableObjectHandler::setSubmit()
     *
     * @param string $value
     * @param string $name
     * @param array $_array
     * @return
     */
    function setSubmit( $value = "", $name = "fct", $_array = array() ) {
        if ( empty( $_array ) ) {
            $_array = array( 'updateall' => 'Update Selected', 'deleteall' => 'Delete Selected', 'cloneall' => 'Clone Selected' );
        }
        $ret = '<select size="1" name="op" id="op">';
        foreach( $_array as $k => $v ) {
            $ret .= '<option value="' . $k . '">' . htmlspecialchars( $v ) . '</option>';
        }
        $ret .= '</select><input type="hidden" name="' . $name . '" value="' . $value . '" /><input type="submit" class="formbutton" value="' . _SUBMIT . '" name="Submit" />';
        return $ret;
    }
}

?>