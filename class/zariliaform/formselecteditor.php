<?php
// $Id: formselecteditor.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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
 * base class
 */
include_once ZAR_ROOT_PATH . "/class/zariliaform/formselect.php";

/**
 * A select box with available editors
 * 
 * @package kernel
 * @subpackage form
 * @author phppp (D.J.) 
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormSelectEditor extends ZariliaFormSelect {
    /**
     * Constructor
     * 
     * @param object $form the form calling the editor selection
     * @param string $name editor name
     * @param string $value Pre-selected text value
     * @param bool $noHtml dohtml disabled
     */
    function ZariliaFormSelectEditor( $caption, $name = "editor", $value = null, $noHtml = false, $size = 1, $multiple = false, $isAdmin = false, $extra = '' )
    {
        global $zariliaConfig;

        $this->ZariliaFormSelect( $caption, $name, $value, $size, $multiple );
        $editor_handler = &zarilia_gethandler( "editor" );
        if ( $isAdmin == true ) {
			$_array = $editor_handler->getList( $noHtml );
        } else {
            $_array = $zariliaConfig['user_select'];
        } 
        $this->addOptionArray( $_array );
        /**
         * $extra = 'onchange="if(this.options[this.selectedIndex].value.length > 0 ){
         * window.document.forms.' . $form->getName() . '.skipValidationJS.value=1;
         * window.document.forms.' . $form->getName() . '.submit();
         * }"';
         * //
         */
        if ( $extra ) {
            $this->setExtra( $extra );
        } 
    } 
} 

?>