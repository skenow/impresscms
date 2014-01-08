<?php

/**
 * Manage Objects
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @category	ICMS
 * @package		Core
 * @version		SVN: $Id: Object.php 12112 2012-11-09 02:15:50Z skenow $
 */
/* * #@+
 * Object datatype
 *
 * */
define('self::DTYPE_DEP_TXTBOX', icms_properties_Handler::DTYPE_DEP_TXTBOX);
define('self::DTYPE_STRING', icms_properties_Handler::DTYPE_STRING);
define('self::DTYPE_INTEGER', icms_properties_Handler::DTYPE_INTEGER);
define('self::DTYPE_DEP_URL', icms_properties_Handler::DTYPE_DEP_URL);
define('self::DTYPE_DEP_EMAIL', icms_properties_Handler::DTYPE_DEP_EMAIL);
define('self::DTYPE_ARRAY', icms_properties_Handler::DTYPE_ARRAY);
define('self::DTYPE_DEP_OTHER', icms_properties_Handler::DTYPE_OTHER);
define('self::DTYPE_DEP_SOURCE', icms_properties_Handler::DTYPE_DEP_SOURCE);
define('self::DTYPE_DEP_STIME', icms_properties_Handler::DTYPE_DEP_STIME);
define('self::DTYPE_DEP_MTIME', icms_properties_Handler::DTYPE_DEP_MTIME);
define('self::DTYPE_DATETIME', icms_properties_Handler::DTYPE_DATETIME);

define('self::DTYPE_LIST', icms_properties_Handler::DTYPE_LIST);
define('self::DTYPE_DEP_CURRENCY', icms_properties_Handler::DTYPE_DEP_CURRENCY);
define('self::DTYPE_FLOAT', icms_properties_Handler::DTYPE_FLOAT);
define('self::DTYPE_DEP_TIME_ONLY', icms_properties_Handler::DTYPE_DEP_TIME_ONLY);
define('self::DTYPE_DEP_URLLINK', icms_properties_Handler::DTYPE_DEP_URLLINK);
define('self::DTYPE_DEP_FILE', icms_properties_Handler::DTYPE_DEP_FILE);
define('self::DTYPE_DEP_IMAGE', icms_properties_Handler::DTYPE_DEP_IMAGE);
define('self::DTYPE_FORM_SECTION', icms_properties_Handler::DTYPE_DEP_FORM_SECTION);
define('self::DTYPE_FORM_SECTION_CLOSE', icms_properties_Handler::DTYPE_DEP_FORM_SECTION_CLOSE);

/* * #@- */

/**
 * Base class for all objects in the kernel (and beyond)
 *
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @license	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @category	ICMS
 * @package	Core
 * @since		XOOPS
 * @author		Kazumi Ono (AKA onokazu)
 * */
class icms_core_Object extends icms_properties_Handler {

    /**
     * is it a newly created object?
     *
     * @var bool
     * @access private
     */
    private $_isNew = false;

    /**
     * errors
     *
     * @var array
     * @access private
     */
    private $_errors = array();

    /**
     * additional filters registered dynamically by a child class object
     *
     * @access private
     */
    private $_filters = array();

    /**
     * constructor
     *
     * normally, this is called from child classes only
     * @access public
     */
    public function __construct() {
        
    }

    /*     * #@+
     * used for new/clone objects
     *
     * @access public
     */

    public function setNew() {
        $this->_isNew = true;
    }

    public function unsetNew() {
        $this->_isNew = false;
    }

    public function isNew() {
        return $this->_isNew;
    }

    /*     * #@- */

    /**
     * initialize variables for the object
     *
     * @access public
     * @param string $key
     * @param int $data_type  set to one of XOBJ_DTYPE_XXX constants (set to self::DTYPE_DEP_OTHER if no data type ckecking nor text sanitizing is required)
     * @param mixed
     * @param bool $required  require html form input?
     * @param int $maxlength  for self::DTYPE_DEP_TXTBOX type only
     * @param string $option  does this data have any select options?
     */
    public function initVar($key, $data_type, $value = null, $required = false, $maxlength = null, $options = '') {
        parent::initVar($key, $data_type, $value, $required, array(
            parent::VARCFG_MAX_LENGTH => $maxlength,
            'options' => $options
                )
        );
    }

    /**
     * Assign values to multiple variables in a batch
     *
     * Meant for a CGI context:
     * - prefixed CGI args are considered safe
     * - avoids polluting of namespace with CGI args
     *
     * @access public
     * @param array $var_arr associative array of values to assign
     * @param string $pref prefix (only keys starting with the prefix will be set)
     */
    public function setFormVars($var_arr = null, $pref = 'xo_', $not_gpc = false) {
        $len = strlen($pref);
        foreach ($var_arr as $key => $value) {
            if ($pref == substr($key, 0, $len)) {
                $this->setVar(substr($key, $len), $value, $not_gpc);
            }
        }
    }

    /**
     * dynamically register additional filter for the object
     *
     * @param string $filtername name of the filter
     * @access public
     */
    public function registerFilter($filtername) {
        $this->_filters[] = $filtername;
    }

    /**
     * load all additional filters that have been registered to the object
     *
     * @access private
     */
    private function _loadFilters() {
        
    }

    public function __call($name, $arguments) {
        switch ($name) {
            case 'xoopsClone':
                trigger_error('Deprecached method xoopsClone', E_USER_DEPRECATED);
                return clone $this;
            case 'setDirty':
                trigger_error('Deprecached method xoopsClone', E_USER_DEPRECATED);
                $this->setVarInfo(null, parent::VARCFG_CHANGED, true);                
                return null;
            case 'unsetDirty':
                trigger_error('Deprecached method unsetDirty', E_USER_DEPRECATED);
                $this->setVarInfo(null, parent::VARCFG_CHANGED, false);
                return null;
            break;
            case 'isDirty':
                trigger_error('Deprecached method isDirty', E_USER_DEPRECATED);
                return count($this->getChangedVars()) > 0;
        }
        parent::__call($name, $arguments);
    }    

    /**
     * Create cloned copy of current object
     */
    public function __clone() {
        $this->setNew();
    }

    /**
     * add an error
     *
     * @param string $value error to add
     * @access public
     */
    public function setErrors($err_str, $prefix = false) {
        if (is_array($err_str)) {
            foreach ($err_str as $str) {
                $this->setErrors($str, $prefix);
            }
        } else {
            if ($prefix) {
                $err_str = "[" . $prefix . "] " . $err_str;
            }
            $this->_errors[] = trim($err_str);
        }
    }

    /**
     * return the errors for this object as an array
     *
     * @return array an array of errors
     * @access public
     */
    public function getErrors() {
        return $this->_errors;
    }

    /**
     * return the errors for this object as html
     *
     * @return string html listing the errors
     * @access public
     */
    public function getHtmlErrors() {
        $ret = '<h4>' . _ERROR . '</h4>';
        if (empty($this->_errors))
            $ret .= _NONE . '<br />';
        else
            $ret .= implode('<br />', $this->_errors);
        return $ret;
    }

    /**
     *
     */
    public function hasError() {
        return count($this->_errors) > 0;
    }

}
