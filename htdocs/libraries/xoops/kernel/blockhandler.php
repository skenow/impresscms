<?php
/**
 * XoopsBlockHandler - For backwards compatibility
 *
 * @since XOOPS
 * @copyright The XOOPS Project <http://www.xoops.org>
 * @author The XOOPS Project Community <http://www.xoops.org>
 *
 * @see icms_core_BlockHandler
 *
 * @deprecated  use icms_core_BlockHandler instead
 * @todo Remove in version 1.4 - all instances have been removed from the core
 */
class XoopsBlockHandler extends icms_core_BlockHandler {

	public function __construct(&$db) {
		parent::__construct(&$db);
		$this->setVar('_errors', icms_deprecated('icms_core_BlockHandler'));
	}

}

