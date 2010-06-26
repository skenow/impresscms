<?php
if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**
 * icms_ipf_view_Row class
 *
 * Class representing a single row of a icms_ipf_view_Single
 *
 * @package ImpressCMS Persistabke Framework
 * @author marcan <marcan@smartfactory.ca>
 * @link http://smartfactory.ca The SmartFactory
 */
class icms_ipf_view_Row {

	var $_keyname;
	var $_align;
	var $_customMethodForValue;
	var $_header;
	var $_class;

	function icms_ipf_view_Row($keyname, $customMethodForValue=false, $header=false, $class=false) {
		$this->_keyname = $keyname;
		$this->_customMethodForValue = $customMethodForValue;
		$this->_header = $header;
		$this->_class = $class;
	}

	function getKeyName() {
		return $this->_keyname;
	}

	function isHeader() {
		return $this->_header;
	}
}
?>