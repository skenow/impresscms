<?php
// $Id: dhtmltextarea.php,v 1.1 2007/03/16 02:42:25 catzwolf Exp $
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
 * @copyright copyright (c) 2007 Zarilia Project - http://www.zarilia.com
 */



class widgEditor 
	extends ZariliaFormTextArea {
    /**
     * Constructor
     * 
     * @param array $configs Editor Options
     * @param binary $checkCompatible true - return false on failure
     */
    function widgEditor( $configs, $checkCompatible = false ) {
		global $zariliaConfig, $zariliaTpl, $zariliaOption;

		if (!isset($zariliaOption['widgEditor_loaded']) && isset($zariliaTpl)) {

			$zariliaTpl->addCss( ZAR_URL.'/class/zariliaeditor/widgeditor/css/info.css');
			$zariliaTpl->addCss( ZAR_URL.'/class/zariliaeditor/widgeditor/css/main.css');
			$zariliaTpl->addCss( ZAR_URL.'/class/zariliaeditor/widgeditor/css/widgEditor.css');

			$zariliaTpl->addScript( ZAR_URL.'/class/zariliaeditor/widgeditor/scripts/widgEditor.js');

			$zariliaOption['widgEditor_loaded'] = true;
		}

		$this->Value = &$this->value;
		
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
		$this->setExtra('class="widgEditor"');
    } 

    function setConfig( $configs ) {
        foreach( $configs as $key => $val ) {
            $this->Config[$key] = $val;
        } 
    } 
} 

?>