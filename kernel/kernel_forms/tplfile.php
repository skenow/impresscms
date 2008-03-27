<?php
// $Id: tplfile.php,v 1.1 2007/03/16 02:44:13 catzwolf Exp $
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

global $addonversion, $zariliaUser;
require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

$form = new ZariliaThemeForm( _MD_EDITTEMPLATE, 'template_form', $addonversion['adminpath'], 'post', true );
$form->setExtra( 'enctype="multipart/form-data"' );
$form->addElement( new ZariliaFormLabel( _MD_FILENAME, $this->getVar( 'tpl_file', 's' ) ) );
$form->addElement( new ZariliaFormLabel( _MD_FILEDESC, $this->getVar( 'tpl_desc', 's' ) ) );
$form->addElement( new ZariliaFormLabel( _MD_LASTMOD, formatTimestamp( $this->getVar( 'tpl_lastmodified', 's' ), 'l' ) ) );

$options['name'] = 'html';
$options['rows'] = 40;
$options['cols'] = 160;
$options['value'] = $this->getVar( 'tpl_source', 'e' );
$textarea = new ZariliaFormEditor( _AM_CONTENT, "textarea", $options, true, $onfailure = "textarea", 1 );
// $textarea->setDescription( '<span style="font-size:x-small;font-weight:bold;">' . _AM_USEFULTAGS . '</span><br /><span style="font-size:x-small;font-weight:normal;">' . sprintf( _AM_BLOCKTAG1, '{X_SITEURL}', ZAR_URL . '/' ) . '</span>' );
$form->addElement( $textarea, true );
// $form->addElement( new ZariliaFormTextArea( _MD_FILEHTML, 'html', $this->getVar( 'tpl_source', 'e' ), 50, '100%' ) );
$form->addElement( new ZariliaFormHidden( 'id', $this->getVar( 'tpl_id' ) ) );
$form->addElement( new ZariliaFormHidden( 'op', 'edittpl' ) );
$form->addElement( new ZariliaFormHidden( 'moddir', $this->getVar( 'tpl_addon' ) ) );

$button_tray = new ZariliaFormElementTray( '', '' );
$button_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
$button_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ) );
if ( $this->getVar( 'tpl_tplset' ) != 'default' ) {
    $button_tray->addElement( new ZariliaFormButton( '', 'submit', _SUBMIT, 'submit' ) );
} else {
    $button_tray->addElement( new ZariliaFormButton( '', 'previewtpl', _DISPLAY, 'submit' ) );
}
$form->addElement( $button_tray );

?>