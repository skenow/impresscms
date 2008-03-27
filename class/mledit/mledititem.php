<?php
// $Id: mledititem.php,v 1.1 2007/03/16 02:40:50 catzwolf Exp $
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

class mlEditItem {
    var $_content = '';

    function mlEditItem()
    {
        die( 'Error: mlEditItem cannot be called directly!' );
    }

    function init( $content = '' )
    {
        $this->_content = $content;
    }

    function render()
    {
        return $this->_content;
    }

    function add( $text )
    {
        $this->_content .= $text;
    }
}

?>