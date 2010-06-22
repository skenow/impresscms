<?php
/**
 * Manage configuration categories
 *
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @package		core
 * @subpackage	config
 * @since		XOOPS
 * @author		Kazumi Ono (aka onokazo)
 * @author		http://www.xoops.org The XOOPS Project
 * @version		$Id: ConfigcategoryHandler.php 19514 2010-06-21 22:50:14Z skenow $
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**
 * XOOPS configuration category handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS configuration category class objects.
 *
 * @author  Kazumi Ono <onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 *
 * @package     kernel
 * @subpackage  config
 */
class icms_core_ConfigcategoryHandler extends core_ObjectHandler {

	/**
	 * Create a new category
	 *
	 * @param	bool    $isNew  Flag the new object as "new"?
	 *
	 * @return	object  New {@link icms_core_Configcategory}
	 * @see htdocs/kernel/core_ObjectHandler#create()
	 */
	function &create($isNew = true) 	{
		$confcat = new icms_core_Configcategory();
		if ($isNew) {
			$confcat->setNew();
		}
		return $confcat;
	}

	/**
	 * Retrieve a {@link icms_core_Configcategory}
	 *
	 * @param	int $id ConfigCategoryID to get
	 *
	 * @return	object|false  {@link icms_core_Configcategory}, FALSE on fail
	 * @see htdocs/kernel/core_ObjectHandler#get($int_id)
	 */
	function &get($id) {
		$confcat = false;
		$id = (int) ($id);
		if ($id > 0) {
			$sql = "SELECT * FROM ".$this->db->prefix('configcategory')." WHERE confcat_id='".$id."'";
			if (!$result = $this->db->query($sql)) {
				return $confcat;
			}
			$numrows = $this->db->getRowsNum($result);
			if ($numrows == 1) {
				$confcat = new icms_core_Configcategory();
				$confcat->assignVars($this->db->fetchArray($result), false);
			}
		}
		return $confcat;
	}

	/**
	 * Insert a {@link icms_core_Configcategory} into the DataBase
	 *
	 * @param	object   &$confcat  {@link icms_core_Configcategory}
	 *
	 * @return	bool    TRUE on success
	 * @see htdocs/kernel/core_ObjectHandler#insert($object)
	 */
	function insert(&$confcat) {
		/**
		 * @TODO: Change to if (!(class_exists($this->className) && $obj instanceof $this->className)) when going fully PHP5
		 */
		if (!is_a($confcat, 'xoopsconfigcategory')) {
			return false;
		}
		if (!$confcat->isDirty()) {
			return true;
		}
		if (!$confcat->cleanVars()) {
			return false;
		}
		foreach ($confcat->cleanVars as $k => $v) {
			${$k} = $v;
		}
		if ($confcat->isNew()) {
			$confcat_id = $this->db->genId('configcategory_confcat_id_seq');
			$sql = sprintf("INSERT INTO %s (confcat_id, confcat_name, confcat_order) VALUES ('%u', %s, '%u')", $this->db->prefix('configcategory'), (int) ($confcat_id), $this->db->quoteString($confcat_name), (int) ($confcat_order));
		} else {
			$sql = sprintf("UPDATE %s SET confcat_name = %s, confcat_order = '%u' WHERE confcat_id = '%u'", $this->db->prefix('configcategory'), $this->db->quoteString($confcat_name), (int) ($confcat_order), (int) ($confcat_id));
		}
		if (!$result = $this->db->query($sql)) {
			return false;
		}
		if (empty($confcat_id)) {
			$confcat_id = $this->db->getInsertId();
		}
		$confcat->assignVar('confcat_id', $confcat_id);
		return $confcat_id;
	}

	/**
	 * Delelete a {@link icms_core_Configcategory}
	 *
	 * @param	object  &$confcat   {@link icms_core_Configcategory}
	 *
	 * @return	bool    TRUE on success
	 * @see htdocs/kernel/core_ObjectHandler#delete($object)
	 */
	function delete(&$confcat) {
		/**
		 * @TODO: Change to if (!(class_exists($this->className) && $obj instanceof $this->className)) when going fully PHP5
		 */
		if (!is_a($confcat, 'xoopsconfigcategory')) {
			return false;
		}

		$sql = sprintf("DELETE FROM %s WHERE confcat_id = '%u'", $this->db->prefix('configcategory'), (int) ($configcategory->getVar('confcat_id')));
		if (!$result = $this->db->query($sql)) {
			return false;
		}
		return true;
	}

	/**
	 * Get some {@link icms_core_Configcategory}s
	 *
	 * @param	object  $criteria   {@link core_CriteriaElement}
	 * @param	bool    $id_as_key  Use the IDs as keys to the array?
	 *
	 * @return	array   Array of {@link icms_core_Configcategory}s
	 */
	function getObjects($criteria = null, $id_as_key = false) {
		$ret = array();
		$limit = $start = 0;
		$sql = 'SELECT * FROM '.$this->db->prefix('configcategory');
		if (isset($criteria) && is_subclass_of($criteria, 'core_CriteriaElement')) {
			$sql .= ' '.$criteria->renderWhere();
			$sort = !in_array($criteria->getSort(), array('confcat_id', 'confcat_name', 'confcat_order')) ? 'confcat_order' : $criteria->getSort();
			$sql .= ' ORDER BY '.$sort.' '.$criteria->getOrder();
			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		}
		$result = $this->db->query($sql, $limit, $start);
		if (!$result) {
			return $ret;
		}
		while ($myrow = $this->db->fetchArray($result)) {
			$confcat = new icms_core_Configcategory();
			$confcat->assignVars($myrow, false);
			if (!$id_as_key) {
				$ret[] =& $confcat;
			} else {
				$ret[$myrow['confcat_id']] =& $confcat;
			}
			unset($confcat);
		}
		return $ret;
	}
}

/**
 * @deprecated	Use icms_core_ConfigcategoryHandler
 * @todo		Remove in version 1.4
 *
 */
class XoopsConfigCategoryHandler extends icms_core_ConfigcategoryHandler {
	public function __construct() {
		parent::__construct();
		$this->setVar('error', icms_deprecated('icms_core_ConfigcategoryHandler', 'This will be removed in version 1.4'));
	}
}
