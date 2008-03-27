<?php
// $Id: logger.php,v 1.7 2007/05/09 14:14:19 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
/**
 * Collects information for a page request
 *
 * <b>Singelton:</b> There can be only one instance of this class and it must
 * be accessed through the {@link instance()} method!
 *
 * records information about database queries, blocks, and execution time
 * and can display it as HTML
 *
 * @author Kazumi Ono
 * @author John Neill
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 */

/**
 * ZariliaLogger
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: logger.php,v 1.7 2007/05/09 14:14:19 catzwolf Exp $
 * @access public
 */
class ZariliaLogger {
    var $queries = array();
    var $blocks = array();
    var $extra = array();
    var $logstart = array();
    var $logend = array();
    var $errors = array();
    var $syserrors = array();
    /**
     */
    var $showErrors = false;
    var $showSql = false;
    var $showBlocks = false;
    var $showSmarty = false;
    var $showExtra = false;
    var $showPageGen = false;
    var $hasPermission = false;
    var $date = null;
    /**
     * constructor
     *
     * @access private
     */
    function ZariliaLogger()
    {
        error_reporting( E_ALL );
    }

    /**
     * get a reference to the only instance of this class
     *
     * @return object ZariliaLogger  reference to the only instance
     */
    function &instance()
    {
        static $instance;
        if ( !isset( $instance ) ) {
            $instance = new ZariliaLogger();
            set_error_handler( 'ZariliaErrorHandler_HandleError' );
        }
        return $instance;
    }

    /**
     * start a timer
     *
     * @param string $name name of the timer
     */
    function startTime( $name = 'ZARILIA' )
    {
        $this->logstart[$name] = explode( ' ', microtime() );
    }

    /**
     * stop a timer
     *
     * @param string $name name of the timer
     */
    function stopTime( $name = 'zarilia' )
    {
        $this->logend[$name] = explode( ' ', microtime() );
    }

    function startGatherStats()
    {
        global $zariliaOptions;

        if ( $zariliaOptions['gatherstats'] ) {
            $stat_array['stat_date'] = time();
            $parsed = parse_url( ZAR_URL );
            $request = isset( $parsed['scheme'] ) ? $parsed['scheme'] . '://' : 'http://';
            if ( isset( $parsed['host'] ) ) {
                $request .= isset( $parsed['port'] ) ? $parsed['host'] . ':' . $parsed['port'] : $parsed['host'];
            } else {
                $request .= zarilia_getenv( 'HTTP_HOST' );
            }
            $stat_array['stat_user_agent'] = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? $_SERVER['HTTP_USER_AGENT'] : "";
            $stat_array['stat_remote_addr'] = ( isset( $_SERVER['REMOTE_ADDR'] ) ) ? $_SERVER['REMOTE_ADDR'] : "";
            $stat_array['stat_http_referer'] = ( isset( $_SERVER['HTTP_REFERER'] ) ) ? $_SERVER['HTTP_REFERER'] : "";
            $stat_array['stat_request_uri'] = ( isset( $_SERVER['REQUEST_URI'] ) ) ? $_SERVER['REQUEST_URI'] : "";
            $stat_array['stat_unique'] = ( !$_SESSION['hit'] ) ? 1 : 0;
            $stat_array['stat_request_addon'] = ( is_object( $zariliaAddon ) ) ? $zariliaAddon->getVar( 'name' ) : $zariliaOption['pagetype'];

            if ( $zariliaConfig['my_ip'] != $_SERVER['REMOTE_ADDR'] ) {
                $stats_handler = &zarilia_gethandler( 'stats' );
                $stats_obj = $stats_handler->create();
                $stats_obj->setVars( $stat_array );
                unset( $stat_array );
                $stats_handler->insert( $stats_obj, false );
            }
        }
    }

    /**
     * log a database query
     *
     * @param string $sql SQL string
     * @param string $error error message (if any)
     * @param int $errno error number (if any)
     */
    function addQuery( $sql, $error = null, $errno = null )
    {
        if ( $this->showSql || $this->showPageGen ) {
            $this->queries[] = compact( 'sql', 'error', 'errno' );
        }
    }

    /**
     * log display of a block
     *
     * @param string $name name of the block
     * @param bool $cached was the block cached?
     * @param int $cachetime cachetime of the block
     */
    function addBlock( $name, $cached = false, $cachetime = 0 )
    {
        if ( $this->showBlocks ) {
            $this->blocks[] = compact( 'name', 'cached', 'cachetime' );
        }
    }

    /**
     * log extra information
     *
     * @param string $name name for the entry
     * @param int $cachetime cachetime for the entry
     */
    function addExtra( $name, $cachetime = 0 )
    {
        if ( $this->showExtra ) {
            $this->extra[] = compact( 'name', 'cachetime' );
        }
    }

    /**
     * ZariliaLogger::addUserError()
     *
     * @param mixed $errno
     * @param mixed $errstr
     * @param mixed $errfile
     * @param mixed $errline
     * @return
     */
    function setSysError( $errno, $errstr, $errfile = '', $errline = '', $errreport = '' )
    {
	if (is_array($errstr)) {
		foreach ($errstr as $err) {
		 	$this->setSysError($errno, $err, $errfile = '', $errline = '', $errreport = '');
		}
		return;
	}
        $this->sysErrors[] = compact( 'errno', 'errstr', 'errfile', 'errline', 'errreport' );
    }

    function getSysError()
    {
        return ( isset( $this->sysErrors ) && count( $this->sysErrors ) ) ? $this->sysErrors : array();
    }

    /*These are User error handling*/
    function getSysErrorCount()
    {
        return ( isset( $this->sysErrors ) && count( $this->sysErrors ) ) ? $this->sysErrors : 0;
    }

    function setSysErrorTitle( $value = '' )
    {
        $this->info['title'] = $value;
    }
    function setSysErrorHeading( $value = '' )
    {
        $this->info['heading'] = $value;
    }
    function setSysErrorDescription( $value = '' )
    {
        $this->info['description'] = $value;
    }
    function setSysErrorImage( $value = '' )
    {
        $this->info['image'] = $value;
    }

    function getSysInfo( $value = '', $default = '' )
    {
        if ( isset( $this->info[$value] ) ) {
            return $this->info[$value];
        } else {
            return $default;
        }
    }
    /**
     * ZariliaErrorHandler::setErrorLevel()
     *
     * @param mixed $showErrors
     * @return
     */
    function setErrorLevel( $errorType = E_ALL )
    {
		$errorType = E_ALL;
        error_reporting( $errorType );
    }

    /**
     * ZariliaLogger::getbugLevel()
     *
     * @param string $debugType
     * @return
     */
    function getbugLevel( $debugType = '' )
    {
        switch ( $debugType ) {
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

    /**
     * ZariliaLogger::setDebugmode()
     *
     * @return
     */
    function setDebugmode()
    {
        global $zariliaConfig, $zariliaUser, $user;
        /**
         * set groups that have permission
         */
        $this->_user_group = ( is_object( $zariliaUser ) ) ? $zariliaUser->getGroups() : array( 0 => ZAR_GROUP_ANONYMOUS );
		if (isset($zariliaConfig['debug_mode_okgrp'])) {
	        $this->hasPermission = ( array_intersect( $this->_user_group, $zariliaConfig['debug_mode_okgrp'] ) ) ? true : false;
		}

		if (isset($zariliaConfig['debug_mode'])) {
	        /**
		     * Setup who can view these
			 */
	        if ( !in_array( 0, $zariliaConfig['debug_mode'] ) ) {
		        // show errors
			    if ( in_array( 1, $zariliaConfig['debug_mode'] ) ) $this->showErrors = true;
				// show sql
	            if ( in_array( 2, $zariliaConfig['debug_mode'] ) ) $this->showSql = true;
		        // show blocks
			    if ( in_array( 3, $zariliaConfig['debug_mode'] ) ) $this->showBlocks = true;
	            // show smarty
		        if ( in_array( 4, $zariliaConfig['debug_mode'] ) ) $this->showSmarty = true;
			    // show extra
				if ( in_array( 5, $zariliaConfig['debug_mode'] ) ) $this->showExtra = true;
	            // show page
	            if ( in_array( 6, $zariliaConfig['debug_mode'] ) ) $this->showPageGen = true;
	        }
		    unset( $this->_user_group );
		}
    }
    /**
     * ZariliaLogger::handleError()
     *
     * @param mixed $errno
     * @param mixed $errstr
     * @param mixed $errfile
     * @param mixed $errline
     * @return
     */
    function handleError( $errno, $errstr, $errfile, $errline )
    {
        if ( $errno == 0 ) return;
        /**
         */
//        $errno = $errno &error_reporting();
		if (( $this->showErrors && $errno) || ($errno==E_USER_ERROR)) {
            $this->errors[] = compact( 'errno', 'errstr', 'errfile', 'errline' );
        }
        /**
         */
        if ( $errno == E_USER_ERROR ) {
            require_once ZAR_ROOT_PATH . '/class/logger_render.php';
            $log_render = new ZariliaLogger_render( $this );
            $log_render->trace();
        }
    }

    function dumpTime( $name = 'zarilia' )
    {
        if ( !isset( $this->logstart[$name] ) ) {
            return 0;
        }
        if ( !isset( $this->logend[$name] ) ) {
            $stop_time = explode( ' ', microtime() );
        } else {
            $stop_time = $this->logend[$name];
        }
        return ( ( float )$stop_time[1] + ( float )$stop_time[0] ) - ( ( float )$this->logstart[$name][1] + ( float )$this->logstart[$name][0] );
    }

    /**
     * ZariliaLogger::dump()
     *
     * @param mixed $display_type
     * @return
     */
    function render()
    {
        global $zariliaConfig;
        // Ok I had to hack this just now. Will do this a little better the next time. Maybe one day I will be a code God like Mith Pff
        if ( $this->showPageGen ) {
            echo '<p align="center">Page generated <span style="color:#ff0000;">' . count( $this->queries ) . '</span> queries - Generation time: <span style="color:#ff0000;">' . sprintf( "%.4f", $this->dumpTime( 'ZARILIA' ) ) . '</span> seconds</p>' . "\n";
        }
        if ( $this->hasPermission ) {
            require_once ZAR_ROOT_PATH . '/class/logger_render.php';
            $log_render = new ZariliaLogger_render( $this );
            $log_render->showError();
            $log_render->showSql();
            $log_render->showBlocks();
            $log_render->showExtra();
            $log_render->showTimers();
            $log_render->render();
        }

    }

    function sysRender( $errno = '', $errstr = '', $errfile = '', $errline = '', $title = '', $heading = '', $description = '', $image = '' )
    {
        $this->setSysError( $errno, $errstr, $errfile, $errline );

        require_once ZAR_ROOT_PATH . '/class/logger_render.php';
        $this->setSysErrorTitle( $title );
        $this->setSysErrorHeading( $heading );
        $this->setSysErrorDescription( $description );
        $this->setSysErrorImage( $image );
        $log_render = new ZariliaLogger_render( $this );
        $log_render->ShowUserError();
    }

    function doPageNotFound()
    {
        global $zariliaConfig;
        // FIX ME Catz We need to put this into template and make it easier to work with
        include_once ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/errorpage.php';
        $this->logDate = time();
        $this->logIPaddress = $_SERVER['REMOTE_ADDR'];

        $errno = ( isset( $_SERVER['REDIRECT_STATUS'] ) ) ? $_SERVER['REDIRECT_STATUS'] : '404';
        $errstr = sprintf( constant( '_ERR_TITLE_' . $errno ), "HTTP $errno -" );
		$strlen = strlen($_SERVER['REQUEST_URI']);
        if ( substr( $_SERVER['REQUEST_URI'], ( $strlen-9 ), 9 ) == 'index.php' ) {
            $_SERVER['REQUEST_URI'] = substr_replace( $_SERVER['REQUEST_URI'], '', ( $strlen-9 ), 9 );
        }
        $url_start = ( isset( $_SERVER['HTTPS'] ) ) ? 'http://' : 'https://';
        $errfile = $url_start . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $errfile .= " IP:" . $_SERVER['REMOTE_ADDR'] . " Browser:" . $_SERVER['HTTP_USER_AGENT'];

        $errreport = $errstr . " Error Report\r\n\r\nA " . $errno . " error was encountered by " . $_SERVER['REMOTE_ADDR'];
        $errreport .= " on $this->logDate.\r\n\r\n";
        $errreport .= "The URI which generated the error is: \nhttp://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\r\n\r\n";
        if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
            $errreport .= "The referring page was:\n" . @$_SERVER['HTTP_REFERER'] . "\r\n\r\n";
        }
        $errreport .= "The used client was:\n" . @$_SERVER['HTTP_USER_AGENT'] . "\r\n\r\n";

        $this->setSysError( $errno, $errstr, $errfile, '', trim( $errreport ) );
        /**
         */
        require_once ZAR_ROOT_PATH . '/class/logger_render.php';
        $log_render = new ZariliaLogger_render( $this );
        $log_render->doLog();
        $log_render->ShowPageNotFound();
    }
}
/**
 * Error handler
 */
function ZariliaErrorHandler_HandleError( $errNo, $errStr, $errFile, $errLine, $errContext = null )
{
    if ( $errNo != 2048 ) {
        $logger = &ZariliaLogger::instance();
        $logger->handleError( $errNo, $errStr, $errFile, $errLine, $errContext );
    }
}

?>