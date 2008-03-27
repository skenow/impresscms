<?php
class ZariliaControl_CPSetup_ProgressBar {

	var $_bordercolor = "#000000";
	var $_backcolor = "#FFFFCC";
	var $_textcolor = "#CC0000";
	var $_fillcolor = "#FF00CC";

	function renderProgressBar ($name, $value = 0, $width = 250, $height = 20) {
		$style = sprintf("overflow:hidden; min-width: %dpx; min-height: %dpx; width: %dpx; height: %dpx;",$width, $height, $width, $height);
		$text = "<div id=\"".$name."_obj\" style=\"position:relative; $style\">";
		$text .= "<div id=\"".$name."_bg\" style=\"background-color:#FFFFCC; $style border-color:#CCCCCC; border-style:solid; border-width: 1px; position:absolute; left:0px; top: 0px;\"><div style=\"$style color:#0000FF; text-align:center\" id=\"".$name."_bg_text\">$value%</div></div>";
		$rez = $this->recalc_width($value, $width);
		$text .= sprintf("<div id=\"%s_graph\" style=\"background-color:#0000FF; overflow:hidden; min-width: %dpx; min-height: %dpx; width: %dpx; height: %dpx; border-style: none; left: 1px; top:1px; position:absolute;\"><div id=\"%s_graph_text\" style=\"%s color:#FFFFCC; text-align:center\">%d%%</div></div>", $name, $rez, $height-2,  $rez, $height-2, $name, $style, $value);
		$text .= "</div>";
		return $text;
	}

	function recalc_width($value = 0, $mwidth = 250) {
		return ($mwidth/100*$value);
	}

}



?>