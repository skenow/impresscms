<?php
/**
 * Manage security configuration categories
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @category	ICMS
 * @package		SecurityConfig
 * @subpackage	Category
 * @author		Vaughan montgomery
 * @version		SVN: $Id:Handler.php 19775 2010-07-11 18:54:25Z malanciault $
 */

defined('ICMS_ROOT_PATH') or die("ImpressCMS root path not defined");

/**
 * Security Configuration category handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of configuration category class objects.
 *
 * @author  	Vaughan montgomery <vaughan@impresscms.org>
 * @category	ICMS
 * @package     SecurityConfig
 * @subpackage  Category
 */
class icms_securityconfig_category_Handler extends icms_core_ObjectHandler {

	/**
	 * Create a new category
	 *
	 * @param	bool    $isNew  Flag the new object as "new"?
	 *
	 * @return	object  New {@link icms_securityconfig_category_Object}
	 * @see htdocs/kernel/icms_core_ObjectHandler#create()
	 */
	public function &create($isNew = true)	{
		$sec_cat = new icms_securityconfig_category_Object();
		if ($isNew) {
			$sec_cat->setNew();
		}
		return $sec_cat;
	}

	/**
	 * Retrieve a {@link icms_securityconfig_category_Object}
	 *
	 * @param	int $id ConfigCategoryID to get
	 *
	 * @return	object|false  {@link icms_securityconfig_category_Object}, FALSE on fail
	 * @see htdocs/kernel/icms_core_ObjectHandler#get($int_id)
	 */
	public function &get($id) {
		$sec_cat = false;
		$id = (int) $id;
		if ($id > 0) {
			$sql = sprintf("SELECT * FROM %s WHERE sec_cat_id = '%u'",
				icms::$xoopsDB->prefix('SecurityConfigCategory'),
				$id
				);
			if (!$result = icms::$xoopsDB->query($sql)) return $sec_cat;

			$numrows = icms::$xoopsDB->getRowsNum($result);
			if ($numrows == 1) {
				$sec_cat = new icms_securityconfig_category_Object();
				$sec_cat->assignVars(icms::$xoopsDB->fetchArray($result), false);
			}
		}
		return $sec_cat;
	}

	/**
	 * Insert a {@link icms_securityconfig_category_Object} into the DataBase
	 *
	 * @param	object   &$sec_cat  {@link icms_securityconfig_category_Object}
	 *
	 * @return	bool    TRUE on success
	 * @see htdocs/kernel/icms_core_ObjectHandler#insert($object)
	 */
	public function insert(&$sec_cat) {
		if (!is_a($sec_cat, 'icmssecurityconfigcategory')) return false;
		if (!$sec_cat->isDirty()) return true;
		if (!$sec_cat->cleanVars()) return false;
		foreach ($sec_cat->cleanVars as $k => $v) {
			${$k} = $v;
		}
		if ($sec_cat->isNew()) {
			$sec_cat_id = icms::$xoopsDB->genId('SecurityConfigCategory_sec_cat_id_seq');
			$sql = sprintf("INSERT INTO %s (sec_cat_id, sec_cat_name, sec_cat_order)
				VALUES ('%u', %s, '%u')",
				icms::$xoopsDB->prefix('SecurityConfigCategory'),
				(int) $sec_cat_id,
				icms::$xoopsDB->quoteString($sec_cat_name),
				(int) $sec_cat_order
				);
		} else {
			$sql = sprintf("UPDATE %s SET sec_cat_name = %s, sec_cat_order = '%u'
				WHERE sec_cat_id = '%u'",
				icms::$xoopsDB->prefix('SecurityConfigCategory'),
				icms::$xoopsDB->quoteString($sec_cat_name),
				(int) $sec_cat_order,
				(int) $sec_cat_id
				);
		}
		if (!$result = icms::$xoopsDB->query($sql)) return false;
		if (empty($sec_cat_id)) {
			$sec_cat_id = icms::$xoopsDB->getInsertId();
		}
		$sec_cat->assignVar('sec_cat_id', $sec_cat_id);
		return $sec_cat_id;
	}

	/**
	 * Delelete a {@link icms_securityconfig_category_Object}
	 *
	 * @param	object  &$sec_cat   {@link icms_securityconfig_category_Object}
	 *
	 * @return	bool    TRUE on success
	 * @see htdocs/kernel/icms_core_ObjectHandler#delete($object)
	 */
	public function delete(&$sec_cat) {
		if (!is_a($sec_cat, 'icmssecurityconfigcategory')) return false;

		$sql = sprintf("DELETE FROM %s WHERE sec_cat_id = '%u'",
			icms::$xoopsDB->prefix('SecurityConfigCategory'),
			(int) $SecurityConfigCategory->getVar('sec_cat_id')
			);
		if (!$result = icms::$xoopsDB->query($sql)) return false;

		return true;
	}

	/**
	 * Get some {@link icms_securityconfig_category_Object}s
	 *
	 * @param	object  $criteria   {@link icms_db_criteria_Element}
	 * @param	bool    $id_as_key  Use the IDs as keys to the array?
	 *
	 * @return	array   Array of {@link icms_securityconfig_category_Object}s
	 */
	public function getObjects($criteria = null, $id_as_key = false) {
		$ret = array();
		$limit = $start = 0;
		$sql = 'SELECT * FROM ' . icms::$xoopsDB->prefix('SecurityConfigCategory');
		if (isset($criteria) && is_subclass_of($criteria, 'icms_db_criteria_Element')) {
			$sql .= ' '.$criteria->renderWhere();
			$sort = !in_array($criteria->getSort(), array('sec_cat_id', 'sec_cat_name', 'sec_cat_order'))
					? 'sec_cat_order'
					: $criteria->getSort();
			$sql .= ' ORDER BY ' . $sort . ' ' . $criteria->getOrder();
			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		}
		$result = icms::$xoopsDB->query($sql, $limit, $start);
		if (!$result) return $ret;
		
		while ($myrow = icms::$xoopsDB->fetchArray($result)) {
			$sec_cat = new icms_securityconfig_category_Object();
			$sec_cat->assignVars($myrow, false);
			if (!$id_as_key) {
				$ret[] =& $sec_cat;
			} else {
				$ret[$myrow['sec_cat_id']] =& $sec_cat;
			}
			unset($sec_cat);
		}
		return $ret;
	}
}

