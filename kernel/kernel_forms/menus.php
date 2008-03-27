<?php
// $Id: menus.php,v 1.3 2007/04/21 09:44:54 catzwolf Exp $
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
//require_once ZAR_ROOT_PATH . '/class/tree.php';
require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

global $addon_handler, $addonversion, $_callback, $do_callback;
static $_cachedAddon_list;

if ( method_exists( $_callback, 'getMenublock' ) ) {
    $allmenus = call_user_func( array( &$_callback, 'getMenublock' ), null, true, true, true );
} else {
    return false;
}
// if ( !empty( $_cachedAddon_list ) ) {
// $addon_list = &$_cachedAddon_list;
// } else {
$criteria = new CriteriaCompo();
//$criteria->add( new Criteria( 'menu_pid', 0 ) );
$addon_list = &$addon_handler->getList( $criteria );
// $_cachedAddon_list = &$addon_list;
// }
// $addon_list[-1] = 'Insert Break';
// $addon_list[0] = 'No Addons';
ksort( $addon_list );

$form = new ZariliaThemeForm( ( !$this->isNew() ) ? sprintf( _MA_AD_EMENUS_MODIFY, $this->getVar( 'menu_title' ) ) : _MA_AD_EMENUS_CREATE, 'menu_form', $addonversion['adminpath'] );
$form->setExtra( 'enctype="multipart/form-data"' );
/**
 */
$_menus = array( 'mainmenu' => _MAINMENU, 'usermenu' => _USERMENU, 'topmenu' => _TOPMENU );
/*
if ( $_menu_type && $this->isNew() ) {
    $this->setVar( 'menu_type', $_menu_type );
}
*/

$form->addElement( new ZariliaFormSelectMenu( _MA_AD_EMENUS_MENUS, 'menu_type', $this->getVar( 'menu_type' ) ), true );
/*Access level*/
if ( $this->isNew() ) {
    $form->addElement( new ZariliaFormSelectGroup( _MA_AD_EMENUS_GROUPS, 'menu_read', true, ZAR_GROUP_ADMIN, 5, true ) );
} else {
    $menu_perm_handler = &zarilia_gethandler( 'groupperm' );
    $form->addElement( new ZariliaFormSelectGroup( _MA_AD_EMENUS_GROUPS, 'menu_read', true, $menu_perm_handler->getGroupIds( 'menu_read', $this->getVar( 'menu_id' ) ), 5, true ) );
    unset( $menu_perm_handler );
}

$menu_tree = new ZariliaFormTree( _MA_AD_EMENUS_MENUPOSITION, 'menu_pid', 'menu_title', '-', $this->getVar( 'menu_pid' ), true, 0 );
$menu_tree->addOptions( $allmenus['list'], 'menu_id', 'menu_pid' );
$form->addElement( $menu_tree, true );

$menu_target = new ZariliaFormSelect( _MA_AD_EMENUS_NAV, 'menu_target', $this->getVar( 'menu_target' ) );
$menu_target->addOption( "", _MA_AD_EMENUS_NONE );
$menu_target->addOption( "_self", _MA_AD_EMENUS_SELF );
$menu_target->addOption( "_blank", _MA_AD_EMENUS_BLANK );
$menu_target->addOption( "_parent", _MA_AD_EMENUS_PARENT );
$menu_target->addOption( "_top", _MA_AD_EMENUS_TOP );
$form->addElement( $menu_target );

$menu_title = new ZariliaFormText( _MA_AD_EMENUS_NAME, 'menu_title', 50, 60, $this->getVar( 'menu_title', 'e' ) );
$menu_title->setDescription( _MA_AD_EMENUS_NAME_DSC );
$form->addElement( $menu_title, false );

$menu_link = new ZariliaFormText( _MA_AD_EMENUS_LINK, 'menu_link', 75, 255, $this->getVar( 'menu_link', 'e' ) );
$menu_link->setDescription( _MA_AD_EMENUS_LINK_DSC );
$form->addElement( $menu_link, false );

$menu_mid = new ZariliaFormSelect( _MA_AD_EMENUS_ADDON, 'menu_mid', $this->getVar( 'menu_mid' ) );
$menu_mid->addOption( '{X_HR}', _MA_AD_EMENUS_SPACER );
$menu_mid->addOptionArray( $addon_list );
$form->addElement( $menu_mid, false );

$menu_image = new ZariliaFormSelectImg( _MA_AD_EMENUS_IMAGE, 'menu_image', $this->getVar( 'menu_image' ), $id = 'zarilia_image', 0 );
$menu_image->setDescription( _MA_AD_EMENUS_IMAGE_DSC );
$form->addElement( $menu_image );

$menu_weight = new ZariliaFormText( _MA_AD_EMENUS_WEIGHT, 'menu_weight', 5, 5, $this->getVar( 'menu_weight', 'e' ) );
$menu_weight->setDescription( _MA_AD_EMENUS_NAME_DSC );
$form->addElement( $menu_weight, false );
$menu_display = new ZariliaFormRadioYN( _MA_AD_EMENUS_DISPLAY, 'menu_display', $this->getVar( 'menu_display' ) , ' ' . _YES . '', ' ' . _NO . '' );
$menu_display->setDescription( _MA_AD_EMENUS_DISPLAY_DSC );
$form->addElement( $menu_display, true );
$menu_class = new ZariliaFormText( _MA_AD_EMENUS_CLASS, 'menu_class', 15, 15, $this->getVar( 'menu_class', 'e' ) );
$menu_class->setDescription( _MA_AD_EMENUS_CLASS_DSC );
$form->addElement( $menu_class, false );

/*hidden values*/
if ( $this->getVar( 'menu_id' ) ) {
    $form->addElement( new ZariliaFormHidden( 'menu_id', $this->getVar( 'menu_id' ) ) );
}
$form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
/*button_tray*/
$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
$form->display();

?>