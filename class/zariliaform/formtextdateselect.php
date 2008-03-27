<?php
// $Id: formtextdateselect.php,v 1.1 2007/03/16 02:41:02 catzwolf Exp $
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
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */

/**
 * A text field with calendar popup
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */

class ZariliaFormTextDateSelect extends ZariliaFormCalendar {
    function ZariliaFormTextDateSelect( $caption, $name, $size = 20, $value = '', $showtime = true )
    {

		$calendar_options['showsTime'] = $showtime;
        $field_attributes['size'] = $size;
		$value = ( $value == 0 ) ? '' : $value;
		if ( $value != '' || $value > 0 ) {
			$field_attributes['value'] = ( is_numeric( $value ) ) ? strftime( '%m/%d/%Y %H:%M', $value ) : $value;
        } else {
			$field_attributes['value'] = '';
        }
        $this->ZariliaFormCalendar( $caption, $name, $value, $calendar_options, $field_attributes );
    }
}

?>