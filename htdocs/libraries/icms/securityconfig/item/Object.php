<?php
/**
 * Manage security configuration items
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @category	ICMS
 * @package		SecurityConfig
 * @subpackage	Item
 * @author		Vaughan Montgomery
 * @version		SVN: $Id: Object.php 20516 2010-12-11 20:50:13Z phoenyx $
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**
 * @category	ICMS
 * @package		SecurityConfig
 * @subpackage	Item
 * @author	    Vaughan montgomery <vaughan@impresscms.org>
 */
class icms_securityconfig_Item_Object extends icms_core_Object {
	/**
	 * Security Config options
	 *
	 * @var	array
	 * @access	private
	 */
	public $_secOptions = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->initVar('sec_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('sec_modid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('sec_catid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('sec_name', XOBJ_DTYPE_OTHER);
		$this->initVar('sec_title', XOBJ_DTYPE_TXTBOX);
		$this->initVar('sec_value', XOBJ_DTYPE_TXTAREA);
		$this->initVar('sec_desc', XOBJ_DTYPE_OTHER);
		$this->initVar('sec_formtype', XOBJ_DTYPE_OTHER);
		$this->initVar('sec_valuetype', XOBJ_DTYPE_OTHER);
		$this->initVar('sec_order', XOBJ_DTYPE_INT);
	}

	/**
	 * Get a config value in a format ready for output
	 *
	 * @return	string
	 */
	public function getSecValueForOutput() {
		switch($this->getVar('sec_valuetype')) {
			case 'int':
				return (int) $this->getVar('sec_value', 'N');
				break;

			case 'array':
				$value = @ unserialize($this->getVar('sec_value', 'N'));
				return $value ? $value : array();

			case 'float':
				$value = $this->getVar('sec_value', 'N');
				return (float) $value;
				break;

			case 'textarea':
				return $this->getVar('sec_value');
			default:
				return $this->getVar('sec_value', 'N');
				break;
		}
	}

	/**
	 * Set a config value
	 *
	 * @param	mixed   &$value Value
	 * @param	bool    $force_slash
	 */
	public function setSecValueForInput($value, $force_slash = false) {
		if ($this->getVar('sec_formtype') == 'textarea') {
			$value = icms_core_DataFilter::filterTextareaInput($value, 1);
		} elseif ($this->getVar('sec_formtype') == 'password') {
			$value = filter_var($value, FILTER_SANITIZE_URL);
		} else {
			$value = StopXSS($value);
		}
		switch($this->getVar('sec_valuetype')) {
			case 'array':
				if (!is_array($value)) {
					$value = explode('|', trim($value));
				}
				$this->setVar('sec_value', serialize($value), $force_slash);
				break;

			case 'text':
				$this->setVar('sec_value', trim($value), $force_slash);
				break;

			default:
				$this->setVar('sec_value', $value, $force_slash);
				break;
		}
	}

	/**
	 * Assign one or more {@link icms_securityconfig_Item_ObjectOption}s
	 *
	 * @param	mixed   $option either a {@link icms_securityconfig_Item_ObjectOption} object or an array of them
	 */
	public function setSecOptions($option) {
		if (is_array($option)) {
			$count = count($option);
			for ( $i = 0; $i < $count; $i++) {
				self::setSecOptions($option[$i]);
			}
		} else {
			if (is_object($option)) {
				$this->_secOptions[] =& $option;
			}
		}
	}

	/**
	 * Get the {@link icms_securityconfig_Item_ObjectOption}s of this Config
	 *
	 * @return	array   array of {@link icms_securityconfig_Item_ObjectOption}
	 */
	public function &getSecOptions() {
		return $this->_secOptions;
	}
}