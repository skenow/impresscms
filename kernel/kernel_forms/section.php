<?php
// $Id: section.php,v 1.2 2007/04/21 09:44:54 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
require_once ZAR_ROOT_PATH . '/class/class.menubar.php';

global $addonversion, $zariliaUser, $perm_handler;

$opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0 );
$tabbar = new ZariliaTabMenu( $opt );
$url = $addonversion['adminpath'] . '&op=create&amp;section_id=' . $this->getVar( 'section_id' );
$this_array = array( _MA_AD_SECTION_INFO => $url );
if ( !$this->isNew() ) {
    $this_array = array_merge( $this_array, array( _MA_AD_SECTION_MENUS => $url ) ) ;
}
$tabbar->addTabArray( $this_array );
$tabbar->renderStart( 0, 1 );

switch ( intval( $opt ) ) {
    case 0:
        $caption = ( !$this->isNew() ) ? $caption = sprintf( _MA_AD_ESECTION_MODIFY, $this->getVar( 'section_title' ) ) : _MA_AD_ESECTION_CREATE;

        $form = new ZariliaThemeForm( $caption, 'section_form', $addonversion['adminpath'] );
        $form->setExtra( 'enctype="multipart/form-data"' );

        /*Set display name*/
        $section_title = new ZariliaFormText( _MA_AD_ESECTION_TITLE, 'section_title', 50, 60, $this->getVar( 'section_title', 'e' ) );
        $section_title->setDescription( _MA_AD_ESECTION_TITLE_DSC );
        $form->addElement( $section_title, true );

        $section_type = new ZariliaFormSelectType( _MA_AD_ESECTION_TYPE, 'section_type', $this->getVar( 'section_type' ) );
        $section_type->setDescription( _MA_AD_ESECTION_SIDE_DSC );
        $form->addElement( $section_type, true );

        $options['name'] = 'section_description';
        $options['value'] = $this->getVar( 'section_description', 'e' );
        $ele = new ZariliaFormEditor( _MD_AM_ESECTION_TEXT, $zariliaUser->getVar( "editor" ), $options, $nohtml = false, $onfailure = "textarea" );
        $ele->setNocolspan( 1 );
        $form->addElement( $ele );

        if ( $this->isNew() ) {
            $form->addElement( new ZariliaFormSelectGroup( _MD_AM_ESECTION_RGRP, 'readgroup', true, ZAR_GROUP_ADMIN, 5, true ) );
            $form->addElement( new ZariliaFormSelectGroup( _MD_AM_ESECTION_WGRP, 'writegroup', true, ZAR_GROUP_ADMIN, 5, true ) );
        } else {
            $form->addElement( new ZariliaFormSelectGroup( _MD_AM_ESECTION_RGRP, 'readgroup', true, $perm_handler->getGroupIds( 'section_read', $this->getVar( 'section_id' ) ), 5, true ) );
            $form->addElement( new ZariliaFormSelectGroup( _MD_AM_ESECTION_WGRP, 'writegroup', true, $perm_handler->getGroupIds( 'section_write', $this->getVar( 'section_id' ) ), 5, true ) );
        }

        $section_image = new ZariliaFormSelectImg( _MA_AD_SMILIES_SELECTIMAGE, 'section_image', $this->getVar( 'section_image' ), 'zarilia_image', 1 );
        $section_image->setDescription( _MA_AD_SMILIES_SELECTIMAGE_DSC );
        $form->addElement( $section_image );

        $section_imageside = new ZariliaFormImageSide( _MA_AD_ESECTION_SIDE, 'section_imageside', $this->getVar( 'section_imageside' ) );
        $section_imageside->setDescription( _MA_AD_ESECTION_SIDE_DSC );
        $form->addElement( $section_imageside, true );

        /*Set display name*/
        $section_weight = new ZariliaFormText( _MA_AD_ESECTION_WEIGHT, 'section_weight', 3, 4, $this->getVar( 'section_weight', 'e' ) );
        $section_weight->setDescription( _MA_AD_ESECTION_WEIGHT_DSC );
        $form->addElement( $section_weight, true );

        /*Set display name*/
        $section_display = new ZariliaFormRadioYN( _MA_AD_ESECTION_DISPLAY, 'section_display', $this->getVar( 'section_display' ) , ' ' . _YES . '', ' ' . _NO . '' );
        $section_display->setDescription( _MA_AD_ESECTION_DISPLAY_DSC );
        $form->addElement( $section_display, false );

        $form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
        $form->addElement( new ZariliaFormHidden( 'section_id', $this->getVar( 'section_id' ) ) );
        $form->addElement( new ZariliaFormHidden( 'section_is', $this->getVar( 'section_is' ) ) );
        /*button_tray*/
        $button_tray = new ZariliaFormElementTray( '', '' );
        $button_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
        $button_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ) );
        $button_tray->addElement( new ZariliaFormButton( '', 'submit', _SUBMIT, 'submit' ) );
        $form->addElement( $button_tray );
        $form->display();
        break;
    case 1:
        if ( !$this->isNew() ) {
            $form = new ZariliaThemeForm( 'Add Menu', 'menu_form', 'index.php' );
            $menus_handler = &zarilia_gethandler( 'menus' );
            $menu = $menus_handler->getMenuItem( 1, 'section', $this->getVar( 'section_id' ) );
            if ( !$menu ) {
                $menu = $menus_handler->create();
                $menu->setVar( 'menu_title', $this->getVar( 'section_title', 'e' ) );
                $menu->setVar( 'menu_type', 0 );
            }

            $section_imageside = new ZariliaFormSelectMenu( _MA_AD_SECTION_MENUTYPE, 'menu_type', $menu->getVar( 'menu_type' ), 1, true );
            $section_imageside->setDescription( _MA_AD_ESECTION_SIDE_DSC );
            $form->addElement( $section_imageside, false );

            $section_title = new ZariliaFormText( _MA_AD_SECTION_MENUTITLE, 'menu_title', 50, 60, $menu->getVar( 'menu_title', 'e' ) );
            $section_title->setDescription( _MA_AD_ESECTION_TITLE_DSC );
            $form->addElement( $section_title, true );

            $menu_perm_handler = &zarilia_gethandler( 'groupperm' );
            $menu_perms = $menu_perm_handler->getGroupIds( 'menu_read', $menu->getVar( 'menu_id' ) );
            if ( !count( $menu_perms ) && $this->isNew() ) {
                $menu_perms = $perm_handler->getGroupIds( 'section_read', $this->getVar( 'section_id' ) );
            }
            $form->addElement( new ZariliaFormSelectGroup( _MA_AD_SECTION_MENUALEVEL, 'menu_read', true, $menu_perms, 5, true ) );
            // Hidden values//
            $form->addElement( new ZariliaFormHidden( 'menu_id', $menu->getVar( 'menu_id' ) ) );
            $form->addElement( new ZariliaFormHidden( 'menu_mid', 1 ) );
            $form->addElement( new ZariliaFormHidden( 'menu_name', 'section' ) );
            $form->addElement( new ZariliaFormHidden( 'menu_sectionid', $this->getVar( 'section_id' ) ) );
            $form->addElement( new ZariliaFormHidden( 'menu_link', '{X_SITEURL}/index.php?page_type=' . $this->getVar( 'section_type' ) ) );
            $form->addElement( new ZariliaFormHidden( 'menu_display', 1 ) );

            $form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
            $form->addElement( new ZariliaFormHidden( 'fct', 'menus' ) );
			$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
            $form->display();
        }
        break;
}

?>