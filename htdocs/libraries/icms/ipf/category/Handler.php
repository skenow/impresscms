<?php
/**
 * Contains the basic classe for managing a category object based on icms_ipf_Object
 *
 * @copyright	The ImpressCMS Project http://www.impresscms.org/
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @package		icms_ipf_Object
 * @since		1.2
 * @author		marcan <marcan@impresscms.org>
 * @author	    Sina Asghari (aka stranger) <pesian_stranger@users.sourceforge.net>
 * @version		$Id: icmspersistablecategory.php 19627 2010-06-25 15:38:42Z malanciault $
 */

if (!defined("ICMS_ROOT_PATH")) {
	die("ImpressCMS root path not defined");
}
/** Make sure the IcmsPersistableOject class is loaded */
include_once ICMS_ROOT_PATH . "/kernel/icmspersistableseoobject.php";

/**
 * Provides data access mechanisms to the icms_ipf_category_Object object
 * @copyright 	The ImpressCMS Project http://www.impresscms.org/
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since 		1.1
 */
class icms_ipf_category_Handler extends icms_ipf_Handler {
	/** */
	var $allCategoriesObj = false;
	/** */
	var $_allCategoriesId = false;

	/**
	 * Constructor for the object handler
	 *
	 * @param object $db A database object
	 * @param string $modulename The directory name for the module
	 * @return icms_ipf_category_Handler
	 */
	function icms_ipf_category_Handler($db, $modulename) {
		$this->icms_ipf_Handler($db, 'category', 'categoryid', 'name', 'description', $modulename);
	}

	/**
	 * Return all categories in an array
	 *
	 * @param int $parentid
	 * @param string $perm_name
	 * @param string $sort
	 * @param string $order
	 * @return array
	 */
	function getAllCategoriesArray($parentid=0, $perm_name=false, $sort = 'parentid', $order='ASC') {

		if (!$this->allCategoriesObj) {
			$criteria = new icms_criteria_Compo();
			$criteria->setSort($sort);
			$criteria->setOrder($order);
			global $icmsUser;
			$userIsAdmin = is_object($icmsUser) && $icmsUser->isAdmin();

			if ($perm_name && !$userIsAdmin) {
				if (!$this->setGrantedObjectsCriteria($criteria, $perm_name)) {
					return false;
				}
			}

			$this->allCategoriesObj =& $this->getObjects($criteria, 'parentid');
		}

		$ret = array();
		if (isset($this->allCategoriesObj[$parentid])) {
			foreach($this->allCategoriesObj[$parentid] as $categoryid=>$categoryObj) {
				$ret[$categoryid]['self'] =& $categoryObj->toArray();
				if (isset($this->allCategoriesObj[$categoryid])) {
					$ret[$categoryid]['sub'] =& $this->getAllCategoriesArray($categoryid);
					$ret[$categoryid]['subcatscount'] = count($ret[$categoryid]['sub']);
				}
			}
		}
		return $ret;
	}

	function getParentIds($parentid, $asString=true) {

		if (!$this->allCategoriesId) {

			$ret = array();
			$sql = 'SELECT categoryid, parentid FROM '.$this->table . " AS " . $this->_itemname . ' ORDER BY parentid';

			$result = $this->db->query($sql);

			if (!$result) {
				return $ret;
			}

			while ($myrow = $this->db->fetchArray($result)) {
				$this->allCategoriesId[$myrow['categoryid']] =  $myrow['parentid'];
			}
		}

		$retArray = array($parentid);
		while ($parentid != 0) {
			$parentid = $this->allCategoriesId[$parentid];
			if ($parentid != 0) {
				$retArray[] = $parentid;
			}
		}
		if ($asString) {
			return implode(', ', $retArray);
		} else {
			return $retArray;
		}
	}
}

?>