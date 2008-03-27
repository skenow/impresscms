<?php
// $Id: formeditor.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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
 * ZariliaEditor hanlder
 *
 * @author D.J.
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 * @subpackage form
 */
class ZariliaFormEditor extends ZariliaFormTextArea {
    var $editor;

    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name "name" attribute
     * @param string $value Initial text
     * @param array $configs configures
     * @param bool $noHtml use non-WYSIWYG eitor onfailure
     * @param string $OnFailure editor to be used if current one failed
     */
    function ZariliaFormEditor( $caption, $name, $editor_configs = null, $noHtml = false, $OnFailure = "", $noColspan = 0 )
    {
        global $zariliaConfig;
        $this->ZariliaFormTextArea( $caption, $editor_configs["name"] );
        $editor_handler = &zarilia_gethandler( "editor" );
        $editor_configs['rows'] = ( isset( $editor_configs['rows'] ) ) ? $editor_configs['rows'] : $zariliaConfig['rows']; //25; // default value = 5
        $editor_configs['cols'] = ( isset( $editor_configs['cols'] ) ) ? $editor_configs['cols'] : $zariliaConfig['cols']; // 60; // default value = 50
        $editor_configs['width'] = ( isset( $editor_configs['width'] ) ) ? $editor_configs['width'] : $zariliaConfig['width'] . "%"; // '100%'; // default value = 100%
        $editor_configs['height'] = ( isset( $editor_configs['height'] ) ) ? $editor_configs['height'] : $zariliaConfig['height'] . "px"; // '400px'; // default value = 400px
        $this->editor = &$editor_handler->get( $name, $editor_configs, $noHtml, $OnFailure );
        $this->setNocolspan( intval( $noColspan ) );
    }

	function setValue($value) {
		$this->editor->value = $value;
		$this->editor->_value = $value;
	}

	function getValue() {
		return $this->editor->value;
	}

    function render()
    {
		$this->editor->setName($this->getName());
        return $this->editor->render();
    }
}

?>
