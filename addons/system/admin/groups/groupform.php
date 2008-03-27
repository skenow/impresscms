<?php
// $Id: groupform.php,v 1.2 2007/04/21 09:42:25 catzwolf Exp $
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
include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
include_once( ZAR_ROOT_PATH . '/addons/system/constants.php' );

$addon_handler = &zarilia_gethandler( 'addon' );
/*
* Get list of system addons aval to the system
*/
$handle = opendir( ZAR_ROOT_PATH . '/addons/system/admin' );
while ( false != $file = readdir( $handle ) ) {
    if ( strtolower( $file ) != 'cvs' && !preg_match( "/[.]/", $file ) && is_dir( ZAR_ROOT_PATH . '/addons/system/admin/' . $file ) ) {
		if (file_exists(ZAR_ROOT_PATH . '/addons/system/admin/' . $file . '/zarilia_version.php')) {
		    require ZAR_ROOT_PATH . '/addons/system/admin/' . $file . '/zarilia_version.php';
	        if ( !empty( $addonversion['category'] ) ) {
		        $cat_array[ $addonversion['category'] ] = ucfirst( $file );
	        }
//		    unset( $addonversion );
			natsort( $cat_array );
		}
    }
}
$form = new ZariliaThemeForm( $form_title, 'group_form', $addonversion['adminpath'] );
$form->addElement( new ZariliaFormText( _MA_AD_NAME, 'name', 30, 50, $name_value ), true );
$form->addElement( new ZariliaFormTextArea( _MA_AD_DESCRIPTION, 'desc', $desc_value ), true );
/*
* List system admin addons and their permissions
*/
$s_cat_checkbox = new ZariliaFormCheckBox( _MA_AD_SYSTEMRIGHTS, 'system_catids[]', $s_cat_value );
$s_cat_checkbox->addOptionArray( $cat_array );
$form->addElement( $s_cat_checkbox );
/*
*/
$a_mod_checkbox = new ZariliaFormCheckBox( _MA_AD_ACTIVERIGHTS, 'admin_mids[]', $a_mod_value );
$criteria = new CriteriaCompo( new Criteria( 'hasadmin', 1 ) );
$criteria->add( new Criteria( 'isactive', 1 ) );
$criteria->add( new Criteria( 'dirname', 'system', '<>' ) );
$a_mod_checkbox->addOptionArray( $addon_handler->getList( $criteria ) );
$form->addElement( $a_mod_checkbox );
/*
*/
$r_mod_checkbox = new ZariliaFormCheckBox( _MA_AD_ACCESSRIGHTS, 'read_mids[]', $r_mod_value );
$criteria = new CriteriaCompo( new Criteria( 'hasmain', 1 ) );
$criteria->add( new Criteria( 'isactive', 1 ) );
$r_mod_checkbox->addOptionArray( $addon_handler->getList( $criteria ) );
$form->addElement( $r_mod_checkbox );
/*
*/
$new_blocks_array = array();
$blocks_array = ZariliaBlock::getAllBlocks( 'list', ZAR_SIDEBLOCK_LEFT );
$r_lblock_checkbox = new ZariliaFormCheckBox( '<b>' . _MA_AD_BLOCKRIGHTS . ' ' . _LEFT . '</b><br />', 'read_bids[]', $r_block_value );
foreach ( $blocks_array as $key => $value ) {
    $new_blocks_array[$key] = "<a href='" . ZAR_URL . "/addons/system/index.php?fct=blocksadmin&amp;op=edit&amp;bid=${key}'>${value}&nbsp;(ID: ${key} )</a><br />";
}
$r_lblock_checkbox->addOptionArray( $new_blocks_array );
$form->addElement( $r_lblock_checkbox );
/*
*/
$r_cblock_checkbox = new ZariliaFormCheckBox( '<b>' . _MA_AD_BLOCKRIGHTS . ' ' . _CENTER . '</b><br />', 'read_bids[]', $r_block_value );
$new_blocks_array = array();
$blocks_array = ZariliaBlock::getAllBlocks( 'list', ZAR_CENTERBLOCK_ALL );
foreach ( $blocks_array as $key => $value ) {
    $new_blocks_array[$key] = "<a href='" . ZAR_URL . "/addons/system/index.php?fct=blocksadmin&amp;op=edit&amp;bid=${key}'>${value}&nbsp;(ID: ${key} )</a><br />";
}
$r_cblock_checkbox->addOptionArray( $new_blocks_array );
$form->addElement( $r_cblock_checkbox );
/*
*/
$r_rblock_checkbox = new ZariliaFormCheckBox( "<b>" . _MA_AD_BLOCKRIGHTS . " " . _RIGHT . "</b><br />", "read_bids[]", $r_block_value );
$new_blocks_array = array();
$blocks_array = ZariliaBlock::getAllBlocks( "list", ZAR_SIDEBLOCK_RIGHT );
foreach ( $blocks_array as $key => $value ) {
    $new_blocks_array[$key] = "<a href='" . ZAR_URL . "/addons/system/index.php?fct=blocksadmin&amp;op=edit&amp;bid=" . $key . "'>" . $value . "&nbsp;(ID: " . $key . ")</a><br />";
}
$r_rblock_checkbox->addOptionArray( $new_blocks_array );
$form->addElement( $r_rblock_checkbox );
/*
*/
$new_blocks_array = array();
$blocks_array = ZariliaBlock::getAllBlocks( 'list', ZAR_BLOCK_INVISIBLE );
$r_iblock_checkbox = new ZariliaFormCheckBox( '<b>' . _MA_AD_BLOCKRIGHTS . ' ' . _INVISIBLE . '</b><br />', 'read_bids[]', $r_block_value );
foreach ( $blocks_array as $key => $value ) {
    $new_blocks_array[$key] = "<a href='" . ZAR_URL . "/addons/system/index.php?fct=blocksadmin&amp;op=edit&amp;bid=" . $key . "'>" . $value . "&nbsp;(ID: " . $key . ")</a><br />";
}
$r_iblock_checkbox->addOptionArray( $new_blocks_array );
$form->addElement( $r_iblock_checkbox );
if ( $clonegroup == 1 ) {
    $g_id_value = 0;
}

$form->addElement( new ZariliaFormHidden( 'op', $op_value ) );
$form->addElement( new ZariliaFormHidden( 'fct', 'groups' ) );
if ( !empty( $g_id_value ) ) {
    $form->addElement( new ZariliaFormHidden( 'g_id', $g_id_value ) );
}
$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
$form->display();

?>