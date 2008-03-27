<?php
// $Id: mledititem_tabs_top.php,v 1.1 2007/03/16 02:40:50 catzwolf Exp $
// ------------------------------------------------------------------------ //
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

require_once ZAR_ROOT_PATH . '/class/mledit/mledititem.php';

class mlEditItem_tabs_top
extends mlEditItem {
    function mlEditItem_tabs_top ( $languages, $form = '', $text = '' )
    {
        global $zariliaOption, $zariliaConfig;
        include_once ZAR_ROOT_PATH . '/class/class.menubar.php';
        $rez = array();
        $id = -1;
        $i = 0;
        foreach( $languages as $language ) {
            if ( $zariliaConfig['language'] == $language->getVar( 'lang_name' ) ) {
                $id = $i;
            } else {
                $i++;
            }
            $flagurl = ZAR_URL . '/images/flags/' . $language->getVar( 'lang_image' );
            $rez["<img src=\"$flagurl\">" . ucfirst( $language->getVar( 'lang_name' ) )] = 'javascript:SelectLanguageForm(' . $language->getVar( 'lang_id' ) . ',' . $zariliaOption['multilanguage_forms_count'] . ');';
        }
        $tabbar = new ZariliaTabMenu( $id, true );
        $tabbar->addTabArray( $rez );
        unset( $rez );
        $this->init( $tabbar->renderStart() );
        $this->add( $text );
    }
}

?>