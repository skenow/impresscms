<?php

require_once ZAR_FRAMEWORK_PATH.'/encryption/base.class.php';

// author: thilo-at-hardtware.de
class ZariliaEncryption_Internal_XorBase64 
	extends ZariliaEncryption {

    function encrypt($string, $key) {
		$result = '';
	    for($i=0; $i<strlen($string); $i++) {
		  $char = substr($string, $i, 1);
	      $keychar = substr($key, ($i % strlen($key))-1, 1);
		  $char = chr(ord($char)+ord($keychar));
	      $result.=$char;
		}

	    return base64_encode($result);
	}

	function decrypt($string, $key) {
	    $result = '';
		$string = base64_decode($string);

	    for($i=0; $i<strlen($string); $i++) {
		  $char = substr($string, $i, 1);
	      $keychar = substr($key, ($i % strlen($key))-1, 1);
		  $char = chr(ord($char)-ord($keychar));
	      $result.=$char;
	    }

		return $result;
	}

}

?>