<?php
// $Id: form_hash.php,v 1.1 2007/03/16 02:34:43 catzwolf Exp $
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

$form = new ZariliaThemeForm( 'Hash', 'hash', ZAR_URL . '/addons/cpTools/admin/index.php' );
$form->addElement( new ZariliaFormText( 'Select CP folder to hash (must be on local server):', 'source', 50, 60, ZAR_ROOT_PATH ), true );
$form->addElement( new ZariliaFormHidden( 'op', 'exec_command' ) );
$form->addElement( new ZariliaFormHidden( 'command', 'hash' ) );
$form->addElement( new ZariliaFormButton( '', 'submit', 'Hash', 'submit' ) );
$form->display();
?>