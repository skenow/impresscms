<?php
// $Id: logger_render.php,v 1.3 2007/05/09 14:14:19 catzwolf Exp $
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
 * ZariliaLogger_render
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: logger_render.php,v 1.3 2007/05/09 14:14:19 catzwolf Exp $
 * @access public
 */
if ( isset( $GLOBALS['zariliaConfig']['language'] ) ) {
    include_once ZAR_ROOT_PATH . '/language/' . $GLOBALS['zariliaConfig']['language'] . '/error.php';
} else {
    include_once ZAR_ROOT_PATH . '/language/english/error.php';
}

if (!defined('E_RECOVERABLE_ERROR')) {
	define('E_RECOVERABLE_ERROR', 4096);
}

if (!defined('E_STRICT')) {
	define('E_STRICT', 2048);
}

class ZariliaLogger_render {
    var $output;
    var $_errors = array();
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
     * ZariliaLogger_render::ZariliaLogger_render()
     */
    function ZariliaLogger_render( &$obj ) {
        $this->data = $obj;
    }

    /**
     * ZariliaLogger_render::showError()
     *
     * @param mixed $obj
     * @return
     */
    function showError() {
        if ( !$this->data->showErrors || empty( $this->data->errors ) ) {
            return;
        }
        $ret = '<table align="center" width="90%" cellpadding="2" cellspacing="1"><tr><th align="left">' . _ERR_PG_ERRORS . '</th></tr>';
        foreach ( $this->data->errors as $error ) {
            $ret .= '<tr><td class="even">';
            $ret .= isset( $this->errortype[ $error['errno'] ] ) ? $this->errortype[ $error['errno'] ] : _ERR_PG_ERRORUNKNOWN;
            $ret .= sprintf( _ERR_PG_ERRORFILELINES, $error['errstr'], $this->cleanPath( $error['errfile'] ), $error['errline'] );
            $ret .= '</td></tr>';
            $this->doLog( $error, null );
        }
        $ret .= "\n</table>\n<br />";
        $this->output .= $ret;
    }

    /**
     * ZariliaLogger_render::showSql()
     *
     * @return
     */
    function showSql() {
        if ( !$this->data->showSql || empty( $this->data->queries ) ) {
            return;
        }
        $ret = '<table align="center" width="90%" cellpadding="2" cellspacing="1"><tr><th align="left">' . _ERR_PG_ERRORQUERIES . '</th></tr>';
        foreach ( $this->data->queries as $q ) {
            if ( isset( $q['error'] ) ) {
                $ret .= '<tr class="head"><td width="30%"><span style="color:#ff0000;">' . htmlentities( $q['sql'] ) . '<br /><b>' . _ERR_PG_ERRORNUM . '</b> ' . $q['errno'] . '<br /><b>' . _ERR_PG_ERRORMESS . '</b> ' . $q['error'] . '</span></td></tr>';
            } else {
                $ret .= '<tr class="even"><td>' . htmlentities( $q['sql'] ) . '</td></tr>';
            }
        }
        $ret .= '<tr class="foot"><td>' . sprintf( _ERR_PG_ERRORTOTALQUERIES, count( $this->data->queries ) ) . '</td></tr></table><br />';
        $this->output .= $ret;
    }

    function showBlocks() {
        if ( !$this->data->showBlocks || empty( $this->data->blocks ) ) {
            return;
        }
        $ret = '<table align="center" width="90%" cellpadding="2" cellspacing="1"><tr><th align="left">' . _ERR_PG_ERRORBLOCKS . '</th></tr>';
        foreach ( $this->data->blocks as $b ) {
            if ( $b['cached'] ) {
                $ret .= '<tr><td width="30%" class="head">' . $b['name'] . ':</td><td class="even">' . sprintf( _ERR_PG_ERRORCACHED, $b['cachetime'] ) . '</td></tr>';
            } else {
                $ret .= '<tr><td class="head">' . $b['name'] . ':</td><td class="even">' . _ERR_PG_ERRORNOCACHE . '</td></tr>';
            }
        }
        $ret .= '<tr class="foot"><td colspan="2">' . sprintf( _ERR_PG_ERRORTBLOCKS, count( $this->data->blocks ) ) . '</td></tr></table><br />';
        $this->output .= $ret;
    }

    function showExtra() {
        if ( !$this->data->showExtra ) {
            return;
        }
        $this->data->addExtra( 'Included files', sprintf( _ERR_PG_ERRORCFILES, count ( get_included_files() ) ) );
        $memory = 0;
        if ( function_exists( 'memory_get_usage' ) ) {
            $memory = memory_get_usage() . _ERR_PG_ERRORBYTES;
        } else {
            $os = isset( $_ENV['OS'] ) ? $_ENV['OS'] : $_SERVER['OS'];
            if ( strpos( strtolower( $os ), 'windows' ) !== false ) {
                $out = array();
                exec( 'tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $out );
                $memory = substr( $out[5], strpos( $out[5], ':' ) + 1 ) . _ERR_PG_ERRORESTIMANTED;
            }
        }
        if ( $memory ) {
            $this->data->addExtra( _ERR_PG_ERRORMENUSAGE, $memory );
        }
        $ret = '<table align="center" width="90%" cellpadding="2" cellspacing="1"><tr><th align="left">' . _ERR_PG_ERRORTEXTRA . '</th></tr>';
        foreach ( $this->data->extra as $ex ) {
            $ret .= '<tr><td width="30%" class="head">' . $ex['name'] . ':</td><td class="even">' . $ex['cachetime'] . '</td></tr>';
        }
        $ret .= '</table><br />';
        $this->output .= $ret;
    }

    /**
     * ZariliaLogger_render::showTimers()
     *
     * @return
     */
    function showTimers() {
        if ( !$this->data->showExtra || empty( $this->data->logstart ) ) {
            return;
        }
        $ret .= '<table align="center" width="90%" cellpadding="2" cellspacing="1"><tr><th align="left">' . _ERR_PG_ERRORTIMERS . '</th></tr>';
        foreach ( $this->data->logstart as $k => $v ) {
            $ret .= '<tr><td width="30%" class="head">' . sprintf( _ERR_PG_ERRORTO, htmlspecialchars( $k ) ) . '</td><td class="even">' . sprintf( _ERR_PG_ERRORLOAD, sprintf( "%.03f", $this->data->dumpTime( $k ) ) ) . '</td></tr>';
        }
        $ret .= '</table><br />';
        $this->output .= $ret;
    }

    /**
     * ZariliaLogger_render::trace()
     *
     * @return
     */
    function trace() {
        include ZAR_ROOT_PATH . '/errorconfig.php';
        require_once ZAR_ROOT_PATH . '/class/template.php';
        $zariliaTpl = new ZariliaTpl();
        /*Better way of doing this?*/
        $zariliaTpl->assign( 'error_errno', $this->data->errors[0]['errno'] );
        $zariliaTpl->assign( 'error_errstr', $this->data->errors[0]['errstr'] );
        $zariliaTpl->assign( 'error_errfile', $this->cleanPath( $this->data->errors[0]['errfile'] ) );
        $zariliaTpl->assign( 'error_errline', $this->data->errors[0]['errline'] );
        $zariliaTpl->addCss( ZAR_THEME_URL . '/default/css/style.css' );

		$ret = '';
        if ( function_exists( 'debug_backtrace' ) /*&& $dobacktrace*/ ) {
            $backtrace = debug_backtrace();
            if ( !$this->_developerdebug ) {
                $ret = '<div><strong>' . _ERR_BACKTRACE . '</strong></div>' . chr( 10 );
                $ret .= '<div>';
                array_shift( $backtrace );
                foreach ( $backtrace as $step ) {
                    if ( isset( $step['file'] ) ) {
                        $ret .= '<strong>' . _ERR_ERRORFILE . '</strong> ' . $this->cleanPath( $step['file'] ) . ' ' . _ERR_ERRORLINE . ' ' . $step['line'] . ' ' . chr( 10 ) . '<br />';
                    }
                }
                $ret .= '</div>';
            } else {
                $ret = '';
                $ret .= '&nbsp;&nbsp;<div><strong>' . _ERR_BACKTRACE . '</strong></div>' . chr( 10 );
                foreach ( $backtrace as $bt ) {
                    $args = '';
                    foreach ( $bt['args'] as $a ) {
                        if ( !empty( $args ) ) {
                            $args .= ', ';
                        }
                        switch ( gettype( $a ) ) {
                            case 'integer':
                            case 'double':
                                $args .= $a;
                                break;
                            case 'string':
                                $a = $this->cleanPath( $a );
                                $args .= "\"$a\"";
                                break;
                            case 'array':
                                $args .= 'Array(' . count( $a ) . ')';
                                break;
                            case 'object':
                                $args .= 'Object(' . get_class( $a ) . ')';
                                break;
                            case 'resource':
                                $args .= 'Resource(' . strstr( $a, '#' ) . ')';
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
                    if ( isset( $bt['file'] ) && $bt['line'] ) {
                        $ret .= '&nbsp;&nbsp;&nbsp;<div><strong>' . _ERR_ERRORFILE . '</strong>  ' . $this->cleanPath( $bt['file'] ) . '  ' . _ERR_ERRORLINE . '  ' . $bt['line'] . '</div>' . chr( 10 );
                    }
                    if ( isset( $bt['class'] ) ) {
                        $ret .= '&nbsp;&nbsp;&nbsp;<div><strong>' . _ERR_ERRORCALL . ':</strong> ' . $bt['class'] . ' ' . $bt['type'] . '</div>' . chr( 10 );
                    }
                    $ret .= '&nbsp;&nbsp;&nbsp;<div><strong>' . _ERR_ERRORFUNCTION . '</strong> ' . $bt['function'] . ' ( ' . strip_tags( $args ) . ' )</div>';
                }
            }
        }
        $this->doLog( $this->data->errors[0], $ret );
        $zariliaTpl->assign( 'error_errreport', $ret );
        $theme = ( !isset( $zariliaConfig['theme_set'] ) ) ? 'default' : $zariliaConfig['theme_set'];
        $zariliaTpl->addCss( ZAR_URL . '/themes/' . $theme . '/css/style.css' );
	if (file_exists(ZAR_THEME_PATH . '/default/addons/system/system_fatalpage.tpl')) {
		$zariliaTpl->display( 'file:' . ZAR_THEME_PATH . '/default/addons/system/system_fatalpage.tpl' );
	} else {
		var_dump($ret);
        }
        exit();
    }

    /**
     * ZariliaLogger_render::doLog()
     *
     * @return
     */
    function doLog() {
        include ZAR_ROOT_PATH . '/errorconfig.php';
        if ( func_num_args() == 2 ) {
            $arg = func_get_args();
            if ( in_array( $arg[0]['errno'], $logtypes ) ) {
                $this->logfile( $arg[0] );
                $this->logMail( $arg[0] );
                $this->logSql( $arg[0], $arg[1] );
            }
        }
    }

    /**
     * ZariliaLogger_render::logfile()
     *
     * @return
     */
    function logfile() {
        include ZAR_ROOT_PATH . '/errorconfig.php';
        $this->data->logDate = time();
        $date = date( "F j, Y, g:i a", $this->data->logDate );
        $this->report = "[$date] ";
        /**
         */
        if ( func_num_args() == 1 ) {
            $arg = func_get_args();
            $log = $this->errortype[ $arg[0]['errno'] ] . ': ' . $arg[0]['errstr'] . ': ' . $arg[0]['errfile'] . ' ' . $this->report .= _ERR_ERRORLINE . $arg[0]['errline'];
            @error_log( "$log\015\012", 3, ZAR_ROOT_PATH . "/${log_path}/" . md5( date( "Ymd" ) ) . ".log" );
        }
    }

    /**
     * ZariliaLogger_render::logMail()
     *
     * @return
     */
    function logMail() {
        include ZAR_ROOT_PATH . '/errorconfig.php';
        if ( @$_SESSION['mailer_skip'] == true ) {
            return false;
        }
        if ( func_num_args() == 1 ) {
            $arg = func_get_args();
            $log = $this->errortype[ $arg[0]['errno'] ] . ': ' . $arg[0]['errstr'] . ': ' . $arg[0]['errfile'] . ' ' . $this->report .= _ERR_ERRORLINE . $arg[0]['errline'];
            if ( class_exists( 'ZariliaObject' ) ) {
                $zariliaMailer = getMailer();
                if ( !is_object( $zariliaMailer ) ) {
                    if ( !@error_log( $this->report, 1, $admin_email ) ) {
                        $_SESSION['mailer_skip'] = true;
                        return false;
                    }
                } else {
                    $zariliaMailer->setTemplate( 'error_report.tpl' );
                    $zariliaMailer->setToEmails( $admin_email );
                    $zariliaMailer->setFromName( 'Webmaster' );
                    $zariliaMailer->setFromEmail( $admin_email );
                    $zariliaMailer->setSubject( _ERR_PG_ERROREMSUBJECT );
                    $zariliaMailer->assign( 'SITENAME', $admin_sitename );
                    $zariliaMailer->assign( 'REPORT', $log );
                    if ( !$zariliaMailer->send() ) {
                        $_SESSION['mailer_skip'] = true;
                        return false;
                    }
                }
            } else {
                if ( !@error_log( $log, 1, $admin_email ) ) {
                    $_SESSION['mailer_skip'] = true;
                    return false;
                }
            }
        }
    }

    /**
     * ZariliaLogger_render::logSql()
     *
     * @return
     */
    function logSql() {
        /**
         * This whole function needs to be re-written to allow for the poss of Zarilia not connecting to database before an error is encounterd.
         */
        if ( func_num_args() == 2 ) {
            $arg = func_get_args();
            $date = date( 'M-d-Y', time() );
            if ( function_exists( 'hash' ) ) {
                $hashkey = hash( 'ripemd160', $date . $this->errortype[ $arg[0]['errno'] ] . ' ' . $arg[0]['errstr'] );
            } else {
                $hashkey = md5( $date . $this->errortype[ $arg[0]['errno'] ] . ' ' . $arg[0]['errstr'] );
            }
            $db = mysql_connect( ZAR_DB_HOST, ZAR_DB_USER, ZAR_DB_PASS );
            if ( mysql_select_db( "database", $db ) ) {
                $result = mysql_query( "SELECT * FROM " . ZAR_DB_PREFIX . "_errors WHERE errors_hash = '$hashkey'", $db );
                $hashcount = mysql_num_rows( $result );
                if ( $hashcount == 0 ) {
                    $errors_no = $arg[0]['errno'];
                    $errors_title = $this->errortype[ $arg[0]['errno'] ] . ' ' . $arg[0]['errstr'];
                    $errors_description = $arg[0]['errfile'] . ' ' . $this->report .= _ERR_ERRORLINE . $arg[0]['errline'];
                    $errors_ip = $this->data->logIPaddress;
                    $errors_date = time();
                    $errors_report = addslashes( $arg[1] );
                    $errors_hash = $hashkey;

                    $sql = "INSERT INTO `" . ZAR_DB_PREFIX . "_errors` ( errors_no, errors_title, errors_description, errors_ip, errors_date, errors_report, errors_hash ) VALUES (	$errors_no,	'" . mysql_real_escape_string( $errors_title ) . "', '" . mysql_real_escape_string( $errors_description ) . "', '" . mysql_real_escape_string( $errors_ip ) . "', $errors_date, '" . mysql_real_escape_string( $errors_report ) . "', '" . mysql_real_escape_string( $errors_hash ) . "' )";
                    $res = mysql_query( $sql );
                }
            }
            return true;
        }
    }

    /**
     * ZariliaLogger_render::ShowUserError()
     *
     * @return
     */
    function ShowUserError() {
        global $zariliaConfig;
        include_once ZAR_ROOT_PATH.'/class/template.php';
		$image = zarilia_img_show( 'important', $this->data->getSysInfo( 'image', 'preference' ), '', 'png', 'addons/system/images/system' );
        $zariliaTpl = new ZariliaTpl( false );
        $zariliaTpl->assign( 'error_title', $this->data->getSysInfo( 'title', _ER_PAGE_DEFAULT_TITLE ) );
        $zariliaTpl->assign( 'error_heading' , $this->data->getSysInfo( 'heading', '' ) );
        $zariliaTpl->assign( 'error_description', $this->data->getSysInfo( 'description', '' ) );
        $zariliaTpl->assign( 'error_image', $image );
        $zariliaTpl->assign( 'error_button', $this->data->getSysInfo( 'show_button', 1 ) );
        $zariliaTpl->assign( 'error_array', $this->data->sysErrors );
        $theme = ( !isset( $zariliaConfig['theme_set'] ) ) ? 'default' : $zariliaConfig['theme_set'];
        $this->doLog( $this->data->sysErrors );
        $zariliaTpl->display( ZAR_THEME_PATH . '/' . $theme . '/addons/system/system_errormessage.tpl' );
    }

    /**
     * ZariliaLogger_render::ShowPageNotFound()
     *
     * @return
     */
    function ShowPageNotFound() {
        global $zariliaConfig, $zariliaTpl;
        $status = $this->data->sysErrors[0]['errno'];
        $zariliaTpl->assign( 'error_contact', $zariliaConfig['adminmail'] );
        $zariliaTpl->assign( 'error_website', _ERR_SEARCH );
        $zariliaTpl->assign( 'lang_error_title', sprintf( constant( "_ERR_TITLE_$status"  ), $status ) );
        $zariliaTpl->assign( 'lang_error_http_footer', sprintf( constant( "_ERR_TITLE_$status" ), "HTTP $status - " ) );
        $zariliaTpl->assign( 'lang_error_desc', sprintf( constant( "_ERR_TITLE_DESC_$status" ), '' ) );
        $zariliaTpl->assign( 'lang_error_info', sprintf( _ERR_TITLE_INFO, ZAR_URL ) );
        $zariliaTpl->assign( 'lang_error_contact', sprintf( _ERR_CONTACT, $zariliaConfig['adminmail'] ) );
        $zariliaTpl->assign( 'lang_error_search', _ERR_SEARCH );
        $zariliaTpl->assign( 'error_description', constant( "_ERR_TITLE_DESC_$status" ) );
        $this->doLog( $this->data->sysErrors );
        $zariliaTpl->display( ZAR_THEME_PATH . '/' . $zariliaConfig['theme_set'] . '/addons/system/system_error_page.html' );
    }

    /**
     * ZariliaLogger_render::render()
     *
     * @return
     */
    function render() {
        echo $this->output;
    }

    function cleanPath( $path ) {
        $pathinfo = pathinfo( strip_tags( $path ) );
        if ( empty( $pathinfo['extension'] ) ) {
            return $path;
        }
        $path = str_replace( realpath( ZAR_ROOT_PATH ), ZAR_URL, $pathinfo['dirname'] );
        $path = str_replace( './', '', $path );
        $path = str_replace( '\\', '/', $path );
        $url = $path . '/' . $pathinfo['basename'];
        return $url;
    }
}

?>