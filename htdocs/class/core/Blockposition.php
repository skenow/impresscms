<?php
/**
 * Block Positions manager for the Impress Persistable Framework
 *
 * Longer description about this page
 *
 * @copyright      http://www.impresscms.org/ The ImpressCMS Project
 * @license         LICENSE.txt
 * @package	core
 * @since            1.0
 * @version		$Id: blockposition.php 19118 2010-03-27 17:46:23Z skenow $
 */

defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

/**
 * core_Blockposition
 *
 */
class core_Blockposition extends IcmsPersistableObject {

	/**
	 * Constructor
	 *
	 * @param core_BlockpositionHandler $handler
	 */
	public function __construct(& $handler) {

		$this->IcmsPersistableObject($handler);

		$this->quickInitVar('id', XOBJ_DTYPE_INT);
		$this->quickInitVar('pname', XOBJ_DTYPE_TXTBOX, true);
		$this->quickInitVar('title', XOBJ_DTYPE_TXTBOX, true);
		$this->quickInitVar('description', XOBJ_DTYPE_TXTAREA);
		$this->quickInitVar('block_default', XOBJ_DTYPE_INT);
		$this->quickInitVar('block_type', XOBJ_DTYPE_TXTBOX);

	}

}

/**
 * IcmsBlockposition
 * @deprecated Use core_Blockposition, instead
 * @todo Remove in version 1.4 - all instances have been removed from the core
 */
class IcmsBlockposition extends core_Blockposition {
	public function __construct() {
		parent::__construct(&$handler);
		$this->setErrors(icms_deprecated('core_Blockposition'));
	}

}