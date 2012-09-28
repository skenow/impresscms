<?php
/**
 * Contains methods for dealing with object properties
 *
 * @copyright           The ImpressCMS Project http://www.impresscms.org/
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @category            ICMS
 * @package		Ipf
 * @subpackage          Properties
 * @since		1.0
 * @author		i.know@mekdrop.name
 */

/**
 * icms_ipf_Properties base class
 *
 * Base class to dealing with object properties
 *
 * @category            ICMS
 * @package		Ipf
 * @subpackage          Properties
 * @author              i.know@mekdrop.name
 * @todo		Properly identify and declare the visibility of vars and functions
 */
abstract class icms_ipf_Properties
    implements Serializable {
    
        const DTYPE_STRING = 2; // XOBJ_DTYPE_TXTAREA
        const DTYPE_INTEGER = 3; // XOBJ_DTYPE_INT
        const DTYPE_FLOAT = 201; // XOBJ_DTYPE_FLOAT
        const DTYPE_BOOLEAN = 105;
        const DTYPE_FILE = 104; // 
        const DTYPE_DATETIME = 11; // XOBJ_DTYPE_LTIME
        const DTYPE_ARRAY = 6; // XOBJ_DTYPE_ARRAY
        const DTYPE_DATA_SOURCE = 102;
        const DTYPE_CRITERIA = 103; 
        const DTYPE_LIST = 101; // XOBJ_DTYPE_SIMPLE_ARRAY
        const DTYPE_OTHER = 7; // XOBJ_DTYPE_OTHER
        
        const DTYPE_DEP_FILE = 204; //XOBJ_DTYPE_FILE
        const DTYPE_DEP_TXTBOX = 1; // XOBJ_DTYPE_TXTBOX
        const DTYPE_DEP_URL = 4; // XOBJ_DTYPE_URL
        const DTYPE_DEP_EMAIL = 5; // XOBJ_DTYPE_EMAIL
        const DTYPE_DEP_SOURCE = 8; // XOBJ_DTYPE_SOURCE
        const DTYPE_DEP_STIME = 9; // XOBJ_DTYPE_STIME
        const DTYPE_DEP_MTIME = 10; // XOBJ_DTYPE_MTIME
        const DTYPE_DEP_CURRENCY = 200; // XOBJ_DTYPE_MTIME
        const DTYPE_DEP_TIME_ONLY = 202; // XOBJ_DTYPE_TIME_ONLY
        const DTYPE_DEP_URLLINK = 203; // XOBJ_DTYPE_URLLINK
        const DTYPE_DEP_IMAGE = 205; // XOBJ_DTYPE_IMAGE
        const DTYPE_DEP_FORM_SECTION = 210; // XOBJ_DTYPE_FORM_SECTION
        const DTYPE_DEP_FORM_SECTION_CLOSE = 211; // XOBJ_DTYPE_FORM_SECTION_CLOSE
        
        const VARCFG_ALLOWED_MIMETYPES = 'allowedMimeTypes';
        const VARCFG_MAX_FILESIZE = 'maxFileSize';
        const VARCFG_MAX_WIDTH = 'maxWidth';
        const VARCFG_MAX_HEIGHT = 'maxHeight';
        const VARCFG_PREFIX = 'prefix';
        const VARCFG_PATH = 'path';
        const VARCFG_FILENAME_FUNCTION = 'filenameGenerator';
        const VARCFG_POSSIBLE_OPTIONS = 'possibleOptions';
        const VARCFG_LOCKED = 'locked';
        const VARCFG_HIDE = 'hide';
        const VARCFG_RENDER_TYPE = 'renderType';
        const VARCFG_SEPARATOR = 'separator';
        const VARCFG_MAX_LENGTH = 'maxLength';
        const VARCFG_VALIDATE_RULE = 'validateRule';
        const VARCFG_SOURCE_FORMATING = 'sourceFormating';
        const VARCFG_FORMAT = 'format';
        const VARCFG_AF_DISABLED = 'autoFormatingDisabled';
        const VARCFG_NOT_GPC = 'not_gpc';
        const VARCFG_CHANGED = 'changed';
        const VARCFG_VALUE = 'value';
        const VARCFG_TYPE = 'data_type';
        const VARCFG_REQUIRED = 'required';
        const VARCFG_CONTROL = 'control';
        const VARCFG_DATA_HANDLER = 'data_handler';
        const VARCFG_DEP_DATA_TYPE = 'depDataType';
        const VARCFG_FORM_CAPTION = 'form_caption';
        const VARCFG_FORM_DESC = 'form_dsc';
        const VARCFG_DEFAULT_VALUE = 'default_value';
        
        protected $_vars   = array();
        
        protected function load($values) {
            foreach ($values as $key => $value) 
                if (isset($this->_vars[$key]))
                    $this->_vars[$key][self::VARCFG_VALUE] = $this->cleanVar($key, $this->_vars[$key][self::VARCFG_TYPE], $value);
        }
        
        protected function detectDataType($value) {
            if (is_int($value))
                return self::DTYPE_INTEGER;
            elseif (is_float($value))
                return self::DTYPE_FLOAT;
            elseif (is_bool($value))
                return self::DTYPE_BOOLEAN;
            elseif (is_array($value))
                return self::DTYPE_ARRAY;
            return self::DTYPE_STRING;
        }
        
        public function __get($name) {
            if ($name == 'vars') {
                if (isset($this->_vars[$name])) {
                    return $this->_vars[$name][self::VARCFG_VALUE];
                } else {
                    $callers=debug_backtrace();
                    trigger_error(sprintf('Deprecached "vars" property use in %s (line %d)', $callers[1]['class'], $callers[1]['line']), E_USER_DEPRECATED);
                    return $this->_vars;
                }
            } elseif ($name == 'cleanVars') {
                trigger_error(sprintf('Deprecached "cleanVars" property use in %s (line %d)', $callers[1]['class'], $callers[1]['line']), E_USER_DEPRECATED);          
                return $this->toArray();
            } else
                if (!isset($this->_vars[$name]))
                    trigger_error(sprintf('%s undefined for %s', $name, get_class($this)), E_USER_WARNING);
                else
                    return $this->_vars[$name][self::VARCFG_VALUE];
        }
        
        public function __set($name, $value) {
            if (!isset($this->_vars[$name])) {
                return trigger_error('Variable '.get_class($this).'::$'.$name.' not found', E_USER_WARNING);
            }
            if ($this->_vars[$name][self::VARCFG_LOCKED])
                return trigger_error('Variable '.get_class($this).'::$'.$name.' locked', E_USER_WARNING);
            if (isset($this->_vars[$name][self::VARCFG_POSSIBLE_OPTIONS]) && !in_array($value, $this->_vars[$name][self::VARCFG_POSSIBLE_OPTIONS]))
                return trigger_error('Option not in array for variable '.get_class($this).'::$'.$name.' not found', E_USER_WARNING);            
            $clean = $this->cleanVar($name, $this->_vars[$name][self::VARCFG_TYPE], $value);
            if ($clean == $this->_vars[$name][self::VARCFG_VALUE])
                return;
            $this->_vars[$name][self::VARCFG_VALUE] = $clean;
            $this->_vars[$name][self::VARCFG_CHANGED] = true;
        }
        
        public function __isset($name) {
            return isset($this->_vars[$name]);
        }
        
       /**
        * returns a specific variable for the object in a proper format
        *
        * @access public
        * @param string $key key of the object's variable to be returned
        * @param string $format format to use for the output
        * @return mixed formatted value of the variable
        */
        public function getVar($name, $format = 's') {  
            switch (strtolower($format)) {
                case 's':
				case 'show':
                case 'p':
				case 'preview':
                    $ret = $this->getVarForDisplay($name);
                break;
				case 'e':
				case 'edit':
                    $ret = $this->getVarForEdit($name);
                break;
				case 'f':
				case 'formpreview':
                    $ret = $this->getVarForForm($name);
                break;
				case 'n':
				case 'none':
				default:
                    $ret = $this->__get($name);
            }         
            return $ret;
        }
        
        public function getVarForDisplay($name) {
            switch ($this->_vars[$name][self::VARCFG_TYPE]) {
                case self::DTYPE_STRING:
                    if (!isset($this->_vars[$name][self::VARCFG_AF_DISABLED]) || !$this->_vars[$name][self::VARCFG_AF_DISABLED]) {
                        $ts =& icms_core_Textsanitizer::getInstance();
						$html = !empty($this->_vars['dohtml']) ? 1 : 0;
						$xcode = (!isset($this->_vars['doxcode']) || $this->_vars['doxcode'][self::VARCFG_VALUE] == 1) ? 1 : 0;
						$smiley = (!isset($this->_vars['dosmiley']) || $this->_vars['dosmiley'][self::VARCFG_VALUE] == 1) ? 1 : 0;
						$image = (!isset($this->_vars['doimage']) || $this->_vars['doimage'][self::VARCFG_VALUE] == 1) ? 1 : 0;
						$br = (!isset($this->_vars['dobr']) || $this->_vars['dobr'][self::VARCFG_VALUE] == 1) ? 1 : 0;
						if ($html) {
							return $ts->displayTarea($this->_vars[$name][self::VARCFG_VALUE], $html, $smiley, $xcode, $image, $br);
						} else {
							return icms_core_DataFilter::checkVar($this->_vars[$name][self::VARCFG_VALUE], 'text', 'output');
						}
                    } else {
                        $ret = icms_core_DataFilter::htmlSpecialchars($this->_vars[$name][self::VARCFG_VALUE]);
                        if (method_exists($this, 'formatForML')) {
							return $this->formatForML($ret);
						} else {
							return $ret;
						}
                        return $ret;
                    }
                case self::DTYPE_INTEGER: // XOBJ_DTYPE_INT
                    return $this->_vars[$name][self::VARCFG_VALUE];
                case self::DTYPE_FLOAT: // XOBJ_DTYPE_FLOAT
                    return sprintf(isset($this->_vars[$name][self::VARCFG_FORMAT])?$this->_vars[$name][self::VARCFG_FORMAT]:'%d', $this->_vars[$name][self::VARCFG_VALUE]);
                case self::DTYPE_BOOLEAN:
                    return $this->_vars[$name][self::VARCFG_VALUE]?_YES:_NO;
                case self::DTYPE_FILE: // XOBJ_DTYPE_FILE                    
                    return icms_core_DataFilter::htmlSpecialchars($this->_vars[$name][self::VARCFG_VALUE]);
                case self::DTYPE_DATETIME: // XOBJ_DTYPE_LTIME
                    return date(isset($this->_vars[$name][self::VARCFG_FORMAT])?$this->_vars[$name][self::VARCFG_FORMAT]:'r', $this->_vars[$name][self::VARCFG_VALUE]);
                case self::DTYPE_ARRAY: // XOBJ_DTYPE_ARRAY
                    return $this->_vars[$name][self::VARCFG_VALUE];
                case self::DTYPE_DATA_SOURCE;
                    return $this->_vars[$name][self::VARCFG_VALUE];
                case self::DTYPE_CRITERIA;
                    if (!$this->_vars[$name])
                        return '';
                    else
                        return $this->_vars[$name][self::VARCFG_VALUE]->render();
                case self::DTYPE_LIST; // XOBJ_DTYPE_SIMPLE_ARRAY
                    return nl2br(implode("\n", $this->_vars[$name][self::VARCFG_VALUE]));
                case self::DTYPE_OTHER;
                    return $this->_vars[$name][self::VARCFG_VALUE];
                default:
                    return null;
            }
        }
        
        /**
        if ($this->vars[$key]['options'] != '' && $ret != '') {
					switch (strtolower($format)) {
						case 's':
						case 'show':
							$selected = explode('|', $ret);
							$options = explode('|', $this->vars[$key]['options']);
							$i = 1;
							$ret = array();
							foreach ($options as $op) {
								if (in_array($i, $selected)) {
									$ret[] = $op;
								}
								$i++;
							}
							return implode(', ', $ret);
						case 'e':
						case 'edit':
							$ret = explode('|', $ret);
							break 1;

						default:
							break 1;
					}

				}
         */
        
        public function getVarForEdit($name) {
            switch ($this->_vars[$name][self::VARCFG_TYPE]) {
                case self::DTYPE_STRING:                    
                case self::DTYPE_INTEGER: // XOBJ_DTYPE_INT
                case self::DTYPE_FLOAT: // XOBJ_DTYPE_FLOAT
                case self::DTYPE_BOOLEAN:
                case self::DTYPE_FILE: // XOBJ_DTYPE_FILE
                case self::DTYPE_DATETIME: // XOBJ_DTYPE_LTIME
                case self::DTYPE_ARRAY: // XOBJ_DTYPE_ARRAY
                case self::DTYPE_DATA_SOURCE;
                case self::DTYPE_CRITERIA;
                case self::DTYPE_LIST; // XOBJ_DTYPE_SIMPLE_ARRAY
                    return icms_core_DataFilter::htmlSpecialchars((string)$this->_vars[$name][self::VARCFG_VALUE], ENT_QUOTES);
                case self::DTYPE_OTHER; // XOBJ_DTYPE_OTHER
                default:
                    return null;
            }
        }        
        
        public function getVarForForm($name) {
            switch ($this->_vars[$name][self::VARCFG_TYPE]) {
                case self::DTYPE_STRING:                    
                case self::DTYPE_INTEGER: // XOBJ_DTYPE_INT
                case self::DTYPE_FLOAT: // XOBJ_DTYPE_FLOAT
                case self::DTYPE_BOOLEAN:
                case self::DTYPE_FILE: // XOBJ_DTYPE_FILE
                case self::DTYPE_DATETIME: // XOBJ_DTYPE_LTIME
                case self::DTYPE_ARRAY: // XOBJ_DTYPE_ARRAY
                case self::DTYPE_DATA_SOURCE;
                case self::DTYPE_CRITERIA;
                case self::DTYPE_LIST; // XOBJ_DTYPE_SIMPLE_ARRAY
                    return icms_core_DataFilter::htmlSpecialchars(icms_core_DataFilter::stripSlashesGPC((string)$this->_vars[$name][self::VARCFG_VALUE]), ENT_QUOTES);
                case self::DTYPE_OTHER; // XOBJ_DTYPE_OTHER
                default:
                    return null;
            }
        }          
        
        public function setVar($name, $value, $options = null) {
            if ($options !== null) {
                if (is_bool($options)) {
                    $this->setVarInfo($name, self::VARCFG_NOT_GPC, $options);
                } elseif (is_array($options)) {
                    foreach ($options as $k2 => $v2)
                        $this->setVarInfo($name, $k2, $v2);
                }
            }            
            return $this->__set($name, $value);
        }
        
       /**
        * assign a value to a variable
        *
        * @access public
        * @param string $key name of the variable to assign
        * @param mixed $value value to assign
        */
        public function assignVar($key, $value) {
            if (isset($value) && isset($this->_vars[$key])) {
                $this->_vars[$key][self::VARCFG_VALUE] =& $value;
            }
        }       
        
        public function getChangedVars() {
            $changed = array();
            foreach ($this->_vars as $key => $format)
                if (isset($format[self::VARCFG_CHANGED]) && $format[self::VARCFG_CHANGED])
                    $changed[] = $key;
            return $changed;
        }        
        
        private function isVarSet($type, $key) {
            switch ($type) {
                case self::DTYPE_LIST:
                case self::DTYPE_ARRAY:
                case self::DTYPE_FILE:
                    return (is_array($this->_vars[$key][self::VARCFG_VALUE]) && !empty($this->_vars[$key][self::VARCFG_VALUE]));
                case self::DTYPE_BOOLEAN:
                case self::DTYPE_INTEGER:
                case self::DTYPE_FLOAT:
                    return true;
                case self::DTYPE_CRITERIA:
                case self::DTYPE_DATA_SOURCE:
                    return is_object($this->_vars[$key][self::VARCFG_VALUE]);
                case self::DTYPE_STRING:
                    return strlen($this->_vars[$key][self::VARCFG_VALUE]) > 0;
                case self::DTYPE_DATETIME:
                    return is_int($this->_vars[$key][self::VARCFG_VALUE]) && ($this->_vars[$key][self::VARCFG_VALUE] > 0);
            }   
        }
        
        public function getProblematicVars() {
            $names = array();
            foreach ($this->_vars as $key => $format)
                if ($format[self::VARCFG_REQUIRED] && !$this->isVarSet($format[self::VARCFG_TYPE], $key))
                   $names[] = $key;
            return $names;
        }
        
        protected function initVar($key, $dataType, $defaultValue = null, $required = false, $otherCfg = null) {
            if (is_array($otherCfg) && !(empty($otherCfg))) {
                $this->_vars[$key] = $otherCfg;
                if (isset($this->_vars[$key][self::VARCFG_CONTROL]) && is_string($this->_vars[$key][self::VARCFG_CONTROL])) {
                    $this->_vars[$key][self::VARCFG_CONTROL] = array('name' => $this->_vars[$key][self::VARCFG_CONTROL]);
                }
                if (isset($this->_vars[$key][self::VARCFG_POSSIBLE_OPTIONS]) && !is_array($this->_vars[$key][self::VARCFG_POSSIBLE_OPTIONS])) {
                    if (is_string($this->_vars[$key][self::VARCFG_POSSIBLE_OPTIONS])) {
                        $this->_vars[$key][self::VARCFG_POSSIBLE_OPTIONS] = explode('|', $this->_vars[$key][self::VARCFG_POSSIBLE_OPTIONS]);
                    } else {
                        $this->_vars[$key][self::VARCFG_POSSIBLE_OPTIONS] = Array($this->_vars[$key][self::VARCFG_POSSIBLE_OPTIONS]);
                    }
                }
            } else {
                $this->_vars[$key] = array();
            }        
            switch ($dataType) {
                case self::DTYPE_DEP_CURRENCY:
                    $this->_vars[$key][self::VARCFG_FORMAT] = '%01.2f';
                    $this->_vars[$key][self::VARCFG_DEP_DATA_TYPE] = $dataType;
                    $dataType = self::DTYPE_FLOAT;
                break;
                case self::DTYPE_DEP_MTIME:                    
                    $this->_vars[$key][self::VARCFG_FORMAT] = _MEDIUMDATESTRING;
                    $this->_vars[$key][self::VARCFG_DEP_DATA_TYPE] = $dataType;
                    $dataType = self::DTYPE_DATETIME;
                break;
                case self::DTYPE_DEP_STIME:
                    $this->_vars[$key][self::VARCFG_FORMAT] = _SHORTDATESTRING;
                    $this->_vars[$key][self::VARCFG_DEP_DATA_TYPE] = $dataType;
                    $dataType = self::DTYPE_DATETIME;
                break;
                case self::DTYPE_DEP_TIME_ONLY:
                    $this->_vars[$key][self::VARCFG_FORMAT] = 's:i';
                    $this->_vars[$key][self::VARCFG_DEP_DATA_TYPE] = $dataType;
                    $dataType = self::DTYPE_DATETIME;
                break;
                case self::DTYPE_DEP_FORM_SECTION:
                case self::DTYPE_DEP_FORM_SECTION_CLOSE:
                    $this->_vars[$key][self::VARCFG_DEP_DATA_TYPE] = $dataType;
                    $dataType = self::DTYPE_OTHER;
                break;
                case self::DTYPE_DEP_SOURCE:
                    $this->_vars[$key][self::VARCFG_SOURCE_FORMATING] = 'php';
                    $this->_vars[$key][self::VARCFG_AF_DISABLED] = true;
                    $this->_vars[$key][self::VARCFG_DEP_DATA_TYPE] = $dataType;
                    $dataType = self::DTYPE_STRING;
                break;
                case self::DTYPE_DEP_URL:
                    $this->_vars[$key][self::VARCFG_VALIDATE_RULE] = "#^http(s)?://[a-z0-9-_.]+\.[a-z]{2,4}#i";
                    $this->_vars[$key][self::VARCFG_AF_DISABLED] = true;
                    $this->_vars[$key][self::VARCFG_DEP_DATA_TYPE] = $dataType;
                    $dataType = self::DTYPE_STRING;
                break;
                case self::DTYPE_DEP_URLLINK:
                    $this->_vars[$key][self::VARCFG_VALIDATE_RULE] = "#^http(s)?://[a-z0-9-_.]+\.[a-z]{2,4}#i";
                    $this->_vars[$key][self::VARCFG_AF_DISABLED] = true;
                    $this->_vars[$key][self::VARCFG_DATA_HANDLER] = 'link';
                    $this->_vars[$key][self::VARCFG_DEP_DATA_TYPE] = $dataType;
                    $dataType = self::DTYPE_INTEGER;
                break;
                case self::DTYPE_DEP_EMAIL:
                    $this->_vars[$key][self::VARCFG_VALIDATE_RULE] = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";
                    $this->_vars[$key][self::VARCFG_AF_DISABLED] = true;
                    $this->_vars[$key][self::VARCFG_DEP_DATA_TYPE] = $dataType;
                    $dataType = self::DTYPE_STRING;
                break;
                case self::DTYPE_DEP_TXTBOX:
                    $this->_vars[$key][self::VARCFG_MAX_LENGTH] = 255;
                    $this->_vars[$key][self::VARCFG_AF_DISABLED] = true;
                    $this->_vars[$key][self::VARCFG_DEP_DATA_TYPE] = $dataType;
                    $dataType = self::DTYPE_STRING;
                break;
                case self::DTYPE_DEP_IMAGE:
                    $this->_vars[$key][self::VARCFG_ALLOWED_MIMETYPES] = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/svg+xml', 'image/tiff', 'image/vnd.microsoft.icon');
                    $this->_vars[$key][self::VARCFG_DATA_HANDLER] = 'image';
                    $this->_vars[$key][self::VARCFG_AF_DISABLED] = true;
                    $this->_vars[$key][self::VARCFG_DEP_DATA_TYPE] = $dataType;
                    $dataType = self::DTYPE_INTEGER;
                case self::DTYPE_DEP_FILE:
                    $this->_vars[$key][self::VARCFG_DATA_HANDLER] = 'file';
                    $this->_vars[$key][self::VARCFG_AF_DISABLED] = true;
                    $this->_vars[$key][self::VARCFG_DEP_DATA_TYPE] = $dataType;
                    $dataType = self::DTYPE_INTEGER;
                case self::DTYPE_FILE:
                    if (!isset($this->_vars[$key][self::VARCFG_ALLOWED_MIMETYPES]))
                        $this->_vars[$key][self::VARCFG_ALLOWED_MIMETYPES] = 0;
                    elseif (is_string($this->_vars[$key][self::VARCFG_ALLOWED_MIMETYPES]))
                        $this->_vars[$key][self::VARCFG_ALLOWED_MIMETYPES] = array($this->_vars[$key][self::VARCFG_ALLOWED_MIMETYPES]);
                    if (!isset($this->_vars[$key][self::VARCFG_MAX_FILESIZE]))
                        $this->_vars[$key][self::VARCFG_MAX_FILESIZE] = 1000000;
                    elseif (!is_int($this->_vars[$key][self::VARCFG_MAX_FILESIZE]))
                        $this->_vars[$key][self::VARCFG_MAX_FILESIZE] = intval($this->_vars[$key][self::VARCFG_MAX_FILESIZE]);
                    if (!isset($this->_vars[$key][self::VARCFG_MAX_WIDTH]))
                        $this->_vars[$key][self::VARCFG_MAX_WIDTH] = 500;
                    elseif (!is_int($this->_vars[$key][self::VARCFG_MAX_WIDTH]))
                        $this->_vars[$key][self::VARCFG_MAX_WIDTH] = intval($this->_vars[$key][self::VARCFG_MAX_WIDTH]);
                    if (!isset($this->_vars[$key][self::VARCFG_MAX_HEIGHT]))
                        $this->_vars[$key][self::VARCFG_MAX_HEIGHT] = 500;
                    elseif (!is_int($this->_vars[$key][self::VARCFG_MAX_HEIGHT]))
                        $this->_vars[$key][self::VARCFG_MAX_HEIGHT] = intval($this->_vars[$key][self::VARCFG_MAX_HEIGHT]);
                    if (!isset($this->_vars[$key][self::VARCFG_PATH]) || empty($this->_vars[$key][self::VARCFG_PATH]))
                        $this->_vars[$key][self::VARCFG_PATH] = ICMS_UPLOAD_PATH;
                    if (!isset($this->_vars[$key][self::VARCFG_PREFIX]))
                        $this->_vars[$key][self::VARCFG_PREFIX] = str_replace(array('icms_ipf_', 'mod_'), '', get_class($this));
                    if (!isset($this->_vars[$key][self::VARCFG_FILENAME_FUNCTION]))
                        $this->_vars[$key][self::VARCFG_FILENAME_FUNCTION] = null;
                break;
                case self::DTYPE_LIST:
                    if (!isset($this->_vars[$key][self::VARCFG_SEPARATOR]))
                        $this->_vars[$key][self::VARCFG_SEPARATOR] = ';';                        
                break;
            }
            if (!isset($this->_vars[$key][self::VARCFG_LOCKED]))
                $this->_vars[$key][self::VARCFG_LOCKED] = false;   
            $this->_vars[$key][self::VARCFG_TYPE] = $dataType;
            $this->_vars[$key][self::VARCFG_DEFAULT_VALUE] = $this->_vars[$key][self::VARCFG_VALUE] = $this->cleanVar($key, $dataType, $defaultValue);
            $this->_vars[$key][self::VARCFG_REQUIRED] = $required;
        }
        
        public function getDefaultVars() {
            $ret = array();
            foreach ($this->_vars as $key => $info)
                $ret[$key] = $info[self::VARCFG_DEFAULT_VALUE];
            return $ret;
        }
        
        private function getFileMimeType($filename) {
            if (function_exists('finfo_open')) {
                $info = finfo_open(FILEINFO_MIME_TYPE);
                $rez = finfo_file($info, $filename);
                finfo_close($info);
                return $rez;
            }
            if (function_exists('mime_content_type'))
                return mime_content_type($filename);
            return 'unknown/unknown';
        }
        
        protected function cleanVar($key, $type, $value) {                                            
            switch ($type) {
                case self::DTYPE_CRITERIA:
                    if ($value instanceof icms_db_criteria_Element)
                        return $value;
                    if (empty($value))
                        return new icms_db_criteria_Compo();
                    if (is_string($value))
                        return new icms_db_criteria_SQLItem(str_replace('%', '%%', $value));
                    return new icms_db_criteria_Compo();
                case self::DTYPE_DATA_SOURCE:
                    if (is_null($value))
                        return null;                    
                    if (is_string($value) && class_exists($value, true)) {
                        if (!class_exists($value, true)) {
                            $value = base64_decode($value);
                            if (!is_string($value))
                                return null;
                            $value = gzinflate($value); 
                            if (!is_string($value))
                                return null;
                            $value = json_decode($value);
                            if (!is_array($value))
                                return null;
                            $value = $value[0];
                        }
                        $refl = new ReflectionClass($value);
                        if (!$refl->isInstantiable())
                            return null;
                        $value = $refl->newInstance(icms::$xoopsDB);                        
                    }                    
                    return (is_object($value) && ($value instanceOf icms_core_ObjectHandler))?$value:null;
                break;
                case self::DTYPE_BOOLEAN:     
                    if (is_bool($value))
                        return $value;
                    if (is_numeric($value))
                        return (bool)intval($value);
                    if (!is_string($value))
                        return (bool)$value;
                    $value = strtolower($value);
                    return ($value == 'yes') || ($value == 'true');
                break;
                case self::DTYPE_LIST:
                    if (is_array($value))
                        return $value;                    
                    return explode($this->_vars[$key][self::VARCFG_SEPARATOR], strval($value));
                case self::DTYPE_FLOAT:
                    return floatval($value);
                case self::DTYPE_INTEGER:
                    return intval($value);
                case self::DTYPE_ARRAY:         
                    if (is_array($value)) 
                        return $value;
                    elseif (is_string($value) && !empty($value)) {                        
                        if (in_array(substr($value, 0, 1), array('{', '[')) ) {
                            $ret = json_decode($value, true);
                            if (is_array($ret))
                                return $ret;
                        } elseif (substr($value, 0, 2) == 'a:') {
                            $ret = unserialize($ret);
                            if (is_array($ret))
                                return $ret;
                        }                        
                        //die('aaa');
                        return array($value); 
                    } elseif (is_null($value) && empty($value))
                        return array();
                    elseif (!is_object($value))
                        return array($value);
                    elseif ($value instanceOf icms_collection_Response) {
                        $data = $value->toArray();
                        if (isset($data['isOK']))
                            unset($data['isOK']);
                        return $data;
                    } elseif (($value instanceOf icms_ipf_Properties) || (method_exists($value, 'toArray'))) {
                        return $value->toArray();
                    } elseif (method_exists($value, 'toResponse')) {
                        return $value->toResponse()->toArray();
                    } else {
                        return (array)$value;
                    }
                case self::DTYPE_FILE:
                    if (isset($_FILES[$key])) {                        
                        $uploader = new icms_file_MediaUploadHandler($this->_vars[$key]['path'],  $this->_vars[$key]['allowedMimeTypes'], $this->_vars[$key]['maxFileSize'], $this->_vars[$key]['maxWidth'], $this->_vars[$key]['maxHeight']);
                        if ($uploader->fetchMedia($key)) {  
                            if (!empty($this->_vars[$key][self::VARCFG_FILENAME_FUNCTION])) {
                                $filename = call_user_func($this->_vars[$key][self::VARCFG_FILENAME_FUNCTION], 'post', $uploader->getMediaType(), $uploader->getMediaName());
                                if (!empty($this->_vars[$key]['prefix']))
                                    $filename = $this->_vars[$key]['prefix'] . $filename;
                                $uploader->setTargetFileName($filename);
                            } elseif (!empty($this->_vars[$key]['prefix'])) {
                                $uploader->setPrefix($this->_vars[$key]['prefix']);
                            }                            
                            if ($uploader->upload()) {
                                return array(
                                    'filename' => $uploader->getSavedFileName(),
                                    'mimetype' => $uploader->getMediaType(),
                                );
                            }
                            return null;
                        }
                    } elseif (is_string($value)) {                        
                        if (file_exists($value)) {
                            return array(
                                'filename' => $value,
                                'mimetype' => $this->getFileMimeType($value),
                            );
                        }
                        $uploader = new icms_file_MediaUploadHandler($this->_vars[$key]['path'],  $this->_vars[$key]['allowedMimeTypes'], $this->_vars[$key]['maxFileSize'], $this->_vars[$key]['maxWidth'], $this->_vars[$key]['maxHeight']);
                        if ($uploader->fetchFromURL($value)) {
                            if (!empty($this->_vars[$key][self::VARCFG_FILENAME_FUNCTION])) {
                                $filename = call_user_func($this->_vars[$key][self::VARCFG_FILENAME_FUNCTION], 'post', $uploader->getMediaType(), $uploader->getMediaName());
                                if (!empty($this->_vars[$key]['prefix']))
                                    $filename = $this->_vars[$key]['prefix'] . $filename;
                                $uploader->setTargetFileName($filename);
                            } elseif (!empty($this->_vars[$key]['prefix'])) {
                                $uploader->setPrefix($this->_vars[$key]['prefix']);
                            }                            
                            if ($uploader->upload()) {
                                return array(
                                    'filename' => $uploader->getSavedFileName(),
                                    'mimetype' => $uploader->getMediaType(),
                                );
                            }       
                            trigger_error(strip_tags($uploader->getErrors()), E_USER_NOTICE);
                            return null;
                        }
                        return null;
                    } elseif (is_array($value)) {
                        if (!isset($value['filename']) || !isset($value['mimetype']))
                            return null;
                        return $value;
                    }
                    return null;
                break;
                case self::DTYPE_DATETIME:
                    if (is_int($value))
                        return $value;
                    if (is_numeric($value))
                        return intval($value);
                    return strtotime($value);
                break;
                case self::DTYPE_STRING:
                default:
                    if (!empty($this->_vars[$key][self::VARCFG_VALUE]) && isset($this->_vars[$key][self::VARCFG_VALIDATE_RULE]) && !empty($this->_vars[$key][self::VARCFG_VALIDATE_RULE]))
                        if (!preg_match($this->_vars[$key][self::VARCFG_VALIDATE_RULE], $value)) {
                            trigger_error(sprintf('Bad format for %s var (%s)', $key, $value), E_USER_ERROR);
                        }
                    elseif (!isset($this->_vars[$key][self::VARCFG_SOURCE_FORMATING]) || empty($this->_vars[$key][self::VARCFG_SOURCE_FORMATING]))
                        $value = icms_core_DataFilter::censorString($value);
                    if (isset($this->_vars[$key][self::VARCFG_NOT_GPC]) && !$this->_vars[$key][self::VARCFG_NOT_GPC])
                        $value = icms_core_DataFilter::stripSlashesGPC($value);
                    if (!is_string($value))
                        $value = strval($value);
                    if (isset($this->_vars[$key][self::VARCFG_MAX_LENGTH]) && ($this->_vars[$key][self::VARCFG_MAX_LENGTH] > 0) && (mb_strlen($value) > $this->_vars[$key][self::VARCFG_MAX_LENGTH])) {
                        icms_loadLanguageFile('core', 'global');
                        trigger_error(sprintf(_XOBJ_ERR_SHORTERTHAN, $key, (int)$this->_vars[$key][self::VARCFG_MAX_LENGTH]), E_USER_WARNING);
                        $value = mb_substr($value, 0, $this->_vars[$key][self::VARCFG_MAX_LENGTH]);
                    }                      
                    return $value;
            }
        }        

	/**
	 * Returns the values of the specified variables
	 *
	 * @param mixed $keys An array containing the names of the keys to retrieve, or null to get all of them
	 * @param string $format Format to use (see getVar)
	 * @param int $maxDepth Maximum level of recursion to use if some vars are objects themselves
	 * @return array associative array of key->value pairs
	 */
	public function getValues($keys = null, $format = 's', $maxDepth = 1) {
		if (!isset($keys)) {
			$keys = array_keys($this->_vars);
		}
		$vars = array();
		foreach ($keys as $key) {
			if (isset($this->_vars[$key])) {
				if (is_object($this->_vars[$key]) && ($this->_vars[$key] instanceof icms_ipf_Properties)) {
					if ($maxDepth) {
						$vars[$key] = $this->_vars[$key]->getValues(null, $format, $maxDepth - 1);
					}
				} else {
					$vars[$key] = $this->getVar($key, $format);
				}
			}
		}
		return $vars;
	}        
        
        
	/**
	 * Returns properties as key-value array
     * 
     * @return array
	 */
	public function toArray() {
        $ret = array();
        foreach ($this->_vars as $name => $value)
            switch ($this->_vars[$name][self::VARCFG_TYPE]) {
                case self::DTYPE_DATA_SOURCE:
                    $r2 = array();
                    if (is_object($value[self::VARCFG_VALUE])) {
                        $item = $value[self::VARCFG_VALUE]->create();
                        $vars = array();
                        foreach ($item->getVars() as $var => $data) {
                            unset($data[self::VARCFG_VALUE]);
                            if (isset($data['persistent']))
                               if (!$data['persistent'])
                                   continue;
                               else
                                   unset($data['persistent']);
                            if (isset($data['path']))
                                unset($data['path']);
                            if (isset($data['prefix']))
                                unset($data['prefix']);                            
                            $vars[$var] = array_filter($data);
                        }                            
                        $ret[$name] = base64_encode(gzdeflate(json_encode(array(get_class($value[self::VARCFG_VALUE]), $vars)), 9));
                       // $ret[$name] = json_encode(array(get_class($value[self::VARCFG_VALUE]), $vars));
                    } else {
                        $ret[$name] = null;
                    }
                break;
                case self::DTYPE_CRITERIA:
                    $ret[$name] = $value[self::VARCFG_VALUE]->render();
                break;
                case self::DTYPE_LIST:
                    $ret[$name] = implode($this->_vars[$name][self::VARCFG_SEPARATOR], $value[self::VARCFG_VALUE]);
                break;
                default:                    
                    $ret[$name] = $value[self::VARCFG_VALUE];
            }            
        return $ret;
    }

	public function getVarInfo($key = null, $info = null, $default = null) {
        if (!$key)
            return $this->_vars;
        elseif (!$info)
            if (isset($this->_vars[$key]))
                return $this->_vars[$key];
            else {
                $callers=debug_backtrace();
                trigger_error(sprintf('%s in %s on line %d doesn\'t exist', $key, $callers[1]['class'], $callers[1]['line']), E_USER_DEPRECATED);
                return $default;
            }
        elseif (isset($this->_vars[$key][$info]))
            return $this->_vars[$key][$info];
        else
            return $default;
	}
    
    /**
	 * returns all variables for the object
	 *
	 * @access public
	 * @return array associative array of key->value pairs
	 */
	public function &getVars() {
		return $this->_vars;
	}
    
    /**
	 * assign values to multiple variables in a batch
	 *
	 * @access public
	 * @param array $var_arr associative array of values to assign
	 * @param bool $not_gpc
	 */
	public function setVars($var_arr, $not_gpc = false) {
		foreach ($var_arr as $key => $value)
			$this->setVar($key, $value, $not_gpc);
	}
        
    public function __call($name, $arguments) {
        switch ($name) {
            case 'assignVars':                
//                trigger_error(sprintf('%s is deprecached method for %s', $name, get_class($this)), (version_compare(PHP_VERSION, '5.3.0') >= 0)?E_USER_DEPRECATED:E_USER_WARNING);
                $this->load($arguments[0]);
            break;
            case 'setType':
                trigger_error(sprintf('%s is deprecached method for %s', $name, get_class($this)), E_USER_DEPRECATED);
                $this->setVarInfo($arguments[0], self::VARCFG_TYPE, $arguments[1]);
            break;
            case 'doSetFieldAsRequired':
                trigger_error(sprintf('%s is deprecached method for %s', $name, get_class($this)), E_USER_DEPRECATED);
                $this->setVarInfo($arguments[0], self::VARCFG_REQUIRED, $arguments[1]);
            break;
            default:
                trigger_error(sprintf('Method \'%s\' doesn\'t exists for %s', $name, get_class($this)), E_USER_ERROR);
        }
    }
    
    public function cleanVars() {
        trigger_error(sprintf('%s is deprecached method for %s', $name, get_class($this)), E_USER_DEPRECATED);                
        return $this->toArray();
    }

	protected function setVarInfo($key, $info, $value) {
        if ($key === null)
            $key = array_keys($this->_vars);
        if (is_array($key)) {
            foreach ($key as $k)
                $this->setVarInfo($k, $info, $value);
        } else {
            if (!isset($this->_vars[$key]))
                return trigger_error('Variable '.get_class($this).'::$'.$key.' not found', E_USER_WARNING);
            $this->_vars[$key][$info] = $value;
            switch ($info) {
                case self::VARCFG_TYPE:
                    $this->$key = $this->_vars[$key][self::VARCFG_VALUE];
                break;
            }
        }
	}
    
     public function serialize() {
         $data = array('vars' => $this->getValues(null, 'n'));
         return serialize($data);                  
     }
     
     public function unserialize($serialized) {
         $data = unserialize($serialized);
         if (method_exists($this, '__construct'))
            $this->__construct();
         foreach ($data['vars'] as $key => $value)
            $this->_vars[$key][self::VARCFG_VALUE] = $value;
     }     


}