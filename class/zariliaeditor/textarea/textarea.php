<?php
// $Id: textarea.php,v 1.1 2007/03/16 02:42:28 catzwolf Exp $
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
 * Pseudo class
 *
 * @author phppp (D.J.)
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */

class FormTextArea extends ZariliaFormTextArea {
    /**
     * Constructor
     *
     * @param array $configs Editor Options
     * @param binary $checkCompatible true - return false on failure
     */
    function FormTextArea( $configs, $checkCompatible = false )
    {
        if ( !empty( $configs ) ) {
            foreach( $configs as $key => $val ) {
                $this->$key = $val;
            }
        }
        $value = isset( $this->value ) ? $this->value : "";
        $name = isset( $this->name ) ? $this->name : "";
        $rows = isset( $this->rows ) ? $this->rows : 15;
        $cols = isset( $this->cols ) ? $this->cols : 75;
        $this->ZariliaFormTextArea( "", $name, $value, $rows, $cols );
    }

    function setConfig( $configs )
    {
        foreach( $configs as $key => $val ) {
            $this->$key = $val;
        }
    }
}

?>
