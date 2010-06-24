<?php
/**
 * XoopsBlock - for backwards compatibility
 *
 * @since XOOPS
 * @copyright The XOOPS Project <http://www.xoops.org>
 * @author The XOOPS Project Community <http://www.xoops.org>
 *
 * @see IcmsBlock
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

/**
 * XoopsBlockHandler - For backwards compatibility
 *
 * @since XOOPS
 * @copyright The XOOPS Project <http://www.xoops.org>
 * @author The XOOPS Project Community <http://www.xoops.org>
 *
 * @see IcmsBlockHandler
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
