<?php
/**
 * Manage configuration items
 *
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @package		core
 * @subpackage	config
 * @since		XOOPS
 * @author		Kazumi Ono (aka onokazo)
 * @author		http://www.xoops.org The XOOPS Project
 * @version		$Id: config.php 19431 2010-06-16 20:46:34Z david-sf $
 */

/**
 * XOOPS configuration handling class.
 * This class acts as an interface for handling general configurations of XOOPS
 * and its modules.
 *
 * @package 	kernel
 * @subpackage 	config
 * @author  Kazumi Ono <webmaster@myweb.ne.jp>
 * @todo    Tests that need to be made:
 *          - error handling
 * @access  public
 * @deprecated	Use icms_config_Handler, instead
 * @todo		Remove in version 1.4
 */
class XoopsConfigHandler extends icms_config_Handler {
	private $_deprecated;
	public function __construct() {
		parent::__construct();
		$this->_deprecated = icms_deprecated('icms_config_Handler', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}
}
