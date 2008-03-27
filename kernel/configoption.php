<?php
// $Id: configoption.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
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
 *
 * @author John Neill AKA Catzwolf <catzwolf@zarilia.com>
 * @copyright copyright (c) 2006 Zarilia
 */

/**
 * Zarilia Age Class
 *
 * @package kernel
 * @author John Neill AKA Catzwolf
 * @copyright (c) 2006 Zarilia
 */
class ZariliaConfigOption extends ZariliaObject
{
    /**
     * Constructor
     */
    function ZariliaConfigOption()
    {
        $this->ZariliaObject();
        $this->initVar('confop_id', XOBJ_DTYPE_INT, null);
        $this->initVar('confop_name', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('confop_value', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('conf_id', XOBJ_DTYPE_INT, 0);
    }
}

/**
 * ZariliaAgeHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: configoption.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
 * @access public
 */
class ZariliaConfigOptionHandler extends ZariliaPersistableObjectHandler
{
   /**
     * ZariliaConfigOptionHandler::ZariliaConfigOptionHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaConfigOptionHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'configoption', 'zariliaconfigoption', 'confop_id' );
    }
}
?>