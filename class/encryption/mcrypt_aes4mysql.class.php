<?php

require_once ZAR_FRAMEWORK_PATH.'/encryption/base.class.php';

// author: rolf at winmutt dot com & pixelchutes AT gmail DOT com
class ZariliaEncryption_Mcrypt_AES4Mysql
	extends ZariliaEncryption {

    function decrypt( $val, $ky ) {
      $mode = MCRYPT_MODE_ECB;
      $enc = MCRYPT_RIJNDAEL_128;
      $dec = @mcrypt_decrypt($enc, $ky, $val, $mode, @mcrypt_create_iv( @mcrypt_get_iv_size($enc, $mode), MCRYPT_DEV_URANDOM ) );
           return rtrim( $dec, ( ( ord(substr( $dec, strlen( $dec )-1, 1 )) >= 0 and ord(substr( $dec, strlen( $dec )-1, 1 ) ) <= 16 ) ? chr(ord(substr( $dec, strlen( $dec )-1, 1 ))): null) );
    }

	function encrypt($val,$ky) {
	    $mode=MCRYPT_MODE_ECB;
	    $enc=MCRYPT_RIJNDAEL_128;
	    $val=str_pad($val, (16*(floor(strlen($val) / 16)+(strlen($val) % 16==0?2:1))), chr(16-(strlen($val) % 16)));
		return mcrypt_encrypt($enc, $ky, $val, $mode, mcrypt_create_iv( mcrypt_get_iv_size($enc, $mode), MCRYPT_DEV_URANDOM));
	}

}

?>