<?php
/**
 * zariliaErrorHandler
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: class_errorhandler.php,v 1.3 2007/04/12 14:15:23 catzwolf Exp $
 * @access public
 */
class zariliaErrorHandler {
    var $errors = array();
    var $info = array();

    /**
     * zariliaErrorHandler::zariliaErrorHandler()
     */
    function zariliaErrorHandler() {
        // dummy
    }

    function setSysError( $errno, $errstr, $errfile = '', $errline = '' ) {
        $this->errors[] = compact( 'errno', 'errstr', 'errfile', 'errline' );
        // print_r_html( $this->errors );
    }

    function setSysErrorArray( $vars ) {
        //$this->errors[] = extract( 'errno', 'errstr', 'errfile', 'errline' );
        // print_r_html( $this->errors );
    }

    function getSysErrorCount() {
        return count( $this->errors );
    }

    function sysRender( $title = '', $heading = '', $description = '', $image = '', $show_button = true ) {
        $this->info = compact( 'title', 'heading', 'description', 'image', 'show_button' );
        /**
         */
        include_once ZAR_ROOT_PATH . '/class/logger_render.php';
        $log_render = new ZariliaLogger_render( $this );
        $log_render->doLog();
        $log_render->ShowUserError();
    }

    function errorArray( $errno = '' ) {
        $this->errorArray = array( '0001' => 'Fatal: Could not connect to the database.',
            '0002' => '',
            '0003' => '',
            '0004' => '',
            '0005' => '',
            '0006' => '',
            '0007' => '',
            );
        return $this->errorArray[$errno];
    }
}

?>