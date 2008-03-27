<?php
// $Id: formdatetime.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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
 * Date and time selection field
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 * @subpackage form
 */
class ZariliaFormDateTime extends ZariliaFormElementTray {
    function ZariliaFormDateTime( $caption, $name, $size = 15, $value = 0, $addtime = false ) {
        $this->ZariliaFormElementTray( $caption, '&nbsp;' );
        $value = ( intval( $value ) > 4000 ) ? $value : 0;
        if ( ( int )$value > 0 ) {
            $datetime = getDate( $value );
        } else {
            $datetime['hours'] = 0;
            $datetime['minutes'] = 0;
        }
        $this->addElement( new ZariliaFormTextDateSelect( '', $name . '[date]', $size, @$datetime[0] ) );
        if ( $addtime == true ) {
            $timearray = array();
            for ( $i = 0; $i < 24; $i++ ) {
                for ( $j = 0; $j < 60; $j = $j + 10 ) {
                    $key = ( $i * 3600 ) + ( $j * 60 );
                    $timearray[$key] = ( $j != 0 ) ? $i . ':' . $j : $i . ':0' . $j;
                }
            }
            ksort( $timearray );
            $timeselect = new ZariliaFormSelect( '', $name . '[time]', $datetime['hours'] * 3600 + 600 * ceil( $datetime['minutes'] / 10 ) );
            $timeselect->addOptionArray( $timearray );
            $this->addElement( $timeselect );
        }
    }
}

?>