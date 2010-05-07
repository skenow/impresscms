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
 * @version		$Id$
 */

/**
 * Collects information for a page request
 *
 * Records information about database queries, blocks, and execution time
 * and can display it as HTML. It also catches php runtime errors.
 * @package kernel
 */
class XoopsLogger {

	public $queries = array();
	public $blocks = array();
	public $extra = array();
	public $errors = array();

	public $usePopup = false;
	public $activated = true;

	/**@access protected*/
	private $renderingEnabled = false;
	var $showErrors = true;
	var $showSql = true;
	var $showBlocks = true;
	var $showSmarty = true;
	var $showExtra = true;
	var $showPageGen = true;
	var $hasPermission = true;
	var $date = null;

	/**
	 * Constructor
	 */
	private function __construct(){ /* Empty! */ }

	/**
	 * Get a reference to the only instance of this class
	 *
	 * @return  object XoopsLogger  (@link XoopsLogger) reference to the only instance
	 * @static
	 */
	static public function &instance() {
		static $instance;
		if ( !isset( $instance ) ) {
			$instance = new XoopsLogger();
			// Always catch errors, for security reasons
			set_error_handler( 'XoopsErrorHandler_HandleError' );
		}
		return $instance;
	}

	/**
	 * Enable logger output rendering
	 * When output rendering is enabled, the logger will insert its output within the page content.
	 * If the string <!--{xo-logger-output}--> is found in the page content, the logger output will
	 * replace it, otherwise it will be inserted after all the page output.
	 */
	public function enableRendering() {
		if ( !$this->renderingEnabled ) {
			ob_start( array( &$this, 'render' ) );
			$this->renderingEnabled = true;
		}
	}

	/**
	 * Disable logger output rendering.
	 */
	public function disableRendering() {
		if ( $this->renderingEnabled ) {
			$this->renderingEnabled = false;
		}
	}

	/**
	 * Disabling logger for some special occasion like AJAX requests and XML
	 *
	 * When the logger absolutely needs to be disabled whatever it is enabled or not in the preferences
	 * and whether user has permission or not to view it
	 */
	public function disableLogger() {
		$this->activated = false;
	}

	/**
	 * Log a database query
	 * @param   string  $sql    SQL string
	 * @param   string  $error  error message (if any)
	 * @param   int     $errno  error number (if any)
	 */
	function addQuery($sql, $error=null, $errno=null) {
		if ( $this->activated )		$this->queries[] = array('sql' => $sql, 'error' => $error, 'errno' => $errno);
		if (defined('ICMS_LOGGING_HOOK') and ICMS_LOGGING_HOOK != '') {
			include ICMS_LOGGING_HOOK;
		}
	}

	/**
	 * Log display of a block
	 * @param   string  $name       name of the block
	 * @param   bool    $cached     was the block cached?
	 * @param   int     $cachetime  cachetime of the block
	 */
	function addBlock($name, $cached = false, $cachetime = 0) {
		if ( $this->activated )
		$this->blocks[] = array('name' => $name, 'cached' => $cached, 'cachetime' => $cachetime);
	}

	/**
	 * Log extra information
	 * @param   string  $name       name for the entry
	 * @param   int     $msg  text message for the entry
	 */
	public function addExtra($name, $msg) {
		if ( $this->activated )
		$this->extra[] = array('name' => $name, 'msg' => $msg);
	}

	/**
	* Set an error that occurred within the system
	*
	* @param mixed $errno
	* @param mixed $errstr
	* @param mixed $errfile
	* @param mixed $errline
	* @param mixed $errreport
	*/
	function setSysError($errno, $errstr, $errfile = '', $errline = '', $errreport = '')
	{
		if (is_array($errstr))
		{
			foreach ($errstr as $err)
			{
				$this->setSysError($errno, $err, $errfile = '', $errline = '', $errreport = '');
			}
			return;
		}
		$this->sysErrors[] = compact('errno', 'errstr', 'errfile', 'errline', 'errreport');
	}

	/**
	* Get all the errors that occurred within the system
	*
	* @return array The errors that occurred
	*/
	function getSysError()
	{
		return (isset($this->sysErrors) && count($this->sysErrors)) ? $this->sysErrors : array();
	}


	/**
	 * Get the number of system errors that have occurred
	 */
	function getSysErrorCount()
	{
		return (isset($this->sysErrors) && count($this->sysErrors)) ? $this->sysErrors : 0;
	}

	/**
	* Set the title for the error page
	*
	* @param	string	The title to set
	*/
	function setSysErrorTitle($value = '')
	{
		$this->info['title'] = $value;
	}

	/**
	* Set the heading for the error page
	*
	* @param	string	The heading to set
	*/
	function setSysErrorHeading($value = '')
	{
		$this->info['heading'] = $value;
	}

	/**
	* Set the description for the error page
	*
	* @param	string	The error description to set
	*/
	function setSysErrorDescription($value = '')
	{
		$this->info['description'] = $value;
	}

	/**
	* Set the image for the error page
	*
	* @param	string	The image for the error page to set
	*/
	function setSysErrorImage($value = '')
	{
		$this->info['image'] = $value;
	}

	/**
	* Set the Error Level
	*
	* @param mixed $errorType
	*/
	function setErrorLevel($errorType = E_ALL)
	{
		$errorType = E_ALL;
		error_reporting($errorType);
	}

	/**
	* get the Debug level that was set in the system
	*
	* @param string $debugType
	* @return
	*/
	/*	To be implemented later
	function getbugLevel($debugType = '')
	{
		switch ($debugType)
		{
			case 'error':
			return $this->showErrors;
			break;
			case 'sql':
			return $this->showSql;
			break;
			case 'blocks':
			return $this->showBlocks;
			break;
			case 'smarty':
			return $this->showSmarty;
			break;
			case 'default':
			default:
			return false;
			break;
		} // switch
	}
	*/

	/**
	* set the Debug mode
	*/
	/*	To be implemented later
	function setDebugmode()
	{
		global $icmsConfig, $icmsUser, $user;

		/**
		* set groups that have permission
		*	/
		$this->_user_group = (is_object($icmsUser)) ? $icmsUser->getGroups() : array(0 => ICMS_GROUP_ANONYMOUS);
		if (isset($icmsConfig['debug_mode_okgrp']))
		{
			//(array_intersect($this->_user_group, $icmsConfig['debug_mode_okgrp'])) ? true : false
			$this->hasPermission = true;
		}

		if (isset($icmsConfig['debug_mode']))
		{
			/**
			* Setup who can view these
			*	/
			if (!in_array(0, $icmsConfig['debug_mode']))
			{
				// show errors
				if (in_array(1, $icmsConfig['debug_mode'])) $this->showErrors = true;
				// show sql
				if (in_array(2, $icmsConfig['debug_mode'])) $this->showSql = true;
				// show blocks
				if (in_array(3, $icmsConfig['debug_mode'])) $this->showBlocks = true;
				// show smarty
				if (in_array(4, $icmsConfig['debug_mode'])) $this->showSmarty = true;
				// show extra
				if (in_array(5, $icmsConfig['debug_mode'])) $this->showExtra = true;
				// show page
				if (in_array(6, $icmsConfig['debug_mode'])) $this->showPageGen = true;
			}
			unset($this->_user_group);
		}
	}
	*/

	/**
	* Handle the error that was sent by the error handler (trigger_error)
	*
	* @param mixed $errno
	* @param mixed $errstr
	* @param mixed $errfile
	* @param mixed $errline
	*/
	public function handleError($errno, $errstr, $errfile, $errline)
	{
		$errstr = $this->sanitizePath( $errstr );
		$errfile = $this->sanitizePath( $errfile );

		if ($errno == 0 || $errno == 8) return;
		/**
		*/
		//$errno = $errno &error_reporting();
		// NOTE: we only store relative pathnames
		if ( $this->activated && ( $errno & error_reporting() ) ) {
			$this->errors[] = compact('errno', 'errstr', 'errfile', 'errline');
		}
		/**
		*/
		if ($errno == E_USER_ERROR)
		{

			$trace = true;
			if ( substr( $errstr, 0, '8' ) == 'notrace:' ) {
				$trace = false;
				$errstr = substr( $errstr, 8 );
			}

			require_once ICMS_ROOT_PATH . '/class/icms_loggerrender.php';
			$log_render = new IcmsLogger_render($this);
			$log_render->trace();
		}
	}

	/**
	 * Error handling callback (called by the zend engine)
	 * @param  string  $errno
	 * @param  string  $errstr
	 * @param  string  $errfile
	 * @param  string  $errline
	 */
	public function OLDhandleError( $errno, $errstr, $errfile, $errline ) {
		$errstr = $this->sanitizePath( $errstr );
		$errfile = $this->sanitizePath( $errfile );
		if ( $this->activated && ( $errno & error_reporting() ) ) {
			// NOTE: we only store relative pathnames
			$this->errors[] = compact( 'errno', 'errstr', 'errfile', 'errline' );
		}

		if ( $errno == E_USER_ERROR ) {
			$trace = true;
			if ( substr( $errstr, 0, '8' ) == 'notrace:' ) {
				$trace = false;
				$errstr = substr( $errstr, 8 );
			}

			icms_loadLanguageFile('core', 'core');

			$errortext = sprintf(_CORE_PAGENOTDISPLAYED, $errstr);
			echo $errortext;
			if ( $trace && function_exists( 'debug_backtrace' ) ) {
				echo "<div style='color:#ffffff;background-color:#ffffff'>Backtrace:<br />";
				$trace = debug_backtrace();
				array_shift( $trace );
				foreach ( $trace as $step ) {
					if ( isset( $step['file'] ) ) {
						echo $this->sanitizePath( $step['file'] );
						echo ' (' . $step['line'] . ")\n<br />";
					}
				}
				echo '</div>';
			}
			exit();
		}
	}

	/**
	 * Sanitize path / url to file in erorr report
	 * @param  string  $path   path to sanitize
	 * @return string  $path   sanitized path
	 * @access protected
	 */
	function sanitizePath( $path ) {
		$path = str_replace(
		array( '\\', ICMS_ROOT_PATH, str_replace( '\\', '/', realpath( ICMS_ROOT_PATH ) ) ),
		array( '/', '', '' ),
		$path
		);
		return $path;
	}

	/**
	 * Output buffering callback inserting logger dump in page output
	 * Determines wheter output can be shown (based on permissions)
	 * @param  string  $output
	 * @return string  $output
	 */
	function render( $output ) {
		global $icmsUser,$icmsModule;
		$this->addExtra( 'Included files', count ( get_included_files() ) . ' files' );
		$this->addExtra( _CORE_MEMORYUSAGE, icms_conv_nr2local(icms_convert_size(memory_get_usage())) );
		$groups   = (is_object($icmsUser)) ? $icmsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
		$moduleid = (isset($icmsModule) && is_object($icmsModule)) ? $icmsModule->mid() : 1;
		$gperm_handler =& xoops_gethandler('groupperm');
		if ( !$this->renderingEnabled || !$this->activated || !$gperm_handler->checkRight('enable_debug', $moduleid, $groups) )
		return $output;
		$this->renderingEnabled = $this->activated = false;

		$log = $this->dump( $this->usePopup ? 'popup' : '' );
		$pattern = '<!--{xo-logger-output}-->';
		$icmsTimer = IcmsTimer::instance();
		$output .= "<div align='center'>Page generated
			<a href='javascript:xoSetLoggerView(\"queries\")'>" . sprintf(_ERR_PG_ERRORTOTALQUERIES, count($this->queries)) . "</a>
			-
			<a href='javascript:xoSetLoggerView(\"timers\")'>" . sprintf(_ERR_PG_ERRORLOAD, sprintf("%.3f", $icmsTimer->dumpTime('ImpressCMS'))) . "</a>
		</div>\n
		";

		$pos = strpos( $output, $pattern );
		if ( $pos !== false )
		return substr( $output, 0, $pos ) . $log . substr( $output, $pos + strlen( $pattern ) );
		else
		return $output . $log;
	}

	/**
	 * dump the logger output
	 *
	 * @param   string  $mode
	 * @return  string  $ret
	 * @access protected
	 */
	public function dump( $mode = '' ) {
		require_once ICMS_ROOT_PATH . '/class/icms_loggerrender.php';
		$renderedlog = '';
		$log_render = new IcmsLogger_render($this);
		$renderedlog = $log_render->render();

		return $renderedlog;
	}

	/**
	 * Will be deleted once it's clear it's not used elsewhere in ImpressCMS
	 *
	 * @param   string  $name   name of the counter
	 * @return  float   current execution time of the counter
	 */
	public function dumpTime( $name = 'ImpressCMS' ) {
		$icmsTimer = $GLOBALS['IcmsTimer'];
		return $icmsTimer->dumpTime($name);
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
 */
function XoopsErrorHandler_HandleError( $errNo, $errStr, $errFile, $errLine, $errContext = null ) {

	if ($errNo != 2048)
	{
		$logger =& XoopsLogger::instance();
		$logger->handleError($errNo, $errStr, $errFile, $errLine, $errContext);
	}
}

?>