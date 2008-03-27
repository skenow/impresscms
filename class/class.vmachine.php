<?php
// $Id: class.vmachine.php,v 1.1 2007/03/16 02:38:58 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//

defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );
/**
 * vMachine
 *
 * @package
 * @author Mekdrop
 * @copyright Copyright (c) 2006
 * @version $Id: class.vmachine.php,v 1.1 2007/03/16 02:38:58 catzwolf Exp $
 * @access public
 **/
class vMachine {
    var $_paths = array( 'ZariliaDatabaseFactory' => '%root_path%/class/database/databasefactory.php',
        'TextSanitizer' => '%root_path%/class/class.textsanitizer.php'
        );

    function isSetupMode()
    {
        global $zariliaOption;
        if ( isset( $zariliaOption['setupMode'] ) ) {
            if ( $zariliaOption['setupMode'] == true ) return true;
        }
        return false;
    }

    function setSetupMode( $value = true )
    {
        global $zariliaOption;
        $zariliaOption['setupMode'] = $value;
    }

    function depends( $code )
    {
        $deps = array( ZAR_ROOT_PATH . '/class/logger.php' );
        foreach ( $this->_paths as $className => $path ) {
            if ( strpos( $code, $className ) != false ) {
                $deps[] = str_replace( "%root_path%", ZAR_ROOT_PATH, $path );
            }
        }
        return $deps;
    }

    function exec( $code, $autoload = false )
    {
        global $zariliaEvents, $zariliaConfig, $zariliaOption, $zariliaTpl, $zariliaAjax;
        if ( $autoload ) {
            $deps = $this->depends( $code );
            foreach ( $deps as $file ) {
                require_once ( $file );
            }
            unset( $deps );
        }
        if ( !defined( '_OKIMG' ) ) {
            define( '_OKIMG', "<img src='img/yes.png' width='16' height='16' border='0' alt='' /> " );
        }
        if ( !defined( '_NGIMG' ) ) {
            define( '_NGIMG', "<img src='img/no.png' width='16' height='16' border='0' alt='' /> " );
        }
        $rcode = "global \$zariliaEvents, \$zariliaConfig, \$zariliaOption, \$zariliaTpl, \$zariliaAjax;
						$code";
        return eval( $rcode );// or die($rcode);
    }
}

?>