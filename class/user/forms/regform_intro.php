<?php
/**
 * $Id: regform_intro.php,v 1.1 2007/04/21 09:45:41 catzwolf Exp $ Untitled 1.php v0.0 14/04/2007 05:47:43 John
 *
 * @Zarilia - 	PHP Content Management System
 * @copyright 2007 Zarilia
 * @Author : 	John (AKA Catzwolf)
 * @URL : 		http://zarilia.com
 * @Project :	Zarilia CMS
 */
global $zariliaConfigUser, $zariliaTpl;
$myts = &MyTextSanitizer::getInstance();

/*form start*/
require ZAR_ROOT_PATH . '/class/zariliaformloader.php';
$register_form = new ZariliaThemeForm( _US_REG_PRIVACY_HEADING, 'registerform', 'index.php', 'post' );
$register_form->addElement( new ZariliaFormlabel( _US_DISCLAIMER, $myts->displayTarea( $this->_zariliaConfigUser['reg_disclaimer'], 1, 0, 0, 0, 1, false ), 1 ), false );

$agree_chk = new ZariliaFormCheckBox( '', 'agree_disc', 0, 1 );
$agree_chk->addOption( 1, _US_IAGREE );
$agree_chk->setRequired( true );
$register_form->addElement( $agree_chk, true );

if ( isset( $this->_hidden ) && !empty( $this->_hidden ) ) {
    foreach( $this->_hidden as $k => $v ) {
        $register_form->addElement( new ZariliaFormHidden( $k, $v ) );
    }
}
/**
 */
$content['form'] = $register_form;
$content['file'] = 'info';
$this->addOptions(
    array( 'title' => _US_REGINTRO,
        'subtitle' => _US_REGINTRO_DSC,
        'content' => $content,
        )
    );

?>