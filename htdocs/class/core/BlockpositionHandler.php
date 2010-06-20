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
 * core_BlockpositionHandler
 *
 */
class core_BlockpositionHandler extends IcmsPersistableObjectHandler {

	/**
	 * Constructor
	 *
	 * @param IcmsDatabase $db
	 */
	public function __construct(& $db) {
		$this->IcmsPersistableObjectHandler($db, 'blockposition', 'id', 'title', 'description', 'icms');
		$this->className = 'core_Blockposition';
		$this->table = $this->db->prefix('block_positions');
	}

	/**
	 * Inserts block position into the database
	 *
	 * @param object  $obj  the block position object
	 * @param bool  $force  force the insertion of the object into the database
	 * @param bool  $checkObject  Check the object before insertion
	 * @param bool  $debug  turn on debug mode?
	 *
	 * @return bool  the result of the insert action
	 */
	public function insert(& $obj, $force = false, $checkObject = true, $debug=false){
		$obj->setVar('block_default', 0);
		$obj->setVar('block_type', 'L');
		return parent::insert( $obj, $force, $checkObject, $debug );
	}

}

/**
 * IcmsBlockpositionHandler
 * @deprecated Use core_BlockpositionHandler, instead
 * @todo Remove in version 1.4 - all instances have been removed from the core
 */
class IcmsBlockpositionHandler extends core_BlockpositionHandler {
	public function __construct() {
		parent::__construct(&$db);
		$this->setVar('_errors', icms_deprecated('core_BlockpositionHandler'));
	}
}
