<?php
// $Id: profileoption.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
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
 * ZariliaProfileOption
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: profileoption.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
 * @access public
 **/
class ZariliaProfileOption extends ZariliaObject
{
    /**
     * Constructor
     */
    function ZariliaProfileOption()
    {
        $this->ZariliaObject();
        $this->initVar('profileop_id', XOBJ_DTYPE_INT, null);
        $this->initVar('profileop_name', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('profileop_value', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->initVar('profile_id', XOBJ_DTYPE_INT, 0);
    }
}

/**
 * ZariliaProfileOptionHandler
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: profileoption.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
 * @access public
 **/
class ZariliaProfileOptionHandler extends ZariliaPersistableObjectHandler
{
   /**
     * ZariliaProfileOptionHandler::ZariliaProfileOptionHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaProfileOptionHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'profileoption', 'zariliaprofileoption', 'profileop_id', 'profileop_name' );
    }
}
?>