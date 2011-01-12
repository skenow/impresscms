<?php
/**
 * Manage security configuration items
 *
 * @copyright    http://www.impresscms.org/ The ImpressCMS Project
 * @license      LICENSE.txt
 * @package      core
 * @subpackage   securityconfig
 * @since        1.4
 * @author       Vaughan montgomery
 * @version      $Id:Handler.php 19775 2010-07-11 18:54:25Z malanciault $
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**#@+
 * Config type
 */
define('ICMS_SEC_CONF', 1);
define('ICMS_SEC_CONF_USER', 2);
define('ICMS_SEC_CONF_AUTH', 3);
/**#@-*/

/**
 * Configuration handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of configuration class objects.
 *
 * @author      Vaughan montgomery <vaughan@impresscms.org>
 * @category	ICMS
 * @package     SecurityConfig
 * @subpackage  Item
 */
class icms_securityconfig_Item_Handler extends icms_core_ObjectHandler {

	/**
	 * Create a new {@link icms_securityconfig_Item_Object}
	 *
	 * @see     icms_securityconfig_Item_Object
	 * @param	bool    $isNew  Flag the config as "new"?
	 * @return	object  reference to the new config
	 */
	public function &create($isNew = true) {
		$config = new icms_securityconfig_Item_Object();
		if ($isNew) {
			$config->setNew();
		}
		return $config;
	}

	/**
	 * Load a config from the database
	 *
	 * @param	int $id ID of the config
	 * @return	object  reference to the config, FALSE on fail
	 */
	public function &get($id) {
		$config = false;
		$id = (int) $id;
		if ($id > 0) {
			$sql = sprintf("SELECT * FROM %s WHERE sec_id = '%u'",
				icms::$xoopsDB->prefix('SecurityConfig'),
				$id
				);
			if (!$result = icms::$xoopsDB->query($sql)) {
				return $config;
			}
			$numrows = icms::$xoopsDB->getRowsNum($result);
			if ($numrows == 1) {
				$myrow = icms::$xoopsDB->fetchArray($result);
				$config = new icms_securityconfig_Item_Object();
				$config->assignVars($myrow);
			}
		}
		return $config;
	}

	/**
	 * Insert a config to the database
	 *
	 * @param	object  &$config    {@link icms_securityconfig_Item_Object} object
	 * @return  mixed   FALSE on fail.
	 */
	public function insert(&$config) {
		if (!is_a($config, 'icms_securityconfig_Item_Object')) return false;
		if (!$config->isDirty()) return true;
		if (!$config->cleanVars()) return false;
		foreach ($config->cleanVars as $k => $v) {
			${$k} = $v;
		}
		if ($config->isNew()) {
			$sec_id = icms::$xoopsDB->genId('SecurityConfig_sec_id_seq');
			$sql = sprintf("INSERT INTO %s (
				sec_id,
				sec_modid,
				sec_catid,
				sec_name,
				sec_title,
				sec_value,
				sec_desc,
				sec_formtype,
				sec_valuetype,
				sec_order
				) VALUES ('%u', '%u', '%u', %s, %s, %s, %s, %s, %s, '%u')",
				icms::$xoopsDB->prefix('SecurityConfig'),
				(int) $sec_id,
				(int) $sec_modid,
				(int) $sec_catid,
				icms::$xoopsDB->quoteString($sec_name),
				icms::$xoopsDB->quoteString($sec_title),
				icms::$xoopsDB->quoteString($sec_value),
				icms::$xoopsDB->quoteString($sec_desc),
				icms::$xoopsDB->quoteString($sec_formtype),
				icms::$xoopsDB->quoteString($sec_valuetype),
				(int) $sec_order
			);
		} else {
			$sql = sprintf("UPDATE %s SET sec_modid = '%u',
				sec_catid = '%u',
				sec_name = %s,
				sec_title = %s,
				sec_value = %s,
				sec_desc = %s,
				sec_formtype = %s,
				sec_valuetype = %s,
				sec_order = '%u'
				WHERE sec_id = '%u'",
				icms::$xoopsDB->prefix('SecurityConfig'),
				(int) $sec_modid,
				(int) $sec_catid,
				icms::$xoopsDB->quoteString($sec_name),
				icms::$xoopsDB->quoteString($sec_title),
				icms::$xoopsDB->quoteString($sec_value),
				icms::$xoopsDB->quoteString($sec_desc),
				icms::$xoopsDB->quoteString($sec_formtype),
				icms::$xoopsDB->quoteString($sec_valuetype),
				(int) $sec_order,
				(int) $sec_id
			);
		}
		if (!$result = icms::$xoopsDB->query($sql))	return false;
		if (empty($sec_id)) {
			$sec_id = icms::$xoopsDB->getInsertId();
		}
		$config->assignVar('sec_id', $sec_id);
		return true;
	}

	/**
	 * Delete a config from the database
	 *
	 * @param	object  &$config    Config to delete
	 * @return	bool    Successful?
	 */
	public function delete(&$config) {
		if (!is_a($config, 'icms_securityconfig_Item_Object')) return false;
		$sql = sprintf("DELETE FROM %s WHERE sec_id = '%u'",
			icms::$xoopsDB->prefix('SecurityConfig'),
			(int) $config->getVar('sec_id')
			);
		if (!$result = icms::$xoopsDB->query($sql))	return false;
		return true;
	}

	/**
	 * Get configs from the database
	 *
	 * @param	object  $criteria   {@link icms_db_criteria_Element}
	 * @param	bool    $id_as_key  return the config's id as key?
	 * @return	array   Array of {@link icms_securityconfig_Item_Object} objects
	 */
	public function getObjects($criteria = null, $id_as_key = false) {
		$ret = array();
		$limit = $start = 0;
		$sql = 'SELECT * FROM ' . icms::$xoopsDB->prefix('SecurityConfig');
		if (isset($criteria) && is_subclass_of($criteria, 'icms_db_criteria_Element')) {
			$sql .= ' ' . $criteria->renderWhere();
			$sql .= ' ORDER BY sec_order ASC';
			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		}
		$result = icms::$xoopsDB->query($sql, $limit, $start);
		if (!$result) return false;

		while ($myrow = icms::$xoopsDB->fetchArray($result)) {
			$config = new icms_securityconfig_item_Object();
			$config->assignVars($myrow);
			if (!$id_as_key) {
				$ret[] =& $config;
			} else {
				$ret[$myrow['sec_id']] =& $config;
			}
			unset($config);
		}
		return $ret;
	}

	/**
	 * Count configs
	 *
	 * @param	object  $criteria   {@link icms_db_criteria_Element}
	 * @return	int     Count of configs matching $criteria
	 */
	public function getCount($criteria = null) {
		$ret = array();
		$limit = $start = 0;
		$sql = 'SELECT * FROM ' . icms::$xoopsDB->prefix('SecurityConfig');
		if (isset($criteria) && is_subclass_of($criteria, 'icms_db_criteria_Element')) {
			$sql .= ' ' . $criteria->renderWhere();
		}
		$result =& icms::$xoopsDB->query($sql);
		if (!$result) return false;

		list($count) = icms::$xoopsDB->fetchRow($result);
		return $count;
	}
}