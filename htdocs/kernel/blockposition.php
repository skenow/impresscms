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

include_once ICMS_ROOT_PATH . '/kernel/icmspersistableseoobject.php';

/**
 * IcmsBlockposition
 * @deprecated	Use icms_block_position_Object, instead
 * @todo		Remove in version 1.4
 */
class IcmsBlockposition extends icms_block_position_Object {
	private $_deprecated;
	/**
	 * Constructor
	 *
	 * @param IcmsBlockpositionHandler $handler
	 */
	public function __construct(& $handler) {
		parent::__construct($handler);
		$this->_deprecated = icms_deprecated('icms_block_position_Object', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}

}

/**
 * IcmsBlockpositionHandler
 * @deprecated	Use icms_block_position_Handler, instead
 * @todo		Remove in version 1.4 *
 */
class IcmsBlockpositionHandler extends icms_block_position_Handler {
	private $_deprecated;

	/**
	 * Constructor
	 *
	 * @param IcmsDatabase $db
	 */
	public function __construct(& $db) {
		parent::__construct($db);
		$this->_deprecated = icms_deprecated('icms_block_position_Handler', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}
}

?>