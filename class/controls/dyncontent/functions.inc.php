<?php

function getQueryVar($name, $value = 0 ) {
	global $zariliaOption;
	if ( isset( $zariliaOption['query'][$name]) ) {
		$value = $zariliaOption['query'][$name];
	}
	return $value;
}

?>