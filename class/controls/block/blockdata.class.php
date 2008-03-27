<?php

class ZariliaControl_BlockData {
	
	var $blockname = '';

	function ZariliaControl_BlockData($blockname) {
		$this->blockname = $blockname;
	}

	function message($msg) {
		$_SESSION['blocks'][$this->blockname] = '<b>'.date('Y-m-d h:i:s').':</b> '.$msg.'<br />';
	}

}

?>