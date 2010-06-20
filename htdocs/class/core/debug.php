<?php
/**
 * A static class for debug and logging
 *
 * Using a static class instead of a include file with global functions, along with
 * autoloading of classes, reduces the memory usage and only includes files when needed.
 *
 * @category	Development
 * @package		Debug
 * @author		Steve Kenow <skenow@impresscms.org>
 * @copyright	(c) 2007-2008 The ImpressCMS Project - www.impresscms.org
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @version		SVN: $Id$
 * @since		1.3
 */

/**
 *
 */
class core_Debug {

	/* Since all the methods are static, there is no __construct necessary	 */

	/**
	 * Output a line of debug
	 * This takes the place of icms_debug()
	 *
	 * @param string $msg
	 * @param boolean $exit
	 */
	public static function message($msg, $exit = false) {
		echo "<div style='padding: 5px; color: red; font-weight: bold'>debug :: $msg</div>";
		if ($exit) {
			die();
		}
	}
	/**
 	 * Output a dump of a variable
 	 * This takes the place of icms_debug_vardump()
 	 *
 	 * @param string $var
 	 */
 	public static function vardump($var) {
 		if (class_exists('MyTextSanitizer')) {
			$myts = MyTextSanitizer::getInstance();
			self::message($myts->displayTarea(var_export($var, true)));
 		} else {
			$var = var_export($var, true);
			$var = preg_replace("/(\015\012)|(\015)|(\012)/", "<br />", $var);
			self::message($var);
 		}
 	}

 	/**
 	 * Provides a backtrace for deprecated methods and functions, will be in the error section of debug
 	 * This takes the place of icms_deprecated()
 	 *
 	 * @param string $replacement Method or function to be used instead of the deprecated method or function
 	 * @param string $extra Additional information to provide about the change
 	 */
 	public static function setDeprecated($replacement='', $extra='') {
		$trace = debug_backtrace();
		array_shift($trace);
		$level = $msg = $message = '';
		$pre = ' <strong><em>(Deprecated)</em></strong> - ';
		if ( $trace[0]['function'] != 'include' && $trace[0]['function'] != 'include_once' && $trace[0]['function'] != 'require' && $trace[0]['function'] != 'require_once') {
			$pre .= $trace[0]['function'] . ': ';
		}

		foreach ( $trace as $step ) {
		    $level .= '-';
			if ( isset($step['file'])) {
			    	$message .= $level . $msg
						. (isset( $step['class'] ) ? $step['class'] : '')
						. (isset( $step['type'] ) ? $step['type'] : '' )
						. $step['function'] . ' in ' . $step['file'] . ', line ' . $step['line']
						. '<br />';
			}
			$msg = 'Called by ';
		}

		trigger_error(
			$pre . ( $replacement ? ' <strong><em>use ' . $replacement . ' instead</em></strong>' : '' )
			. ( $extra ? ' <strong><em> ' . $extra . ' </em></strong>' : '' )
			. '<br />Call Stack: <br />' . $message
		, E_USER_NOTICE
		);
 	}
 }