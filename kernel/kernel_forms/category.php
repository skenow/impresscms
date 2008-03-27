<?php
// $Id: category.php,v 1.2 2007/04/21 09:44:54 catzwolf Exp $
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

global $addonversion, $zariliaUser, $_callback, $do_callback;

if ( method_exists( $_callback, 'getCategoryObj' ) ) {
    $categorys = call_user_func( array( &$_callback, 'getCategoryObj' ), $do_callback->getId() );
} else {
    return false;
}

$opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0 );
require_once ZAR_ROOT_PATH . '/class/class.menubar.php';
$tabbar = new ZariliaTabMenu( $opt );
$url = $addonversion['adminpath'] . '&op=edit&amp;category_id=' . $this->getVar( 'category_id' );
$this_array = array( _MA_AD_CATEGORY_INFO => $url, _MA_AD_CATEGORY_MENUS => $url );
if ( $this->isNew() ) {
    unset( $this_array[_MA_AD_CATEGORY_MENUS] );
}
$tabbar->addTabArray( $this_array );
$tabbar->renderStart( 0, 1 );

switch ( intval( $opt ) ) {
    case 0:
    default:
        $caption = ( !$this->isNew() ) ? $caption = sprintf( _MA_AD_ECATEGORY_MODIFY, $this->getVar( 'category_title' ) ) : _MA_AD_ECATEGORY_CREATE;
        $form = new ZariliaThemeForm( $caption, 'category_form', $addonversion['adminpath'] );
        $form->setExtra( 'enctype="multipart/form-data"' );
        $category_sid = new ZariliaFormSelectSection( _MA_AD_ECATEGORY_CSECTION, 'category_sid', $this->getVar( 'category_sid' ), 1, false );
        $category_sid->setDescription( _MA_AD_SMILIES_SELECTIMAGE_DSC );
        $form->addElement( $category_sid );

        $category_id = new ZariliaFormTree( _MA_AD_ECATEGORY_CSUBCATEGORY, 'category_pid', 'category_title', '-', $this->getVar( 'category_pid' ), true, 0 );
        $category_id->addOptions( $categorys['list'], 'category_id', 'category_pid' );
        $form->addElement( $category_id, true );
        // Set display name
        $category_title = new ZariliaFormText( _MA_AD_ECATEGORY_TITLE, 'category_title', 50, 60, $this->getVar( 'category_title', 'e' ) );
        $category_title->setDescription( _MA_AD_ECATEGORY_TITLE_DSC );
        $form->addElement( $category_title, true );
        // $category_type = new ZariliaFormSelectType( _MA_AD_ECATEGORY_TYPE, 'category_type', $this->getVar( 'category_type' ) );
        // $category_type->setDescription( _MA_AD_ECATEGORY_SIDE_DSC );
        // $form->addElement( $category_type, true );
        $options['name'] = 'category_description';
        $options['value'] = $this->getVar( 'category_description', 'e' );
        $ele = new ZariliaFormEditor( _MD_AM_ECATEGORY_TEXT, $zariliaUser->getVar( 'editor' ), $options, $nohtml = false, $onfailure = "textarea" );
        $ele->setNocolspan( 1 );
        $form->addElement( $ele );

        if ( $this->isNew() ) {
            $form->addElement( new ZariliaFormSelectGroup( _MD_AM_ECATEGORY_RGRP, 'readgroup', true, array( 1, 2, 3 ), 5, true ) );
            $form->addElement( new ZariliaFormSelectGroup( _MD_AM_ECATEGORY_WGRP, 'writegroup', true, ZAR_GROUP_ADMIN, 5, true ) );
        } else {
            $perm_handler = &zarilia_gethandler( 'groupperm' );
            $form->addElement( new ZariliaFormSelectGroup( _MD_AM_ECATEGORY_RGRP, 'readgroup', true, $perm_handler->getGroupIds( 'category_read', $this->getVar( 'category_id' ) ), 5, true ) );
            $form->addElement( new ZariliaFormSelectGroup( _MD_AM_ECATEGORY_WGRP, 'writegroup', true, $perm_handler->getGroupIds( 'category_write', $this->getVar( 'category_id' ) ), 5, true ) );
        }
        $category_image = new ZariliaFormSelectImg( _MA_AD_SMILIES_SELECTIMAGE, 'category_image', $this->getVar( 'category_image' ), 'zarilia_image', 1 );
        $category_image->setDescription( _MA_AD_SMILIES_SELECTIMAGE_DSC );
        $form->addElement( $category_image );
        $category_imageside = new ZariliaFormImageSide( _MA_AD_ECATEGORY_SIDE, 'category_imageside', $this->getVar( 'category_imageside' ) );
        $category_imageside->setDescription( _MA_AD_ECATEGORY_SIDE_DSC );
        $form->addElement( $category_imageside, true );
        /*Set display name*/
        $category_weight = new ZariliaFormText( _MA_AD_ECATEGORY_WEIGHT, 'category_weight', 3, 4, $this->getVar( 'category_weight', 'e' ) );
        $category_weight->setDescription( _MA_AD_ECATEGORY_WEIGHT_DSC );
        $form->addElement( $category_weight, true );
        /*Set display name*/
        $category_display = new ZariliaFormRadioYN( _MA_AD_ECATEGORY_DISPLAY, 'category_display', $this->getVar( 'category_display' ) , ' ' . _YES . '', ' ' . _NO . '' );
        $category_display->setDescription( _MA_AD_ECATEGORY_DISPLAY_DSC );
        $form->addElement( $category_display, false );
        $form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
        $form->addElement( new ZariliaFormHidden( 'category_id', $this->getVar( 'category_id' ) ) );
        /*button_tray*/
        $form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
        $form->display();
        unset( $perm_handler );
        break;

    case 1:

        $content_handler = &zarilia_gethandler( 'content' );
        $content_type = ( $this->getVar( 'category_sid' ) ) ? $content_handler->getcType( $this->getVar( 'category_sid' ) ) : '';
        $form = new ZariliaThemeForm( 'Add Menu', 'menu_form', 'index.php' );
        if ( !$this->isNew() ) {
            $menus_handler = &zarilia_gethandler( 'menus' );
            $menu = $menus_handler->getMenuItem( 1, 'section', $this->getVar( 'category_id' ) );
            if ( !$menu ) {
                $menu = $menus_handler->create();
                $menu->setVar( 'menu_title', $this->getVar( 'category_title', 'e' ) );
                $menu->setVar( 'menu_type', 0 );
            }
            $category_menuside = new ZariliaFormSelectMenu( _MA_AD_CATEGORY_MENUTYPE, 'menu_type', $menu->getVar( 'menu_type' ), 1, true );
            $category_menuside->setDescription( _MA_AD_ECATEGORY_SIDE_DSC );
            $form->addElement( $category_menuside, false );
            $category_title = new ZariliaFormText( _MA_AD_CATEGORY_MENUTITLE, 'menu_title', 50, 60, $menu->getVar( 'menu_title', 'e' ) );
            $category_title->setDescription( _MA_AD_ECATEGORY_TITLE_DSC );
            $form->addElement( $category_title, true );

            $menu_perm_handler = &zarilia_gethandler( 'groupperm' );
            $menu_perms = $menu_perm_handler->getGroupIds( 'menu_read', $menu->getVar( 'menu_id' ) );
            if ( !count( $menu_perms ) && $this->isNew() ) {
                $menu_perms = $perm_handler->getGroupIds( 'category_read', $this->getVar( 'category_id' ) );
            }
            $form->addElement( new ZariliaFormSelectGroup( _MA_AD_CATEGORY_MENUALEVEL, 'menu_read', true, $menu_perms, 5, true ) );
            // Hidden values//
            $form->addElement( new ZariliaFormHidden( 'menu_id', $menu->getVar( 'menu_id' ) ) );
            $form->addElement( new ZariliaFormHidden( 'menu_mid', 1 ) );
            $form->addElement( new ZariliaFormHidden( 'menu_name', 'section' ) );
            $form->addElement( new ZariliaFormHidden( 'menu_sectionid', $this->getVar( 'category_id' ) ) );
            $form->addElement( new ZariliaFormHidden( 'menu_link', '{X_SITEURL}/index.php?page_type=' . $content_type . '&cid=' . $this->getVar( 'category_id' ) ) );
            $form->addElement( new ZariliaFormHidden( 'menu_display', 1 ) );
            $form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
            $form->addElement( new ZariliaFormHidden( 'fct', 'menus' ) );
            $form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
            $form->display();
        }
        unset( $menu_perm_handler );
        break;
}

?>