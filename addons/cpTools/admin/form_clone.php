<?php
// $Id: form_clone.php,v 1.1 2007/03/16 02:34:43 catzwolf Exp $
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

$form = new ZariliaThemeForm( 'Clone addon', 'clone', ZAR_URL . '/addons/cpTools/admin/index.php' );
$addons = new ZariliaFormSelect( 'Select addon to clone:', 'addon', null, 10 );
$dir = ZAR_ROOT_PATH . '/addons/';
if ( $handle = opendir( $dir ) ) {
    while ( false !== ( $file = readdir( $handle ) ) ) {
        if ( $file[0] == '.' ) continue;
        if ( $file == 'CVS' ) continue;
        if ( $file == 'SVN' ) continue;
        if ( $file == 'system' ) continue;
        if ( $file == 'cpTools' ) continue;
        if ( is_dir( "$dir/$file" ) ) {
            $addons->addOption( $file, $file );
        }
    }
}
$addons->setExtra( 'onchange = "if (document.getElementById(\'newname\').value == \'\') document.getElementById(\'newname\').value = this.value;"' );
$form->addElement( $addons, true );
$newaddon = new ZariliaFormText( 'New addon name:' , 'newname', 50, 60 );
$form->addElement( $newaddon, true );
$form->addElement( new ZariliaFormHidden( 'op', 'exec_command' ) );
$form->addElement( new ZariliaFormHidden( 'command', 'clone' ) );
$form->addElement( new ZariliaFormButton( '', 'submit', 'Clone', 'submit' ) );
$form->display();

?>