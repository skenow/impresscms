<?php 
// $Id: sampleform.inc.php,v 1.1 2007/03/16 02:40:56 catzwolf Exp $
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
 * ZARILIA editor usage example
 * 
 * @author phppp (D.J.) 
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */

defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' ); 

/*
 * Edit form with selected editor
 */
$sample_form = new ZariliaThemeForm( '', 'sample_form', "action.php" );
$sample_form -> setExtra( 'enctype="multipart/form-data"' );
// Not required but for user-friendly concern
$editor = !empty( $_REQUEST['editor'] ) ? $_REQUEST['editor'] : "";
if ( !empty( $editor ) ) {
    setcookie( "editor", $editor ); // save to cookie
} else {
    /*
	*  Or use user pre-selected editor through profile
	*/
    // if ( is_object( $zariliaUser ) ) {
    // $editor = $zariliaUser -> getVar( "editor" ); // Need set through user profile
    // }
    // Add the editor selection box
    // If dohtml is disabled, set $noHtml = true
    $sample_form -> addElement( new ZariliaFormSelectEditor( $sample_form, "editor", $editor, $noHtml = false ) ); 
    // options for the +editor
    // required configs
    $options['name'] = 'required_element';
    $options['value'] = empty( $_REQUEST['message'] )? "" : $_REQUEST['message']; 
    // optional configs
    $options['rows'] = 25; // default value = 5
    $options['cols'] = 60; // default value = 50
    $options['width'] = '100%'; // default value = 100%
    $options['height'] = '400px'; // default value = 400px       
    // "textarea": if the selected editor with name of $editor can not be created, the editor "textarea" will be used
    // if no $onFailure is set, then the first available editor will be used
    // If dohtml is disabled, set $noHtml to true
    $sample_form -> addElement( new ZariliaFormEditor( _MD_MESSAGEC, $editor, $editor_configs, $nohtml = false, $onfailure = "textarea" ), true );
    $sample_form -> addElement( new ZariliaFormText( "SOME REQUIRED ELEMENTS", "required_element2", 50, 255, $required_element2 ), true );
    $sample_form -> addElement( new ZariliaFormButton( '', 'save', _SUBMIT, "submit" ) );
    $sample_form -> display();
} 

?>
