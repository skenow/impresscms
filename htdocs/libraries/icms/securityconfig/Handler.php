<?php
/**
 * Manage Security configuration items
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @category	ICMS
 * @package		SecurityConfig
 * @author		Vaughan Montgomery
 * @version		SVN: $Id:Handler.php 19775 2010-07-11 18:54:25Z malanciault $
 */

defined('ICMS_ROOT_PATH') or die("ImpressCMS root path not defined");

/**
 * Security Configuration handling class.
 * This class acts as an interface for handling general security configurations
 * and its modules.
 *
 * @category	ICMS
 * @package 	Security Config
 * @author		Vaughan montgomery <vaughan@impresscms.org>
 * @todo		Tests that need to be made:
 * 				- error handling
 * @access		public
 */
class icms_securityconfig_Handler {
	static protected $instance;
	/**
	 * Initialize the security config handler.
	 * @param $db
	 */
	static public function service() {
		if (isset(self::$instance)) return self::$instance;
		$instance = icms::handler('icms_securityconfig');
		$configs = $instance->getConfigsByCat(
			array(
				ICMS_SEC_CONF, ICMS_SEC_CONF_USER, ICMS_SEC_CONF_HTMLFILTER
			)
		);
		$GLOBALS['icmsSecurityConfig']					= $configs[ICMS_SEC_CONF];
		$GLOBALS['icmsSecurityConfigUser']				= $configs[ICMS_SEC_CONF_USER];
		$GLOBALS['icmsSecurityConfigHTMLFilter']		= $configs[ICMS_SEC_CONF_HTMLFILTER];
		return self::$instance = $instance;
	}

	/**
	 * holds reference to security config item handler(DAO) class
	 *
	 * @var     object
	 * @access	private
	 */
	private $_scHandler;

	/**
	 * holds reference to config option handler(DAO) class
	 *
	 * @var	    object
	 * @access	private
	 */
	private $_soHandler;

	/**
	 * holds an array of cached references to security config value arrays,
	 *  indexed on module id and security category id
	 *
	 * @var     array
	 * @access  private
	 */
	private $_cachedConfigs = array();

	/**
	 * Constructor
	 *
	 * @param	object  &$db    reference to database object
	 */
	public function __construct(&$db) {
		$this->_scHandler = new icms_securityconfig_item_Handler($db);
		$this->_soHandler = new icms_securityconfig_option_Handler($db);
	}

	/**
	 * Create a config
	 *
	 * @see     icms_securityconfig_Item_Object
	 * @return	object  reference to the new {@link icms_securityconfig_Item_Object}
	 */
	public function &createConfig() {
		$instance =& $this->_scHandler->create();
		return $instance;
	}

	/**
	 * Get a config
	 *
	 * @param	int     $id             ID of the config
	 * @param	bool    $withoptions    load the config's options now?
	 * @return	object  reference to the {@link icmsSecurityConfig}
	 */
	public function &getConfig($id, $withoptions = false) {
		$config =& $this->_scHandler->get($id);
		if ($withoptions == true) {
			$config->setSecOptions(self::getConfigOptions(new icms_db_criteria_Item('sec_id', $id)));
		}
		return $config;
	}

	/**
	 * insert a new config in the database
	 *
	 * @param	object  &$config    reference to the {@link icms_securityconfig_Item_Object}
	 * @return	true|false if inserting config succeeded or not
	 */
	public function insertConfig(&$config) {
		if (!$this->_scHandler->insert($config)) {
			return false;
		}
		$options =& $config->getSecOptions();
		$count = count($options);
		$sec_id = (int) $config->getVar('sec_id');
		for ($i = 0; $i < $count; $i++) {
			$options[$i]->setVar('sec_id', $sec_id);
			if(!$this->_soHandler->insert($options[$i])) {
				foreach ($options[$i]->getErrors() as $msg) {
					$config->setErrors($msg);
				}
			}
		}

		if (!empty($this->_cachedConfigs[$config->getVar('sec_modid')][$config->getVar('sec_catid')])) {
			unset($this->_cachedConfigs[$config->getVar('sec_modid')][$config->getVar('sec_catid')]);
		}
		return true;
	}

	/**
	 * Delete a config from the database
	 *
	 * @param	object  &$config    reference to a {@link icms_securityconfig_Item_Object}
	 * @return	true|false if deleting config item succeeded or not
	 */
	public function deleteConfig(&$config) {
		if (!$this->_scHandler->delete($config)) {
			return false;
		}
		$options =& $config->getSecOptions();
		$count = count($options);
		if ($count == 0) {
			$options = self::getConfigOptions(new icms_db_criteria_Item('sec_id', $config->getVar('sec_id')));
			$count = count($options);
		}
		if (is_array($options) && $count > 0) {
			for ($i = 0; $i < $count; $i++) {
				$this->_soHandler->delete($options[$i]);
			}
		}
		if (!empty($this->_cachedConfigs[$config->getVar('sec_modid')][$config->getVar('sec_catid')])) {
			unset($this->_cachedConfigs[$config->getVar('sec_modid')][$config->getVar('sec_catid')]);
		}
		return true;
	}

	/**
	 * get one or more Configs
	 *
	 * @param	object  $criteria       {@link icms_db_criteria_Element}
	 * @param	bool    $id_as_key      Use the configs' ID as keys?
	 * @param	bool    $with_options   get the options now?
	 *
	 * @return	array   Array of {@link icms_securityconfig_Item_Object} objects
	 */
	public function getConfigs($criteria = null, $id_as_key = false, $with_options = false) {
		return $this->_scHandler->getObjects($criteria, $id_as_key);
	}

	/**
	 * Count some configs
	 *
	 * @param	object  $criteria   {@link icms_db_criteria_Element}
	 * @return	int count result
	 */
	public function getConfigCount($criteria = null) {
		return $this->_scHandler->getCount($criteria);
	}

	/**
	 * Get configs from a certain category
	 *
	 * @param	int $category   ID of a category
	 * @param	int $module     ID of a module
	 *
	 * @return	array   array of {@link XoopsConfig}s
	 */
	public function &getConfigsByCat($category, $module = 0) {
		static $_cachedConfigs;

		if (is_array($category)) {
			$criteria = new icms_db_criteria_Compo(new icms_db_criteria_Item('sec_modid', (int) $module));
			$criteria->add(new icms_db_criteria_Item('sec_catid', '(' . implode(',', $category) . ')', 'IN'));
			$configs = self::getConfigs($criteria, true);
			if (is_array($configs)) {
				foreach (array_keys($configs) as $i) {
					$ret[$configs[$i]->getVar('sec_catid')][$configs[$i]->getVar('sec_name')] = $configs[$i]->getSecValueForOutput();
				}
				foreach ($ret as $key => $value) {
					$_cachedConfigs[$module][$key] = $value;
				}
				return $ret;
			}
		} else {
			if (!empty($_cachedConfigs[$module][$category])) return $_cachedConfigs[$module][$category];

			$criteria = new icms_db_criteria_Compo(new icms_db_criteria_Item('sec_modid', (int) $module));
			if (!empty($category)) {
				$criteria->add(new icms_db_criteria_Item('sec_catid', (int) $category));
			}
			$ret = array();
			$configs = self::getConfigs($criteria, true);
			if (is_array($configs)) {
				foreach (array_keys($configs) as $i) {
					$ret[$configs[$i]->getVar('sec_name')] = $configs[$i]->getSecValueForOutput();
				}
			}
			$_cachedConfigs[$module][$category] = $ret;
			return $_cachedConfigs[$module][$category];
		}
	}

	/**
	 * Make a new {@link icms_securityconfig_option_Object}
	 *
	 * @return	object  {@link icms_securityconfig_option_Object}
	 */
	public function &createConfigOption() {
		$inst =& $this->_soHandler->create();
		return $inst;
	}

	/**
	 * Get a {@link icms_securityconfig_option_Object}
	 *
	 * @param	int $id ID of the config option
	 *
	 * @return	object  {@link icms_securityconfig_option_Object}
	 */
	public function &getConfigOption($id) {
		$inst =& $this->_soHandler->get($id);
		return $inst;
	}

	/**
	 * Get one or more {@link icms_securityconfig_option_Object}s
	 *
	 * @param	object  $criteria   {@link icms_db_criteria_Element}
	 * @param	bool    $id_as_key  Use IDs as keys in the array?
	 *
	 * @return	array   Array of {@link icms_securityconfig_option_Object}s
	 */
	public function getConfigOptions($criteria = null, $id_as_key = false) {
		return $this->_soHandler->getObjects($criteria, $id_as_key);
	}

	/**
	 * Count some {@link icms_securityconfig_option_Object}s
	 *
	 * @param	object  $criteria   {@link icms_db_criteria_Element}
	 *
	 * @return	int     Count of {@link icms_securityconfig_option_Object}s matching $criteria
	 */
	public function getConfigOptionsCount($criteria = null) {
		return $this->_soHandler->getCount($criteria);
	}

	/**
	 * Get a list of configs
	 *
	 * @param	int $sec_modid ID of the modules
	 * @param	int $sec_catid ID of the category
	 *
	 * @return	array   Associative array of name=>value pairs.
	 */
	public function getConfigList($sec_modid, $sec_catid = 0) {
		if (!empty($this->_cachedConfigs[$sec_modid][$sec_catid])) {
			return $this->_cachedConfigs[$sec_modid][$sec_catid];
		} else {
			$criteria = new icms_db_criteria_Compo(new icms_db_criteria_Item('sec_modid', $sec_modid));
			if (empty($sec_catid)) {
				$criteria->add(new icms_db_criteria_Item('sec_catid', $sec_catid));
			}
			$configs =& $this->_scHandler->getObjects($criteria);
			$seccount = count($configs);
			$ret = array();
			for ($i = 0; $i < $seccount; $i++) {
				$ret[$configs[$i]->getVar('sec_name')] = $configs[$i]->getSecValueForOutput();
			}
			$this->_cachedConfigs[$sec_modid][$sec_catid] =& $ret;
			return $ret;
		}
	}
}