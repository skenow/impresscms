<?php
// $Id: formselectgroup.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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

/**
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */

/**
 * Parent
 */
include_once ZAR_ROOT_PATH . "/class/zariliaform/formselect.php";

/**
 * A select field with a choice of available groups
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormSelectGroup extends ZariliaFormSelect {
    /**
     * Constructor
     *
     * @param string $caption
     * @param string $name
     * @param bool $include_anon Include group "anonymous"?
     * @param mixed $value Pre-selected value (or array of them).
     * @param int $size Number or rows. "1" makes a drop-down-list.
     * @param bool $multiple Allow multiple selections?
     */
    function ZariliaFormSelectGroup( $caption, $name, $include_anon = false, $value = null, $size = 1, $multiple = false, $addempty = 0 )
    {
        $this->ZariliaFormSelect( $caption, $name, $value, $size, $multiple );
        $member_handler = &zarilia_gethandler( 'member' );
        if ( $addempty != 0 ) {
            $this->addOption( -1, 'No Selection' );
        }
        if ( !$include_anon ) {
            $criteria = new CriteriaCompo();
            $criteria->add( new Criteria( 'groupid', ZAR_GROUP_ANONYMOUS, '!=' ) );
            $criteria->add( new Criteria( 'groupid', ZAR_GROUP_BANNED, '!=' ) );
            $this->addOptionArray( $member_handler->getGroupList( $criteria ) );
        } else {
            $this->addOptionArray( $member_handler->getGroupList() );
        }
    }
}

?>