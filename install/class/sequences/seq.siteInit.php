<?php
$javacript = "<script type='text/javascript'>
	<!--//
	/**
	 * DHTML email validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
	 */

	function zariliaFormValidate_zariliainstall() {
		myform = document.zariliainstall;
		if ( myform.adminlogin.value == \"\" ) {
			alert(\"" . sprintf( _INSTALL_INVAlID_ENTRY, _INSTALL_L37A ) . "\"); myform.adminlogin.focus(); return false;
		}
		if ( myform.adminuname.value ==\"\"  ) {
			alert(\"" . sprintf( _INSTALL_INVAlID_ENTRY, _INSTALL_L37 ) . "'\"); myform.adminuname.focus(); return false;
		}
		if ( myform.adminlogin.value == myform.adminuname.value  ) {
			alert(\"" . _INSTALL_INVAlID_MUSTNOT_MATCH . "\"); myform.adminuname.focus(); myform.adminuname.select(); return false;
		}
	    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
	    if (!myform.adminmail.value.match(re)) {
	       alert(\"" . _INSTALL_INVAlID_EMAIL . "\");
	       myform.adminmail.focus();
	       myform.adminmail.select();
	       return (false);
	    }
		if ( myform.adminpass.value == \"\"  ) {
			alert(\"" . sprintf( _INSTALL_INVAlID_ENTRY, _INSTALL_L39 ) . "'\"); myform.adminpass.focus(); return false;
		}
		if ( myform.adminpass2.value == \"\"  ) {
			alert(\"" . sprintf( _INSTALL_INVAlID_ENTRY, _INSTALL_L74 ) . "'\"); myform.adminpass2.focus(); return false;
		}
		if ( myform.adminpass.value != myform.adminpass2.value  ) {
			alert(\"" . _INSTALL_INVAlID_MUST_MATCH . "\"); myform.adminpass2.focus(); myform.adminpass2.select(); return false;
		}
        return true;
	}
	//--></script>";
$onclick = 'onsubmit="return zariliaFormValidate_zariliainstall();"';
$installer->setArgs( 'javascript', $javacript );
$installer->setArgs( 'onclick', $onclick );
$installer->setArgs( 'title', _INSTALL_L36 );
$installer->setArgs( 'subtitle', _INSTALL_L36A );
$installer->render( 'install.siteInit.tpl.php' );

?>