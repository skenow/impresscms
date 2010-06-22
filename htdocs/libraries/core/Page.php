<?php
/**
 * Classes responsible for managing core page objects
 *
 * @copyright	The ImpressCMS Project <http://www.impresscms.org/>
 * @license	LICENSE.txt
 * @package	core
 * @since	ImpressCMS 1.1
 * @author	modified by UnderDog <underdog@impresscms.org>
 * @author	Gustavo Pilla (aka nekro) <nekro@impresscms.org> <gpilla@nubee.com.ar>
 * @version	$Id: page.php 19450 2010-06-18 14:15:29Z malanciault $
 */

defined('ICMS_ROOT_PATH') or die('ImpressCMS root path not defined');

include_once ICMS_ROOT_PATH . '/kernel/icmspersistableobject.php';

/**
 * ImpressCMS page class.
 *
 * @since	ImpressCMS 1.2
 * @author	Gustavo Pilla (aka nekro) <nekro@impresscms.org> <gpilla@nubee.com.ar>
 */
class core_Page extends IcmsPersistableObject {

	public function __construct( & $handler ){

		$this->IcmsPersistableObject( $handler );

		$this->quickInitVar('page_id', XOBJ_DTYPE_INT);
        $this->quickInitVar('page_moduleid', XOBJ_DTYPE_INT, true);
       	$this->quickInitVar('page_title', XOBJ_DTYPE_TXTBOX, true);
        $this->quickInitVar('page_url', XOBJ_DTYPE_TXTBOX, true);
        $this->quickInitVar('page_status', XOBJ_DTYPE_INT, true);
	}
}
?>