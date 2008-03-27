<?php

class charsetConvert {

	function to7bit($text,$from_enc='auto') {
		$text = html_entity_decode(mb_convert_encoding($text,'HTML-ENTITIES',$from_enc));
	    return $text;
	}

}

?>