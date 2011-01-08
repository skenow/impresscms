<?php
/**
 * Manage security configuration options
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @category	ICMS
 * @package		SecurityConfig
 * @subpackage	Option
 * @author		vaughan montgomery
 * @version		SVN: $Id:Handler.php 19775 2010-07-11 18:54:25Z malanciault $
 */

defined('ICMS_ROOT_PATH') or die("ImpressCMS root path not defined");

/**
 * Configuration option handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of configuration option class objects.
 *
 * @author  Kazumi Ono <onokazu@xoops.org>
 *
 * @category	ICMS
 * @package     SecurityConfig
 * @subpackage  Option
 */
class icms_securityconfig_option_Handler extends icms_core_ObjectHandler {

	/**
	 * Create a new option
	 *
	 * @param	bool    $isNew  Flag the option as "new"?
	 *
	 * @return	object  {@link icms_securityconfig_option_Object}
	 */
	public function &create($isNew = true) {
		$secoption = new icms_securityconfig_option_Object();
		if ($isNew) {
			$secoption->setNew();
		}
		return $secoption;
	}

	/**
	 * Get an option from the database
	 *
	 * @param	int $id ID of the option
	 *
	 * @return	object  reference to the {@link icms_securityconfig_option_Object}, FALSE on fail
	 */
	public function &get($id) {
		$secoption = false;
		$id = (int) $id;
		if ($id > 0) {
			$sql = sprintf("SELECT * FROM %s WHERE sec_op_id = '%u'",
					icms::$xoopsDB->prefix('SecurityConfigOption'),
					$id);
			if (!$result = icms::$xoopsDB->query($sql)) {
				return $secoption;
			}
			$numrows = icms::$xoopsDB->getRowsNum($result);
			if ($numrows == 1) {
				$secoption = new icms_securityconfig_option_Object();
				$secoption->assignVars(icms::$xoopsDB->fetchArray($result));
			}
		}
		return $secoption;
	}

	/**
	 * Insert a new option in the database
	 *
	 * @param	object  &$secoption    reference to a {@link icms_securityconfig_option_Object}
	 * @return	bool    TRUE if successfull.
	 */
	public function insert(&$secoption) {
		if (!is_a($secoption, 'icms_securityconfig_option_Object')) {
			return false;
		}
		if (!$secoption->isDirty()) {
			return true;
		}
		if (!$secoption->cleanVars()) {
			return false;
		}
		foreach ($secoption->cleanVars as $k => $v) {
			${$k} = $v;
		}
		if ($secoption->isNew()) {
			$sec_op_id = icms::$xoopsDB->genId('SecurityConfigOption_sec_op_id_seq');
			$sql = sprintf("INSERT INTO %s (sec_op_id, sec_op_name, sec_op_value, sec_id)
				VALUES ('%u', %s, %s, '%u')",
				icms::$xoopsDB->prefix('SecurityConfigOption'),
				(int) $sec_op_id,
				icms::$xoopsDB->quoteString($sec_op_name),
				icms::$xoopsDB->quoteString($sec_op_value),
				(int) $sec_id
				);
		} else {
			$sql = sprintf("UPDATE %s SET sec_op_name = %s, sec_op_value = %s
				WHERE sec_op_id = '%u'",
				icms::$xoopsDB->prefix('SecurityConfigOption'),
				icms::$xoopsDB->quoteString($sec_op_name),
				icms::$xoopsDB->quoteString($sec_op_value),
				(int) ($sec_op_id)
				);
		}
		if (!$result = icms::$xoopsDB->query($sql)) {
			return false;
		}
		if (empty($sec_op_id)) {
			$sec_op_id = icms::$xoopsDB->getInsertId();
		}
		$secoption->assignVar('sec_op_id', $sec_op_id);
		return $sec_op_id;
	}

	/**
	 * Delete an option
	 *
	 * @param	object  &$secoption    reference to a {@link icms_securityconfig_option_Object}
	 * @return	bool    TRUE if successful
	 */
	public function delete(&$secoption) {
		if (!is_a($secoption, 'icms_securityconfig_option_Object')) {
			return false;
		}
		$sql = sprintf("DELETE FROM %s WHERE sec_op_id = '%u'",
			icms::$xoopsDB->prefix('SecurityConfigOption'),
			(int) ($secoption->getVar('sec_op_id'))
			);
		if (!$result = icms::$xoopsDB->query($sql)) {
			return false;
		}
		return true;
	}

	/**
	 * Get some {@link icms_securityconfig_option_Object}s
	 *
	 * @param	object  $criteria   {@link icms_db_criteria_Element}
	 * @param	bool    $id_as_key  Use the IDs as array-keys?
	 *
	 * @return	array   Array of {@link icms_securityconfig_option_Object}s
	 */
	public function getObjects($criteria = null, $id_as_key = false) {
		$ret = array();
		$limit = $start = 0;
		$sql = 'SELECT * FROM ' . icms::$xoopsDB->prefix('SecurityConfigOption');
		if (isset($criteria) && is_subclass_of($criteria, 'icms_db_criteria_Element')) {
			$sql .= ' ' . $criteria->renderWhere() . ' ORDER BY sec_op_id ' . $criteria->getOrder();
			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		}
		$result = icms::$xoopsDB->query($sql, $limit, $start);
		if (!$result) return $ret;

		while ($myrow = icms::$xoopsDB->fetchArray($result)) {
			$secoption = new icms_securityconfig_option_Object();
			$secoption->assignVars($myrow);
			if (!$id_as_key) {
				$ret[] =& $secoption;
			} else {
				$ret[$myrow['sec_op_id']] =& $secoption;
			}
			unset($secoption);
		}
		return $ret;
	}
}