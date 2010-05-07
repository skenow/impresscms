<?php
/**
* The Renderer functions of the Error logger
*
* @copyright	http://www.xoops.org/ The XOOPS Project
* @copyright	XOOPS_copyrights.txt
* @copyright	http://www.impresscms.org/ The ImpressCMS Project
* @license	LICENSE.txt
* @package	Core
* @since	XOOPS
* @author	http://www.xoops.org The XOOPS Project
* @author	modified by UnderDog <underdog@impresscms.org>
* @version	$Id: logger_render.php 9483 2009-11-04 20:07:54Z underdog $
*/

if (!defined('ICMS_ROOT_PATH'))
{
	die('(Make Beautiful funcion) ImpressCMS Root Path Not Defined in file '.__FILE__.' line '.__LINE__);
}
error_reporting(E_ALL);

if (isset($GLOBALS['icmsConfig']['language']))
{
	include_once ICMS_ROOT_PATH . '/language/' . $GLOBALS['icmsConfig']['language'] . '/error.php';
}
else
{
	include_once ICMS_ROOT_PATH . '/language/english/error.php';
}


include_once ICMS_ROOT_PATH . '/language/english/error.php';


if (!defined('E_RECOVERABLE_ERROR'))
{
	define('E_RECOVERABLE_ERROR', 4096);
}

if (!defined('E_STRICT'))
{
	define('E_STRICT', 2048);
}




class IcmsLogger_render
{
	var $output;
	var $_errors = array();
	var $oddeven = 'odd' ;
	/**
	*/
	var $dumpLog = false;
	var $mailLog = false;
	var $sqlLog = false;
	var $_developerdebug = false;

	var $errortype = array (
		E_ERROR => 'Error',
		E_WARNING => 'Warning',
		E_PARSE => 'Parsing Error',
		E_NOTICE => 'Notice',
		E_CORE_ERROR => 'Core Error',
		E_CORE_WARNING => 'Core Warning',
		E_COMPILE_ERROR => 'Compile Error',
		E_COMPILE_WARNING => 'Compile Warning',
		E_USER_ERROR => 'User Error',
		E_USER_WARNING => 'User Warning',
		E_USER_NOTICE => 'User Notice',
		E_RECOVERABLE_ERROR => 'Recoverable Error'
	);

	/**
	* IcmsLogger_render::icmsLogger_render()
	*/
	function IcmsLogger_render(&$obj)
	{
		$this->data = $obj;
	}

	/**
	* Show all the errors that occurred while loading the page in an ordered fashion
	*
	* @return string $ret
	*/
	function showError()
	{
		if(empty($mode))
		{
			$types = array(
				E_USER_NOTICE => _NOTICE,
				E_USER_WARNING => _WARNING,
				E_USER_ERROR => _ERROR,
				E_NOTICE => _NOTICE,
				E_WARNING => _WARNING,
				E_STRICT => _STRICT,
			);
			$oddeven = 'even';
			$ret = '';
			$ret .= '<table border="2" width="90%" align="center" cellpadding="2" cellspacing="1" id="xo-logger-errors" class="outer"><tr><th align="left"><tr><th>'._ERR_PG_ERRORS.'</th></tr>';
			foreach ( $this->data->errors as $error )
			{
				$oddeven = ( $oddeven == 'odd' ? 'even' : 'odd' ) ;
				$ret .= "\r\n<tr><td class='".$oddeven."'>\r\n";
				$ret .= isset( $types[ $error['errno'] ] ) ? $types[ $error['errno'] ] : _ERR_PG_ERRORUNKNOWN;
				$ret .= sprintf(_ERR_PG_ERRORFILELINES, $error['errstr'], $error['errfile'], $error['errline'] );
				$ret .= "</td></tr>";
			}
			$ret .= "\r\n</table>\r\n";
		}
		return $ret;
	}

	/**
	* Show all the SQL queries that were executed while loading the page
	*
	* @return string $ret
	*/
	function showSql()
	{
		$oddeven = 'even';
		$ret = '';
		$ret .= '<table border="2" width="90%" align="center" cellpadding="2" cellspacing="1" id="xo-logger-queries" class="outer"><tr><th align="left" colspan="2">' . _ERR_PG_ERRORQUERIES . '</th></tr>';
		foreach ($this->data->queries as $q)
		{
			//This is the info to be displayed : $sql, $error, $errno, $query_time, $calledfromfile, $calledfromline

			$oddeven = ( $oddeven == 'odd' ? 'even' : 'odd' );
			if (isset($q['error']))
			{
				$ret .= "
				<tr class=\"head\">\r\n
					<td class='".$oddeven."'>\r\n
						<div class=\"sqltrigger\" style=\"color:#FF0000;\">\r\n" . htmlentities($q['sql']) . "</div>\r\n
						<div class=\"sqlcontainer\" style=\"display:none;\">
							" . _ERR_PG_ERRORNUM . " " . $q['errno'] . "<BR />\r\n
							" . _ERR_PG_ERRORMESS . " " . $q['error'] . "<BR />\r\n
							Called FROM <strong>" .$q['calledfromfile'] ."</strong> Line <strong>". $q['calledfromline'] ."</strong>\r\n
						</div>
					</td>\r\n
					<td class='".$oddeven."' width=\"25%\">\r\n
						Click the SQL for more information
					</td>\r\n
				</tr>\r\n
				";
			}
			else
			{
				$ret .= "<tr>\r\n<td class='".$oddeven."' colspan='2'>\r\n" . htmlentities($q['sql']) . "</td>\r\n</tr>\r\n";
			}
		}
		$ret .= '<tr class="foot"><td>' . sprintf(_ERR_PG_ERRORTOTALQUERIES, count($this->data->queries)) . '</td></tr></table><br />';
		return $ret;
	}

	/**
	* Show all the blocks that were executed while loading the page
	*
	* @return string $ret
	*/
	function showBlocks()
	{
		$oddeven = 'even';
		$ret = '';
		$ret .= '<table border="2" width="90%" align="center" cellpadding="2" cellspacing="1" id="xo-logger-blocks" class="outer"><tr><th align="left" colspan="2">' . _ERR_PG_ERRORBLOCKS . '</th></tr>';
		foreach ($this->data->blocks as $b)
		{
			$oddeven = ( $oddeven == 'odd' ? 'even' : 'odd' );
			if ($b['cached'])
			{
				$ret .= '<tr><td width="30%" class="head">' . $b['name'] . ':</td><td class="even">' . sprintf(_ERR_PG_ERRORCACHED, $b['cachetime']) . '</td></tr>';
			}
			else
			{
				$ret .= '<tr><td class="head">' . $b['name'] . ':</td><td class="even">' . _ERR_PG_ERRORNOCACHE . '</td></tr>';
			}
		}
		$ret .= '<tr class="foot"><td colspan="2">' . sprintf(_ERR_PG_ERRORTBLOCKS, count($this->data->blocks)) . '</td></tr></table><br />';
		return $ret;
	}

	/**
	* Show all the extra information while loading the page
	*
	* @return string $ret
	*/
	function showExtra()
	{
		$oddeven = 'even';
		$ret = '';
		$ret .= '<table border="2" width="90%" align="center" cellpadding="2" cellspacing="1" id="xo-logger-extra" class="outer"><tr><th colspan="2">'._ERR_PG_ERRORTEXTRA.'</th></tr>';
		foreach ($this->data->extra as $ex)
		{
			$oddeven = ( $oddeven == 'odd' ? 'even' : 'odd' );
			$ret .= '<tr><td class="'.$oddeven.'"><strong>'.htmlspecialchars($ex['name']).':</strong> '.htmlspecialchars($ex['msg']).'</td></tr>';
			$class = ($class == 'odd') ? 'even' : 'odd';
		}
		$ret .= '</table>';
		return $ret;
	}

	/**
	* Show all the timer information while loading the page
	*
	* @return string $ret
	*/
	function showTimers()
	{
		$icmsTimer = IcmsTimer::instance();
		$oddeven = 'even';
		$ret = '';
		$ret .= '<table border="2" width="90%" align="center" cellpadding="2" cellspacing="1" id="xo-logger-timers" class="outer"><tr><th align="left" colspan="2"><a name="timers">' . _ERR_PG_ERRORTIMERS . '</a></th></tr>';
		foreach ($icmsTimer->logstart as $k => $v)
		{
			$oddeven = ($oddeven == 'odd') ? 'even' : 'odd';
			$ret .= "<tr><td width='30%' class='head'>" . sprintf(_ERR_PG_ERRORTO, htmlspecialchars($k)) . "</td><td class='".$oddeven."'>" . sprintf(_ERR_PG_ERRORLOAD, sprintf("%.03f", $icmsTimer->dumpTime($k))) . "</td></tr>";
		}
		$ret .= '</table><br />';
		return $ret;
	}

	/**
	* Render all the information while loading the page
	*
	* @return string The output
	*/
	function render()
	{
		$this->output = '';

		$this->output .= "\r\n<div id=\"xo-logger-output\">\r\n<div id='xo-logger-tabs'>\r\n";
		$this->output .= "<hr /><a href='javascript:xoSetLoggerView(\"none\")'>"._NONE."</a> | \r\n";
		$this->output .= "<a href='javascript:xoSetLoggerView(\"\")'>"._ALL."</a> | \r\n";
		$count = count( $this->data->errors );
		$this->output .= "<a href='javascript:xoSetLoggerView(\"errors\")'>"._ERRORS." (".icms_conv_nr2local($count).")</a>\r\n";
		$count = count( $this->data->queries );
		$this->output .= "<a href='javascript:xoSetLoggerView(\"queries\")'>"._QUERIES." (".icms_conv_nr2local($count).")</a>\r\n";
		$count = count( $this->data->blocks );
		$this->output .= "<a href='javascript:xoSetLoggerView(\"blocks\")'>"._BLOCKS." (".icms_conv_nr2local($count).")</a>\r\n";
		$count = count( $this->data->extra );
		$this->output .= "<a href='javascript:xoSetLoggerView(\"extra\")'>"._EXTRA." (".icms_conv_nr2local($count).")</a>\r\n";
		$count = count( $icmsTimer->logstart );
		$this->output .= "<a href='javascript:xoSetLoggerView(\"timers\")'>"._TIMERS." (".icms_conv_nr2local($count).")</a>\r\n";
		$this->output .= "</div>\r\n";

		$this->output .= $this->showError();
		$this->output .= $this->showSql();

		$this->output .= $this->showBlocks();
		$this->output .= $this->showTimers();

		$this->output .= $this->showExtra();

		$this->output .= "
		<script type=\"text/javascript\">
				function xoLogCreateCookie(name,value,days) {
					if (days) {
						var date = new Date();
						date.setTime(date.getTime()+(days*24*60*60*1000));
						var expires = '; expires='+date.toGMTString();
					}
					else var expires = '';
					document.cookie = name+'='+value+expires+'; path=/';
				}

				function xoLogReadCookie(name) {
					var nameEQ = name + '=';
					var ca = document.cookie.split(';');
					for(var i=0;i < ca.length;i++) {
						var c = ca[i];
						while (c.charAt(0)==' ') c = c.substring(1,c.length);
						if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
					}
					return null;
				}
				function xoLogEraseCookie(name) {
					createCookie(name,\"\",-1);
				}

			function xoSetLoggerView( name ) {
					var log = document.getElementById( 'xo-logger-output' );
					if ( !log ) return;
					var i, elt;
					for ( i=0; i!=log.childNodes.length; i++ ) {
						elt = log.childNodes[i];
						if ( elt.tagName && elt.tagName.toLowerCase() != 'script' && elt.id != 'xo-logger-tabs' ) {
							elt.style.display = ( !name || elt.id == 'xo-logger-' + name ) ? 'block' : 'none';
						}
					}
					xoLogCreateCookie( 'XOLOGGERVIEW', name, 1 );
				}
				xoSetLoggerView( xoLogReadCookie( 'XOLOGGERVIEW' ) );
		</script>";

		return $this->output;
	}

	/**
	* Display the user error in a nice template
	* This will become one of the most important error Handler Functions
	* It will show the error in a beautiful way
	*/
	function ShowUserError()
	{
		global $icmsConfig;

		include_once ICMS_ROOT_PATH.'/class/template.php';
		$image = icms_img_show('important', $this->data->getSysInfo('image', 'preference'), '', 'png', 'modules/system/images/system');
		$icmsTpl = new IcmsTpl(false);

		$icmsTpl->assign('icms_imageurl' , ICMS_THEME_URL . "/" . $icmsConfig['theme_set']);

		$icmsTpl->assign('error_title', $this->data->getSysInfo('title', _ER_PAGE_DEFAULT_TITLE));
		$icmsTpl->assign('error_heading' , $this->data->getSysInfo('heading', ''));
		$icmsTpl->assign('error_description', $this->data->getSysInfo('description', ''));
		$icmsTpl->assign('error_image', $image);
		$icmsTpl->assign('error_button', $this->data->getSysInfo('show_button', 1));

		$icmsTpl->assign('error_array', $this->data->sysErrors);

		$theme = (!isset($icmsConfig['theme_set'])) ? 'default' : $icmsConfig['theme_set'];
		$this->doLog($this->data->sysErrors);
		$icmsTpl->display(ICMS_MODULES_PATH . '/system/templates/system_errormessage.html');
	}

	/**
	* Display the page not found in a nice template
	*/
	function ShowPageNotFound()
	{
		global $icmsConfig, $icmsTpl;
		$status = $this->data->sysErrors[0]['errno'];
		$icmsTpl->assign('error_contact', $icmsConfig['adminmail']);
		$icmsTpl->assign('error_website', _ERR_SEARCH);
		$icmsTpl->assign('lang_error_title', sprintf(constant("_ERR_TITLE_$status"), $status));
		$icmsTpl->assign('lang_error_http_footer', sprintf(constant("_ERR_TITLE_$status"), "HTTP $status - "));
		$icmsTpl->assign('lang_error_desc', sprintf(constant("_ERR_TITLE_DESC_$status"), ''));
		$icmsTpl->assign('lang_error_info', sprintf(_ERR_TITLE_INFO, ICMS_URL));
		$icmsTpl->assign('lang_error_contact', sprintf(_ERR_CONTACT, $icmsConfig['adminmail']));
		$icmsTpl->assign('lang_error_search', _ERR_SEARCH);
		$icmsTpl->assign('error_description', constant("_ERR_TITLE_DESC_$status"));
		$this->doLog($this->data->sysErrors);
		$icmsTpl->display(ICMS_THEME_PATH . '/' . $icmsConfig['theme_set'] . '/modules/system/system_error_page.html');
	}

	/**
	* Display the fatal error in a nice template
	* Whenever a catchable fatal error has occurred this will be called
	*/
	function trace()
	{
		include ICMS_TRUST_PATH . '/errorconfig.php';
		require_once ICMS_ROOT_PATH . '/class/template.php';
		$icmsTpl = new IcmsTpl();
		/*Better way of doing this?*/
		$icmsTpl->assign('error_errno', $this->data->errors[0]['errno']);
		$icmsTpl->assign('error_errstr', $this->data->errors[0]['errstr']);
		$icmsTpl->assign('error_errfile', $this->sanitizePath($this->data->errors[0]['errfile']));
		$icmsTpl->assign('error_errline', $this->data->errors[0]['errline']);
		$icmsTpl->addCss(ICMS_THEME_URL . '/default/css/style.css');
		 
		$ret = '';
		if (function_exists('debug_backtrace') /*&& $dobacktrace*/)
		{
			$backtrace = debug_backtrace();
			$ret = '';
			// Used to be : echo "<div style='color:#ffffff;background-color:#ffffff'>Backtrace:<br />";
			$ret .= '&nbsp;&nbsp;<div><strong>' . _ERR_BACKTRACE . '</strong></div>' . chr(10);
			foreach ($backtrace as $bt)
			{
				$args = '';
				foreach ($bt['args'] as $a)
				{
					if (!empty($args))
					{
						$args .= ', ';
					}
					switch (gettype($a))
					{
						case 'integer':
						case 'double':
						$args .= $a;
						break;
						case 'string':
						$a = $this->sanitizePath($a);
						$args .= "\"$a\"";
						break;
						case 'array':
						$args .= 'Array(' . count($a) . ')';
						break;
						case 'object':
						$args .= 'Object(' . get_class($a) . ')';
						break;
						case 'resource':
						$args .= 'Resource(' . strstr($a, '#') . ')';
						break;
						case 'boolean':
						$args .= $a ? _ERR_PG_ERRORTRUE : _ERR_PG_ERRORFALSE;
						break;
						case 'NULL':
						$args .= _ERR_PG_ERRORNULL;
						break;
						default:
						$args .= _ERR_PG_ERRORUNKNOWN;
					}
				}
				if (isset($bt['file']) && $bt['line'])
				{
					$ret .= '&nbsp;&nbsp;&nbsp;<div><strong>' . _ERR_ERRORFILE . '</strong>  ' . $this->sanitizePath($bt['file']) . '  ' . _ERR_ERRORLINE . '  ' . $bt['line'] . '</div>' . chr(10);
				}
				if (isset($bt['class']))
				{
					$ret .= '&nbsp;&nbsp;&nbsp;<div><strong>' . _ERR_ERRORCALL . ':</strong> ' . $bt['class'] . ' ' . $bt['type'] . '</div>' . chr(10);
				}
				$ret .= '&nbsp;&nbsp;&nbsp;<div><strong>' . _ERR_ERRORFUNCTION . '</strong> ' . $bt['function'] . ' (' . strip_tags($args) . ')</div>';
			}
		}

		// For future version
		//$this->doLog($this->data->errors[0], $ret);
		// END for future version

		$icmsTpl->assign('error_errreport', $ret);
		if (file_exists(ICMS_THEME_PATH . '/' . $icmsConfig['theme_set'] . '/modules/system/system_fatalpage.html'))
		{
			$icmsTpl->display('file:' . ICMS_THEME_PATH . '/' . $icmsConfig['theme_set'] . '/modules/system/system_fatalpage.html');
		}
		else
		{
			var_dump($ret);
		}
		exit();
	}

}
?>