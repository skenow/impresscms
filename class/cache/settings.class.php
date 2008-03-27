<?php

if (defined('ZAR_ROOT_PATH')) { 
	require_once ZAR_ROOT_PATH.'/class/cache/cache.class.php';
} else {
	require_once 'cache.class.php';
}

class ZariliaSettings
	extends ZariliaCache {

	var $_settings = array();

	function &getInstance() {
		return parent::getInstance('settings');
	}

	function ZariliaSettings() {
		$this->ZariliaCache('settings');
	}

	function existsModule($module) {
		return file_exists($this->path.$module.'.php');
	}

	function existsCategory($module, $category) {
		if (!isset($this->_settings[$module])) $this->readAll($module);
		return isset($this->_settings[$module][$category]);
	}

	function read($module, $category, $setting, $default=null) {
		if (isset($this->_settings[$module][$category][$setting])) return $this->_settings[$module][$category][$setting];
		if (!file_exists($this->path.$module.'.php')) return $default;
		if (($i = strpos($data = parent::read($module), "\n"))!==false) {
			$this->_settings[$module] = eval(trim(substr($data,$i)));
		}
		if (!isset($this->_settings[$module][$category][$setting])) $this->write($module, $category, $this->_settings, $default);
		return $this->_settings[$module][$category][$setting];
	}

	function &readAll($module) {
		$this->_settings[$module] = eval((substr($data = parent::read($module),strpos($data, "\n"))));
		return $this->_settings[$module];
	}

	function &readCat($module, $category) {
		if (!isset($this->_settings[$module][$category])) {
			$this->_settings[$module] = eval((substr($data = parent::read($module),strpos($data, "\n"))));
		}
		return $this->_settings[$module][$category];
	}

	function write($module, $category, $setting, $value) {
		if (($setting === null)||($category===null)) return;
		$this->readAll($module);
		if (!isset($this->_settings[$module])) $this->_settings[$module] = array();
		if (!isset($this->_settings[$module][$category])) $this->_settings[$module][$category] = array();		
		$this->_settings[$module][$category][$setting] = $value;
//		echo "_$module, _$category, _$setting, _$value";
		parent::write($module, 'return '.var_export($this->_settings[$module], true).';');
//		die();
	}

	function remove($module, $category, $setting=null) {
		if ($setting!==null) {
			unset($this->_settings[$module][$category][$setting]);
		} else {
			unset($this->_settings[$module][$category]);
		}
		parent::write($module, 'return '.var_export($this->_settings[$module], true).';');
	}

	function copy($module, $newmodule) {
		return copy($this->path.$module.'.php', $this->path.$newmodule.'.php');
	}

	function delete($module) {
		return unlink($this->path.$module.'.php');
	}
}

?>