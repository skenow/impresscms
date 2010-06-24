<?php
/**
 * @copyright 	The XOOPS Project <http://www.xoops.org>
 * @license		GNU General Public License (GPL) <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 *
 * @since 		XOOPS
 * @author		The XOOPS Project Community <http://www.xoops.org>
 *
 * @deprecated use icms_core_Block instead
 * @todo Remove in version 1.4 - all instances have been removed from the core
 */
class XoopsBlock extends icms_core_Block {

	public function __construct(&$db) {
		parent::__construct(&$db);
		$this->setErrors = icms_deprecated('icms_core_Block');
	}

}

