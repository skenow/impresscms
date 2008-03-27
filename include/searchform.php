<?php
// $Id: searchform.php,v 1.2 2007/04/21 09:44:17 catzwolf Exp $
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
include_once ZAR_ROOT_PATH . "/class/zariliaformloader.php";
// create form
$search_form = new ZariliaThemeForm( _SR_SEARCH, "search", "search.php", 'get' );
// create form elements
$search_form -> addElement( new ZariliaFormText( _SR_KEYWORDS, "query", 30, 255, htmlspecialchars( stripslashes( implode( " ", $queries ) ), ENT_QUOTES ) ), true );
$type_select = new ZariliaFormSelect( _SR_TYPE, "andor", $andor );
$type_select -> addOptionArray( array( "AND" => _SR_ALL, "OR" => _SR_ANY, "exact" => _SR_EXACT ) );
$search_form -> addElement( $type_select );
if ( !empty( $mids ) ) {
    $mods_checkbox = new ZariliaFormCheckBox( _SR_SEARCHIN, "mids[]", $mids );
} else {
    $mods_checkbox = new ZariliaFormCheckBox( _SR_SEARCHIN, "mids[]", $mid );
} 
if ( empty( $addons ) ) {
    $criteria = new CriteriaCompo();
    $criteria -> add( new Criteria( 'hassearch', 1 ) );
    $criteria -> add( new Criteria( 'isactive', 1 ) );
    if ( !empty( $available_addons ) ) {
        $criteria -> add( new Criteria( 'mid', "(" . implode( ',', $available_addons ) . ")", 'IN' ) );
    } 
    $addon_handler = &zarilia_gethandler( 'addon' );
    $mods_checkbox -> addOptionArray( $addon_handler -> getList( $criteria ) );
} else {
    foreach ( $addons as $mid => $addon ) {
        $addon_array[$mid] = $addon -> getVar( 'name' );
    } 
    $mods_checkbox -> addOptionArray( $addon_array );
} 
$search_form -> addElement( $mods_checkbox );
if ( $zariliaConfigSearch['keyword_min'] > 0 ) {
    $search_form -> addElement( new ZariliaFormLabel( _SR_SEARCHRULE, sprintf( _SR_KEYIGNORE, $zariliaConfigSearch['keyword_min'] ) ) );
} 
$search_form -> addElement( new ZariliaFormHidden( "op", "results" ) );
$search_form -> addElement( new ZariliaFormButton( "", "submit", _SR_SEARCH, "submit" ) );

?>