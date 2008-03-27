<?php
// $Id: class.errors.php,v 1.1 2007/04/22 07:21:32 catzwolf Exp $
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
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

include_once ZAR_ROOT_PATH . '/language/' . $GLOBALS['zariliaConfig']['language'] . '/error.php';
/**
 * ZariliaErrorsHandler
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: class.errors.php,v 1.1 2007/04/22 07:21:32 catzwolf Exp $
 * @access public
 */
class ZariliaErrorsHandler {
    /**
     * ZariliaErrorsHandler::ZariliaErrorsHandler()
     */
    function ZariliaErrorsHandler() {
        // dummy
    }
    /**
     * ZariliaErrorsHandler::getSystemError()
     *
     * @param mixed $errno
     * @param array $custom_array
     * @return
     */
    function getSystemError( $errno, $custom_array = array(), $extra ) {
        // Note: Numbers 1 - 1000 are reserved for system error messages only
        // Numbers 1001 > are reserved for modules only.
        $error_array = array(
			'001' => _ER_ARRAY_101,
            '9999' => _ER_PAGE_9999
		);

		if ( count( $custom_array ) > 0 ) {
            $error_array = array_merge_recursive( $error_array, $custom_array );
        }

		$_errno = array_keys( $error_array );
        if ( !in_array( $errno, $_errno ) ) {
            $errno = '9999';
        }
        return $error_array[$errno];
    }
}

?>