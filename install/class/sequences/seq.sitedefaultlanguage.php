<?php
include '../mainfile.php';
/*insert New user*/

/*Set page Title and subtitle*/
$installer->setArgs( 'title', _INSTALL_L166 );
$installer->setArgs( 'subtitle', _INSTALL_L166a );
/*Javascript required for validation*/
$installer->setArgs( 'javascript', "<script type='text/javascript'>
	<!--//
	/**
	 * DHTML email validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
	 */
	function zariliaFormValidate_zariliainstall() {
		myform = document.zariliainstall;
		if ( myform.name.value == \"\" ) {
			alert(\"" . sprintf( _INSTALL_INVAlID_ENTRY, _INSTALL_L37A ) . "\"); myform.name.focus(); return false;
		}
		if ( myform.code.value == \"\" ) {
			alert(\"" . sprintf( _INSTALL_INVAlID_ENTRY, _INSTALL_L37A ) . "\"); myform.code.focus(); return false;
		}
		if ( myform.charset.value == \"\" ) {
			alert(\"" . sprintf( _INSTALL_INVAlID_ENTRY, _INSTALL_L37A ) . "\"); myform.charset.focus(); return false;
		}
        return true;
	}
	//--></script>" );
$installer->setArgs( 'onclick', 'onsubmit="return zariliaFormValidate_zariliainstall();"' );

/*Flag Selection vars*/
$lang = $_SESSION[$zariliaOption['InstallPrefix']]['language'];
$installer->setArgs( 'firstfile', ZAR_URL . '/images/flags/' . strtolower( $lang ) . '.gif' );
$flag_arr = getFileList( ZAR_ROOT_PATH . '/images/flags/' );
foreach ( $flag_arr as $flag ) {
    $name = substr( $flag, 0, strrpos( $flag, '.' ) );
    $installer->addVars( 'flags', $flag );
    $installer->addVars( 'flagname', ucfirst( $name ) );
    if ( strtolower( $name ) == $lang ) {
        $installer->addVars( 'selected', 'selected="selected"' );
    } else {
        $installer->addVars( 'selected', '' );
    }
}
/*Language Selection vars*/
$langarr = getDirList( ZAR_ROOT_PATH . '/language/' );
foreach ( $langarr as $lang ) {
    $installer->addVars( 'languages', ucfirst( $lang ) );
    if ( strtolower( $lang ) == $_SESSION[$zariliaOption['InstallPrefix']]['language'] ) {
        $installer->addVars( 'selected', 'selected="selected"' );
    } else {
        $installer->addVars( 'selected', '' );
    }
}

if ( !isset( $_SESSION[$zariliaOption['InstallPrefix']]['admin'] ) || isset( $_POST['submit'] ) ) {
    unset( $_POST['submit'], $_POST['op'] );
    $_SESSION[$zariliaOption['InstallPrefix']]['admin'] = $_POST;
}
$installer->render( 'install.defaultlangselect.tpl.php' );

?>