<?php
/**
 * XoopsLogger component main class file
 *
 * See the enclosed file LICENSE for licensing information.
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Kazumi Ono  <onokazu@xoops.org>
 * @author		Skalpa Keo <skalpa@xoops.org>
 * @since		XOOPS
 * @package		core
 * @subpackage	XoopsLogger
 * @version		$Id: logger.php 19163 2010-04-28 14:37:42Z mekdrop $
 * @deprecated	Use icms_core_Logger instead
 */

/**
 * Collects information for a page request
 *
 * Records information about database queries, blocks, and execution time
 * and can display it as HTML. It also catches php runtime errors.
 * @package kernel
 * @deprecated Use icms_core_Logger instead
 * @todo Remove in version 1.4
 */
class XoopsLogger extends icms_core_Logger {
	private $_deprecated;
	public function __construct() {
		parent::__construct();
		$this->_deprecated = icms_deprecated('icms_core_Logger', 'This will be removed in version 1.4');
	}
}

/**
 * PHP Error handler
 *
 * NB: You're not supposed to call this function directly, if you dont understand why, then
 * you'd better spend some time reading your PHP manual before you hurt somebody
 *
 * @internal: Using a function and not calling the handler method directly coz old PHP versions
 * set_error_handler() have problems with the array( obj,methodname ) syntax
 * @deprecated
 * @todo Remove this - but replace with the appropriate function in libraries/icms/core/Logger.php
 */
function XoopsErrorHandler_HandleError( $errNo, $errStr, $errFile, $errLine, $errContext = null ) {
	$logger =& XoopsLogger::instance();
	$logger->handleError( $errNo, $errStr, $errFile, $errLine, $errContext );
}

