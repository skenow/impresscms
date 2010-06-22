<?php
/**
 * Manage of modules
 *
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license	LICENSE.txt
 * @package	core
 * @since	XOOPS
 * @author	http://www.xoops.org The XOOPS Project
 * @author	modified by UnderDog <underdog@impresscms.org>
 * @version	$Id: module.php 19450 2010-06-18 14:15:29Z malanciault $
 */

if(!defined('ICMS_ROOT_PATH')){exit();}

/**
 * XOOPS module handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS module class objects.
 *
 * @package	kernel
 * @author	Kazumi Ono 	<onokazu@xoops.org>
 * @copyright	(c) 2000-2003 The Xoops Project - www.xoops.org
 **/
class icms_core_ModuleHandler extends core_ObjectHandler
{
	/**
	 * holds an array of cached module references, indexed by module id
	 *
	 * @var    array
	 * @access private
	 **/
	var $_cachedModule_mid = array();
	/**
	 * holds an array of cached module references, indexed by module dirname
	 *
	 * @var    array
	 * @access private
	 */
	var $_cachedModule_dirname = array();

	/**
	 * Create a new {@link icms_core_Module} object
	 *
	 * @param   boolean     $isNew   Flag the new object as "new"
	 * @return  object      {@link icms_core_Module}
	 **/
	function &create($isNew = true)
	{
		$module = new icms_core_Module();
		if($isNew) {$module->setNew();}
		return $module;
	}

	/**
	 * Load a module from the database
	 *
	 * @param  	int     $id     ID of the module
	 * @return	object  {@link icms_core_Module} FALSE on fail
	 **/
	function &get($id)
	{
		static $_cachedModule_dirname;
		static $_cachedModule_mid;
		$id = (int) ($id);
		$module = false;
		if($id > 0)
		{
			if(!empty( $_cachedModule_mid[$id]))
			{
				return $_cachedModule_mid [$id];
			}
			else
			{
				$sql = "SELECT * FROM ".$this->db->prefix('modules')." WHERE mid = '".$id."'";
				if(!$result = $this->db->query($sql)) {return $module;}
				$numrows = $this->db->getRowsNum($result);
				if($numrows == 1)
				{
					$module = new icms_core_Module();
					$myrow = $this->db->fetchArray($result);
					$module->assignVars($myrow);
					$_cachedModule_mid[$id] = & $module;
					$_cachedModule_dirname[$module->getVar('dirname')] = & $module;
					return $module;
				}
			}
		}
		return $module;
	}

	/**
	 * Load a module by its dirname
	 *
	 * @param	string    $dirname
	 * @return	object  {@link icms_core_Module} FALSE on fail
	 **/
	function &getByDirname($dirname)
	{
		static $_cachedModule_mid;
		static $_cachedModule_dirname;
		if(!empty( $_cachedModule_dirname[$dirname]) && $_cachedModule_dirname[$dirname]->dirname() == $dirname)
		{
			return $_cachedModule_dirname[$dirname];
		}
		else
		{
			$module = false;
			$sql = "SELECT * FROM ".$this->db->prefix('modules')." WHERE dirname = '".trim($dirname)."'";
			if(!$result = $this->db->query($sql)) {return $module;}
			$numrows = $this->db->getRowsNum($result);
			if($numrows == 1)
			{
				$module = new icms_core_Module();
				$myrow = $this->db->fetchArray($result);
				$module->assignVars($myrow);
				$_cachedModule_dirname[$dirname] = & $module;
				$_cachedModule_mid[$module->getVar('mid')] = & $module;
			}
			return $module;
		}
	}

	/**
	 * Inserts a module into the database
	 *
	 * @param   object  &$module reference to a {@link icms_core_Module}
	 * @return  bool
	 **/
	function insert(&$module)
	{
		if(strtolower(get_class($module)) != 'xoopsmodule') {return false;}
		if(!$module->isDirty()) {return true;}
		if(!$module->cleanVars()) {return false;}

		/**
		 * Editing the insert and update methods
		 * this is temporaray as will soon be based on a persistableObjectHandler
		 **/
		$fieldsToStoreInDB = array();
		foreach($module->cleanVars as $k => $v)
		{
			if($k == 'last_update') {$v = time();}
			if($module->vars[$k]['data_type'] == XOBJ_DTYPE_INT)
			{
				$cleanvars[$k] = (int) ($v);
			}
			elseif(is_array($v))
			{
				$cleanvars[$k] = $this->db->quoteString(implode(',', $v));
			}
			else
			{
				$cleanvars[$k] = $this->db->quoteString($v);
			}
			$fieldsToStoreInDB[$k] = $cleanvars[$k];
		}

		if($module->isNew())
		{
			$sql = "INSERT INTO ".$this->db->prefix('modules')." (".implode(',', array_keys($fieldsToStoreInDB)).") VALUES (".implode(',', array_values($fieldsToStoreInDB)).")";
		}
		else
		{
			$sql = "UPDATE ".$this->db->prefix('modules')." SET";
			foreach($fieldsToStoreInDB as $key => $value)
			{
				if(isset($notfirst)) {$sql .= ",";}
				$sql .= " ".$key." = ".$value;
				$notfirst = true;
			}
			$whereclause = 'mid'." = ".$module->getVar('mid');
			$sql .= " WHERE ".$whereclause;
		}

		if(!$result = $this->db->query($sql)) {return false;}
		if($module->isNew()) {$module->assignVar('mid', $this->db->getInsertId());}
		if(!empty($this->_cachedModule_dirname[$module->getVar('dirname')])) {unset($this->_cachedModule_dirname[$module->getVar('dirname')]);}
		if(!empty($this->_cachedModule_mid[$module->getVar('mid')])) {unset($this->_cachedModule_mid[$module->getVar('mid')]);}
		return true;
	}

	/**
	 * Delete a module from the database
	 *
	 * @param   object  &$module {@link icms_core_Module}
	 * @return  bool
	 **/
	function delete(&$module) {
		if(strtolower(get_class($module)) != 'xoopsmodule') {return false;}

		$sql = sprintf("DELETE FROM %s WHERE mid = '%u'", $this->db->prefix('modules'), (int) ($module->getVar('mid')));
		if(!$result = $this->db->query($sql )) {return false;}

		// delete admin permissions assigned for this module
		$sql = sprintf("DELETE FROM %s WHERE gperm_name = 'module_admin' AND gperm_itemid = '%u'", $this->db->prefix('group_permission'), (int) ($module->getVar ('mid')));
		$this->db->query($sql);
		// delete read permissions assigned for this module
		$sql = sprintf("DELETE FROM %s WHERE gperm_name = 'module_read' AND gperm_itemid = '%u'", $this->db->prefix('group_permission'), (int) ($module->getVar ('mid')));
		$this->db->query($sql);

		$sql = sprintf("SELECT block_id FROM %s WHERE module_id = '%u'", $this->db->prefix('block_module_link'), (int) ($module->getVar('mid')));
		if($result = $this->db->query($sql))
		{
			$block_id_arr = array();
			while($myrow = $this->db->fetchArray($result)) {array_push($block_id_arr, $myrow['block_id']);}
		}

		// loop through block_id_arr
		if(isset($block_id_arr))
		{
			foreach($block_id_arr as $i)
			{
				$sql = sprintf("SELECT block_id FROM %s WHERE module_id != '%u' AND block_id = '%u'", $this->db->prefix('block_module_link'), (int) ($module->getVar('mid')), (int) ($i));
				if($result2 = $this->db->query($sql))
				{
					if(0 < $this->db->getRowsNum($result2))
					{
						// this block has other entries, so delete the entry for this module
						$sql = sprintf("DELETE FROM %s WHERE (module_id = '%u') AND (block_id = '%u')", $this->db->prefix('block_module_link'), (int) ($module->getVar('mid')), (int) ($i));
						$this->db->query($sql);
					}
					else
					{
						// this block doesnt have other entries, so disable the block and let it show on top page only. otherwise, this block will not display anymore on block admin page!
						$sql = sprintf("UPDATE %s SET visible = '0' WHERE bid = '%u'", $this->db->prefix('newblocks'), (int) ($i));
						$this->db->query($sql);
						$sql = sprintf("UPDATE %s SET module_id = '-1' WHERE module_id = '%u'", $this->db->prefix('block_module_link'), (int) ($module->getVar('mid')));
						$this->db->query($sql);
					}
				}
			}
		}

		if(!empty($this->_cachedModule_dirname[$module->getVar('dirname')])) {unset($this->_cachedModule_dirname[$module->getVar('dirname')]);}
		if(!empty($this->_cachedModule_mid[$module->getVar('mid')])) {unset($this->_cachedModule_mid[$module->getVar('mid')]);}
		return true;
	}

	/**
	 * Load some modules
	 *
	 * @param   object  $criteria   {@link icms_core_CriteriaElement}
	 * @param   boolean $id_as_key  Use the ID as key into the array
	 * @return  array
	 **/
	function getObjects($criteria = null, $id_as_key = false)
	{
		$ret = array();
		$limit = $start = 0;
		$sql = "SELECT * FROM ".$this->db->prefix('modules');
		if(isset($criteria) && is_subclass_of($criteria, 'icms_core_CriteriaElement'))
		{
			$sql .= " ".$criteria->renderWhere();
			$sql .= " ORDER BY weight ".$criteria->getOrder().", mid ASC";
			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		}
		$result = $this->db->query($sql, $limit, $start);
		if(!$result) {return $ret;}
		while($myrow = $this->db->fetchArray($result))
		{
			$module = new icms_core_Module();
			$module->assignVars($myrow);
			if(!$id_as_key)
			{
				$ret[] = & $module;
			}
			else
			{
				$ret[$myrow['mid']] = & $module;
			}
			unset($module);
		}
		return $ret;
	}

	/**
	 * Count some modules
	 *
	 * @param   object  $criteria   {@link icms_core_CriteriaElement}
	 * @return  int
	 **/
	function getCount($criteria = null)
	{
		$sql = "SELECT COUNT(*) FROM ".$this->db->prefix('modules');
		if(isset($criteria) && is_subclass_of($criteria, 'icms_core_CriteriaElement')) {$sql .= " ".$criteria->renderWhere();}
		if(!$result = $this->db->query($sql)) {return 0;}
		list($count) = $this->db->fetchRow($result);
		return $count;
	}

	/**
	 * returns an array of module names
	 *
	 * @param   bool    $criteria
	 * @param   boolean $dirname_as_key
	 *      if true, array keys will be module directory names
	 *      if false, array keys will be module id
	 * @return  array
	 **/
	function getList($criteria = null, $dirname_as_key = false)
	{
		$ret = array();
		$modules = & $this->getObjects($criteria, true);
		foreach(array_keys($modules) as $i)
		{
			if(!$dirname_as_key)
			{
				$ret[$i] = $modules[$i]->getVar('name');
			}
			else
			{
				$ret[$modules[$i]->getVar('dirname')] = $modules[$i]->getVar('name');
			}
		}
		return $ret;
	}
}

?>