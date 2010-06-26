<?php
/**
 * icms_ipf_Object Table Listing
 *
 * Contains the classes responsible for displaying a highly configurable and features rich listing of IcmseristableObject objects
 *
 * @copyright	The ImpressCMS Project http://www.impresscms.org/
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @package		icms_ipf_Object
 * @since		1.1
 * @author		marcan <marcan@impresscms.org>
 * @version		$Id: icmspersistabletable.php 19623 2010-06-25 14:59:15Z malanciault $
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**
 * icms_ipf_view_Column class
 *
 * @copyright	The ImpressCMS Project http://www.impresscms.org/
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @package		icms_ipf_Object
 * @since		1.1
 * @author		marcan <marcan@impresscms.org>
 * @version		$Id: icmspersistabletable.php 19623 2010-06-25 14:59:15Z malanciault $
 */
class icms_ipf_view_Column {

	var $_keyname;
	var $_align;
	var $_width;
	var $_customMethodForValue;
	var $_extraParams;
	var $_sortable;
	var $_customCaption;

	function icms_ipf_view_Column($keyname, $align=_GLOBAL_LEFT, $width=false, $customMethodForValue=false, $param = false, $customCaption = false, $sortable = true) {
		$this->_keyname = $keyname;
		$this->_align = $align;
		$this->_width = $width;
		$this->_customMethodForValue = $customMethodForValue;
		$this->_sortable = $sortable;
		$this->_param = $param;
		$this->_customCaption = $customCaption;
	}

	function getKeyName() {
		return $this->_keyname;
	}

	function getAlign() {
		return $this->_align;
	}

	function isSortable() {
		return $this->_sortable;
	}

	function getWidth() {
		if ($this->_width) {
			$ret = $this->_width;
		} else {
			$ret = '';
		}
		return $ret;
	}

	function getCustomCaption() {
		return $this->_customCaption;
	}

}
?>