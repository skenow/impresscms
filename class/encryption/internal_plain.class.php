<?php

require_once ZAR_FRAMEWORK_PATH.'/encryption/base.class.php';

class ZariliaEncryption_Internal_Plain
	extends ZariliaEncryption {

    function encrypt($string, $key) {
	    return $string;
	}

	function decrypt($string, $key) {
	    return $string;
	}

}

?>