<?php

require_once ZAR_FRAMEWORK_PATH.'/encryption/base.class.php';

// author: NicholasSolutions @ http://www.expertsrt.com/scripts/Matt/EasyCrypt.html
class ZariliaEncryption_Mcrypt_Blowfish_CBC
	extends ZariliaEncryption {

	function encrypt($string, $key){
		$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
		   $iv = mcrypt_create_iv($iv_size, MCRYPT_DEV_URANDOM);
		   $string = mcrypt_encrypt(MCRYPT_BLOWFISH, $key,
                            $string, MCRYPT_MODE_CBC, $iv);

	   return base64_encode(serialize(array(base64_encode($string), base64_encode($iv))));
	}

	 //decodes a string
	 //(the first argument is an array as returned by easy_encrypt()) - replaced with seriliazed base64 string
	 function decrypt($string, $key){
		   $cyph_arr = unserialize(base64_decode($string));
		   $out = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, base64_decode($cyph_arr[0]),
                         MCRYPT_MODE_CBC, base64_decode($cyph_arr[1]));

		   return trim($out);
	  }

}

?>