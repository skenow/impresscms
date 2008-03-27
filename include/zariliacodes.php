<?php
// $Id: zariliacodes.php,v 1.1 2007/03/16 02:39:07 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//

/*
*  displayes zariliaCode buttons and target textarea to which zariliacodes are inserted
*  $textarea_id is a unique id of the target textarea
*/
function zariliaCodeTarea($textarea_id, $cols=60, $rows=15, $suffix=null, $areacontent = '')
{
	$hiddentext = isset($suffix) ? 'zariliaHiddenText'.trim($suffix) : 'zariliaHiddenText';
	//Hack for url, email ...., the anchor is for having a link on [_More...]
	echo "<a name='moresmiley'></a><img src='".ZAR_URL."/images/url.gif' alt='url' onmouseover='style.cursor=\"hand\"' onclick='zariliaCodeUrl(\"$textarea_id\", \"".htmlspecialchars(_ENTERURL, ENT_QUOTES)."\", \"".htmlspecialchars(_ENTERWEBTITLE, ENT_QUOTES)."\");'/>&nbsp;<img src='".ZAR_URL."/images/email.gif' alt='email' onmouseover='style.cursor=\"hand\"' onclick='zariliaCodeEmail(\"$textarea_id\", \"".htmlspecialchars(_ENTEREMAIL, ENT_QUOTES)."\");' />&nbsp;<img src='".ZAR_URL."/images/imgsrc.gif' alt='imgsrc' onmouseover='style.cursor=\"hand\"' onclick='zariliaCodeImg(\"$textarea_id\", \"".htmlspecialchars(_ENTERIMGURL, ENT_QUOTES)."\", \"".htmlspecialchars(_ENTERIMGPOS, ENT_QUOTES)."\", \"".htmlspecialchars(_IMGPOSRORL, ENT_QUOTES)."\", \"".htmlspecialchars(_ERRORIMGPOS, ENT_QUOTES)."\");' />&nbsp;<img src='".ZAR_URL."/images/image.gif' alt='image' onmouseover='style.cursor=\"hand\"' onclick='openWithSelfMain(\"".ZAR_URL."/imagemanager.php?target=".$textarea_id."\",\"imgmanager\",400,430);' />&nbsp;<img src='".ZAR_URL."/images/code.gif' alt='code' onmouseover='style.cursor=\"hand\"' onclick='zariliaCodeCode(\"$textarea_id\", \"".htmlspecialchars(_ENTERCODE, ENT_QUOTES)."\");' />&nbsp;<img src='".ZAR_URL."/images/quote.gif' alt='quote' onmouseover='style.cursor=\"hand\"' onclick='zariliaCodeQuote(\"$textarea_id\");'/><br />\n";

	$sizearray = array("xx-small", "x-small", "small", "medium", "large", "x-large", "xx-large");
	echo "<select id='".$textarea_id."Size' onchange='setVisible(\"zariliaHiddenText\");setElementSize(\"".$hiddentext."\",this.options[this.selectedIndex].value);'>\n";
	echo "<option value='SIZE'>"._SIZE."</option>\n";
	foreach ( $sizearray as $size ) {
		echo "<option value='$size'>$size</option>\n";
	}
	echo "</select>\n";

	$fontarray = array("Arial", "Courier", "Georgia", "Helvetica", "Impact", "Verdana");
	echo "<select id='".$textarea_id."Font' onchange='setVisible(\"zariliaHiddenText\");setElementFont(\"".$hiddentext."\",this.options[this.selectedIndex].value);'>\n";
	echo "<option value='FONT'>"._FONT."</option>\n";
	foreach ( $fontarray as $font ) {
		echo "<option value='$font'>$font</option>\n";
	}
	echo "</select>\n";

	$colorarray = array("00", "33", "66", "99", "CC", "FF");
	echo "<select id='".$textarea_id."Color' onchange='setVisible(\"zariliaHiddenText\");setElementColor(\"".$hiddentext."\",this.options[this.selectedIndex].value);'>\n";
	echo "<option value='COLOR'>"._COLOR."</option>\n";
	foreach ( $colorarray as $color1 ) {
		foreach ( $colorarray as $color2 ) {
			foreach ( $colorarray as $color3 ) {
				echo "<option value='".$color1.$color2.$color3."' style='background-color:#".$color1.$color2.$color3.";color:#".$color1.$color2.$color3.";'>#".$color1.$color2.$color3."</option>\n";
			}
		}
	}
	echo "</select><span id='".$hiddentext."'>"._EXAMPLE."</span><br />\n";

    $areacontent = (isset( $GLOBALS[$textarea_id]) && $areacontent == '' ) ? $GLOBALS[$textarea_id] : $areacontent;
	echo "
	  <span>
		<img onMouseOver=\"style.cursor='pointer';\"	src='" . ZAR_URL . "/images/bold.gif' 		align='middle' title='Make text Bold' 			alt='bold' 			onclick='javascript:doVisible(\"" . $text_value . "\"); doBold(\"" . $text_value . "\");' />&nbsp;
		<img onMouseOver=\"style.cursor='pointer';\"	src='" . ZAR_URL . "/images/italic.gif' 		align='middle' title='Make text italic' 		alt='italic' 		onclick='javascript:doVisible(\"" . $text_value . "\"); doItalic(\"" . $text_value . "\");'  />&nbsp;
		<img onMouseOver=\"style.cursor='pointer';\" 	src='" . ZAR_URL . "/images/underline.gif' 	align='middle' title='Make text underline' 	alt='underline' 	onclick='javascript:doVisible(\"" . $text_value . "\"); doUnderline(\"" . $text_value . "\");' />&nbsp;
		<img onMouseOver=\"style.cursor='pointer';\"  	src='" . ZAR_URL . "/images/linethrough.gif' 	align='middle' title='Make text linethrough' 	alt='linethrough' 	onclick='javascript:doVisible(\"" . $text_value . "\"); doLineThrough(\"" . $text_value . "\");' />&nbsp;
	  </span>";
	echo "<input type='text' id='".$textarea_id."Addtext' size='20' />&nbsp;<input type='button' class='formbutton' onclick='zariliaCodeText(\"$textarea_id\", \"".$hiddentext."\", \"".htmlspecialchars(_ENTERTEXTBOX, ENT_QUOTES)."\")' value='"._ADD."' /><br /><br /><textarea id='".$textarea_id."' name='".$textarea_id."' cols='$cols' rows='$rows'>".$areacontent."</textarea><br />\n";
}

/*
*  Displays smilie image buttons used to insert smilie codes to a target textarea in a form
* $textarea_id is a unique of the target textarea
*/
function zariliaSmilies($textarea_id)
{
	$myts =& MyTextSanitizer::getInstance();
	$smiles = $myts->getSmileys();
	if (empty($smileys)) {
		$db =& ZariliaDatabaseFactory :: getdatabaseconnection();
		if ($result = $db->Execute('SELECT * FROM '.$db->prefix('smiles').' WHERE display=1')) {
			while ($smiles = $result->FetchRow()) {
			//hack smilies move for the smilies !!
				echo "<img src='".ZAR_UPLOAD_URL."/".htmlspecialchars($smiles['smile_url'])."' border='0' onmouseover='style.cursor=\"hand\"' alt='' onclick='zariliaCodeSmilie(\"".$textarea_id."\", \" ".$smiles['code']." \");' />";
			//fin du hack
			}
		}
	} else {
		$count = count($smiles);
		for ($i = 0; $i < $count; $i++) {
			if ($smiles[$i]['display'] == 1) {
			//hack bis
				echo "<img src='".ZAR_UPLOAD_URL."/".htmlSpecialChars($smiles['smile_url'])."' border='0' alt='' onclick='zariliaCodeSmilie(\"".$textarea_id."\", \" ".$smiles[$i]['code']." \");' onmouseover='style.cursor=\"hand\"' />";
			//fin du hack
			}
		}
	}
	//hack for more
	echo "&nbsp;[<a href='#moresmiley' onmouseover='style.cursor=\"hand\"' alt='' onclick='openWithSelfMain(\"".ZAR_URL."/misc.php?type=smilies&amp;target=".$textarea_id."\",\"smilies\",300,475);'>"._MORE."</a>]";
}  //fin du hack
?>